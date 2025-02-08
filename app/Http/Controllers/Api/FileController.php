<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function save(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf',
            'name' => 'required',
        ]);

        $file = $request->file('file');
        $newName = time() . $file->getClientOriginalName();
        $path = $file->storeAs('files', $newName, 'public');
        $storedFile = File::create([
            'name' => $request->name,
            'path' => $path,
            'type' => $file->getClientOriginalExtension(),
        ]);

        return ApiResponse::sendResponse(200, 'File successfully uploaded.', new FileResource($storedFile));
    }
}
