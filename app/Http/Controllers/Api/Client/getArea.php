<?php

namespace App\Http\Controllers\Api\Client;

use App\Area;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class getArea extends Controller
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
            $data = Area::get();

            return response()->json([
                'status' => 'success',
                'message' => 'success to get data',
                'area' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get data',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
