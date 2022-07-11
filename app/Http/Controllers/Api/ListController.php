<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Distributor;
use App\PartnerGroup;

class ListController extends Controller
{
    public function listDistributor()
    {
        try {
        $distributor = Distributor::get();

        return response()->json([
            'status' => 'success',
            'message' => 'success to get data',
            'data' => $distributor
        ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get data',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function listGroup()
    {
        try {
            $group = PartnerGroup::get();

            return response()->json([
                'status' => 'success',
                'message' => 'success to get list group',
                'data' => $group,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get data',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
