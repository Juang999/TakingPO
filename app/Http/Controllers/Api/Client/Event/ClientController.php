<?php

namespace App\Http\Controllers\Api\Client\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\{Distributor, PartnerAddress, PartnerGroup};
use Illuminate\Support\Facades\{Auth, DB};
use App\Http\Requests\Client\Auth\ClientRegisterRequest;

class ClientController extends Controller
{
    public function register(ClientRegisterRequest $request)
    {
        try {
            $dataPartnerGroup = $this->getPartnerGroup($request->partner_group_id);

            DB::beginTransaction();
                $distributor = Distributor::create([
                    'name' => $request->name,
                    'phone' => $request->phone_1,
                    'distributor_id' => ($request->partner_group_id == 1) ? 0 : $request->distributor_id,
                    'partner_group_id' => $dataPartnerGroup->id,
                    'group_code' => $dataPartnerGroup->prtnr_code,
                    'level' => 'BRONZE',
                    'training_level' => 1
                ]);

                $this->createClientAddress($request, $distributor->id);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $distributor,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function login(Request $request)
    {
        try {
            $client = Distributor::select('id', 'phone')->where('phone', '=', $request->phone)->first();

            if ($client == null) {
                return response()->json([
                    'status' => 'failed',
                    'data' => null,
                    'error' => 'unauthorize!'
                ], 300);
            }

            return response()->json([
                'status' => 'success',
                'data' => $client,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function partnerGroupList()
    {
        try {
            $dataPartnerGroup = PartnerGroup::select('id', 'prtnr_name')->get();

            return response()->json([
                'status' => 'success',
                'data' => $dataPartnerGroup,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage()
            ], 400);
        }
    }

    private function getPartnerGroup($partnerGrouId)
    {
        $partnerGroup = PartnerGroup::select('id', 'prtnr_code')->where('id', '=', $partnerGrouId)->first();

        return $partnerGroup;
    }

    private function createClientAddress($request, $client_id)
    {
        PartnerAddress::create([
            'distributor_id' => $client_id,
            'address' => $request->address,
            'district' => '-',
            'regency' => '-',
            'province' => '-',
            'phone_1' => $request->phone_1
        ]);
    }
}
