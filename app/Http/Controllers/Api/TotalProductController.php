<?php

namespace App\Http\Controllers\Api;

use App\BufferProduct;
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
        $clothess = BufferProduct::OrderBy('qty_process', 'DESC')->with('Clothes', 'Size')->get();

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
