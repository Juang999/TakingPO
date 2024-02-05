<?php

namespace App\Http\Controllers\Api;

use App\Agent;
// use App\;
use App\Http\Controllers\Controller;
// use App\;
use App\PartnerAddress;
// use App\;
use App\TableName;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\;
// use Illuminate\Support\Facades\Schema;
use App\Http\Requests\AgentRequest;
use Illuminate\Support\Facades\{DB, Auth};
use App\{MutifStoreAddress, Distributor, MutifStoreMaster, PartnerGroup};

class AgentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datas = MutifStoreMaster::get();

        foreach ($datas as $data) {
            $data->agent = Distributor::where('id', $data->distributor_id)->first();
            $data->distributor = Distributor::where('id', $data->agent->distributor_id)->first();
            // $data->distributor['total_agent'] = Distributor::where('distributor_id', $data->distributor)->count();

            $data->makeHidden([
                'ms_add_by',
                'ms_upd_by',
                'group_code',
                'msdp',
                'partner_group_id',
                'url',
                'remarks',
                'created_at',
                'updated_at',
                'distributor_id'
            ]);

            $data->agent->makeHidden([
                'distributor_id',
                'group_code',
                'group_code',
                'partner_group_id',
                'level',
                'training_level',
                'prtnr_add_by',
                'prtnr_upd_by',
                'deleted_at'
            ]);

            // $data->distributor->makeHidden([
            //     'distributor_id',
            //     'group_code',
            //     'group_code',
            //     'partner_group_id',
            //     'level',
            //     'training_level',
            //     'prtnr_add_by',
            //     'prtnr_upd_by',
            //     'deleted_at',
            //     'phone'
            // ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'success to get data',
            'data' => $datas
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
                $partner_group = PartnerGroup::where('id', $request->partner_group_id)->first();
                $user_id = Auth::user()->id;
                $db_account = Distributor::firstOrCreate([
                    'name' => $request->db_name,
                    'group_code' => 'DB',
                    'partner_group_id' => 1,
                    'level' => $request->db_level
                ]);

                $partner_group = PartnerGroup::firstOrCreate([
                    'prtnr_name' => $request->role
                ]);

                $agent = Distributor::firstOrCreate([
                    'name' => $request->ms_name,
                    'phone' => $request->ms_phone,
                    'prtnr_add_by' => $user_id,
                    'distributor_id' => $db_account->id,
                    'group_code' => $partner_group->prtnr_code,
                    'training_level' => $request->ms_training_level,
                    'partner_group_id' => $partner_group->id,
                ]);

                if ($request->ms_open_date) {
                    $mutif_store = MutifStoreMaster::create([
                        'mutif_store_name' => $request->ms_ms_name,
                        'mutif_store_code' => $request->ms_code,
                        'ms_add_by' => $user_id,
                        'group_code' => $partner_group->prtnr_code,
                        'distributor_id' => $agent->id,
                        'partner_group_id' => $partner_group->id,
                        'status' => $request->ms_status,
                        'open_date' => $request->ms_open_date,
                        'msdp' => $request->ms_msdp
                    ]);
                } else {
                    $mutif_store = MutifStoreMaster::create([
                        'mutif_store_name' => $request->ms_ms_name,
                        'mutif_store_code' => $request->ms_code,
                        'ms_add_by' => $user_id,
                        'group_code' => $partner_group->prtnr_code,
                        'distributor_id' => $agent->id,
                        'partner_group_id' => $partner_group->id,
                        'status' => $request->ms_status,
                        'msdp' => $request->ms_msdp
                    ]);
                }


                MutifStoreAddress::create([
                    'mutif_store_master_id' => $mutif_store->id,
                    'prtnr_add_by' => $user_id,
                    'address' => $request->ms_address,
                    'province' => $request->ms_province,
                    'regency' => $request->ms_regency,
                    'district' => $request->ms_district,
                    'phone_1' => $request->ms_phone,
                    'phone_2' => ($request->ms_phone_2) ? $request->ms_phone_2 : 0
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
        try {
            $agent = Distributor::where('id',$id)->with('PartnerGroup', 'MutifStoreMaster.MutifStoreAddress')->first();
            $agent['distributor'] = Distributor::where('id', $agent->distributor_id)->first();

            // $agent['distributor'] = Distributor::where('id', $agent->distributor_id)->first();

            return response()->json([
                'status' => 'success',
                'message' => 'success to get detail data',
                'data' => $agent
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get detail data',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Distributor $agent)
    {
        try {
        DB::beginTransaction();
            $TableName = TableName::where('distributor_id', $agent->id)->first();

            $user_id = Auth::user()->id;

            $agent->update([
                'name' => ($request->name) ? $request->name : $agent->name,
                'phone' => ($request->phone) ? $request->phone : $agent->phone,
                'prtnr_upd_by' => $user_id
            ]);

            $mutif_store_master = MutifStoreMaster::where('distributor_id', $agent->id)->first();
            $mutif_store_address = MutifStoreAddress::where('mutif_store_master_id', $mutif_store_master->id)->first();

            if ($request->partner_group_id) {
                $partner_group = PartnerGroup::where('id', $request->partner_group_id)->first();
            }

            $mutif_store_master->update([
                'mutif_store_master' => ($request->ms_ms_name) ? $request->ms_ms_name : $mutif_store_master->mutif_store_name,
                'mutif_store_code' => ($request->ms_code) ? $request->ms_code : $mutif_store_master->mutif_store_code,
                'ms_upd_by' => $user_id,
                'group_code' => ($request->partner_group_id) ? $partner_group->prtnr_code : $mutif_store_master->group_code,
                'partner_group_id' => ($request->partner_group_id) ? $partner_group->id : $mutif_store_master->partner_group_id,
                'distributor_id' => ($request->distributor_id) ? $request->distributor_id : $mutif_store_master->distributor_id,
                'open_date' => ($request->open_date) ? $request->open_date : $mutif_store_master->open_date,
                'status' => ($request->status) ? $request->status : $mutif_store_master->status,
                'msdp' => ($request->msdp) ? $request->msdp : $mutif_store_master->msdp,
                'url' => ($request->url) ? $request->url : $mutif_store_master->url,
                'remarks' => ($request->remarks) ? $request->remarks : $mutif_store_master->remarks
            ]);

            $mutif_store_address->update([
                'prtnr_upd_by' => $user_id,
                'address' => ($request->address) ? $request->address : $mutif_store_address->address,
                'province' => ($request->province) ? $request->province : $mutif_store_address->province,
                'regency' => ($request->regency) ? $request->regency : $mutif_store_address->regency,
                'district' => ($request->district) ? $request->district : $mutif_store_address->district,
                'phone_1' => ($request->phone_1) ? $request->phone_1 : $mutif_store_address->phone_1,
                'fax_1' => ($request->fax_1) ? $request->fax_1 : '-',
                'addr_type' => ($request->addr_type) ? $request->addr_type : $mutif_store_address->addr_type,
                'zip' => ($request->zip) ? $request->zip : $mutif_store_address->zip,
                'comment' => ($request->comment) ? $request->comment : $mutif_store_address->comment
            ]);

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'success to update'
        ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to update data',
                'error' => $th->getMessage()
            ], 400);
        }
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
