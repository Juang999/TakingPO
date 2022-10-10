<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\{Clothes, BufferProduct, Distributor, TableName};
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\Admin\LoopFunction;

class TotalController extends Controller
{
    public function totalOrder()
    {
        try {
            $theData = Clothes::with('BufferProduct.Size')->get();

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
            $theData = TableName::whereIn('distributor_id', function ($query) {
                $query->select('id')
                    ->from('distributors')
                    ->get();
            })->with('Distributor')->get();

            $loopFunction = new LoopFunction();

            foreach ($theData as $data) {
                    $data['mutif'] = $loopFunction->totalClient($data->table_name, 'MUTIF');
                    $data['damoza'] = $loopFunction->totalClient($data->table_name, 'DAMOZA');
                    $data['upmore'] = $loopFunction->totalClient($data->table_name, 'UPMORE');

                $data['total'] = $data['mutif'] + $data['damoza'] + $data['upmore'];
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
}
