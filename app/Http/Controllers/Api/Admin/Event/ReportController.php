<?php

namespace App\Http\Controllers\Api\Admin\Event;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function highestOrder($eventId)
    {
        try {
            $dataOrder = Order::select(
                                'distributors.name',
                                DB::raw('sum(size_S + size_M + size_L + size_XL + size_XXL + size_XXXL + size_2 + size_4 + size_6 + size_8 + size_10 + size_12 + size_27 + size_28 + size_29 + size_30 + size_31 + size_32 + size_33 + size_34 + size_35 + size_36 + size_37 + size_38 + size_39 + size_40 + size_41 + size_42 + size_other) as total_order')
                            )->leftJoin('distributors', 'distributors.id', '=', 'orders.client_id')
                            ->where('event_id', '=', $eventId)
                            ->groupBy('distributors.name')
                            ->orderByDesc('total_order')
                            ->get();

            return response()->json([
                'status' => 'success',
                'data' => $dataOrder,
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
