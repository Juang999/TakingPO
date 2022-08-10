<?php

namespace App\Http\Controllers\Api\Admin;

use App\Distributor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchAgent extends Controller
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
            $agent = Distributor::where([
                ['partner_group_id', '!=', 1],
                ['name', 'LIKE', '%'.$search.'%']
            ])->get();

            return response()->json([
                'status' => 'success',
                'message' => 'success to get agent',
                'agent' => $agent
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get agent',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}