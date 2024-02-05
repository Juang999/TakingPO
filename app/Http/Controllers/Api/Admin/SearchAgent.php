<?php

namespace App\Http\Controllers\Api\Admin;

use App\{Distributor, MutifStoreMaster};
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
            $agents = MutifStoreMaster::where('mutif_store_name', 'LIKE', '%'.$search.'%')->get();
            foreach ($agents as $data) {
                $data['agent'] = Distributor::where('id', $data->distributor_id)->first();
                $data['distributor'] = Distributor::where('id', $data['agent']->distributor_id)->first();
                $data->makeHidden(['ms_add_by', 'ms_upd_by', 'group_code', 'msdp', 'partner_group_id', 'url', 'remarks', 'created_at', 'updated_at', 'distributor_id']);
                $data->agent->makeHidden(['distributor_id', 'group_code', 'group_code', 'partner_group_id', 'level', 'training_level', 'prtnr_add_by', 'prtnr_upd_by', 'deleted_at']);
                $data->distributor->makeHidden(['distributor_id', 'group_code', 'group_code', 'partner_group_id', 'level', 'training_level', 'prtnr_add_by', 'prtnr_upd_by', 'deleted_at', 'phone']);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'success to get agent',
                'agent' => $agents
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
