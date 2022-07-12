<?php

namespace App\Http\Controllers\Api;

use App\Agent;
use App\Distributor;
use App\Http\Controllers\Controller;
use App\Http\Requests\AgentRequest;
use App\MutifStoreAddress;
use App\MutifStoreMaster;
use App\PartnerGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Distributor::where('partner_group_id', 2)->with('PartnerGroup')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'success to get data',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AgentRequest $request)
    {
        try {
            DB::beginTransaction();
                $partner_group = PartnerGroup::where('id', $request->partner_group_id)->first();
                $user_id = Auth::user()->id;
                $db_account = Distributor::firstOrCreate([
                    'name' => $request->db_name,
                    'group_code' => 'DB',
                    'partner_group_id' => 1,
                    'level' => $request->level
                ]);

                $partner_group = PartnerGroup::firstOrCreate([
                    'prtnr_name' => $request->role
                ]);

                $agent = Distributor::firstOrCreate([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'partner_add_by' => $user_id,
                    'distributor_id' => $db_account->id,
                    'group_code' => $partner_group->prtnr_code,
                    'training_level' => $request->training_level,
                    'partner_group_id' => $partner_group->id,
                    'join_date' => $request->join_date
                ]);

                $mutif_store = MutifStoreMaster::create([
                    'mutif_store_name' => $request->ms_name,
                    'mutif_store_code' => $request->ms_code,
                    'ms_add_by' => $user_id,
                    'group_code' => $partner_group->prtnr_code,
                    'agent_id' => $agent->id,
                    'partner_group_id' => $partner_group->id,
                    'status' => $request->status,
                    'msdp' => $request->msdp
                ]);

                MutifStoreAddress::create([
                    'mutif_store_master_id' => $mutif_store->id,
                    'prtnr_add_by' => $user_id,
                    'address' => $request->address,
                    'province' => $request->province,
                    'regency' => $request->regency,
                    'district' => $request->district,
                    'phone_1' => $request->phone
                ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'distributor registered',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'failed',
                'message' => 'failed to create distributor',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
