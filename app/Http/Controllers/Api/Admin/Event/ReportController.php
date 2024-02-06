<?php

namespace App\Http\Controllers\Api\Admin\Event;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\{Order, Distributor, Entity};

class ReportController extends Controller
{
    public function highestOrder($eventId)
    {
        try {
            $dataOrder = Order::select(
                                'orders.client_id',
                                'distributors.name',
                                'events.event_name',
                                DB::raw('sum(size_S + size_M + size_L + size_XL + size_XXL + size_XXXL + size_2 + size_4 + size_6 + size_8 + size_10 + size_12 + size_27 + size_28 + size_29 + size_30 + size_31 + size_32 + size_33 + size_34 + size_35 + size_36 + size_37 + size_38 + size_39 + size_40 + size_41 + size_42 + size_other) as total_order')
                            )->leftJoin('distributors', 'distributors.id', '=', 'orders.client_id')
                            ->leftJoin('events', 'events.id', '=', 'orders.event_id')
                            ->where('event_id', '=', $eventId)
                            ->groupBy('distributors.name', 'events.event_name', 'orders.client_id')
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

    public function detailOrder($eventId, $clientId)
    {
        try {
            $detailOrder = Distributor::select(
                                        DB::raw('distributors.id'),
                                        DB::raw('distributors.name AS agent_name'),
                                        DB::raw('partner_groups.prtnr_name'),
                                        DB::raw('distributor.name AS distributor_name')
                    )->rightJoin('partner_groups', 'partner_groups.id', '=', 'distributors.partner_group_id')
                    ->leftJoin('distributors AS distributor', 'distributor.id', '=', 'distributors.distributor_id')
                    ->where('distributors.id', '=', $clientId)
                    ->with(['Order' => function ($query) use ($eventId) {
                        $query->select(
                                'orders.id',
                                'orders.client_id',
                                'products.article_name',
                                'events.event_name',
                                'events.event_desc',
                                'products.type_id',
                                'types.type',
                                'orders.size_S',
                                'orders.size_M',
                                'orders.size_L',
                                'orders.size_XL',
                                'orders.size_XXL',
                                'orders.size_XXXL',
                                'orders.size_2',
                                'orders.size_4',
                                'orders.size_6',
                                'orders.size_8',
                                'orders.size_10',
                                'orders.size_12',
                                'orders.size_27',
                                'orders.size_28',
                                'orders.size_29',
                                'orders.size_30',
                                'orders.size_31',
                                'orders.size_32',
                                'orders.size_33',
                                'orders.size_34',
                                'orders.size_35',
                                'orders.size_36',
                                'orders.size_37',
                                'orders.size_38',
                                'orders.size_39',
                                'orders.size_40',
                                'orders.size_41',
                                'orders.size_42',
                                'orders.size_other',
                                DB::raw('CAST(orders.created_at AS DATE) AS created_at'),
                                DB::raw('CAST(orders.updated_at AS DATE) AS updated_at'),
                            )->leftJoin('products', 'products.id', '=', 'orders.product_id')
                            ->leftJoin('events', 'events.id', '=', 'orders.event_id')
                            ->leftJoin('types', 'types.id', '=', 'products.type_id')
                            ->where('orders.event_id', '=', $eventId);
                    }])
                    ->first();

            return response()->json([
                'status' => 'success',
                'data' => $detailOrder,
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

    public function highestOrderDistributor($eventId)
    {
        try {
            $dataOrderDistributor = Order::select(
                                            DB::raw('distributor.id AS distributor_id'),
                                            'distributor.name',
                                            'distributor.partner_group_id',
                                            'events.event_name',
                                            DB::raw('sum(size_S + size_M + size_L + size_XL + size_XXL + size_XXXL + size_2 + size_4 + size_6 + size_8 + size_10 + size_12 + size_27 + size_28 + size_29 + size_30 + size_31 + size_32 + size_33 + size_34 + size_35 + size_36 + size_37 + size_38 + size_39 + size_40 + size_41 + size_42 + size_other) as total_order')
                                        )->leftJoin('distributors AS client', 'client.id', '=', 'orders.client_id')
                                        ->leftJoin('distributors AS distributor', 'distributor.id', '=', 'client.distributor_id')
                                        ->leftJoin('events', 'events.id', '=', 'orders.event_id')
                                        ->where('orders.event_id', '=', $eventId)
                                        ->groupBy('distributor_id', 'distributor.name', 'distributor.partner_group_id', 'events.event_name')
                                        ->orderByDesc('total_order')
                                        ->get();

            return response()->json([
                'status' => 'success',
                'data' => $dataOrderDistributor,
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

    public function highestOrderProduct($eventId)
    {
        try {
            $entity = (request()->entity) ? [request()->entity] : $this->getEntity();

            $dataOrderProduct = Order::select(
                                            'products.id',
                                            'products.article_name',
                                            'events.event_name',
                                            'entities.entity_name',
                                            DB::raw('sum(size_S + size_M + size_L + size_XL + size_XXL + size_XXXL + size_2 + size_4 + size_6 + size_8 + size_10 + size_12 + size_27 + size_28 + size_29 + size_30 + size_31 + size_32 + size_33 + size_34 + size_35 + size_36 + size_37 + size_38 + size_39 + size_40 + size_41 + size_42 + size_other) as total_order')
                                        )->leftJoin('products', 'products.id', '=', 'orders.product_id')
                                        ->leftJoin('events', 'events.id', '=', 'orders.event_id')
                                        ->leftJoin('entities', 'entities.entity_name', '=', 'products.entity_name')
                                        ->where('orders.event_id', '=', $eventId)
                                        ->whereIn('products.entity_name', $entity)
                                        ->groupBy('products.id', 'products.article_name', 'entities.entity_name', 'events.event_name')
                                        ->orderByDesc('total_order')
                                        ->get();

            return response()->json([
                'status' => 'success',
                'data' => $dataOrderProduct,
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

    private function getEntity()
    {
        $entity = Entity::select('entity_name')->pluck('entity_name')->toArray();

        return $entity;
    }
}
