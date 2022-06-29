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

            $table_name = TableName::where('distributor_id', $transaction_code->distributor_id)->first();

            $preorder = DB::table('clothes')->select('clothes.id', 'clothes.entity_name','clothes.article_name', 'clothes.group_article', 'clothes.type_id', $table_name->table_name.'.info', $table_name->table_name.'.veil', $table_name->table_name.'.size_s', $table_name->table_name.'.size_m', $table_name->table_name.'.size_l', $table_name->table_name.'.size_xl', $table_name->table_name.'.size_xxl', $table_name->table_name.'.size_xxxl', $table_name->table_name.'.size_2', $table_name->table_name.'.size_4', $table_name->table_name.'.size_6', $table_name->table_name.'.size_8', $table_name->table_name.'.size_10', $table_name->table_name.'.size_12', $table_name->table_name.'.total')->join($table_name->table_name, 'clothes.id', '=', $table_name->table_name.'.clothes_id')->where('transaction_code_id', '=', $transaction_code->id)->get();

            return response()->json([
                'status' => 'success',
                'message' => 'success to get  data',
                'data' => compact('transaction_code', 'preorder')
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
