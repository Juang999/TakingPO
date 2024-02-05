<?php

namespace App\Http\Controllers\Api\Admin;

use App\Distributor;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SingleAgent extends Controller
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
            $distributor_hasNot_MS = Distributor::whereNotIn('id', function ($query) {
                $query->select('distributor_id')
                    ->from('mutif_store_masters')
                    ->get()->toArray();
            })->with('MutifStoreMaster')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'success to get unregistered user',
                'user' => $distributor_hasNot_MS
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get unregistered user',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
