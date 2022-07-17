<?php

namespace App\Http\Controllers\Api;

use App\Agent;
use App\BufferProduct;
use App\Clothes;
use App\Distributor;
use App\Http\Controllers\Controller;
use App\Http\Requests\PreOrderRequest;
use App\Http\Requests\RegisterRequest;
use App\IsActive;
use App\PartnerGroup;
use App\Size;
use App\TableName;
use App\TemporaryStorage;
use App\Transaction;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class PreOrderController extends Controller
{
    public function getClothes($phone)
    {
        $user  = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed to get data',
                'message' => 'phone '.$phone.' not registered'
            ], 400);
        }

        $clothess = Clothes::orderBy('entity_name')->with('Type', 'Image', 'BufferProduct.Size')->get();

        if ($clothess) {
            foreach ($clothess as $clothes) {
                if ($clothes->combo != '-') {
                    $clothes['combo'] = explode(",", $clothes->combo);
                }
                $clothes['size_2'] = explode(",", $clothes->size_2);
                $clothes['size_4'] = explode(",", $clothes->size_4);
                $clothes['size_6'] = explode(",", $clothes->size_6);
                $clothes['size_8'] = explode(",", $clothes->size_8);
                $clothes['size_10'] = explode(",", $clothes->size_10);
                $clothes['size_12'] = explode(",", $clothes->size_12);
            }
        }
        return response()->json([
        'status' => 'success',
            'message' => 'success get data',
            'data' => $clothess
        ], 200);
    }

    public function getDetailClothes($phone, $id)
    {
        $user = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'number '.$phone.'not registerd'
            ], 400);
        }

        try {
            $clothes = Clothes::where('id', $id)->with('Type', 'Image', 'BufferProduct')->first();

            return response()->json([
                'status' => 'success',
                'message' => 'success to get detail clothes',
                'data' => $clothes
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get data',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function storeClothes(PreOrderRequest $request, $phone)
    {
        try {
            $user = Distributor::where('phone', $phone)->with('PartnerGroup')->first();

            if (!$user) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'number '.$phone.' not registered'
                ], 400);
            }

            DB::beginTransaction();

            $size_s = Size::where('size', 's')->first();
            $BufferStock_s = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_s->id
            ])->first();


            if ($BufferStock_s) {
                if ($BufferStock_s->qty_buffer != 0) {
                    if ($BufferStock_s->qty_avaliable != 0 && $BufferStock_s->qty_avaliable > $request->size_s) {
                        $qty_avaliable = $BufferStock_s->qty_avaliable - $request->size_s;
                        $qty_process = $BufferStock_s->qty_process + $request->size_s;

                        $BufferStock_s->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);

                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'to many request'
                        ], 400);
                    }

                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'not ready'
                    ], 400);
                }
            }

            $size_m = Size::where('size', 'm')->first();
            $BufferStock_m = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_m->id
            ])->first();

            if ($BufferStock_m) {
                if ($BufferStock_m->qty_buffer != 0) {
                    if ($BufferStock_m->qty_avaliable != 0 && $BufferStock_m->qty_avaliable > $request->size_m) {
                        $qty_avaliable = $BufferStock_m->qty_avaliable - $request->size_m;
                        $qty_process = $BufferStock_m->qty_process + $request->size_m;

                        $BufferStock_m->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'sold out'
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'not ready '
                    ], 400);
                }
            }

            $size_l = Size::where('size', 'l')->first();
            $BufferStock_l = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_l->id
            ])->first();

            if ($BufferStock_l) {
                if ($BufferStock_l->qty_buffer != 0) {
                    if ($BufferStock_l->qty_avaliable != 0 && $BufferStock_l->qty_avaliable > $request->size_l) {
                        $qty_avaliable = $BufferStock_l->qty_avaliable - $request->size_l;
                        $qty_process = $BufferStock_l->qty_process + $request->size_l;

                        $BufferStock_l->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'failed to update data',
                            'message' => 'sold out'
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'failed to update data',
                        'message' => 'not ready'
                    ], 400);
                }
            }

            $size_xl = Size::where('size', 'xl')->first();
            $BufferStock_xl = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_xl->id
            ])->first();

            if ($BufferStock_xl) {
                if ($BufferStock_xl->qty_buffer != 0) {
                    if ($BufferStock_xl->qty_avaliable != 0 && $BufferStock_xl->qty_avaliable > $request->size_xl) {
                        $qty_avaliable = $BufferStock_xl->qty_avaliable - $request->size_xl;
                        $qty_process = $BufferStock_xl->qty_process + $request->size_xl;

                        $BufferStock_xl->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'failed to update data',
                            'message' => 'sold out'
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'failed to update data',
                        'message' => 'not ready'
                    ], 400);
                }
            }

            $size_xxl = Size::where('size', 'xxl')->first();
            $BufferStock_xxl = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_xxl->id
            ])->first();

            if ($BufferStock_xxl) {
                if ($BufferStock_xxl->qty_buffer != 0) {
                    if ($BufferStock_xxl->qty_avaliable != 0 && $BufferStock_xxl->qty_avaliable > $request->size_xxl) {
                        $qty_avaliable = $BufferStock_xxl->qty_avaliable - $request->size_xxl;
                        $qty_process = $BufferStock_xxl->qty_process + $request->size_xxl;

                        $BufferStock_xxl->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'failed to update data',
                            'message' => 'sold out'
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'failed to update data',
                        'message' => 'not ready'
                    ], 400);
                }
            }

            $size_xxxl = Size::where('size', 'xxxl')->first();
            $BufferStock_xxxl = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_xxxl->id
            ])->first();

            if ($BufferStock_xxxl) {
                if ($BufferStock_xxxl->qty_buffer != 0) {
                    if ($BufferStock_xxxl->qty_avaliable != 0 && $BufferStock_xxxl->qty_avaliable > $request->size_xxxl) {
                        $qty_avaliable = $BufferStock_xxxl->qty_avaliable - $request->size_xxxl;
                        $qty_process = $BufferStock_xxxl->qty_process + $request->size_xxxl;

                        $BufferStock_xxxl->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'failed to update data',
                            'message' => 'sold out'
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'failed to update data',
                        'message' => 'not ready'
                    ], 400);
                }
            }

            $size_2 = Size::where('size', '2')->first();
            $BufferStock_2 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_2->id
            ])->first();

            if ($BufferStock_2) {
                if ($BufferStock_2->qty_buffer != 0) {
                    if ($BufferStock_2->qty_avaliable != 0 && $BufferStock_2->qty_avaliable > $request->size_2) {
                        $qty_avaliable = $BufferStock_2->qty_avaliable - $request->size_2;
                        $qty_process = $BufferStock_2->qty_process + $request->size_2;

                        $BufferStock_2->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'failed to update data',
                            'message' => 'sold out'
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'failed to update data',
                        'message' => 'not ready'
                    ], 400);
                }
            }

            $size_4 = Size::where('size', '4')->first();
            $BufferStock_4 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_4->id
            ])->first();

            if ($BufferStock_4) {
                if ($BufferStock_4->qty_buffer != 0) {
                    if ($BufferStock_4->qty_avaliable != 0 && $BufferStock_4->qty_avaliable > $request->size_4) {
                        $qty_avaliable = $BufferStock_4->qty_avaliable - $request->size_4;
                        $qty_process = $BufferStock_4->qty_process + $request->size_4;

                        $BufferStock_4->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'failed to update data',
                            'message' => 'sold out'
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'failed to update data',
                        'message' => 'not ready'
                    ], 400);
                }
            }

            $size_6 = Size::where('size', '6')->first();
            $BufferStock_6 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_6->id
            ])->first();

            if ($BufferStock_6) {
                if ($BufferStock_6->qty_buffer != 0) {
                    if ($BufferStock_6->qty_avaliable != 0 && $BufferStock_6->qty_avaliable > $request->size_6) {
                        $qty_avaliable = $BufferStock_6->qty_avaliable - $request->size_6;
                        $qty_process = $BufferStock_6->qty_process + $request->size_6;

                        $BufferStock_6->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'failed to update data',
                            'message' => 'sold out'
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'failed to update data',
                        'message' => 'not ready'
                    ], 400);
                }
            }

            $size_8 = Size::where('size', '8')->first();
            $BufferStock_8 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_8->id
            ])->first();

            if ($BufferStock_8) {
                if ($BufferStock_8->qty_buffer != 0) {
                    if ($BufferStock_8->qty_avaliable != 0 && $BufferStock_8->qty_avaliable > $request->size_8) {
                        $qty_avaliable = $BufferStock_8->qty_avaliable - $request->size_8;
                        $qty_process = $BufferStock_8->qty_process + $request->size_8;

                        $BufferStock_8->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'failed to update data',
                            'message' => 'sold out'
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'failed to update data',
                        'message' => 'not ready'
                    ], 400);
                }
            }

            $size_10 = Size::where('size', '10')->first();
            $BufferStock_10 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_10->id
            ])->first();

            if ($BufferStock_10) {
                if ($BufferStock_10->qty_buffer != 0) {
                    if ($BufferStock_10->qty_avaliable != 0 && $BufferStock_10->qty_avaliable > $request->size_10) {
                        $qty_avaliable = $BufferStock_10->qty_avaliable - $request->size_10;
                        $qty_process = $BufferStock_10->qty_process + $request->size_10;

                        $BufferStock_10->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'failed to update data',
                            'message' => 'sold out'
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'failed to update data',
                        'message' => 'not ready'
                    ], 400);
                }
            }

            $size_12 = Size::where('size', '12')->first();
            $BufferStock_12 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_12->id
            ])->first();

            if ($BufferStock_12) {
                if ($BufferStock_12->qty_buffer != 0) {
                    if ($BufferStock_12->qty_avaliable != 0 && $BufferStock_12->qty_avaliable > $request->size_12) {
                        $qty_avaliable = $BufferStock_12->qty_avaliable - $request->size_12;
                        $qty_process = $BufferStock_12->qty_process + $request->size_12;

                        $BufferStock_12->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'failed to update data',
                            'message' => 'sold out'
                        ], 400);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'failed to update data',
                        'message' => 'not ready'
                    ], 400);
                }
            }

            $data = TemporaryStorage::create([
                'distributor_id' => $user->id,
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

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'success input data',
                'data' => $data,
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();

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
            DB::beginTransaction();

            $user = Distributor::where('phone', $phone)->with('PartnerGroup')->first();
            if (!$user) {
                $user = Agent::where('phone', $phone)->with('PartnerGroup')->first();
                if (!$user) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'number '.$phone.' not registered'
                    ], 400);
                }
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
                        'total' => $data->total
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
