<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function login($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
            $user = User::where('email', $socialUser->getEmail())->first(); // do i really need this ?
            if ($provider == 'facebook') {
                $firstName = $socialUser->user['first_name'] ?? $socialUser->getName(); // need to be divided
                $lastName = $socialUser->user['last_name'] ?? '';
            }
            elseif ($provider == 'google') {
                $firstName = $socialUser->user['given_name'] ?? $socialUser->getName();
                $lastName = $socialUser->user['family_name'] ?? '';
            }
            else {
                $firstName = $socialUser->getName();
                $lastName =  ' ';
            }
            if (!$user) {
                $newUser = User::updateOrCreate([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                ],[
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $socialUser->getEmail(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'email_verified_at' => now(),
                ]);
            }
            $token = $newUser->createToken('APIToken')->plainTextToken;
            $data = [
                'token' => $token,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $newUser->email,
            ];
            return ApiResponse::sendResponse(200,'User Logged In Successfully',$data);
        }
        catch (\Exception $e) {
            return ApiResponse::sendResponse(500,'Something Went Wrong',['error'=>$e->getMessage()]);
        }
    }
}
