<?php

namespace App\Http\Controllers\Api;

use App\Distributor;
use App\Http\Controllers\Controller;
use App\Http\Requests\DistributorRequest;
use App\TableName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DistributorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'success get data distributor',
                'data' => Distributor::get()
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed get data distributor',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DistributorRequest $request)
    {
        try {
            Distributor::create([
                'status' => 'success',
                'message' => 'distributor registered',
                'data' => Distributor::create($request->all())
            ]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Distributor $distributor)
    {
        try {
            return response()->json([
                'status' => 'success',
                'message' => 'success get data distributor',
                'data' => $distributor
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'success',
                'message' => 'success get data distributor',
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
    public function update(DistributorRequest $request, Distributor $distributor)
    {
        try {
            $table_name = TableName::where('user_id', $distributor->id)->first();

            if ($table_name->exists()) {
                Schema::rename($table_name->table_name, 'db_'.$request->phone);
                $table_name->update([
                    'table_name' => 'db_'.$request->phone
                ]);
            }

            $distributor->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'success update distributor',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to update data',
                'eror' => $th->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Distributor $distributor)
    {
        try {
            $distributor->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'success delete data'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to delete data',
                'error' => $th->getMessage()
            ]);
        }
    }
}
