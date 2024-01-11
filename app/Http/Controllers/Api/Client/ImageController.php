<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\{Image, Models\Partnumber};
use Illuminate\Support\Facades\DB;

class ImageController extends Controller
{
    public function show($partnumber) {
        try {
            $photo = Image::select('photo')->where(['id' => function ($query) use ($partnumber) {
                $query->select('image_id')
                    ->from('partnumbers')
                    ->where('partnumber', $partnumber)
                    ->first();
            }])->first();

            $image = ($photo) ? $photo : '-';

            return response()->json([
                'status' => 'success!',
                'data' => $image,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage()
            ]);
        }
    }

    public function getImageCatalog () {
        try {
            $image = Partnumber::select(
                'partnumbers.partnumber AS partnumber',
                DB::raw("CASE WHEN images.photo IS NULL THEN '-' ELSE images.photo END AS photo")
            )
            ->leftJoin('images', 'partnumbers.image_id', '=', 'images.id')
            ->get();

            return response()->json([
                'status' => 'success',
                'data' => $image,
                'error' => null
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage()
            ]);
        }
    }
}
