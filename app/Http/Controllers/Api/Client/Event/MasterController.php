<?php

namespace App\Http\Controllers\Api\Client\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\{Distributor, PartnerGroup, Models\Event};

class MasterController extends Controller
{
    public function distributorList()
    {
        try {
            $distributor = Distributor::select('id', 'name')->where('partner_group_id', '=', 1)->get();

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

    public function partnerGroupList()
    {
        try {
            $partnerGroup = PartnerGroup::select('id', 'prtnr_code', 'prtnr_name')->get();

            return response()->json([
                'status' => 'success',
                'data' => $partnerGroup,
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

    public function activeEvent()
    {
        try {
            $event = Event::select('id', 'event_name', 'event_desc', 'start_date', 'end_date', 'is_active')->where('is_active', '=', true)->first();

            return response()->json([
                'status' => 'success',
                'data' => $event,
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
}
