<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if($user){
            return ApiResponse::sendResponse(200, 'Profile Retrieved Successfully', new ProfileResource($user));
        }
        return ApiResponse::sendResponse(400, 'Invalid Request');
    }

    public function store(UpdateProfileRequest $request)
    {
        $user = $request->user();
        if($user){
            $user->fill($request->validated());

            if ($request->user()->isDirty('email')) {
                $request->user()->email_verified_at = null;
            }
            // Verify later
    //        if ($user->isDirty('phone')) {
    //            //send otp to check
    //        }

            $request->user()->save();
            return ApiResponse::sendResponse(200, 'Profile Updated Successfully', new ProfileResource($user));
        }
        return ApiResponse::sendResponse(400, 'Invalid Request');
    }
}
