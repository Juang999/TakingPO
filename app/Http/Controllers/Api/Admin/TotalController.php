<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Admin\LoopFunction;
use App\{Clothes, BufferProduct, Distributor, PartnerGroup, TableName};

class TotalController extends Controller
{
    public function totalOrder()
    {
        try {
            $theData = Clothes::with('BufferProduct.Size')->orderBy('group_article', 'ASC')->get();

            foreach ($theData as $data) {
                $data['total'] = BufferProduct::where('clothes_id', $data->id)->sum('qty_process');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'success to get data',
                'data' => $theData
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get data',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function totalProductOrderClient()
    {
        try {
            $theData = Distributor::whereIn('id', function ($query) {
                $query->select('distributor_id')
                    ->from('table_names')
                    ->get();
            })->get();

            $loopFunction = new LoopFunction();

            foreach ($theData as $data) {
                $tableName = TableName::where('distributor_id', $data->id)->first();
                $discount = PartnerGroup::where('id', $data->partner_group_id)->first('discount');

                $data['mutif'] = $loopFunction->totalClient($tableName->table_name, 'MUTIF');
                $data['damoza'] = $loopFunction->totalClient($tableName->table_name, 'DAMOZA');
                $data['upmore'] = $loopFunction->totalClient($tableName->table_name, 'UPMORE');

                $data['total'] = $data['mutif'] + $data['damoza'] + $data['upmore'];

                $data['nominal_mutif'] = $loopFunction->totalNominalClient($tableName->table_name, 'MUTIF', $discount);
                $data['nominal_damoza'] = $loopFunction->totalNominalClient($tableName->table_name, 'DAMOZA', $discount);
                $data['nominal_upmore'] = $loopFunction->totalNominalClient($tableName->table_name, 'UPMORE', $discount);

                $data['total_nominal'] = $data['nominal_mutif'] + $data['nominal_damoza'] + $data['nominal_upmore'];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'success to get total data',
                'data' => $theData
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function totalAgentWithDB()
    {
        try {
            $clients = TableName::get();
            $users = [];

            foreach ($clients as $client) {
                $user = Distributor::where('id', $client->distributor_id)->first();
                $data = DB::table($client->table_name)->selectRaw('(SELECT name FROM distributors WHERE id = '.$user->distributor_id.') AS DB, (SELECT name FROM distributors WHERE id = '.$user->id.') AS AGEN, (SELECT discount FROM partner_groups WHERE id = '.$user->partner_group_id.') AS discount, clothes.entity_name, clothes.category, clothes.article_name, clothes.group_article, clothes.type_id, '.$client->table_name.'.size_s, '.$client->table_name.'.size_m, '.$client->table_name.'.size_l, '.$client->table_name.'.size_xl, '.$client->table_name.'.size_xxl, '.$client->table_name.'.size_xxxl, '.$client->table_name.'.size_2, '.$client->table_name.'.size_4, '.$client->table_name.'.size_6, '.$client->table_name.'.size_8, '.$client->table_name.'.size_10, '.$client->table_name.'.size_12, '.$client->table_name.'.size_27, '.$client->table_name.'.size_28, '.$client->table_name.'.size_29, '.$client->table_name.'.size_30, '.$client->table_name.'.size_31, '.$client->table_name.'.size_32, '.$client->table_name.'.size_33, '.$client->table_name.'.size_34, '.$client->table_name.'.size_35, '.$client->table_name.'.size_36, '.$client->table_name.'.size_37, '.$client->table_name.'.size_38, '.$client->table_name.'.size_39, '.$client->table_name.'.size_40, '.$client->table_name.'.size_41, '.$client->table_name.'.size_42, '.$client->table_name.'.other, ('.$client->table_name.'.size_s + '.$client->table_name.'.size_m + '.$client->table_name.'.size_l + '.$client->table_name.'.size_xl + '.$client->table_name.'.size_xxl + '.$client->table_name.'.size_xxxl + '.$client->table_name.'.size_2 + '.$client->table_name.'.size_4 + '.$client->table_name.'.size_6 + '.$client->table_name.'.size_8 + '.$client->table_name.'.size_10 + '.$client->table_name.'.size_12 + '.$client->table_name.'.size_27 + '.$client->table_name.'.size_28 + '.$client->table_name.'.size_29 + '.$client->table_name.'.size_30 + '.$client->table_name.'.size_31 + '.$client->table_name.'.size_32 + '.$client->table_name.'.size_33 + '.$client->table_name.'.size_34 + '.$client->table_name.'.size_35 + '.$client->table_name.'.size_36 + '.$client->table_name.'.size_37 + '.$client->table_name.'.size_38 + '.$client->table_name.'.size_39 + '.$client->table_name.'.size_40 + '.$client->table_name.'.size_41 + '.$client->table_name.'.size_42 + '.$client->table_name.'.other) AS size_total, ('.$client->table_name.'.size_s * clothes.size_s) AS nominal_s, ('.$client->table_name.'.size_m * clothes.size_m) nominal_m, ('.$client->table_name.'.size_l * clothes.size_l) AS nominal_l, ('.$client->table_name.'.size_xl * clothes.size_xl) AS nominal_xl, ('.$client->table_name.'.size_xxl * clothes.size_xxl) AS nominal_xxl, ('.$client->table_name.'.size_xxxl * clothes.size_xxxl) AS nominal_xxxl, ('.$client->table_name.'.size_2 * clothes.size_2) AS nominal_2, ('.$client->table_name.'.size_4 * clothes.size_4) AS nominal_4, ('.$client->table_name.'.size_6 * clothes.size_6) AS nominal_6, ('.$client->table_name.'.size_8 * clothes.size_8) AS nominal_8, ('.$client->table_name.'.size_10 * clothes.size_10) AS nominal_10, ('.$client->table_name.'.size_12 * clothes.size_12) AS nominal_12, ('.$client->table_name.'.size_27 * clothes.size_27) AS nominal_27, ('.$client->table_name.'.size_28 * clothes.size_28) AS nominal_28, ('.$client->table_name.'.size_29 * clothes.size_29) AS nominal_29, ('.$client->table_name.'.size_30 * clothes.size_30) AS nominal_30, ('.$client->table_name.'.size_31 * clothes.size_31) AS nominal_31, ('.$client->table_name.'.size_32 * clothes.size_32) AS nominal_32, ('.$client->table_name.'.size_33 * clothes.size_33) AS nominal_33, ('.$client->table_name.'.size_34 * clothes.size_34) AS nominal_34, ('.$client->table_name.'.size_35 * clothes.size_35) AS nominal_35, ('.$client->table_name.'.size_36 * clothes.size_36) AS nominal_36, ('.$client->table_name.'.size_37 * clothes.size_37) AS nominal_37, ('.$client->table_name.'.size_38 * clothes.size_38) AS nominal_38, ('.$client->table_name.'.size_39 * clothes.size_39) AS nominal_39, ('.$client->table_name.'.size_40 * clothes.size_40) AS nominal_40, ('.$client->table_name.'.size_31 * clothes.size_41) AS nominal_41, ('.$client->table_name.'.size_42 * clothes.size_42) AS nominal_42, ('.$client->table_name.'.other * clothes.other) AS nominal_other, (('.$client->table_name.'.size_s * clothes.size_s) + ('.$client->table_name.'.size_m * clothes.size_m) + ('.$client->table_name.'.size_l * clothes.size_l) + ('.$client->table_name.'.size_xl * clothes.size_xl) + ('.$client->table_name.'.size_xxl * clothes.size_xxl) + ('.$client->table_name.'.size_xxxl * clothes.size_xxxl) + ('.$client->table_name.'.size_2 * clothes.size_2) + ('.$client->table_name.'.size_4 * clothes.size_4) + ('.$client->table_name.'.size_6 * clothes.size_6) + ('.$client->table_name.'.size_8 * clothes.size_8) + ('.$client->table_name.'.size_10 * clothes.size_10) + ('.$client->table_name.'.size_12 * clothes.size_12) + ('.$client->table_name.'.size_27 * clothes.size_27) + ('.$client->table_name.'.size_28 * clothes.size_28) + ('.$client->table_name.'.size_29 * clothes.size_29) + ('.$client->table_name.'.size_30 * clothes.size_30) + ('.$client->table_name.'.size_31 * clothes.size_31) + ('.$client->table_name.'.size_32 * clothes.size_32) + ('.$client->table_name.'.size_33 * clothes.size_33) + ('.$client->table_name.'.size_34 * clothes.size_34) + ('.$client->table_name.'.size_35 * clothes.size_35) + ('.$client->table_name.'.size_36 * clothes.size_36) + ('.$client->table_name.'.size_37 * clothes.size_37) + ('.$client->table_name.'.size_38 * clothes.size_38) + ('.$client->table_name.'.size_39 * clothes.size_39) + ('.$client->table_name.'.size_40 * clothes.size_40) + ('.$client->table_name.'.size_31 * clothes.size_41) + ('.$client->table_name.'.size_42 * clothes.size_42) + ('.$client->table_name.'.other)) AS nominal_total')
                ->leftJoin('clothes', $client->table_name.'.clothes_id', '=', 'clothes.id')
                ->orderBy('clothes.group_article')
                ->get();
                $users[] = $data;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'success to get data',
                'data' => $users
            ], 200);

            return response()->json([
                'data' => $users
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get report',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function detailTotalAgentWithDB($id)
    {
        try {
            $clients = TableName::whereIn('distributor_id', function ($query) use ($id) {
                $query->select('id')
                    ->from('distributors')
                    ->where('distributor_id', $id)
                    ->get();
            })->get();

            $theData = [];

            foreach ($clients as $client) {
                $user = Distributor::where('id', $client->distributor_id)->first();
                $data = DB::table($client->table_name)->selectRaw('(SELECT name FROM distributors WHERE id = '.$user->distributor_id.') AS DB, (SELECT name FROM distributors WHERE id = '.$user->id.') AS AGEN, (SELECT discount FROM partner_groups WHERE id = '.$user->partner_group_id.') AS discount, clothes.entity_name, clothes.category, clothes.article_name, clothes.group_article, clothes.type_id, '.$client->table_name.'.size_s, '.$client->table_name.'.size_m, '.$client->table_name.'.size_l, '.$client->table_name.'.size_xl, '.$client->table_name.'.size_xxl, '.$client->table_name.'.size_xxxl, '.$client->table_name.'.size_2, '.$client->table_name.'.size_4, '.$client->table_name.'.size_6, '.$client->table_name.'.size_8, '.$client->table_name.'.size_10, '.$client->table_name.'.size_12, '.$client->table_name.'.size_27, '.$client->table_name.'.size_28, '.$client->table_name.'.size_29, '.$client->table_name.'.size_30, '.$client->table_name.'.size_31, '.$client->table_name.'.size_32, '.$client->table_name.'.size_33, '.$client->table_name.'.size_34, '.$client->table_name.'.size_35, '.$client->table_name.'.size_36, '.$client->table_name.'.size_37, '.$client->table_name.'.size_38, '.$client->table_name.'.size_39, '.$client->table_name.'.size_40, '.$client->table_name.'.size_41, '.$client->table_name.'.size_42, '.$client->table_name.'.other, ('.$client->table_name.'.size_s + '.$client->table_name.'.size_m + '.$client->table_name.'.size_l + '.$client->table_name.'.size_xl + '.$client->table_name.'.size_xxl + '.$client->table_name.'.size_xxxl + '.$client->table_name.'.size_2 + '.$client->table_name.'.size_4 + '.$client->table_name.'.size_6 + '.$client->table_name.'.size_8 + '.$client->table_name.'.size_10 + '.$client->table_name.'.size_12 + '.$client->table_name.'.size_27 + '.$client->table_name.'.size_28 + '.$client->table_name.'.size_29 + '.$client->table_name.'.size_30 + '.$client->table_name.'.size_31 + '.$client->table_name.'.size_32 + '.$client->table_name.'.size_33 + '.$client->table_name.'.size_34 + '.$client->table_name.'.size_35 + '.$client->table_name.'.size_36 + '.$client->table_name.'.size_37 + '.$client->table_name.'.size_38 + '.$client->table_name.'.size_39 + '.$client->table_name.'.size_40 + '.$client->table_name.'.size_41 + '.$client->table_name.'.size_42 + '.$client->table_name.'.other) AS size_total, ('.$client->table_name.'.size_s * clothes.size_s) AS nominal_s, ('.$client->table_name.'.size_m * clothes.size_m) nominal_m, ('.$client->table_name.'.size_l * clothes.size_l) AS nominal_l, ('.$client->table_name.'.size_xl * clothes.size_xl) AS nominal_xl, ('.$client->table_name.'.size_xxl * clothes.size_xxl) AS nominal_xxl, ('.$client->table_name.'.size_xxxl * clothes.size_xxxl) AS nominal_xxxl, ('.$client->table_name.'.size_2 * clothes.size_2) AS nominal_2, ('.$client->table_name.'.size_4 * clothes.size_4) AS nominal_4, ('.$client->table_name.'.size_6 * clothes.size_6) AS nominal_6, ('.$client->table_name.'.size_8 * clothes.size_8) AS nominal_8, ('.$client->table_name.'.size_10 * clothes.size_10) AS nominal_10, ('.$client->table_name.'.size_12 * clothes.size_12) AS nominal_12, ('.$client->table_name.'.size_27 * clothes.size_27) AS nominal_27, ('.$client->table_name.'.size_28 * clothes.size_28) AS nominal_28, ('.$client->table_name.'.size_29 * clothes.size_29) AS nominal_29, ('.$client->table_name.'.size_30 * clothes.size_30) AS nominal_30, ('.$client->table_name.'.size_31 * clothes.size_31) AS nominal_31, ('.$client->table_name.'.size_32 * clothes.size_32) AS nominal_32, ('.$client->table_name.'.size_33 * clothes.size_33) AS nominal_33, ('.$client->table_name.'.size_34 * clothes.size_34) AS nominal_34, ('.$client->table_name.'.size_35 * clothes.size_35) AS nominal_35, ('.$client->table_name.'.size_36 * clothes.size_36) AS nominal_36, ('.$client->table_name.'.size_37 * clothes.size_37) AS nominal_37, ('.$client->table_name.'.size_38 * clothes.size_38) AS nominal_38, ('.$client->table_name.'.size_39 * clothes.size_39) AS nominal_39, ('.$client->table_name.'.size_40 * clothes.size_40) AS nominal_40, ('.$client->table_name.'.size_31 * clothes.size_41) AS nominal_41, ('.$client->table_name.'.size_42 * clothes.size_42) AS nominal_42, ('.$client->table_name.'.other * clothes.other) AS nominal_other, (('.$client->table_name.'.size_s * clothes.size_s) + ('.$client->table_name.'.size_m * clothes.size_m) + ('.$client->table_name.'.size_l * clothes.size_l) + ('.$client->table_name.'.size_xl * clothes.size_xl) + ('.$client->table_name.'.size_xxl * clothes.size_xxl) + ('.$client->table_name.'.size_xxxl * clothes.size_xxxl) + ('.$client->table_name.'.size_2 * clothes.size_2) + ('.$client->table_name.'.size_4 * clothes.size_4) + ('.$client->table_name.'.size_6 * clothes.size_6) + ('.$client->table_name.'.size_8 * clothes.size_8) + ('.$client->table_name.'.size_10 * clothes.size_10) + ('.$client->table_name.'.size_12 * clothes.size_12) + ('.$client->table_name.'.size_27 * clothes.size_27) + ('.$client->table_name.'.size_28 * clothes.size_28) + ('.$client->table_name.'.size_29 * clothes.size_29) + ('.$client->table_name.'.size_30 * clothes.size_30) + ('.$client->table_name.'.size_31 * clothes.size_31) + ('.$client->table_name.'.size_32 * clothes.size_32) + ('.$client->table_name.'.size_33 * clothes.size_33) + ('.$client->table_name.'.size_34 * clothes.size_34) + ('.$client->table_name.'.size_35 * clothes.size_35) + ('.$client->table_name.'.size_36 * clothes.size_36) + ('.$client->table_name.'.size_37 * clothes.size_37) + ('.$client->table_name.'.size_38 * clothes.size_38) + ('.$client->table_name.'.size_39 * clothes.size_39) + ('.$client->table_name.'.size_40 * clothes.size_40) + ('.$client->table_name.'.size_31 * clothes.size_41) + ('.$client->table_name.'.size_42 * clothes.size_42) + ('.$client->table_name.'.other)) AS nominal_total')
                ->leftJoin('clothes', $client->table_name.'.clothes_id', '=', 'clothes.id')
                ->orderBy('clothes.group_article')
                ->get();
                $theData[] = $data;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'success to get data',
                'data' => $theData
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get report',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
