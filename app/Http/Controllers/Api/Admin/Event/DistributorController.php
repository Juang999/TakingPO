<?php

namespace App\Http\Controllers\Api\Admin\Event;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Auth, DB};
use App\Http\Requests\{Admin\Distributor\DistributorRequest, UpdateDistributorRequest};
use App\{PartnerGroup, Distributor, PartnerAddress, MutifStoreMaster};

class DistributorController extends Controller
{
    public function index()
    {
        $datas = Distributor::where('partner_group_id', 1)
                            ->when(request()->searchdistributor, function ($query) {
                                $query->where('name', 'like', '%'.request()->searchname.'%');
                            })
                            ->with('PartnerGroup')
                            ->get();

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

    public function store(DistributorRequest $request)
    {
        try {
            $user_id = Auth::user()->id;

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

                $address = $this->createAddressDistributor($request, $distributor->id, $user_id);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'distributor registered',
                'data' => ["agent" => $distributor, "address" => $address],
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

    public function show($distributor)
    {
        $data = Distributor::where('id', $distributor)->with('PartnerAddress')->first();

        $agents = Distributor::where('distributor_id', $data->id)->get(['id', 'name']);

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

    public function update(Request $request, Distributor $distributor)
    {
        try {
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

    private function createAddressDistributor($request, $distributor_id, $user_id)
    {
        PartnerAddress::create([
            'distributor_id' => $distributor_id,
            'prtnra_add_by' => $user_id,
            'address' => $request->address,
            'district' => $request->district,
            'regency' => $request->regency,
            'province' => $request->province,
            'addr_type' => $request->addr_type,
            'zip' => $request->zip,
        ]);
    }
}
