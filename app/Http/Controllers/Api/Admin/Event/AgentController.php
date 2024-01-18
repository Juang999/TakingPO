<?php

namespace App\Http\Controllers\Api\Admin\Event;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{DB, Auth, Hash};
use App\Http\Requests\{Admin\Agent\UpdateAgentRequest};
use App\{MutifStoreAddress, Distributor, MutifStoreMaster, PartnerGroup, User};

class AgentController extends Controller
{
    public function index()
    {
        $datas = MutifStoreMaster::get();

        foreach ($datas as $data) {
            $data->agent = Distributor::where('id', $data->distributor_id)->first();
            $data->distributor = Distributor::where('id', $data->agent->distributor_id)->first();

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
        }

        return response()->json([
            'status' => 'success',
            'message' => 'success to get data',
            'data' => $datas
        ], 200);
    }

    public function store(Request $request)
    {
        try {
            // get mandatory data
            $user_id = Auth::user()->id;
            $dataDistributor = $this->checkExistanceDistributor($request->distributor_id);
            $getDataPartnerGroup = PartnerGroup::where('id', $request->partner_group_id)->first();

            // check existance distributor
            if ($dataDistributor['status'] == false) {
                return response()->json([
                    'status' => 'rejected',
                    'message' => 'distributor tidak ditemukan!',
                ], 300);
            }

            // start create data agent & user
            DB::beginTransaction();
                $agent = Distributor::firstOrCreate([
                    'name' => $request->ms_name,
                    'phone' => $request->ms_phone,
                    'prtnr_add_by' => $user_id,
                    'distributor_id' => $dataDistributor['data']['id'],
                    'group_code' => $getDataPartnerGroup->prtnr_code,
                    'training_level' => $request->ms_training_level,
                    'partner_group_id' => $getDataPartnerGroup->id,
                ]);

                $this->createUser($request->ms_name, $agent->id);
                $this->createMutifStore($request, $user_id, $getDataPartnerGroup, $agent->id);
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

    public function update(UpdateAgentRequest $request, $id)
    {
        try {
            $user_id = Auth::user()->id;
            $getDataAgent = Distributor::where('id', '=', $id)->first();
            $partnerGroup = PartnerGroup::where('id', $request->partner_group_id)->first();
            $mutifStoreMaster = MutifStoreMaster::where('distributor_id', $getDataAgent->id)->first();
            $mutifStoreAddress = MutifStoreAddress::where('mutif_store_master_id', $mutifStoreMaster->id)->first();

            // request
            $req = $this->checkRequest($request, $getDataAgent, $mutifStoreMaster, $mutifStoreAddress, $partnerGroup);

            DB::beginTransaction();
                $getDataAgent->update([
                    'name' => $req['requestAgent']['name'],
                    'phone' => $req['requestAgent']['phone'],
                    'prtnr_upd_by' => $user_id
                ]);

                $this->updateMutifStore($req, $user_id, $mutifStoreMaster, $mutifStoreAddress);
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

    private function checkExistanceDistributor($dbId)
    {
        $dataDistributor = Distributor::where('id', '=', $dbId)->first();

        return ($dataDistributor) ? ["data" => $dataDistributor, "status" => true] : ["data" => [], "status" => false];
    }

    private function createUser($msName, $agentId)
    {
        $explodeName = explode(' ', strtolower($msName));

        $implodeNameWithHyphen = implode('-', $explodeName);
        $implodeName = implode('', $explodeName);

        $user = User::create([
            "name" => $msName,
            "email" => "$implodeName@sb.com",
            "password" => Hash::make($implodeNameWithHyphen),
            "partner_id" => $agentId
        ]);
    }

    private function createMutifStore($request, $userId, $partnerGroup, $agentId)
    {
            $mutif_store = MutifStoreMaster::create([
                        'mutif_store_name' => $request->ms_ms_name,
                        'mutif_store_code' => $request->ms_code,
                        'ms_add_by' => $userId,
                        'group_code' => $partnerGroup->prtnr_code,
                        'distributor_id' => $agentId,
                        'partner_group_id' => $partnerGroup->id,
                        'status' => $request->ms_status,
                        'open_date' => ($request->ms_open_date) ? $request->ms_open_date : null,
                        'msdp' => $request->ms_msdp
                    ]);

            MutifStoreAddress::create([
                    'mutif_store_master_id' => $mutif_store->id,
                    'prtnr_add_by' => $userId,
                    'address' => $request->ms_address,
                    'province' => $request->ms_province,
                    'regency' => $request->ms_regency,
                    'district' => $request->ms_district,
                    'phone_1' => $request->ms_phone,
                    'phone_2' => ($request->ms_phone_2) ? $request->ms_phone_2 : 0
                ]);
    }

    private function checkRequest($request, $agent, $mutifStoreMaster, $mutifStoreAddress, $partnerGroup)
    {
        $requestAgent = [
            'name' => ($request->name) ? $request->name : $agent->name,
            'phone' => ($request->phone) ? $request->phone : $agent->phone,
        ];

        $requestMutifStore = [
            'mutif_store_master' => ($request->ms_ms_name) ? $request->ms_ms_name : $mutifStoreMaster->mutif_store_name,
            'mutif_store_code' => ($request->ms_code) ? $request->ms_code : $mutifStoreMaster->mutif_store_code,
            'group_code' => ($request->partner_group_id) ? $partnerGroup->prtnr_code : $mutifStoreMaster->group_code,
            'partner_group_id' => ($request->partner_group_id) ? $partnerGroup->id : $mutifStoreMaster->partner_group_id,
            'distributor_id' => ($request->distributor_id) ? $request->distributor_id : $mutifStoreMaster->distributor_id,
            'open_date' => ($request->open_date) ? $request->open_date : $mutifStoreMaster->open_date,
            'status' => ($request->status) ? $request->status : $mutifStoreMaster->status,
            'msdp' => ($request->msdp) ? $request->msdp : $mutifStoreMaster->msdp,
            'url' => ($request->url) ? $request->url : $mutifStoreMaster->url,
            'remarks' => ($request->remarks) ? $request->remarks : $mutifStoreMaster->remarks
        ];

        $requestMutifStoreAddress = [
            'address' => ($request->address) ? $request->address : $mutifStoreAddress->address,
            'province' => ($request->province) ? $request->province : $mutifStoreAddress->province,
            'regency' => ($request->regency) ? $request->regency : $mutifStoreAddress->regency,
            'district' => ($request->district) ? $request->district : $mutifStoreAddress->district,
            'phone_1' => ($request->phone_1) ? $request->phone_1 : $mutifStoreAddress->phone_1,
            'fax_1' => ($request->fax_1) ? $request->fax_1 : '-',
            'addr_type' => ($request->addr_type) ? $request->addr_type : $mutifStoreAddress->addr_type,
            'zip' => ($request->zip) ? $request->zip : $mutifStoreAddress->zip,
            'comment' => ($request->comment) ? $request->comment : $mutifStoreAddress->comment
        ];

        return compact('requestAgent', 'requestMutifStore', 'requestMutifStoreAddress');
    }

    private function updateMutifStore($req, $user_id, $mutifStoreMaster, $mutifStoreAddress)
    {
        $mutifStoreMaster->update([
            'ms_upd_by' => $user_id,
            'mutif_store_master' => $req['requestMutifStore']['mutif_store_master'],
            'mutif_store_code' => $req['requestMutifStore']['mutif_store_code'],
            'group_code' => $req['requestMutifStore']['group_code'],
            'partner_group_id' => $req['requestMutifStore']['partner_group_id'],
            'distributor_id' => $req['requestMutifStore']['distributor_id'],
            'open_date' => $req['requestMutifStore']['open_date'],
            'status' => $req['requestMutifStore']['status'],
            'msdp' => $req['requestMutifStore']['msdp'],
            'url' => $req['requestMutifStore']['url'],
            'remarks' => $req['requestMutifStore']['remarks']
        ]);

        $mutifStoreAddress->update([
            'prtnr_upd_by' => $user_id,
            'fax_1' => $req['requestMutifStoreAddress']['fax_1'],
            'address' => $req['requestMutifStoreAddress']['address'],
            'province' => $req['requestMutifStoreAddress']['province'],
            'regency' => $req['requestMutifStoreAddress']['regency'],
            'district' => $req['requestMutifStoreAddress']['district'],
            'phone_1' => $req['requestMutifStoreAddress']['phone_1'],
            'addr_type' => $req['requestMutifStoreAddress']['addr_type'],
            'zip' => $req['requestMutifStoreAddress']['zip'],
            'comment' => $req['requestMutifStoreAddress']['comment']
        ]);
    }
}
