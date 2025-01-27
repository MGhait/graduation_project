<?php

namespace App\Http\Controllers\Api;


use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Mail\OTPEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OTPController extends Controller
{
    public function forgetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ],[
            'email.email' => 'Please Enter a Valid Email Address',
            'email.required' => 'Please Enter Your Email Address',
            'email.exists' => 'Email Address Does Not Exist',
        ],[
            'email' => 'Email',
        ]);

        $user = User::where('email', request('email'))->first();

        if (!$user) {
            return ApiResponse::sendResponse(422,'The provided credentials are incorrect.');
        }

        $user->generateOTP();
        // send OTP to sms using provider
//        $this->mailtrapApi->sendEmail(
//            $user->email,
//            'Your OTP Code',
//            'Hello ' . $user->name . ', Your OTP code is ' . $user->otp
//        );
        Mail::to($user->email)->send(new OTPEmail($user->first_name, $user->otp));
//        $data['redirect_url'] = route('otp.verify', ['email' => $user->email]);
//        $data['link'] = url('/verify-otp?email=' . $user->email);

        return ApiResponse::sendResponse(200,'The OTP Has Sent To Your Email!');
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'otp' => 'required',
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', request('email'))->first();

        if (!$user) {
            return ApiResponse::sendResponse(422,'The Provided Credentials Are Incorrect.');
        }

        if ($user && $user->otp == $request->otp) {
            if (now() < $user->otp_till) {
                $user->resetPass();
                $data['email'] = $user->email;
                return ApiResponse::sendResponse(200,'Correct OTP ! You Can Change Your Password Now.',$data);
            } else {
                return ApiResponse::sendResponse(200,'The Provided OTP Is Not Valid, Please Try Again.');
            }
        }
        return ApiResponse::sendResponse(200,'The Provided Credentials Are Incorrect.');
    }
}
