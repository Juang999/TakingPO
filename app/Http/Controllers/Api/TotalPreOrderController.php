<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\{Distributor, TableName, Transaction};

class TotalPreOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $table_names = TableName::whereIn('distributor_id', function ($query) {
            $query->select('id')
                ->from('distributors')
                ->where('partner_group_id', 1)
                ->get();
        })->with('Distributor')->get();
        if (!$table_names) {
            return response([
                'message' => 'No Order'
            ], 200);
        }

        try {
        foreach ($table_names as $table_name) {
            // $total_preorder = DB::table($table_name->table_name)->select(DB::raw('SUM('.$table_name->table_name.'.size_s + '.$table_name->table_name.'.size_m + '.$table_name->table_name.'.size_l + '.$table_name->table_name.'.size_xl + '.$table_name->table_name.'.size_xxl + '.$table_name->table_name.'.size_xxxl + '.$table_name->table_name.'.size_2 + '.$table_name->table_name.'.size_4 + '.$table_name->table_name.'.size_6 + '.$table_name->table_name.'.size_8 + '.$table_name->table_name.'.size_10 + '.$table_name->table_name.'.size_12) AS total'))->get();
            // $table_name['total_preorder'] = $total_preorder[0]->total;

            $table_name['total_preorder'] = Transaction::where('distributor_id', $table_name->Distributor->id)->count();
            $agent = Distributor::where('distributor_id', $table_name->distributor_id)->get();
                if ($agent->count() >= 1) {
                    $table_name['agent'] = $agent;
                }

            $table_name->Distributor->makeHidden(['image', 'address']);
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

            return response()->json([
                'status' => 'success',
                'message' => 'success get PO distributor',
                'data' => $data
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'success',
                'message' => 'success get PO distributor',
                'error' => $th->getMessage()
            ], 400);
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
