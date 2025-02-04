<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Mail\VerificationEmail;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:' . User::class,
            'password' => ['required',Rules\Password::defaults()],
            'phone' => 'nullable|string|max:255',
        ],[],[
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'password' => 'Password',
            'phone' => 'Phone',
        ]);

        if($validator->fails()){
            return ApiResponse::sendResponse(422, 'Register Validation Error.', $validator->errors());
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone ?? null,
            'longitude' => $request->longitude ?? null,
            'latitude' => $request->latitude ?? null,
            'google_id' => $request->google_id ?? null,
        ]);

        $token = Str::random(64);
        DB::table('email_verifications')->insert([
            'email' => $user->email,
            'token' => $token,
        ]);
//        Mail::to($user->email)->send(new VerificationEmail(Crypt::encryptString($user->email),$user->first_name, Crypt::encryptString($token)));

        try {
            Mail::to($user->email)->send(new VerificationEmail(Crypt::encryptString($user->email), $user->first_name, Crypt::encryptString($token)));
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
        }

        $data['token']= $user->createToken('APIToken')->plainTextToken;
        $data['first_name']= $user->first_name;
        $data['last_name']= $user->last_name;
        $data['email']= $user->email;
        return ApiResponse::sendResponse(201,'User Created Successfully',$data);

    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => ['required'],
        ],[],[
            'email' => 'Email',
            'password' => 'Password',
        ]);

        if($validator->fails()){
            return ApiResponse::sendResponse(422, 'Login Validation Error.', $validator->errors());
        }

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $data['token']= $user->createToken('APIToken')->plainTextToken;
            $data['first_name']= $user->first_name;
            $data['last_name']= $user->last_name;
            $data['email']= $user->email;
            return ApiResponse::sendResponse(200,'User Logged In Successfully',$data);
        }
        return ApiResponse::sendResponse(422,'These credentials do not match our records.',[]);
    }

    public function verify(Request $request)
    {
        $token = Crypt::decryptString($request->query('token'));
        $email = Crypt::decryptString($request->query('email'));

        $record = DB::table('email_verifications')
            ->where('token', $token)
            ->where('email', $email)
            ->first();

        if (!$record) {
            return ApiResponse::sendResponse(400, 'Invalid Token');
        }

        if ($request->header('User-Agent') && str_contains($request->header('User-Agent'), 'Android')) {

            $user = User::where('email', $email)->first();
            if ($user) {
                $user->email_verified_at = now();
                $user->save();
                DB::table('email_verifications')->where('email', $record->email)->delete();

                $link ='gadetguru://verify?email=' . $email ;
                return ApiResponse::sendResponse(200, 'Email Verified Successfully', $link);
            }
            return ApiResponse::sendResponse(400, 'User Not Found Failed To Verify Email');
        }

        $user = User::where('email', $email)->first();
        if ($user) {
            Log::info('User found:', $user->toArray());

            $user->email_verified_at = now();
            $user->save();
            Log::info('User after update:', $user->toArray());

            DB::table('email_verifications')->where('email', $record->email)->delete();
            // route to the verification successfully view in front
            $url ='/verify?email=' . $email . '&token=' . $token; ;
            return ApiResponse::sendResponse(200, 'Email Verified Successfully', $url);
        }
        $url = '/verification-error?email=' . $email . '&token=' . $token;
        return ApiResponse::sendResponse(200, 'User Not Found', $url);

    }

    public function sendVerificationEmail(Request $request)
    {
        if ($request->user('web')->hasVerifiedEmail()) {
            return ApiResponse::sendResponse(200, 'User already verified.', null);
        }

        $request->user('web')->sendEmailVerificationNotification();

        return ApiResponse::sendResponse(200,'Email verification link sent on your email address.', null);
    }

    public function resetPassword(Request $request)
    {
        //user's email must be sent with the request
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        //check otp then change it and reset pasword and send the response
        $user = User::where('email', $request->email)->first();
        if (!$user)
        {
            return ApiResponse::sendResponse(200, 'User not found.', []);
        }
        else
        {
            if ($user->otp_till < now()) {
                return ApiResponse::sendResponse(200, 'Your OTP Has Expired.', []);
            }
            if ($user->otp != 'access'){
                return ApiResponse::sendResponse(200, 'Your OTP Need Verification.', []);
            }
            if ($user->otp_till >= now() && $user->otp == 'access') {
                $user->email_verified_at = now();
                $user->password= Hash::make($request->password);
                $user->resetOTP();
                $user->save();
                return ApiResponse::sendResponse(200, 'Password Reset Successfully.', []);
            }
            return ApiResponse::sendResponse(200, 'Something Went Wrong ! Please try again Later.', []);
        }



    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::sendResponse(200,'Logged Out Successfully',[]);
    }
}
