<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Helpers\Username;
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
            $user = User::where('email', $socialUser->getEmail())->first();
            $nameParts = explode(' ', $socialUser->getName() ?? '');
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';

            if ($provider === 'facebook' || $provider === 'google') {
                $firstName = $socialUser->user['first_name'] ?? $socialUser->user['given_name'] ?? $firstName;
                $lastName = $socialUser->user['last_name'] ?? $socialUser->user['family_name'] ?? $lastName;
            }
            if (!$user) {
                $user = User::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => Username::generateUsername($firstName, $lastName),
                    'email' => $socialUser->getEmail(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'email_verified_at' => now(),
                ]);
            } else {
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ]);
            }
            $token = $user->createToken('APIToken')->plainTextToken;
            $data = [
                'token' => $token,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $user->email,
            ];
            return ApiResponse::sendResponse(200,'User Logged In via ' . $provider .' Successfully',$data);
        }
        catch (\Exception $e) {
            return ApiResponse::sendResponse(500,'Something Went Wrong',['error'=>$e->getMessage()]);
        }
    }
}
