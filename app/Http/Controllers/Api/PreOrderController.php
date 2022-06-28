<?php

namespace App\Http\Controllers\Api;

use App\Clothes;
use App\Distributor;
use App\Http\Controllers\Controller;
use App\Http\Requests\PreOrderRequest;
use App\IsActive;
use App\TableName;
use App\TemporaryStorage;
use App\Transaction;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PreOrderController extends Controller
{
    public function getClothes($phone)
    {
        $distributor1 = Distributor::where('phone', $phone)->first();
        if (!$distributor1) {
            return response()->json([
                'status' => 'failed',
                'message' => 'number '.$phone.' not registered!'
            ], 400);
        }

        $entity = IsActive::find(1);

        if ($entity) {
            $clothess = Clothes::where([
                'entity_name' => $entity->name,
                'is_active' => 1
                ])->with('Type')->paginate(5);
        } else {
            $clothess = Clothes::where([
                'entity_name' => 'MUTIF',
                'is_active' => 1
                ])->with('Type')->paginate(5);
        }

        if ($entity->name == 'DONE') {
            $data = TemporaryStorage::where('distributor_id', $distributor1->id)
                    ->with('Distributor', 'Clothes')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'success get data',
                'data' => $data
            ], 200);
        }

        foreach ($clothess as $clothes) {
            $clothes['combo'] = explode(",", $clothes->combo);
            $clothes['size_2'] = explode(",", $clothes->size_2);
            $clothes['size_4'] = explode(",", $clothes->size_4);
            $clothes['size_6'] = explode(",", $clothes->size_6);
            $clothes['size_8'] = explode(",", $clothes->size_8);
            $clothes['size_10'] = explode(",", $clothes->size_10);
            $clothes['size_12'] = explode(",", $clothes->size_12);
        }

        $distributor = $distributor1->name;

        return response()->json([
            'status' => 'success',
            'message' => 'success get clothes',
            'data' => compact('distributor', 'clothess')
        ], 200);
    }

    public function storeClothes(PreOrderRequest $request, $phone)
    {
        try {
            $distributor = Distributor::where('phone', $phone)->first();

            if(!$distributor) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'error get data',
                    'data' => 'Distributor not found'
                ], 404);
            }

            $data = TemporaryStorage::create([
                'distributor_id' => $distributor->id,
                'clothes_id' => $request->clothes_id,
                'info' => $request->info,
                'veil' => $request->veil,
                'size_s' => $request->size_s,
                'size_m' => $request->size_m,
                'size_l' => $request->size_l,
                'size_xl' => $request->size_xl,
                'size_xxl' => $request->size_xxl,
                'size_xxxl' => $request->size_xxxl,
                'size_2' => $request->size_2,
                'size_4' => $request->size_4,
                'size_6' => $request->size_6,
                'size_8' => $request->size_8,
                'size_10' => $request->size_10,
                'size_12' => $request->size_12,
                'total' => $request->total
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'success input data',
                'data' => $data,
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to create pre-order',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function storeAllClothes($phone)
    {
        try {
            $distributor = Distributor::where('phone', $phone)->first();

            $data = TemporaryStorage::where('distributor_id', $distributor->id)->with('Clothes')->get();

            if (!TableName::where('distributor_id', $distributor->id)->first()) {
                $tableName = TableName::create([
                    'distributor_id' => $distributor->id,
                    'table_name' => "db_$distributor->phone"
                ]);

                Schema::create($tableName->table_name, function (Blueprint $table) {
                    $table->id();
                    $table->integer('transaction_code_id')->constrained('transactions');
                    $table->integer('clothes_id')->constrained('clothes');
                    $table->text('info');
                    $table->boolean('veil')->default(0);
                    $table->integer('size_s')->default(0);
                    $table->integer('size_m')->default(0);
                    $table->integer('size_l')->default(0);
                    $table->integer('size_xl')->default(0);
                    $table->integer('size_xxl')->default(0);
                    $table->integer('size_xxxl')->default(0);
                    $table->integer('size_2')->default(0);
                    $table->integer('size_4')->default(0);
                    $table->integer('size_6')->default(0);
                    $table->integer('size_8')->default(0);
                    $table->integer('size_10')->default(0);
                    $table->integer('size_12')->default(0);
                    $table->integer('total')->default(0);
                    $table->timestamps();
                });
            }

            DB::beginTransaction();
                $transaction = Transaction::where('distributor_id', $distributor->id)->get();
                if (!$transaction) {
                    $transaction_code = Transaction::create([
                        'distributor_id' => $distributor->id,
                        'transaction_code' => 'PO-'.time().'/'.date('dmy').'/NUMBER/'.$distributor->id.'/PRE-ORDER/' . 1
                    ]);
                } else {
                    $transaction_code = Transaction::create([
                        'distributor_id' => $distributor->id,
                        'transaction_code' => 'PO-'.time().'/'.date('dmy').'/DISTRIBUTOR/'.$distributor->id.'/PRE-ORDER/'. ($transaction->count() + 1)
                    ]);
                }

                DB::table('total_products')->insert([
                    'clothes_id' => $data->clothes_id,
                    'size_s' => $data->size_s,
                    'size_m' => $data->size_m,
                    'size_l' => $data->size_l,
                    'size_xl' => $data->size_xl,
                    'size_xxl' => $data->size_xxl,
                    'size_xxxl' => $data->size_xxxl,
                    'size_2' => $data->size_2,
                    'size_4' => $data->size_4,
                    'size_6' => $data->size_6,
                    'size_8' => $data->size_8,
                    'size_10' => $data->size_10,
                    'size_12' => $data->size_12,
                    'total' => $data->total
                ]);

                $tableName = TableName::where('distributor_id', $distributor->id)->first();

                DB::table($tableName->table_name)->insert([
                    [
                        'transaction_code_id' => $transaction_code->id,
                        'clothes_id' => $data->clothes_id,
                        'size_s' => $data->size_s,
                        'size_m' => $data->size_m,
                        'size_l' => $data->size_l,
                        'size_xl' => $data->size_xl,
                        'size_xxl' => $data->size_xxl,
                        'size_xxxl' => $data->size_xxxl,
                        'size_2' => $data->size_2,
                        'size_4' => $data->size_4,
                        'size_6' => $data->size_6,
                        'size_8' => $data->size_8,
                        'size_10' => $data->size_10,
                        'size_12' => $data->size_12,
                        'total' => $data->total
                    ]
                ]);

            $data->destroy();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'success create pre-order'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'failed',
                'message' => 'failed to store data',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
