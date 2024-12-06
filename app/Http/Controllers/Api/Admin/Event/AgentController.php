<?php

namespace App\Http\Controllers\Api\Admin\Event;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{DB, Auth, Hash};
use App\Http\Requests\{Admin\Agent\UpdateAgentRequest};
use App\Models\{MutifStoreAddress, Distributor, MutifStoreMaster, PartnerGroup};

class AgentController extends Controller
{
    public function index()
    {
        $dataAgent = MutifStoreMaster::select(
                                    'mutif_store_masters.id',
                                    'mutif_store_masters.mutif_store_name',
                                    'mutif_store_masters.mutif_store_code',
                                    'mutif_store_masters.open_date',
                                    'mutif_store_masters.status',
                                    'mutif_store_addresses.address',
                                    'mutif_store_addresses.province',
                                    'mutif_store_addresses.regency',
                                    'mutif_store_addresses.district',
                                    'mutif_store_addresses.phone_1',
                                    'mutif_store_addresses.phone_2',
                                    'mutif_store_addresses.fax_1',
                                    'mutif_store_addresses.fax_2',
                                    'mutif_store_addresses.addr_type',
                                    'mutif_store_addresses.zip',
                                    'mutif_store_masters.distributor_id AS agent_id',
                                    'agent.name AS agent_name',
                                    'distributor.id AS distributor_id',
                                    'distributor.name AS distributor_name'
                                )->leftJoin('distributors AS agent', 'agent.id', '=', 'mutif_store_masters.distributor_id')
                                ->leftJoin('distributors AS distributor', 'distributor.id', '=', 'agent.distributor_id')
                                ->leftJoin('mutif_store_addresses', 'mutif_store_addresses.mutif_store_master_id', '=', 'mutif_store_masters.id')
                                ->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'success to get data',
            'data' => $dataAgent
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

    public function getClient()
    {
        try {
            $isDistributor = (request()->is_distributor) ? request()->is_distributor : 'Y';

            if ($isDistributor == 'Y') {
                $dataClient = $this->getListDistributor();
            } else {
                $dataClient = $this->getLIstAgent();
            }

            return response()->json([
                'status' => 'success',
                'data' => $dataClient,
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

    public function show($id)
    {
        try {
            $MutifStore = MutifStoreMaster::select(
                                                'mutif_store_masters.id',
                                                'mutif_store_name',
                                                'mutif_store_code',
                                                'distributor_id',
                                                'open_date',
                                                'status',
                                                'msdp',
                                                'url',
                                                'remarks',
                                                'msa.address',
                                                'msa.province',
                                                'msa.regency',
                                                'msa.district',
                                                'msa.phone_1',
                                                'msa.phone_2',
                                                'msa.fax_1',
                                                'msa.fax_2',
                                                'msa.addr_type',
                                                'msa.zip'
                                            )->leftJoin('mutif_store_addresses AS msa', 'msa.mutif_store_master_id', '=', 'mutif_store_masters.id')
                                            ->with(['Agent' => function ($query) {
                                                $query->select('distributors.id AS id', 'distributors.name AS agent_name', 'distributors.phone', 'distributors.group_code', 'groups.prtnr_name', 'distributor.id AS distributor_id', 'distributor.name AS distributor_name')
                                                    ->leftJoin('partner_groups AS groups', 'groups.id', '=', 'distributors.partner_group_id')
                                                    ->leftJoin('distributors AS distributor', 'distributor.id', '=', 'distributors.distributor_id');
                                            }])
                                            ->where('mutif_store_masters.id', '=', $id)
                                            ->first();

            return response()->json([
                'status' => 'success',
                'message' => 'success to get detail data',
                'data' => $MutifStore
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
            $mutifStoreMaster = MutifStoreMaster::where('id', $id)->first();
            $getDataAgent = Distributor::where('id', '=', function($query) use ($id) {
                $query->select('distributor_id')
                    ->from('mutif_store_masters')
                    ->where('id', '=', $id)
                    ->first();
            })->first();
            $partnerGroup = PartnerGroup::where('id', $request->partner_group_id)->first();
            $mutifStoreAddress = MutifStoreAddress::where('mutif_store_master_id', $mutifStoreMaster->id)->first();

            // request
            $req = $this->checkRequest($request, $getDataAgent, $mutifStoreMaster, $mutifStoreAddress, $partnerGroup);

            DB::beginTransaction();
                $getDataAgent->update([
                    'name' => $req['requestAgent']['name'],
                    'phone' => $req['requestAgent']['phone'],
                    'distributor_id' => $req['requestAgent']['distributor_id'],
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
                    'phone_2' => ($request->ms_phone_2) ? $request->ms_phone_2 : 0,
                    'fax_1' => $request->fax_1,
                    'addr_type' => 'billTo',
                    'comment' => $request->comment,
                    'zip' => $request->zip
                ]);
    }

    private function checkRequest($request, $agent, $mutifStoreMaster, $mutifStoreAddress, $partnerGroup)
    {
        $requestAgent = [
            'name' => ($request->name) ? $request->name : $agent->name,
            'phone' => ($request->phone) ? $request->phone : $agent->phone,
            'distributor_id' => ($request->distributor_id) ? $request->distributor_id : $agent->distributor_id
        ];

        $requestMutifStore = [
            'mutif_store_master' => ($request->ms_ms_name) ? $request->ms_ms_name : $mutifStoreMaster->mutif_store_name,
            'mutif_store_code' => ($request->ms_code) ? $request->ms_code : $mutifStoreMaster->mutif_store_code,
            'group_code' => ($request->partner_group_id) ? $partnerGroup->prtnr_code : $mutifStoreMaster->group_code,
            'partner_group_id' => ($request->partner_group_id) ? $partnerGroup->id : $mutifStoreMaster->partner_group_id,
            // 'distributor_id' => ($request->distributor_id) ? $request->distributor_id : $mutifStoreMaster->distributor_id,
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
            // 'distributor_id' => $req['requestMutifStore']['distributor_id'],
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

    private function getListDistributor()
    {
        $dataDistributor = Distributor::select('id', 'name')->where('partner_group_id', '=', 1)->get();

        return $dataDistributor;
    }

    private function getListAgent()
    {
        $dataAgent = Distributor::select('id', 'name')->where('partner_group_id', '<>', 1)->get();

        return $dataAgent;
    }
}
