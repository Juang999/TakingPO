<?php

namespace App\Http\Controllers\Api\Admin\Event;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\{Order, Distributor, Entity, Product};

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
                                        ->limit(3)
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

    public function ReportPerProduct($id)
    {
        try {
            $product = Product::select(
                'products.id',
                'products.entity_name',
                'products.article_name',
                'products.type_id',
                DB::raw('SUM(size_S + size_M + size_L + size_XL + size_XXL + size_XXXL + size_2 + size_4 + size_6 + size_8 + size_10 + size_12 + size_27 + size_28 + size_29 + size_30 + size_31 + size_32 + size_33 + size_34 + size_35 + size_36 + size_37 + size_38 + size_39 + size_40 + size_41 + size_42 + size_other) AS total_order'),
            )->leftJoin('orders', 'orders.product_id', '=', 'products.id')
            ->where('products.id', '=', $id)
            ->with(['DetailOrder' => function ($query) {
                $query->select(
                        'orders.client_id',
                        'orders.product_id',
                        'products.type_id',
                        DB::raw('distributors.name AS agent_name'),
                        DB::raw('db.name AS distributor_name'),
                        DB::raw('events.event_name'),
                        DB::raw('SUM(size_S + size_M + size_L + size_XL + size_XXL + size_XXXL + size_2 + size_4 + size_6 + size_8 + size_10 + size_12 + size_27 + size_28 + size_29 + size_30 + size_31 + size_32 + size_33 + size_34 + size_35 + size_36 + size_37 + size_38 + size_39 + size_40 + size_41 + size_42 + size_other) AS total_order'),
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
                    )
                    ->leftJoin('distributors', 'distributors.id', '=', 'orders.client_id')
                    ->leftJoin('distributors AS db', 'db.id', '=', 'distributors.distributor_id')
                    ->leftJoin('events', 'events.id', '=', 'orders.event_id')
                    ->leftJoin('products', 'products.id', '=', 'orders.product_id')
                    ->groupBy(
                        'orders.client_id',
                        'orders.product_id',
                        'distributors.name',
                        'db.name',
                        'events.event_name',
                        'products.type_id',
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
                        'orders.size_other'
                    )
                    ;
            }])
            ->groupBy('products.id','products.entity_name','products.article_name', 'products.type_id')
            ->first();

            return response()->json([
                'status' => 'success',
                'data' => $product,
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

    public function getOrderedProduct($eventId)
    {
        try {
            $product_article = request()->search;

            $product = Product::select(
                                    'products.id',
                                    'products.article_name',
                                    'products.entity_name',
                                    'events.event_name',
                                    DB::raw('SUM(size_S + size_M + size_L + size_XL + size_XXL + size_XXXL + size_2 + size_4 + size_6 + size_8 + size_10 + size_12 + size_27 + size_28 + size_29 + size_30 + size_31 + size_32 + size_33 + size_34 + size_35 + size_36 + size_37 + size_38 + size_39 + size_40 + size_41 + size_42 + size_other) AS total_order')
                        )->leftJoin('orders', 'orders.product_id', '=', 'products.id')
                        ->leftJoin('events', 'events.id', '=', 'orders.event_id')
                        ->whereIn('products.id', function ($query) use ($eventId) {
                            $query->select('product_id')
                                ->from('orders')
                                ->where('event_id', $eventId)
                                ->get();
                        })
                        ->when($product_article, function ($query) use ($product_article) {
                            $query->where('article_name', 'LIKE', "%$product_article%");
                        })
                        ->groupBy('products.id','products.article_name','products.entity_name','events.event_name')
                        ->orderByDesc('total_order')
                        ->get();

            return response()->json([
                'status' => 'success',
                'data' => $product,
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

    public function getAllReport()
    {
        try {
            $order = Order::select(
                'orders.id',
                'entities.entity_name',
                'orders.created_at',
                'products.type_id',
                'distributors.name AS agent_name',
                'db.name AS distributor_name',
                'events.event_name',
                'products.article_name',
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
                DB::raw('SUM(size_S + size_M + size_L + size_XL + size_XXL + size_XXXL + size_2 + size_4 + size_6 + size_8 + size_10 + size_12 + size_27 + size_28 + size_29 + size_30 + size_31 + size_32 + size_33 + size_34 + size_35 + size_36 + size_37 + size_38 + size_39 + size_40 + size_41 + size_42 + size_other) AS total_order')
            )->leftJoin('products', 'products.id', '=', 'orders.product_id')
            ->leftJoin('events', 'events.id', '=', 'orders.event_id')
            ->leftJoin('distributors', 'distributors.id', '=', 'orders.client_id')
            ->leftJoin('distributors AS db', 'db.id', '=', 'distributors.distributor_id')
            ->leftJoin('entities', 'entities.entity_name', '=', 'products.entity_name')
            ->leftJoin('types', 'types.id', '=', 'products.type_id')
            ->groupBy(
                'orders.id',
                'entities.entity_name',
                'types.type',
                'products.type_id',
                'orders.created_at',
                'distributors.name',
                'db.name',
                'events.event_name',
                'products.article_name',
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
                'orders.size_other'
            )
            ->orderBy('orders.event_id', 'ASC')
            ->get();

            return response()->json([
                'status' => 'success',
                'data' => $order,
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

    public function getReportDistributor($id)
    {
        try {
            $dataOrder = Distributor::select(
                'distributors.id',
                'distributors.name',
                'partner_groups.prtnr_name',
                DB::raw("(SELECT SUM(size_S + size_M + size_L + size_XL + size_XXL + size_XXXL + size_2 + size_4 + size_6 + size_8 + size_10 + size_12 + size_27 + size_28 + size_29 + size_30 + size_31 + size_32 + size_33 + size_34 + size_35 + size_36 + size_37 + size_38 + size_39 + size_40 + size_41 + size_42 + size_other) FROM orders WHERE client_id = distributors.id AND product_id IN (SELECT id FROM products WHERE entity_name = 'MUTIF')) AS MUTIF"),
                DB::raw("(SELECT SUM(size_S + size_M + size_L + size_XL + size_XXL + size_XXXL + size_2 + size_4 + size_6 + size_8 + size_10 + size_12 + size_27 + size_28 + size_29 + size_30 + size_31 + size_32 + size_33 + size_34 + size_35 + size_36 + size_37 + size_38 + size_39 + size_40 + size_41 + size_42 + size_other) FROM orders WHERE client_id = distributors.id AND product_id IN (SELECT id FROM products WHERE entity_name = 'DAMOZA')) AS DAMOZA"),
                DB::raw("(SELECT SUM(size_S + size_M + size_L + size_XL + size_XXL + size_XXXL + size_2 + size_4 + size_6 + size_8 + size_10 + size_12 + size_27 + size_28 + size_29 + size_30 + size_31 + size_32 + size_33 + size_34 + size_35 + size_36 + size_37 + size_38 + size_39 + size_40 + size_41 + size_42 + size_other) FROM orders WHERE client_id = distributors.id AND product_id IN (SELECT id FROM products WHERE entity_name = 'UPMORE')) AS UPMORE"),
                DB::raw("(SELECT SUM(size_S + size_M + size_L + size_XL + size_XXL + size_XXXL + size_2 + size_4 + size_6 + size_8 + size_10 + size_12 + size_27 + size_28 + size_29 + size_30 + size_31 + size_32 + size_33 + size_34 + size_35 + size_36 + size_37 + size_38 + size_39 + size_40 + size_41 + size_42 + size_other) FROM orders WHERE client_id = distributors.id AND product_id) AS TOTAL")
            )->join('partner_groups', 'partner_groups.id', '=', 'distributors.partner_group_id')
            ->where('distributors.id', '=', $id)
            ->orWhere('distributor_id', '=', $id)
            ->orderByDesc('TOTAL')
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

    public function sumOrderedProduct()
    {
        try {
            $dataOrder = Order::select(
                'products.entity_name',
                'products.article_name',
                'product.type_id',
                DB::raw('SUM(size_S) AS size_S'),
                DB::raw('SUM(size_M) AS size_M'),
                DB::raw('SUM(size_L) AS size_L'),
                DB::raw('SUM(size_XL) AS size_XL'),
                DB::raw('SUM(size_XXL) AS size_XXL'),
                DB::raw('SUM(size_XXXL) AS size_XXXL'),
                DB::raw('SUM(size_2) AS size_2'),
                DB::raw('SUM(size_4) AS size_4'),
                DB::raw('SUM(size_6) AS size_6'),
                DB::raw('SUM(size_8) AS size_8'),
                DB::raw('SUM(size_10) AS size_10'),
                DB::raw('SUM(size_12) AS size_12'),
                DB::raw('SUM(size_27) AS size_27'),
                DB::raw('SUM(size_28) AS size_28'),
                DB::raw('SUM(size_29) AS size_29'),
                DB::raw('SUM(size_30) AS size_30'),
                DB::raw('SUM(size_31) AS size_31'),
                DB::raw('SUM(size_32) AS size_32'),
                DB::raw('SUM(size_33) AS size_33'),
                DB::raw('SUM(size_34) AS size_34'),
                DB::raw('SUM(size_35) AS size_35'),
                DB::raw('SUM(size_36) AS size_36'),
                DB::raw('SUM(size_37) AS size_37'),
                DB::raw('SUM(size_38) AS size_38'),
                DB::raw('SUM(size_39) AS size_39'),
                DB::raw('SUM(size_40) AS size_40'),
                DB::raw('SUM(size_41) AS size_41'),
                DB::raw('SUM(size_42) AS size_42'),
                DB::raw('SUM(size_other) AS size_other'),
                DB::raw("SUM(size_S + size_M + size_L + size_XL + size_XXL + size_XXXL + size_2 + size_4 + size_6 + size_8 + size_10 + size_12 + size_27 + size_28 + size_29 + size_30 + size_31 + size_32 + size_33 + size_34 + size_35 + size_36 + size_37 + size_38 + size_39 + size_40 + size_41 + size_42 + size_other) AS TOTAL")
            )->leftJoin('products', 'products.id', '=', 'orders.product_id')
            ->leftJoin('entities', 'entities.entity_name', '=', 'products.entity_name')
            ->groupBy('products.entity_name', 'products.type_id', 'products.article_name', 'entities.id')
            ->orderBy('entities.id')
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

    public function getDetailOrderDistributor($id)
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
            ->where('distributor.id', '=', $id)
            ->with(['Agent' => function ($query) {
                $query->select(
                        'distributors.name',
                        'products.article_name',
                        DB::raw("SUM(orders.size_S + orders.size_M + orders.size_L + orders.size_XL + orders.size_XXL + orders.size_XXXL + orders.size_2 + orders.size_4 + orders.size_6 + orders.size_8 + orders.size_10 + orders.size_12 + orders.size_27 + orders.size_28 + orders.size_29 + orders.size_30 + orders.size_31 + orders.size_32 + orders.size_33 + orders.size_34 + orders.size_35 + orders.size_36 + orders.size_37 + orders.size_38 + orders.size_39 + orders.size_40 + orders.size_41 + orders.size_42 + orders.size_other) AS TOTAL")
                    )->leftJoin('orders', 'orders.client_id', '=', 'distributors.id')
                    ->leftJoin('products', 'products.id', '=', 'orders.product_id')
                    ->groupBy('distributors.name', 'products.article_name')
                    ->get();
            }])
            ->groupBy('distributor_id', 'distributor.name', 'distributor.partner_group_id', 'events.event_name')
            ->orderByDesc('total_order')
            ->first();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    private function getEntity()
    {
        $entity = Entity::select('entity_name')->pluck('entity_name')->toArray();

        return $entity;
    }
}
