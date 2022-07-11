<?php

namespace App\Http\Controllers\Api;

use App\Distributor;
use App\Http\Controllers\Controller;
use App\Http\Requests\DistributorRequest;
use App\MutifStoreAddress;
use App\MutifStoreMaster;
use App\PartnerGroup;
use App\TableName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DistributorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'success get data distributor',
                'data' => Distributor::with('PartnerGroup')->get()
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed get data distributor',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DistributorRequest $request)
    {
        try {
            $partner_group = PartnerGroup::where('id', $request->partner_group_id)->first();
            $user_id = Auth::user()->id;
            $db_account = Distributor::firstOrCreate([
                'name' => $request->db_name,
                'db_id' => 0,
                'group_code' => 'DB',
                'partner_group_id' => 1,
                'level' => $request->level
            ]);

            $partner_group = PartnerGroup::firstOrCreate([
                'prtnr_name' => $request->role
            ]);

            $agent = Distributor::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'prtnr_add_by' => $user_id,
                'db_id' => $db_account->id,
                'group_code' => $partner_group->prtnr_code,
                'partner_group_id' => $partner_group->id,
                'level' => $request->level,
                'training_level' => $request->training_level,
            ]);

            $mutif_store = MutifStoreMaster::create([
                'mutif_store_master' => $request->ms_name,
                'mutif_store_code' => $request->ms_code,
                'ms_add_by' => $user_id,
                'group_code' => $partner_group->code,
                'distributor_id' => $agent->id
            ]);

            $mutif_store_address = MutifStoreAddress::create([
                'mutif_store_master_id' => $mutif_store->id,
                'distributor_id' => $agent->id,
                'prtnr_add_by' => $user_id,
                'address' => $request->address,
                'province' => $request->province,
                ''
            ]);

            Distributor::create([
                'status' => 'success',
                'message' => 'distributor registered',
                // 'data' => $distributor
            ]);
        } catch (\Throwable $th) {
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
    public function show(Distributor $distributor)
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'success get data distributor',
                'data' => $distributor
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'success',
                'message' => 'success get data distributor',
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
    public function update(DistributorRequest $request, Distributor $distributor)
    {
        try {
            $table_name = TableName::where('user_id', $distributor->id)->first();

            if ($table_name->exists()) {
                Schema::rename($table_name->table_name, 'db_'.$request->phone);
                $table_name->update([
                    'table_name' => 'db_'.$request->phone
                ]);
            }

            $distributor->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'success update distributor',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to update data',
                'eror' => $th->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Distributor $distributor)
    {
        try {
            $distributor->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'success delete data'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to delete data',
                'error' => $th->getMessage()
            ]);
        }
    }
}
