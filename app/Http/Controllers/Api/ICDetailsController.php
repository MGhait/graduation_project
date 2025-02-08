<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Helpers\Resource;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDetailsRequest;
use App\Http\Requests\StorefeaturesRequest;
use App\Http\Requests\StorePackagesRequest;
use App\Http\Requests\StoreParametersRequest;
use App\Http\Resources\FeatureResource;
use App\Http\Resources\ICDetailsResource;
use App\Http\Resources\PackagesResource;
use App\Http\Resources\ParametersResource;
use App\Models\Feature;
use App\Models\ICDetails;
use App\Models\Package;
use App\Models\Parameter;
use Illuminate\Http\Request;

class ICDetailsController extends Controller
{

    public function viewDetails(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:ic_details,id',
        ], [
            'id.required' => 'The IC Details ID is required.',
            'id.exists' => 'The specified IC Details does not exist.',
        ], [
            'id' => 'IC Details',
        ]);
        $details = ICDetails::findOrFail(request('id'));
        if($details){
            return ApiResponse::sendResponse(200,'Details Retrieved Successfully',Resource::make(ICDetailsResource::class, $details));

        }
        return ApiResponse::sendResponse(200,'Details Not Found',[]);
    }
    public function storeDetails( StoreDetailsRequest $request)
    {
        $data = $request->validated();
        $ic_details = ICDetails::create($data);
        if ($ic_details) {
            return ApiResponse::sendResponse(200, "IC Details added successfully.", []);
        }
        return ApiResponse::sendResponse(200, "Something went wrong.",Resource::make(ICDetailsResource::class,$ic_details));
    }

    public function storeParameter(StoreParametersRequest $request)
    {
        $data = $request->validated();
        $parameters = Parameter::create($data);
        if ($parameters) {
            return ApiResponse::sendResponse(200, "Parameters added successfully.",  new ParametersResource($parameters));
        }
        return ApiResponse::sendResponse(200, "Something went wrong.", []);
    }

    public function storePackages(StorePackagesRequest $request)
    {
        $data = $request->validated();
        $packages = Package::create($data);
        if ($packages) {
            return ApiResponse::sendResponse(200, "Packages added successfully.", Resource::make(PackagesResource::class, $packages));
        }
        return ApiResponse::sendResponse(200, "Something went wrong.", []);
    }

    public function storeFeatures(StoreFeaturesRequest $request)
    {
        $data = $request->validated();
        $features = Feature::create($data);
        if ($features) {
            return ApiResponse::sendResponse(200, 'Features added successfully.', Resource::make(FeatureResource::class, $features));
        }
        return ApiResponse::sendResponse(200, "Something went wrong.", []);
    }

}
