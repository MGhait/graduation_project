<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreICImageRequest;
use App\Http\Requests\StoreICRequest;
use App\Http\Requests\StoreTruthTable;
use App\Http\Requests\UpdateICRequest;
use App\Http\Resources\ICResource;
use App\Http\Resources\ImageResource;
use App\Models\IC;
use App\Models\Image;
use App\Models\TruthTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ICController extends Controller
{
    public function index()
    {
        $ics = IC::with(['mainImage', 'blogDiagram'])->get();
        if (count($ics) > 0) {
            return ApiResponse::sendResponse(200, 'IC Retrieved Successfully', ICResource::collection($ics));
        }
        return ApiResponse::sendResponse(200, 'No Ics Found', []);
    }

    public function store(StoreIcRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['commName']);
        IC::create($data);
        return ApiResponse::sendResponse(201, 'IC Record Created Successfully', []);
    }

    public function show($id)
    {
        $ic = IC::with(['mainImage', 'blogDiagram'])->find($id);
        if (count($ic) > 0) {
            $ic->increment('views');
            return ApiResponse::sendResponse(200, 'IC Retrieved Successfully', New ICResource($ic));
        }
        return ApiResponse::sendResponse(200, 'No Ics Found', []);
    }

    public function popularICs()
    {
        $ic = IC::with(['mainImage', 'blogDiagram'])->orderBy('views', 'DESC')->take(10)->get();
        if (count($ic) > 0) {
            return ApiResponse::sendResponse(200, 'ICs Retrieved Successfully', ICResource::collection($ic));
        }
        return ApiResponse::sendResponse(200, 'No ICs Found', []);
    }

    public function saveIc(Request $request)
    {
        $request->validate([
            'ic_id' => 'required|exists:ics,id',
        ]);

        $user = auth()->user(); // Get the authenticated user
        $icId = $request->input('ic_id');

        // Attach the IC to the user (prevents duplicates)
        $user->savedIcs()->syncWithoutDetaching($icId);
        $data['user'] = $user->email;
        $data['ic_id'] = $icId;

        return ApiResponse::sendResponse(200, 'IC Saved Successfully', $data);
    }

    public function getSavedICs(Request $request){
        $user = auth()->user();
        $savedIcs = $user->savedIcs;
        if (count($savedIcs) > 0) {
            return ApiResponse::sendResponse(200, 'ICs Retrieved Successfully', ICResource::collection($savedIcs));
        }
        return ApiResponse::sendResponse(200, 'No ICs Found', []);
    }

    public function removeSavedIC(Request $request)
    {
        $request->validate([
            'ic_id' => 'required|exists:ics,id',
        ],[],[
            'ic_id' => 'IC',
        ]);
        $user = auth()->user();
        $icId = $request->input('ic_id');

        // check if the ic exits in the user saved list first
        if (!$user->savedIcs()->where('ic_id', $icId)->exists()) {
            return ApiResponse::sendResponse(404, 'IC not found in user\'s saved list');
        }
        $user->savedIcs()->detach($icId);
        return ApiResponse::sendResponse(200, 'IC Removed Successfully');
    }

    public function storeTruthTable(StoreTruthTable $request)
    {
        $data = $request->validated();
        $truthTable = TruthTable::create($data);
        if ($truthTable)
        {
            return ApiResponse::sendResponse(201, 'Truth Table Created Successfully', []);
        }
        return ApiResponse::sendResponse(200, 'Something Went Wrong', []);
    }

    public function searchIC2(Request $request)
    {
        $query = $request->input('query');
        $codes = IC::extractICCodes($query);
//        dd($codes);
        $query = IC::query();

        foreach ($codes as $code) {
            $query->orWhere('name', 'like', "%{$code}%");
        }

        $ics = $query->get();
        if (count($ics) > 0) {
            return ApiResponse::sendResponse(200, 'IC Retrieved Successfully', ICResource::collection($ics));
        }
        return ApiResponse::sendResponse(200, 'No Ics Found', []);
    }

    public function search3(Request $request)
    {
        $query = $request->input('query');
        $out = IC::regxSearch($query);
        if (count($out) > 0) {
            return ApiResponse::sendResponse(200, 'IC Retrieved Successfully', $out);
        }
        return ApiResponse::sendResponse(200, 'No Ics Found',[]);
    }
    public function searchIC(Request $request)
    {
        $query = $request->input('query');

        $ics = IC::with(['mainImage', 'blogDiagram', 'store'])
            ->where('commName', 'like', '%' . $query . '%')
            ->orWhere('name', 'like', '%' . $query . '%')
            ->orWhere('slug', 'like', '%' . $query . '%')
            ->get();

        if (count($ics) > 0) {
            return ApiResponse::sendResponse(200, 'IC Retrieved Successfully', ICResource::collection($ics));
        }
        return ApiResponse::sendResponse(200, 'No Ics Found', null);
    }
    public function update(UpdateICRequest $request, IC $ic)
    {

    }
    public function storeImage(StoreIcImageRequest $request)
    {
        $request->validated();

        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalName();
        $image->storeAs('images', $imageName, 'public');

        $image=Image::create([
            'url' => $imageName,
        ]);
        return ApiResponse::sendResponse(201,'Image Uploaded Successfully',new ImageResource($image));
    }
}
