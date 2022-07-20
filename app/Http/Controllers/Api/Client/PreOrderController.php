<?php

namespace App\Http\Controllers\Api\Client;

use App\BufferProduct;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Clothes;
use App\Distributor;
use App\Http\Requests\PreOrderRequest;
use App\Size;
use App\TemporaryStorage;
use Illuminate\Support\Facades\DB;
use malkusch\lock\mutex\Mutex;

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
            $clothes = Clothes::where('id', $id)->with('Type', 'Image', 'BufferProduct')->first();

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
    public function update(PreOrderRequest $request, $id, $phone)
    {
        $user = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'rejected',
                'message' => 'number '.$phone.' not registered',
            ], 300);
        }

        try {
            $temporary_storage = TemporaryStorage::find($id);

            DB::beginTransaction();

            $size_s = Size::where('size', 'S')->first();
            $BufferStock_s = BufferProduct::where([
                'clothes_id' => $temporary_storage->clothes_id,
                'size_id' => $size_s->id
            ])->first();


            if ($BufferStock_s) {
                if ($BufferStock_s->qty_buffer != 0) {
                    if ($request->size_s && $request->size_s > $temporary_storage->size_s) {
                        $increment = $request->size_s - $temporary_storage->size_s;
                        $qty_avaliable = $BufferStock_s->qty_avaliable - $increment;
                        $qty_process = $BufferStock_s->qty_process + $increment;

                        $BufferStock_s->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);

                    } elseif ($request->size_s && $request->size_s < $temporary_storage->size_s) {
                        $decrement = $temporary_storage->size_s - $request->size_s;
                        $qty_avaliable = $BufferStock_s->qty_avaliable + $decrement;
                        $qty_process = $BufferStock_s->qty_process - $decrement;

                        $BufferStock_s->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'not ready'
                    ], 400);
                }
            }

            $size_m = Size::where('size', 'M')->first();
            $BufferStock_m = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_m->id
            ])->first();

            if ($BufferStock_m) {
                if ($BufferStock_m->qty_buffer != 0) {
                    if ($request->size_m && $request->size_m > $temporary_storage->size_m) {
                        $increment = $request->size_m - $temporary_storage->size_m;
                        $qty_avaliable = $BufferStock_m->qty_avaliable - $increment;
                        $qty_process = $BufferStock_m->qty_process + $increment;

                        $BufferStock_m->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_m && $request->size_m < $temporary_storage->size_m) {
                        $decrement = $temporary_storage->size_m - $request->size_m;
                        $qty_avaliable = $BufferStock_m->qty_avaliable + $decrement;
                        $qty_process = $BufferStock_m->qty_process - $decrement;
                        $BufferStock_m->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
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
                    if ($request->size_l && $request->size_l > $temporary_storage->size_l) {
                        $increment = $request->size_l - $temporary_storage->size_l;
                        $qty_avaliable = $BufferStock_l->qty_avaliable - $increment;
                        $qty_process = $BufferStock_l->qty_process + $increment;

                        $BufferStock_l->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($temporary_storage->size_l && $temporary_storage->size_l < $temporary_storage->size_l) {
                        $decrement = $temporary_storage->size_l - $request->size_l;
                        $qty_avaliable = $BufferStock_l->qty_avaliable + $decrement;
                        $qty_process = $BufferStock_l->qty_process - $decrement;

                        $BufferStock_l->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
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
                    if ($request->size_xl && $request->size_xl > $temporary_storage->size_xl) {
                        $increment = $request->size_xl - $temporary_storage->size_xl;
                        $qty_avaliable = $BufferStock_xl->qty_avaliable - $increment;
                        $qty_process = $BufferStock_xl->qty_process + $increment;

                        $BufferStock_xl->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_xl && $request->size_xl < $temporary_storage->size_xl) {
                        $decrement = $temporary_storage->size_xl - $request->size_xl;
                        $qty_avaliable = $BufferStock_xl->qty_avaliable + $decrement;
                        $qty_process = $BufferStock_xl->qty_process - $decrement;

                        $BufferStock_xl->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
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
                    if ($request->size_xxl && $request->size_xxl > $temporary_storage->size_xxl) {
                        $increment = $request->$size_xxl - $temporary_storage->size_xxl;
                        $qty_avaliable = $BufferStock_xxl->qty_avaliable - $increment;
                        $qty_process = $BufferStock_xxl->qty_process + $increment;

                        $BufferStock_xxl->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_xxl && $request->size_xxl < $temporary_storage->size_xxl) {
                        $decrement = $temporary_storage->$size_xxl - $request->size_xxl;
                        $qty_avaliable = $BufferStock_xxl->qty_avaliable + $decrement;
                        $qty_process = $BufferStock_xxl->qty_process - $decrement;

                        $BufferStock_xxl->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
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
                    if ($request->size_xxxl && $request->size_xxxl > $temporary_storage->size_xxxl) {
                        $increment = $request->siize_xxxl - $temporary_storage->size_xxxl;
                        $qty_avaliable = $BufferStock_xxxl->qty_avaliable - $increment;
                        $qty_process = $BufferStock_xxxl->qty_process + $increment;

                        $BufferStock_xxxl->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_xxxl && $request->size_xxxl < $temporary_storage->size_xxxl) {
                        $decrement = $temporary_storage->size_xxxl - $request->size_xxxl;
                        $qty_avaliable = $BufferStock_xxxl->qty_avaliable + $decrement;
                        $qty_process = $BufferStock_xxxl->qty_process - $decrement;

                        $BufferStock_xxxl->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
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
                    if ($request->size_2 && $request->size_2 > $temporary_storage->size_2) {
                        $increment = $request->size_2 - $temporary_storage->size_2;
                        $qty_avaliable = $BufferStock_2->qty_avaliable - $increment;
                        $qty_process = $BufferStock_2->qty_process + $increment;

                        $BufferStock_2->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_2 && $request->size_2 < $temporary_storage->size_2) {
                        $decrement = $temporary_storage->size_2 - $request->size_2;
                        $qty_avaliable = $BufferStock_2->qty_avaliable + $increment;
                        $qty_process = $BufferStock_2->qty_process - $increment;

                        $BufferStock_2->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
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
                    if ($request->size_4 && $request->size_4 > $temporary_storage->size_4) {
                        $increment = $request->size_4 - $temporary_storage->size_4;
                        $qty_avaliable = $BufferStock_4->qty_avaliable - $increment;
                        $qty_process = $BufferStock_4->qty_process + $increment;

                        $BufferStock_4->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_4 && $request->size_4 < $temporary_storage->size_4) {
                        $decrement = $temporary_storage->size_4 - $request->size_4;
                        $qty_avaliable = $BufferStock_4->qty_avaliable + $decrement;
                        $qty_process = $BufferStock_4->qty_process - $decrement;

                        $BufferStock_4->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
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
                    if ($request->size_6 && $request->size_6 > $temporary_storage->size_6) {
                        $increment = $request->size_6 - $temporary_storage->size_6;
                        $qty_avaliable = $BufferStock_6->qty_avaliable - $increment;
                        $qty_process = $BufferStock_6->qty_process + $increment;

                        $BufferStock_6->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_6 && $request->size_6 < $temporary_storage->size_6) {
                        $decrement = $temporary_storage->size_6 - $request->size_6;
                        $qty_avaliable = $BufferStock_6->qty_avaliable + $decrement;
                        $qty_process = $BufferStock_6->qty_process - $decrement;

                        $BufferStock_6->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
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
                    if ($request->size_8 && $request->size_8 > $temporary_storage->size_8) {
                        $increment = $request->size_8 - $temporary_storage->size_8;
                        $qty_avaliable = $BufferStock_8->qty_avaliable - $increment;
                        $qty_process = $BufferStock_8->qty_process + $increment;

                        $BufferStock_8->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_8 && $request->size_8 < $temporary_storage->size_8) {
                        $decrement = $temporary_storage->size_8 - $request->size_8;
                        $qty_avaliable = $BufferStock_8->qty_avaliable + $decrement;
                        $qty_process = $BufferStock_8->qty_process - $decrement;

                        $BufferStock_8->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
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
                    if ($request->size_10 && $request->size_10 > $temporary_storage->size_10) {
                        $increment = $request->size_10 - $temporary_storage->size_10;
                        $qty_avaliable = $BufferStock_10->qty_avaliable - $request->size_10;
                        $qty_process = $BufferStock_10->qty_process + $request->size_10;

                        $BufferStock_10->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_10 && $request->size_10 < $temporary_storage->size_10) {
                        $decrement = $temporary_storage->size_10 - $request->size_10;
                        $qty_avaliable = $BufferStock_10->qty_avaliable + $decrement;
                        $qty_process = $BufferStock_10->qty_process - $decrement;

                        $BufferStock_10->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
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
                    if ($request->size_12 && $request->size_12 > $temporary_storage->size_12) {
                        $increment = $request->size_12 - $temporary_storage->size_12;
                        $qty_avaliable = $BufferStock_12->qty_avaliable - $increment;
                        $qty_process = $BufferStock_12->qty_process + $increment;

                        $BufferStock_12->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_12 && $request->size_12 < $temporary_storage->size_12) {
                        $decrement = $temporary_storage->size_12 - $request->size_12;
                        $qty_avaliable = $BufferStock_12->qty_avaliable - $decrement;
                        $qty_process = $BufferStock_12->qty_process + $decrement;

                        $BufferStock_12->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'failed to update data',
                        'message' => 'not ready'
                    ], 400);
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
                'total' => $request->total
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
    public function destroy($id, $phone)
    {

        $user = Distributor::where('phone', $phone)->first();
        if (!$user) {
            return response()->json([
                'status' => 'rejected',
                'message' => 'number '.$phone.' not registered',
            ], 300);
        }

        try {
            TemporaryStorage::where('id', $id)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'success to delete data'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to delete data',
                'error' => $th->getMessage()
            ], 200);
        }
    }
}
