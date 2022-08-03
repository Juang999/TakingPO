<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Distributor;

class SearchDistributor extends Controller
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
            $distributor = Distributor::where([
                ['partner_group_id', '=', 1],
                ['name', 'LIKE', '%'.$search.'%']
            ])->get();

            return response()->json([
                'status' => 'success',
                'message' => 'success to get data',
                'distributor' => $distributor
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
