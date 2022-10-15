<?php

namespace App\Http\Controllers\Api\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\{Size, BufferProduct, TemporaryStorage};

class Order extends Controller
{
    public function createOrder($request, $clothesId, $theSize)
    {
        $size = Size::where('size', $theSize)->first();
        if ($size) {
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothesId,
                'size_id' => $size->id
            ])->first();

            if ($BufferStock) {
                if ($BufferStock->qty_buffer != 0) {
                    if ($BufferStock->qty_avaliable >= $request) {
                        $qty_avaliable = $BufferStock->qty_avaliable - $request;
                        $qty_process = $BufferStock->qty_process + $request;

                        $BufferStock->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        DB::rollback();
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'size s to many request'
                        ], 400);
                    }
                } elseif ($BufferStock->qty_buffer == 0) {
                    $qty_process = $BufferStock->qty_process + $request;

                    $BufferStock->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }
        }
    }

    public function updateOrder($id, $theSize, $request)
    {
        $temporary_storage = TemporaryStorage::find($id);

        $size = Size::firstOrCreate([
            'size' => $theSize
        ]);

        $objectSize = "size_".strtolower($theSize);

        $BufferStock = BufferProduct::where([
            'clothes_id' => $temporary_storage->clothes_id,
            'size_id' => $size->id
        ])->first();

        if ($BufferStock) {
            if ($BufferStock->qty_buffer != 0) {
                if ($request && $BufferStock->qty_avaliable != 0 && $request > $temporary_storage->$objectSize) {
                    $increment = $request - $temporary_storage->$objectSize;
                    $qty_avaliable = $BufferStock->qty_avaliable - $increment;
                    $qty_process = $BufferStock->qty_process + $increment;

                    $BufferStock->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_process' => $qty_process
                    ]);

                } elseif ($request < $temporary_storage->$objectSize) {
                    if ($request == 0) {
                        $qty_avaliable = $BufferStock->qty_avaliable + $temporary_storage->$objectSize;
                        $qty_process = $BufferStock->qty_process - $temporary_storage->$objectSize;

                        $BufferStock->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        $decrement = $temporary_storage->$objectSize - $request;
                        $qty_avaliable = $BufferStock->qty_avaliable + $decrement;
                        $qty_process = $BufferStock->qty_process - $decrement;

                        $BufferStock->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    }
                }
            } elseif ($BufferStock->qty_buffer == 0) {
                if ($request > $temporary_storage->$objectSize) {
                    $increment = $request - $temporary_storage->$objectSize;
                    $qty_process = $BufferStock->qty_process + $increment;

                    $BufferStock->update([
                        'qty_process' => $qty_process
                    ]);
                } elseif ($request < $temporary_storage->$objectSize) {
                    if ($request == 0) {
                        $qty_process = $BufferStock->qty_process - $temporary_storage->$objectSize;

                        $BufferStock->update([
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        $decrement = $temporary_storage->$objectSize - $request;
                        $qty_process = $BufferStock->qty_process - $decrement;

                        $BufferStock->update([
                            'qty_process' => $qty_process
                        ]);
                    }
                }
            }
        }
    }

    public function deleteOrder($id, $theSize)
    {
        $detailCart = TemporaryStorage::find($id);

        $size = Size::firstOrCreate([
            'size' => $theSize
        ]);
        $BufferStock = BufferProduct::where([
            'clothes_id' => $detailCart->clothes_id,
            'size_id' => $size->id
            ])->first();

            $objectSize = "size_".strtolower($theSize);

        if ($BufferStock) {
            if ($BufferStock->qty_buffer > 0) {
                $qty_avaliable = $BufferStock->qty_avaliable + $detailCart->$objectSize;
                $qty_process = $BufferStock->qty_process - $detailCart->$objectSize;

                $BufferStock->update([
                    'qty_avaliable' => $qty_avaliable,
                    'qty_proccess' => $qty_process
                ]);
            } elseif ($BufferStock->qty_buffer == 0) {
                $qty_process = $BufferStock->qty_process - $detailCart->$objectSize;

                $BufferStock->update([
                    'qty_proccess' => $qty_process
                ]);
            }
        }
    }
}
