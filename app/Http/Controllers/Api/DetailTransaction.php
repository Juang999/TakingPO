<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clothes;
use App\{Transaction, PartnerGroup, TableName};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailTransaction extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke($id)
    {
        try {
            $transaction_code = Transaction::find($id);

            $transaction_code->makeHidden('distributor_id');

            // return response()->json($transaction_code);
            $table_name = TableName::where('distributor_id', $transaction_code->distributor_id)->first();

            $product = DB::table($table_name->table_name)
                        ->where('transaction_code_id', $transaction_code->id)
                        ->get(['id', 'transaction_code_id', 'clothes_id', 'info', 'total', 'created_at', 'updated_at']);

            $products = json_decode($product, true);

            for ($i=0; $i < count($products); $i++) {
                $clothes = Clothes::find($products[$i]['clothes_id']);

                if ($clothes->type_id == 1) {
                    $products[$i]['detail_product'] = DB::table('clothes')->select('clothes.id', 'clothes.entity_name','clothes.article_name', 'clothes.group_article', 'clothes.type_id', 'clothes.size_2 AS Harga_size_2', 'clothes.size_4 AS Harga_size_4', 'clothes.size_6 AS Harga_size_6', 'clothes.size_8 AS Harga_size_8', 'clothes.size_10 AS Harga_size_10', 'clothes.size_12 AS Harga_size_12', $table_name->table_name.'.info', $table_name->table_name.'.veil',$table_name->table_name.'.size_2', $table_name->table_name.'.size_4', $table_name->table_name.'.size_6', $table_name->table_name.'.size_8', $table_name->table_name.'.size_10', $table_name->table_name.'.size_12', $table_name->table_name.'.total')->join($table_name->table_name, 'clothes.id', '=', $table_name->table_name.'.clothes_id')->where([
                        ['transaction_code_id', '=', $transaction_code->id],
                        ['clothes_id', '=', $clothes->id]
                    ])->first();
                } elseif ($clothes->type_id == 2) {
                    $products[$i]['detail_product'] = DB::table('clothes')->select('clothes.id', 'clothes.entity_name','clothes.article_name', 'clothes.group_article', 'clothes.type_id', 'clothes.size_s AS Harga_size_s', 'clothes.size_m AS Harga_size_m', 'clothes.size_l AS Harga_size_l', 'clothes.size_xl AS Harga_size_xl', 'clothes.size_xxl AS Harga_size_xxl', 'clothes.size_xxxl AS Harga_size_xxxl', $table_name->table_name.'.info', $table_name->table_name.'.veil',$table_name->table_name.'.size_s', $table_name->table_name.'.size_m', $table_name->table_name.'.size_l', $table_name->table_name.'.size_xl', $table_name->table_name.'.size_xxl', $table_name->table_name.'.size_xxxl', $table_name->table_name.'.total')->join($table_name->table_name, 'clothes.id', '=', $table_name->table_name.'.clothes_id')->where([
                        ['transaction_code_id', '=', $transaction_code->id],
                        ['clothes_id', '=', $clothes->id]
                    ])->first();
                } elseif ($clothes->type_id == 3) {
                    $products[$i]['detail_product'] = DB::table('clothes')->select('clothes.id', 'clothes.entity_name','clothes.article_name', 'clothes.group_article', 'clothes.type_id', 'clothes.size_2 AS Harga_size_2', 'clothes.size_4 AS Harga_size_4', 'clothes.size_6 AS Harga_size_6', 'clothes.size_8 AS Harga_size_8', 'clothes.size_10 AS Harga_size_10', 'clothes.size_12 AS Harga_size_12', $table_name->table_name.'.info', $table_name->table_name.'.veil',$table_name->table_name.'.size_2', $table_name->table_name.'.size_4', $table_name->table_name.'.size_6', $table_name->table_name.'.size_8', $table_name->table_name.'.size_10', $table_name->table_name.'.size_12', $table_name->table_name.'.total')->join($table_name->table_name, 'clothes.id', '=', $table_name->table_name.'.clothes_id')->where([
                        ['transaction_code_id', '=', $transaction_code->id],
                        ['clothes_id', '=', $clothes->id]
                    ])->first();
                } elseif ($clothes->type_id == 4) {
                    $products[$i]['detail_product'] = DB::table('clothes')->select('clothes.id', 'clothes.entity_name','clothes.article_name', 'clothes.group_article', 'clothes.type_id', 'clothes.size_27 AS Harga_size_27', 'clothes.size_28 AS Harga_size_28', 'clothes.size_29 AS Harga_size_29', 'clothes.size_29 AS Harga_size_29', 'clothes.size_30 AS Harga_size_30', 'clothes.size_31 AS Harga_size_31', 'clothes.size_32 AS Harga_size_32', 'clothes.size_33 AS Harga_size_33', 'clothes.size_34 AS Harga_size_34', 'clothes.size_35 AS Harga_size_35', 'clothes.size_36 AS Harga_size_36', 'clothes.size_37 AS Harga_size_37', 'clothes.size_38 AS Harga_size_38', 'clothes.size_39 AS Harga_size_39', 'clothes.size_40 AS Harga_size_40', 'clothes.size_41 AS Harga_size_41', 'clothes.size_42 AS Harga_size_42', $table_name->table_name.'.info', $table_name->table_name.'.veil', $table_name->table_name.'.size_27', $table_name->table_name.'.size_28', $table_name->table_name.'.size_29', $table_name->table_name.'.size_30', $table_name->table_name.'.size_31', $table_name->table_name.'.size_32', $table_name->table_name.'.size_33', $table_name->table_name.'.size_34', $table_name->table_name.'.size_35', $table_name->table_name.'.size_36', $table_name->table_name.'.size_37', $table_name->table_name.'.size_38', $table_name->table_name.'.size_39', $table_name->table_name.'.size_40', $table_name->table_name.'.size_41', $table_name->table_name.'.size_42', $table_name->table_name.'.total')->join($table_name->table_name, 'clothes.id', '=', $table_name->table_name.'.clothes_id')->where([
                        ['transaction_code_id', '=', $transaction_code->id],
                        ['clothes_id', '=', $clothes->id]
                    ])->first();
                } elseif ($clothes->type_id == 5) {
                    $products[$i]['detail_product'] = DB::table('clothes')->select('clothes.id', 'clothes.entity_name','clothes.article_name', 'clothes.group_article', 'clothes.type_id', 'clothes.other AS Harga_lainnya', $table_name->table_name.'.info', $table_name->table_name.'.veil', $table_name->table_name.'.other', $table_name->table_name.'.total')->join($table_name->table_name, 'clothes.id', '=', $table_name->table_name.'.clothes_id')->where([
                        ['transaction_code_id', '=', $transaction_code->id],
                        ['clothes_id', '=', $clothes->id]
                    ])->first();
                }
            }

            $discount = PartnerGroup::where(function ($query) use ($transaction_code) {
                $query->select('partner_group_id')
                    ->from('distributors')
                    ->where('id', $transaction_code->distributor_id)
                    ->first();
            })->first(['discount']);

            return response()->json([
                'transaction_code' => $transaction_code,
                'data' => $products,
                'discount'  => $discount->discount
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get data',
                'data' => $th->getMessage()
            ]);
        }
    }
}
