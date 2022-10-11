<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Clothes;

class SearchProduct extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke($search)
    {
        try {
            $products = Clothes::where('article_name', 'LIKE', '%'.$search.'%')->with('Type', 'Image', 'BufferProduct.Size')->get();
            foreach ($products as $clothes) {
                $clothes->combo = explode(',', $clothes->combo);
                $clothes->BufferProduct->makeHidden(['created_at', 'updated_at', 'qty_avaliable', 'qty_process']);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'success to get products',
                'data' => $products
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
