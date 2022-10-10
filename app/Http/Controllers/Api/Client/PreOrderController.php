<?php

namespace App\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\PreOrderRequest;
use App\Http\Controllers\Api\Client\Order;
use App\{IsActive, MutifStoreMaster, TemporaryStorage, Distributor, Clothes, BufferProduct};

class PreOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($phone)
    {
        $user  = Distributor::where('phone', $phone)->with('PartnerGroup')->first();


        $activate = IsActive::find(1);
        if ($activate->name == 'DONE') {
            return response()->json([
                'status' => 'closed',
                'message' => 'ISMAIL BURIQUE',
                'data' => []
            ], 300);
        }

        if (!$user) {
            return response()->json([
                'status' => 'failed to get data',
                'message' => 'phone '.$phone.' not registered'
            ], 400);
        }

        $MS = MutifStoreMaster::where('distributor_id', $user->id)->count();

        if ($MS == 0) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Akun ini belum memiliki MS'
            ], 400);
        }

        $clothess = Clothes::where('is_active', 1)
        ->with('Type', 'Image', 'BufferProduct.Size')
        ->orderBy('group_article')
        ->get();

        if ($clothess) {
            foreach ($clothess as $clothes) {
                if ($clothes->combo != '-') {
                    $clothes['combo'] = explode(", ", $clothes->combo);
                }
                if ($clothes['size_2'] > 0) {
                    $clothes['size_2'] = explode(",", $clothes->size_2);
                    $clothes['size_4'] = explode(",", $clothes->size_4);
                    $clothes['size_6'] = explode(",", $clothes->size_6);
                    $clothes['size_8'] = explode(",", $clothes->size_8);
                    $clothes['size_10'] = explode(",", $clothes->size_10);
                    $clothes['size_12'] = explode(",", $clothes->size_12);
                }
            }
        }
        return response()->json([
        'status' => 'success',
            'message' => 'success get data',
            'discount' => $user->PartnerGroup->discount,
            'data' => $clothess
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PreOrderRequest $request, $phone)
    {
        $user = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'number '.$phone.' not registered'
            ], 400);
        }

        try {

        DB::beginTransaction();

        $createOrder = new Order();

        $createOrder->createOrder($request->size_s, $request->clothes_id, 'S');
        $createOrder->createOrder($request->size_m, $request->clothes_id, 'M');
        $createOrder->createOrder($request->size_l, $request->clothes_id, 'L');
        $createOrder->createOrder($request->size_xl, $request->clothes_id, 'XL');
        $createOrder->createOrder($request->size_xxl, $request->clothes_id, 'XXL');
        $createOrder->createOrder($request->size_xxxl, $request->clothes_id, 'xxxl');
        $createOrder->createOrder($request->size_2, $request->clothes_id, '2');
        $createOrder->createOrder($request->size_4, $request->clothes_id, '4');
        $createOrder->createOrder($request->size_6, $request->clothes_id, '6');
        $createOrder->createOrder($request->size_8, $request->clothes_id, '8');
        $createOrder->createOrder($request->size_10, $request->clothes_id, '10');
        $createOrder->createOrder($request->size_12, $request->clothes_id, '12');
        $createOrder->createOrder($request->size_27, $request->clothes_id, '27');
        $createOrder->createOrder($request->size_28, $request->clothes_id, '28');
        $createOrder->createOrder($request->size_29, $request->clothes_id, '29');
        $createOrder->createOrder($request->size_30, $request->clothes_id, '30');
        $createOrder->createOrder($request->size_31, $request->clothes_id, '31');
        $createOrder->createOrder($request->size_32, $request->clothes_id, '32');
        $createOrder->createOrder($request->size_33, $request->clothes_id, '33');
        $createOrder->createOrder($request->size_34, $request->clothes_id, '34');
        $createOrder->createOrder($request->size_35, $request->clothes_id, '35');
        $createOrder->createOrder($request->size_36, $request->clothes_id, '36');
        $createOrder->createOrder($request->size_37, $request->clothes_id, '37');
        $createOrder->createOrder($request->size_38, $request->clothes_id, '38');
        $createOrder->createOrder($request->size_39, $request->clothes_id, '39');
        $createOrder->createOrder($request->size_40, $request->clothes_id, '40');
        $createOrder->createOrder($request->size_41, $request->clothes_id, '41');        
        $createOrder->createOrder($request->size_42, $request->clothes_id, '42');

            $BufferStock_other = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
            ])->first();

            if ($BufferStock_other) {
                if ($BufferStock_other->qty_buffer != 0) {
                    if ($BufferStock_other->qty_avaliable >= $request->other) {
                        $qty_avaliable = $BufferStock_other->qty_avaliable - $request->other;
                        $qty_process = $BufferStock_other->qty_process + $request->other;

                        $BufferStock_other->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'failed to update data',
                            'message' => 'size 12 sold out'
                        ], 400);
                    }
                } elseif ($BufferStock_other->qty_buffer == 0) {
                    $qty_process = $BufferStock_other->qty_process + $request->other;

                    $BufferStock_other->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $cart = TemporaryStorage::create([
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
                'size_27' => $request->size_27,
                'size_28' => $request->size_28,
                'size_29' => $request->size_29,
                'size_30' => $request->size_30,
                'size_31' => $request->size_31,
                'size_32' => $request->size_32,
                'size_33' => $request->size_33,
                'size_34' => $request->size_34,
                'size_35' => $request->size_35,
                'size_36' => $request->size_36,
                'size_37' => $request->size_37,
                'size_38' => $request->size_38,
                'size_39' => $request->size_39,
                'size_40' => $request->size_40,
                'size_41' => $request->size_41,
                'size_42' => $request->size_42,
                'other' => $request->other,
                'total' => $request->total
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'success input to cart',
                'data' => $cart
            ], 200);
        } catch (\Throwable $th) {

            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'failed input to cart',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($phone, $id)
    {
        $user = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'number '.$phone.'not registerd'
            ], 400);
        }

        try {
            $clothes = Clothes::where('id', $id)->with('Type', 'Image', 'BufferProduct.Size')->first();

            if ($clothes->combo != '-') {
                $clothes['combo'] = explode(",", $clothes->combo);
            }
            $clothes['size_2'] = explode(",", $clothes->size_2);
            $clothes['size_4'] = explode(",", $clothes->size_4);
            $clothes['size_6'] = explode(",", $clothes->size_6);
            $clothes['size_8'] = explode(",", $clothes->size_8);
            $clothes['size_10'] = explode(",", $clothes->size_10);
            $clothes['size_12'] = explode(",", $clothes->size_12);

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $phone, $id)
    {
        $user = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'rejected',
                'message' => 'number '.$phone.' not registered',
            ], 300);
        }

        $updateOrder = new Order();

        try {
            $temporary_storage = TemporaryStorage::find($id);

            DB::beginTransaction();

            $updateOrder->updateOrder($id, 'S', $request->size_s);
            $updateOrder->updateOrder($id, 'M', $request->size_m);
            $updateOrder->updateOrder($id, 'L', $request->size_l);
            $updateOrder->updateOrder($id, 'XL', $request->size_xl);
            $updateOrder->updateOrder($id, 'XXL', $request->size_xxl);
            $updateOrder->updateOrder($id, 'XXXL', $request->size_xxxl);
            $updateOrder->updateOrder($id, '2', $request->size_2);
            $updateOrder->updateOrder($id, '4', $request->size_4);
            $updateOrder->updateOrder($id, '6', $request->size_6);
            $updateOrder->updateOrder($id, '8', $request->size_8);
            $updateOrder->updateOrder($id, '10', $request->size_10);
            $updateOrder->updateOrder($id, '12', $request->size_12);
            $updateOrder->updateOrder($id, '27', $request->size_27);
            $updateOrder->updateOrder($id, '28', $request->size_28);
            $updateOrder->updateOrder($id, '29', $request->size_29);
            $updateOrder->updateOrder($id, '30', $request->size_30);
            $updateOrder->updateOrder($id, '31', $request->size_31);
            $updateOrder->updateOrder($id, '32', $request->size_32);
            $updateOrder->updateOrder($id, '33', $request->size_33);
            $updateOrder->updateOrder($id, '34', $request->size_34);
            $updateOrder->updateOrder($id, '35', $request->size_35);
            $updateOrder->updateOrder($id, '36', $request->size_36);
            $updateOrder->updateOrder($id, '37', $request->size_37);
            $updateOrder->updateOrder($id, '38', $request->size_38);
            $updateOrder->updateOrder($id, '39', $request->size_39);
            $updateOrder->updateOrder($id, '40', $request->size_40);
            $updateOrder->updateOrder($id, '41', $request->size_41);
            $updateOrder->updateOrder($id, '42', $request->size_42);
            $BufferStock_other = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
            ])->first();

            if ($BufferStock_other) {
                if ($BufferStock_other->qty_buffer != 0) {
                    if ($BufferStock_other->qty_avaliable != 0 && $request->other > $temporary_storage->other) {
                        $increment = $request->other - $temporary_storage->other;
                        $qty_avaliable = $BufferStock_other->qty_avaliable - $increment;
                        $qty_process = $BufferStock_other->qty_process + $increment;

                        $BufferStock_other->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->other < $temporary_storage->other) {
                        if ($request->other == 0) {
                            $qty_avaliable = $BufferStock_other->qty_avaliable + $temporary_storage->other;
                            $qty_process = $BufferStock_other->qty_process - $temporary_storage->other;

                            $BufferStock_other->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->other - $request->other;
                            $qty_avaliable = $BufferStock_other->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_other->qty_process - $decrement;

                            $BufferStock_other->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_other->qty_buffer == 0) {
                    if ($request->other > $temporary_storage->other) {
                        $increment = $request->other - $temporary_storage->other;
                        $qty_process = $BufferStock_other->qty_process + $increment;

                        $BufferStock_other->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->other < $temporary_storage->other) {
                        if ($request->other == 0) {
                            $qty_process = $BufferStock_other->qty_process - $temporary_storage->other;

                            $BufferStock_other->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->other - $request->other;
                            $qty_process = $BufferStock_other->qty_process - $decrement;

                            $BufferStock_other->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $temporary_storage->update([
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
                'size_27' => $request->size_27,
                'size_28' => $request->size_28,
                'size_29' => $request->size_29,
                'size_30' => $request->size_30,
                'size_31' => $request->size_31,
                'size_32' => $request->size_32,
                'size_33' => $request->size_33,
                'size_34' => $request->size_34,
                'size_35' => $request->size_35,
                'size_36' => $request->size_36,
                'size_37' => $request->size_37,
                'size_38' => $request->size_38,
                'size_39' => $request->size_39,
                'size_40' => $request->size_40,
                'size_41' => $request->size_41,
                'size_42' => $request->size_42,
                'other' => $request->other,
                'total' => $request->total,
                'created_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'success to update cart'
            ], 200);
        } catch (\Throwable $th) {

            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to update cart',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($phone, $id)
    {
        $user = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'rejected',
                'message' => 'number '.$phone.' not registered',
            ], 370);
        }

        try {
            $detailCart = TemporaryStorage::find($id);
            $clothes = Clothes::find($detailCart->clothes_id);

            DB::beginTransaction();

            $deleteOrder = new Order();

            $deleteOrder->deleteOrder($id, 'S');
            $deleteOrder->deleteOrder($id, 'M');
            $deleteOrder->deleteOrder($id, 'L');
            $deleteOrder->deleteOrder($id, 'XL');
            $deleteOrder->deleteOrder($id, 'XXL');
            $deleteOrder->deleteOrder($id, 'XXXL');
            $deleteOrder->deleteOrder($id, '2');
            $deleteOrder->deleteOrder($id, '4');
            $deleteOrder->deleteOrder($id, '6');
            $deleteOrder->deleteOrder($id, '8');
            $deleteOrder->deleteOrder($id, '10');
            $deleteOrder->deleteOrder($id, '12');
            $deleteOrder->deleteOrder($id, '27');
            $deleteOrder->deleteOrder($id, '28');
            $deleteOrder->deleteOrder($id, '29');
            $deleteOrder->deleteOrder($id, '30');
            $deleteOrder->deleteOrder($id, '31');
            $deleteOrder->deleteOrder($id, '32');
            $deleteOrder->deleteOrder($id, '33');
            $deleteOrder->deleteOrder($id, '34');
            $deleteOrder->deleteOrder($id, '35');
            $deleteOrder->deleteOrder($id, '36');
            $deleteOrder->deleteOrder($id, '37');
            $deleteOrder->deleteOrder($id, '38');
            $deleteOrder->deleteOrder($id, '39');
            $deleteOrder->deleteOrder($id, '40');
            $deleteOrder->deleteOrder($id, '41');
            $deleteOrder->deleteOrder($id, '42');

            $BufferStock_other = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
            ])->first();

            if ($BufferStock_other) {
                if ($BufferStock_other->qty_buffer > 0) {
                    $qty_avaliable = $BufferStock_other->qty_avaliable + $detailCart->other;
                    $qty_process = $BufferStock_other->qty_process - $detailCart->other;

                    $BufferStock_other->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_other->qty_buffer == 0) {
                    $qty_process = $BufferStock_other->qty_process - $detailCart->other;

                    $BufferStock_other->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $detailCart->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'success to delete data'
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to delete data',
                'error' => $th->getMessage()
            ], 200);
        }
    }
}
