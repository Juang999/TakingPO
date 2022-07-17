<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\TableName;
use App\Transaction;
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


            $preorders = DB::table('clothes')->select('clothes.id', 'clothes.entity_name','clothes.article_name', 'clothes.group_article', 'clothes.type_id', 'clothes.size_s AS Harga_size_s', 'clothes.size_m AS Harga_size_m', 'clothes.size_l AS Harga_size_l', 'clothes.size_xl AS Harga_size_xl', 'clothes.size_xxl AS Harga_size_xxl', 'clothes.size_xxxl AS Harga_size_xxxl', 'clothes.size_2 AS Harga_size_2', 'clothes.size_4 AS Harga_size_4', 'clothes.size_6 AS Harga_size_6', 'clothes.size_8 AS Harga_size_8', 'clothes.size_10 AS Harga_size_10', 'clothes.size_12 AS Harga_size_12', $table_name->table_name.'.info', $table_name->table_name.'.veil', $table_name->table_name.'.size_s', $table_name->table_name.'.size_m', $table_name->table_name.'.size_l', $table_name->table_name.'.size_xl', $table_name->table_name.'.size_xxl', $table_name->table_name.'.size_xxxl', $table_name->table_name.'.size_2', $table_name->table_name.'.size_4', $table_name->table_name.'.size_6', $table_name->table_name.'.size_8', $table_name->table_name.'.size_10', $table_name->table_name.'.size_12', $table_name->table_name.'.total')->join($table_name->table_name, 'clothes.id', '=', $table_name->table_name.'.clothes_id')->where('transaction_code_id', '=', $transaction_code->id)->get();

            foreach ($preorders as $preorder) {
                $preorder->Harga_size_2 = explode(',', $preorder->Harga_size_2);
                $preorder->Harga_size_4 = explode(',', $preorder->Harga_size_4);
                $preorder->Harga_size_6 = explode(',', $preorder->Harga_size_6);
                $preorder->Harga_size_8 = explode(',', $preorder->Harga_size_8);
                $preorder->Harga_size_10 = explode(',', $preorder->Harga_size_10);
                $preorder->Harga_size_12 = explode(',', $preorder->Harga_size_12);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'success to get  data',
                'data' => compact('transaction_code', 'preorders')
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
