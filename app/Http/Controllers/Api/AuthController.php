<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:' . User::class,
            'password' => ['required',Rules\Password::defaults()],
            'phone' => 'nullable|string|max:255',
        ],[],[
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'phone' => 'Phone',
        ]);

        if($validator->fails()){
            return ApiResponse::sendResponse(422, 'Register Validation Error.', $validator->errors());
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone ?? null,
            'longitude' => $request->longitude ?? null,
            'latitude' => $request->latitude ?? null,
            'google_id' => $request->google_id ?? null,
        ]);

        $data['token']= $user->createToken('APIToken')->plainTextToken;
        $data['name']= $user->name;
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
            $data['name']= $user->name;
            $data['email']= $user->email;
            return ApiResponse::sendResponse(200,'User Logged In Successfully',$data);
        }
        return ApiResponse::sendResponse(422,'These credentials do not match our records.',[]);
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
