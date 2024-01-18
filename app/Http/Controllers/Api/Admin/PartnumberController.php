<?php

namespace App\Http\Controllers\Api\Admin;

use App\Image;
use App\Models\Partnumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partnumber\CreatePartnumberRequest;

class PartnumberController extends Controller
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
    public function store(CreatePartnumberRequest $request)
    {
        try {
            $partnumber = Partnumber::create([
                'clothes_id' => $request->clothes_id,
                'image_id' => $request->image_id,
                'image_id' => $request->partnumber
            ]);

            return response()->json([
                'status' => 'success!',
                'data' => $partnumber,
                'message' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed!',
                'data' => null,
                'message' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($partnumber)
    {
        try {
            Partnumber::where('partnumber', '=', $partnumber)->destroy();

            return response()->json([
                'status' => 'success',
                'data' => true,
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
