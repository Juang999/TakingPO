<?php

namespace App\Http\Controllers\Api;

use App\Clothes;
use App\Http\Controllers\Controller;
use App\Http\Requests\PhotoRequest;
use App\Image;
use Illuminate\Http\Request;

class UploadPhoto extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(PhotoRequest $request, $id)
    {
        try {
            Image::create([
                'clothes_id' => $id,
                'photo' => $request->photo
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'photo added!'
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to upload photo!',
                'error' => $th->getMessage()
            ]);
        }
    }
}
