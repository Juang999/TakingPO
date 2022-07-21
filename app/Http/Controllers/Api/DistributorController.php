<?php

namespace App\Http\Controllers\Api;

use App\Distributor;
use App\Http\Controllers\Controller;
use App\Http\Requests\DistributorRequest;
use App\Http\Requests\UpdateDistributorRequest;
use App\MutifStoreAddress;
use App\MutifStoreMaster;
use App\PartnerAddress;
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
        $datas = Distributor::where('partner_group_id', 1)->with('PartnerGroup')->get();

        foreach ($datas as $data) {
            $data['total_agent'] = Distributor::where('distributor_id', $data->id)->count();
        }

        try {
            return response()->json([
                'status' => 'success',
                'message' => 'success get data distributor',
                'data' => $datas
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
        $user_id = Auth::user()->id;

        try {
            DB::beginTransaction();
                $distributor = Distributor::create([
                    'name' => $request->name,
                    'group_code' => 'DB',
                    'distributor_id' => 0,
                    'partner_group_id' => 1,
                    'level' => $request->level,
                    'prtnr_add_by' => $user_id,
                    'phone' => $request->phone
                ]);

                $address = PartnerAddress::create([
                    'distributor_id' => $distributor->id,
                    'prtnra_add_by' => $user_id,
                    'address' => $request->address,
                    'district' => $request->district,
                    'regency' => $request->regency,
                    'province' => $request->province,
                    'addr_type' => $request->addr_type,
                    'zip' => $request->zip,
                ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'distributor registered',
                'data' => $distributor,
                'address' => $address
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
    public function show($distributor)
    {
        $data = Distributor::where('id', $distributor)->with('PartnerAddress')->first();

        $agents = Distributor::where('distributor_id', $data->id)->get(['id', 'name']);

        // return response()->json($agents);
        // dd($agents);
        foreach ($agents as $agent) {
            $ms_code = MutifStoreMaster::where('distributor_id', $agent->id)->first('mutif_store_code');
            if ($ms_code) {
                $agent['ms_code'] = $ms_code['mutif_store_code'];
            } else {
                $agent['ms_code'] = '-';
            }
        }


        $data['agent'] = $agents;

        try {
            return response()->json([
                'status' => 'success',
                'message' => 'success get data distributor',
                'data' => $data
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
    public function update(Request $request, Distributor $distributor)
    {
        try {
            $table_name = TableName::where('distributor_id', $distributor->id)->first();

            if ($table_name) {
                Schema::rename($table_name->table_name, 'db_'.$request->phone);
                $table_name->update([
                    'table_name' => 'db_'.$request->phone
                ]);
            }

            $user_id = Auth::user()->id;

            $distributor->update([
                'name' => ($request->name)? $request->name : $distributor->name,
                'phone' => ($request->phone)? $request->phone : $distributor->phone,
                'prtnr_upd_by' => $user_id
            ]);


            if ($request->address) {
                $address = PartnerAddress::where('distributor_id', $distributor->id)->first();

                if ($address) {
                    $address->update([
                        'distributor_id' => $distributor->id,
                        'address' => ($request->address)? $request->address : $address->address,
                        'district' => ($request->district)? $request->district : $address->district,
                        'regency' => ($request->regency)? $request->regency : $address->regency,
                        'province' => ($request->province)? $request->province : $address->province
                    ]);
                } else {
                    PartnerAddress::create([
                        'distributor_id' => $distributor->id,
                        'address' => $request->address,
                        'district' => $request->district,
                        'regency' => $request->regency,
                        'province' => $request->province
                    ]);
                }
            }

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
