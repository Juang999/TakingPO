<?php

namespace App\Http\Controllers\Api;

use App\Clothes;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivateClothes extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Clothes $clothes)
    {
        if ($clothes->is_active == 1) {
            $clothes->update([
                'is_active' => 0
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'status in-active'
            ]);
        } else {
            $clothes->update([
                'is_active' => 1
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'status active'
            ]);
        }
    }
}
