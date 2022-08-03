<?php

namespace App\Http\Controllers\Api\Admin;

use App\Distributor;
use App\Http\Controllers\Controller;
use App\MutifStoreAddress;
use App\MutifStoreMaster;
use Illuminate\Http\Request;
use App\Requests\MutifStoreRequest;

class MutifStoreMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $mutif_store_master = MutifStoreMaster::with('Distributor')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'success to get data',
                'data' => $mutif_store_master
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get data',
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
    public function store(Request $request)
    {
        $user = Distributor::find($request->agent_id);

        try {
            $MutifStoreMaster = MutifStoreMaster::create([
                'mutif_store_name' => $request->ms_ms_name,
                'mutif_store_code' => $request->ms_code,
                'group_code' => $user->group_code,
                'distributor_id' => $user->id,
                'partner_group_id' => $user->partner_group_id,
                'open_date' => $request->open_date,
                'status' => $request->status,
                'msdp' => $request->msdp
            ]);

            $MutfStoreAddress = MutifStoreAddress::create([
                'mutif_store_master_id' => $MutifStoreMaster->id,
                'address' => $request->address,
                'province' => $request->province,
                'regency' => $request->regency,
                'district' => $request->district,
                'phone_1' => $user->phone,
                'phone_2' => $request->phone,
                'fax_1' => $request->fax,
                'addr_type' => $request->addr_type,
                'zip' => $request->zip,
                'comment' => $request->comment
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'success to create MS',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to create MS',
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
