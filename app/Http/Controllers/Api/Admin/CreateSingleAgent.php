<?php

namespace App\Http\Controllers\Api\Admin;

use App\Distributor;
use App\PartnerGroup;
use App\MutifStoreMaster;
use App\MutifStoreAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\SingleAgentRequest;

class CreateSingleAgent extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(SingleAgentRequest $request)
    {
        $user_id = Auth::user()->id;

        try {
            DB::beginTransaction();

            $partner_group = PartnerGroup::find($request->partner_group_id);

            $agent = Distributor::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'distributor_id' => $request->distributor_id,
                'group_code' => $partner_group->prtnr_code,
                'partner_group_id' => $partner_group->id,
            ]);

            $mutif_store_master = MutifStoreMaster::create([
                'mutif_store_name' => $request->ms_ms_name,
                'mutif_store_code' => $request->ms_code,
                'ms_add_by' => $user_id,
                'group_code' => $partner_group->prtnr_code,
                'partner_group_id' => $partner_group->id,
                'distributor_id' => $agent->id,
                'open_date' => $request->open_date,
                'msdp' => $request->msdp,
                'status' => $request->status
            ]);

            $mutif_store_address = MutifStoreAddress::create([
                'mutif_store_master_id' => $mutif_store_master->id,
                'prtnr_add_by' => $user_id,
                'address' => $request->address,
                'district' => $request->district,
                'regency' => $request->regency,
                'province' => $request->province,
                'phone_1' => $request->phone,
                'phone_2' => $request->phone_2,
                'fax_1' => $request->fax_1,
                'comment' => $request->comment,
                'zip' => $request->zip,
                'addr_type' => $request->addr_type
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'success to create agent',
                'agent' => $agent,
                'mutif_store_master' => $mutif_store_master,
                'mutif_store_address' => $mutif_store_address
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'failed',
                'message' => 'failed to create data',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
