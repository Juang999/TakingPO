<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\TotalProduct;
use Illuminate\Http\Request;
use App\Clothes;
use Illuminate\Support\Facades\DB;

class TotalProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clothess = DB::table('clothes')->selectRaw('clothes.id, clothes.article_name,SUM(total_products.size_s + total_products.size_m + total_products.size_l + total_products.size_xl + total_products.size_xxl + total_products.size_xxxl + total_products.size_2 + total_products.size_4 + total_products.size_6 + total_products.size_8 + total_products.size_10 + total_products.size_12) AS total')->leftJoin('total_products', 'clothes.id', '=', 'total_products.clothes_id')->groupBy('clothes.id')->orderBy('total', 'DESC')->get();

        foreach ($clothess as $clothes) {
            if ($clothes->total == NULL) {
                $clothes->total = 0;
            }
        }

        return response()->json([
            'data' => $clothess
        ]);
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
     * @param  \App\TotalProduct  $totalProduct
     * @return \Illuminate\Http\Response
     */
    public function show(TotalProduct $totalProduct)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TotalProduct  $totalProduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TotalProduct $totalProduct)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\TotalProduct  $totalProduct
     * @return \Illuminate\Http\Response
     */
    public function destroy(TotalProduct $totalProduct)
    {
        //
    }
}
