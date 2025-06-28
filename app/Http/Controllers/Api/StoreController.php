<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\StoreResource;
use App\Models\IC;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{

    public function index(Request $request)
    {
        $stores = Store::all();
        if (count($stores) > 0) {
            return ApiResponse::sendResponse(200, 'Stores Retrieved Successfully', StoreResource::collection($stores));
        }
        return ApiResponse::sendResponse(200, 'No stores were found',[]);
    }

    public function nearby(Request $request){
        $lat = $request->lat;
        $lng = $request->lng;
        $store = Store::getNearby($lat, $lng);
        if (count($store) > 0) {
            return ApiResponse::sendResponse(200, 'Nearby Stores Found', StoreResource::collection($store));
        }
        return ApiResponse::sendResponse(200, 'No stores were found',[]);
    }

    public function findIc($icId)
    {
        $ic = IC::with('stores')->find($icId);
        if (!$ic) {
            return ApiResponse::sendResponse(200, 'IC Out Of Stock', []);
        }
        return ApiResponse::sendResponse(200, 'IC Found In These Stores', StoreResource::collection($ic->stores));
    }

    public function nearat(Request $request, $distance)
    {
        $lat = $request->lat;
        $lng = $request->lng;
        $store = Store::getNearby($lat, $lng, $distance);
        if (count($store) > 0) {
            return ApiResponse::sendResponse(200, 'Stores in this area', StoreResource::collection($store));
        }
        return ApiResponse::sendResponse(200, 'No stores were found at selected location',[]);
    }
}
