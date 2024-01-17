<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\{Image, Models\Partnumber};

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function show(Image $image)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Image $image)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Image  $image
     * @return \Illuminate\Http\Response
     */
    public function destroy(Image $image)
    {
        //
    }

    public function uploadPhoto(Request $request, $clothes_id)
    {
        try {
            DB::beginTransaction();
                $image = Image::create([
                    'clothes_id' => $clothes_id,
                    'photo' => $request->photo
                ]);

                $this->checkExistancePartnumber($clothes_id, $image->id);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'photo added!'
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to upload photo!',
                'error' => $th->getMessage()
            ]);
        }
    }

    private function checkExistancePartnumber($clothes_id, $image_id)
    {
        $checkDataPartnumber = Partnumber::where('clothes_id', '=', $clothes_id)->first();

        if ($checkDataPartnumber) {
            $checkDataPartnumber->update([
                'image_id' => $image_id
            ]);
        }
    }
}
