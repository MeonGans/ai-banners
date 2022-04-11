<?php

namespace App\Http\Controllers;

use App\Http\Resources\FileResource;
use App\Models\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $file['url'] = $request->file('file')->store('files/' . date('Y-m-d'));
        $file = File::query()->create($file);
        return $this->sendResponse(new FileResource($file), 'File uploaded successfully.');
    }
}
