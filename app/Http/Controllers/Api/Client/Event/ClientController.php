<?php

namespace App\Http\Controllers\Api\Client\Event;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Auth, DB};
use App\Http\Requests\Client\Auth\{ClientRegisterRequest, DistributorRegisterRequest};
use App\{Distributor, PartnerAddress, PartnerGroup, MutifStoreMaster, MutifStoreAddress};

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
                    'distributor_id' => $request->distributor_id,
                    'partner_group_id' => $dataPartnerGroup->id,
                    'group_code' => $dataPartnerGroup->prtnr_code,
                    'level' => 'BRONZE',
                    'training_level' => 1
                ]);

                $this->createClientAddress($request, $distributor->id);
                $this->createMutifStore($request, $distributor->id);
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
            $client = Distributor::select('distributors.id','partner_group_id','name','phone','pg.prtnr_name')
                                ->join('partner_groups AS pg', 'pg.id', '=', 'distributors.partner_group_id')
                                ->where('distributors.phone', '=', $request->phone)->first();

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
            $dataPartnerGroup = PartnerGroup::select('id', 'prtnr_name')->where('id', '<>', 1)->get();

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

    public function registerDistributor(DistributorRegisterRequest $request)
    {
        try {
            DB::beginTransaction();
                $distributor = Distributor::create([
                    'name' => $request->name,
                    'phone' => $request->phone_1,
                    'distributor_id' => 0,
                    'partner_group_id' => 1,
                    'group_code' => 'DB',
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

    public function verification()
    {
        try {
            $phoneNumber = request()->header('phone');

            $dataClient = Distributor::where('phone', '=', $phoneNumber)->first();

            return response()->json([
                'status' => ($dataClient) ? 'success' : 'failed',
                'data' => ($dataClient) ? 'nomor terdaftar' : 'nomor tidak terdaftar',
                'error' => null
            ], ($dataClient) ? 200 : 300);
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
            'district' => $request->subdistrict,
            'regency' => $request->city,
            'province' => $request->province,
            'phone_1' => $request->phone_1,
            'zip' => $request->post_code,
            'active' => true
        ]);
    }

    private function createMutifStore($request, $clientId)
    {
        $msCode = $this->createMsCode($request->open_date);

        $mutifStoreMaster = MutifStoreMaster::create([
            'mutif_store_name' => "MUTIF STORE $request->subdistrict",
            'mutif_store_code' => $msCode,
            'distributor_id' => $clientId,
            'open_date' => $request->open_date,
            'status' => 'active'
        ]);

        MutifStoreAddress::create([
            'mutif_store_master_id' => $mutifStoreMaster->id,
            'address' => $request->address,
            'province' => $request->province,
            'regency' => $request->city,
            'district' => $request->subdistrict,
            'phone_1' => $request->phone_1,
            'addr_type' => 'shipTo',
            'zip' => $request->zip,
        ]);
    }

    private function createMsCode($openDate)
    {
        $randomInteger = rand(0000, 9999);
        $parseOpenDate = Carbon::parse($openDate)->format('d/m/y');

        $msCode = "$randomInteger/MS/$parseOpenDate";

        return $msCode;
    }
}
