<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Distributor;
use App\TableName;
use App\Transaction;

class TotalPreOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $table_names = TableName::with('Distributor')->get();
        if (!$table_names) {
            return response([
                'message' => 'No Order'
            ], 200);
        }

        try {
        foreach ($table_names as $table_name) {
            $total_preorder = DB::table($table_name->table_name)->select(DB::raw('SUM('.$table_name->table_name.'.size_s + '.$table_name->table_name.'.size_m + '.$table_name->table_name.'.size_l + '.$table_name->table_name.'.size_xl + '.$table_name->table_name.'.size_xxl + '.$table_name->table_name.'.size_xxxl + '.$table_name->table_name.'.size_2 + '.$table_name->table_name.'.size_4 + '.$table_name->table_name.'.size_6 + '.$table_name->table_name.'.size_8 + '.$table_name->table_name.'.size_10 + '.$table_name->table_name.'.size_12) AS total'))->get();

            $distributor['total_preorder'] = $total_preorder[0];
        }

            return response()->json([
            'status' => 'success',
            'message' => 'success get data',
            'data' => $table_names,
        ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get data',
                'error' => $th->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = Transaction::where('distributor_id', $id)->get();

        // $history = DB::table('clothes')->select('clothes.id','clothes.product_code', 'clothes.product_name', 'db_'.$distributor->phone.'.size_s', 'db_'.$distributor->phone.'.size_m', 'db_'.$distributor->phone.'.size_l', 'db_'.$distributor->phone.'.size_xl', 'db_'.$distributor->phone.'.size_xxl', 'db_'.$distributor->phone.'.size_xxxl', 'db_'.$distributor->phone.'.size_2', 'db_'.$distributor->phone.'.size_4', 'db_'.$distributor->phone.'.size_6', 'db_'.$distributor->phone.'.size_8', 'db_'.$distributor->phone.'.size_10', 'db_'.$distributor->phone.'.size_12')->join('db_'.$distributor->phone, 'clothes.id', '=', 'db_'.$distributor->phone.'.clothes_id')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'success get PO distributor',
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'success',
                'message' => 'success get PO distributor',
                'error' => $th->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
