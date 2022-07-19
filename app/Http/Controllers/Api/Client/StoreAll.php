<?php

namespace App\Http\Controllers\Api\Client;

use App\Distributor;
use App\Http\Controllers\Controller;
use App\TableName;
use App\TemporaryStorage;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StoreAll extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke($phone)
    {
        try {
            DB::beginTransaction();

            $user = Distributor::where('phone', $phone)->with('PartnerGroup')->first();
            if (!$user) {
                return response()->json([
                    'status' => 'rejected',
                    'message' => 'number '.$phone.' not registered'
                ], 200);
            }

            $datas = TemporaryStorage::where('distributor_id', $user->id)->with('Clothes')->get();

            if (!TableName::where('distributor_id', $user->id)->first()) {
                $tableName = TableName::create([
                    'distributor_id' => $user->id,
                    'table_name' => "db_$user->phone"
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

                $transaction = Transaction::where('distributor_id', $user->id)->get();
                if (!$transaction) {
                    $transaction_code = Transaction::create([
                        'distributor_id' => $user->id,
                        'transaction_code' => 'PO-'.Carbon::now()->format('HIS').'/'.date('dmy').'/ID/'.$user->id.'/PRE-ORDER/' . 1
                    ]);
                } else {
                    $transaction_code = Transaction::create([
                        'distributor_id' => $user->id,
                        'transaction_code' => 'PO-'.Carbon::now()->format('HIS').'/'.date('dmy').'/ID/'.$user->id.'/PRE-ORDER/'. ($transaction->count() + 1)
                    ]);
                }

                foreach ($datas as $data) {
                    DB::table('total_products')->insert([
                    [
                        'clothes_id' => $data->clothes_id,
                        'veil' => $data->veil,
                        'info' => $data->info,
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
                        'total' => $data->total,
                        'created_at' => now()
                    ]
                    ]);

                $tableName = TableName::where('distributor_id', $user->id)->first();

                    DB::table($tableName->table_name)->insert([
                        [
                            'transaction_code_id' => $transaction_code->id,
                            'clothes_id' => $data->clothes_id,
                            'info' => $data->info,
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
                }

                foreach ($datas as $data) {
                    $data->delete();
                }

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
