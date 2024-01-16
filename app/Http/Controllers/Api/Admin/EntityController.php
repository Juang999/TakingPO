<?php

namespace App\Http\Controllers\Api\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{DB, Schema, Blueprint};
use App\{Distributor, IsActive, TableName, TemporaryStorage, Transaction, Models\Entity};

class EntityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $dataEntities = Entity::select([
                                    'entities.id','entity_name',
                                    DB::raw('CASE WHEN is_actives.name IS NULL THEN false ELSE true END AS status')
                                ])->leftJoin('is_actives', 'is_actives.entity_id', '=', 'entities.id')
                                ->orderBy('entities.id')
                                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $dataEntities,
                'error' => null
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => null,
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($entity)
    {
        $active = IsActive::find(1);

        if (!$active) {
            IsActive::create([
                'name' => $entity
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'status change'
            ]);
        } elseif ($entity == 'DONE') {
            $clients = Distributor::whereIn('id', function ($query) {
                $query->select('distributor_id')
                    ->from('temporary_storages')
                    ->groupBy('distributor_id')
                    ->get();
            })->get();

            foreach ($clients as $client) {
                $theData = TemporaryStorage::where('distributor_id', $client->id)->with('Clothes')->get();

                $tableName = TableName::where('distributor_id', $client->id)->first();

                if ($tableName == NULL) {
                    $tableName = TableName::create([
                        'distributor_id' => $client->id,
                        'table_name' => "db_$client->phone"
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
                        $table->integer('size_27')->default(0);
                        $table->integer('size_28')->default(0);
                        $table->integer('size_29')->default(0);
                        $table->integer('size_30')->default(0);
                        $table->integer('size_31')->default(0);
                        $table->integer('size_32')->default(0);
                        $table->integer('size_33')->default(0);
                        $table->integer('size_34')->default(0);
                        $table->integer('size_35')->default(0);
                        $table->integer('size_36')->default(0);
                        $table->integer('size_37')->default(0);
                        $table->integer('size_38')->default(0);
                        $table->integer('size_39')->default(0);
                        $table->integer('size_40')->default(0);
                        $table->integer('size_41')->default(0);
                        $table->integer('size_42')->default(0);
                        $table->integer('other')->default(0);
                        $table->integer('total')->default(0);
                        $table->timestamps();
                    });
                }

                $transaction = Transaction::where('distributor_id', $client->id)->get();
                if (!$transaction) {
                    $transaction_code = Transaction::create([
                        'distributor_id' => $client->id,
                        'transaction_code' => 'PO-'.Carbon::now()->format('HIS').'/'.date('dmy').'/ID/'.$client->id.'/PRE-ORDER/' . 1
                    ]);
                } else {
                    $transaction_code = Transaction::create([
                        'distributor_id' => $client->id,
                        'transaction_code' => 'PO-'.Carbon::now()->format('HIS').'/'.date('dmy').'/ID/'.$client->id.'/PRE-ORDER/'. ($transaction->count() + 1)
                    ]);
                }

                foreach ($theData as $data) {
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
                            'size_27' => $data->size_27,
                            'size_28' => $data->size_28,
                            'size_29' => $data->size_29,
                            'size_30' => $data->size_30,
                            'size_31' => $data->size_31,
                            'size_32' => $data->size_32,
                            'size_33' => $data->size_33,
                            'size_34' => $data->size_34,
                            'size_35' => $data->size_35,
                            'size_36' => $data->size_36,
                            'size_37' => $data->size_37,
                            'size_38' => $data->size_38,
                            'size_39' => $data->size_39,
                            'size_40' => $data->size_40,
                            'size_41' => $data->size_41,
                            'size_42' => $data->size_42,
                            'other' => $data->other,
                            'total' => $data->total,
                            'created_at' => Carbon::translateTimeString(now())
                        ]
                    ]);


                        $data->delete();
                }
            }

            $active->update([
                'name' => $entity
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'status change'
            ], 200);
        } else {
            $active->update([
                'name' => $entity
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'status change'
            ]);
        }
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
