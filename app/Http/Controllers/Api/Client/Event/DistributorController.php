<?php

namespace App\Http\Controllers\Api\Client\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Distributor;

class DistributorController extends Controller
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
}
