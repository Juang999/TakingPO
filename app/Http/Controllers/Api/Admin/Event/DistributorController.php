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
        $distributorName = request()->searchdistributor;

        $datas = Distributor::select(
                                'distributors.id',
                                'distributors.name',
                                'distributors.phone',
                                'distributors.group_code',
                                'distributors.partner_group_id',
                                'distributors.level',
                                'distributors.training_level',
                                'distributors.prtnr_add_by',
                                'distributors.prtnr_upd_by',
                                DB::raw("(SELECT COUNT(agent.id) FROM distributors AS agent WHERE agent.distributor_id = distributors.id) AS total_agent"),
                                'partner_groups.prtnr_name AS partner_group',
                                DB::raw("CAST(partner_groups.discount * 100 AS UNSIGNED INTEGER) AS discount"),
                            )->leftJoin('partner_groups', 'partner_groups.id', '=', 'distributors.partner_group_id')
                            ->where('distributors.partner_group_id', '=', 1)
                            ->when($distributorName, function ($query) use ($distributorName) {
                                $query->where('distributors.name', 'like', '%'.$distributorName.'%')->get();
                            })
                            ->paginate(10);

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

    public function show($id)
    {
        $data = Distributor::select(
                                'distributors.id',
                                'distributors.name',
                                'distributors.phone',
                                'distributors.distributor_id',
                                'distributors.group_code',
                                'distributors.partner_group_id',
                                'distributors.level',
                                'distributors.training_level',
                                'partner_addresses.address',
                                'partner_addresses.district',
                                'partner_addresses.regency',
                                'partner_addresses.province',
                                'partner_addresses.phone_1',
                                'partner_addresses.phone_2',
                                'partner_addresses.fax_1',
                                'partner_addresses.fax_2',
                                'partner_addresses.addr_type',
                                'partner_addresses.zip',
                                'partner_addresses.comment',
                                'partner_addresses.active',
                            )->with([
                                'Agent' => function ($query) {
                                    $query->select(
                                            'distributors.id',
                                            'distributors.name',
                                            'distributors.phone',
                                            'distributors.distributor_id',
                                            'distributors.group_code',
                                            'distributors.partner_group_id',
                                            'distributors.level',
                                            'distributors.training_level',
                                            'mutif_store_masters.mutif_store_code'
                                        )->leftJoin('mutif_store_masters', 'mutif_store_masters.distributor_id', '=', 'distributors.id');
                                }
                                ])->leftJoin('partner_addresses', 'partner_addresses.distributor_id', '=', 'distributors.id')
                                ->where('distributors.id', $id)
                    ->first();


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

    public function update(Request $request, $id)
    {
        try {
            $user_id = Auth::user()->id;
            $req = $this->checkRequest($request, $id);

            Distributor::where('id', '=', $id)->update([
                'name' => $req['checkRequestDistributor']['name'],
                'phone' => $req['checkRequestDistributor']['phone'],
                'prtnr_upd_by' => $user_id
            ]);

            if ($request->address) {
                $this->updateAddressDistributor($req['checkRequestDistributorAddress'], $id);
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

    private function checkRequest($request, $id)
    {
        $distributor = Distributor::where('id', '=', $id)->first();
        $address = PartnerAddress::where('distributor_id', $distributor->id)->first();

        $checkRequestDistributor = [
            'name' => ($request->name) ? $request->name : $distributor->name,
            'phone' => ($request->phone) ? $request->phone : $distributor->phone,
        ];

        $checkRequestDistributorAddress = [
            'address' => ($request->address) ? $request->address : $address->address,
            'district' => ($request->district) ? $request->district : $address->district,
            'regency' => ($request->regency) ? $request->regency : $address->regency,
            'province' => ($request->province) ? $request->province : $address->province
        ];

        return compact('checkRequestDistributor', 'checkRequestDistributorAddress');
    }

    private function updateAddressDistributor($request, $id)
    {
        PartnerAddress::where('distributor_id', '=', $id)->update([
            'address' => $request['address'],
            'district' => $request['district'],
            'regency' => $request['regency'],
            'province' => $request['province']
        ]);
    }
}
