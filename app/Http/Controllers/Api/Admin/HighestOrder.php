<?php

namespace App\Http\Controllers\Api\Admin;

use App\BufferProduct;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HighestOrder extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        try {
            $data = BufferProduct::orderBy('qty_process', 'DESC')->with('Size', 'Clothes')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'success to get highest order',
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get highest order',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
