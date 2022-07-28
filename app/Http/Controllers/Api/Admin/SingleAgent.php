<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Distributor;
use App\MutifStoreMaster;

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
            $MutifStoreMaster = MutifStoreMaster::get()->toArray();

            $distributor_id = array_column($MutifStoreMaster, 'distributor_id');

            $distributor_hasNot_MS = Distributor::whereNotIn('id', $distributor_id)->where('partner_group_id', '!=', 1)->with('MutifStoreMaster')->get();

            // dd($distributor_hasNot_MS);

            return response()->json([
                'status' => 'success',
                'message' => 'success to get unregistered user',
                'user' => $distributor_hasNot_MS
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get unregistered user',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
