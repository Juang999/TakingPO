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

        $clothess = Clothes::orderBy('group_article')->with('Type', 'Image', 'BufferProduct.Size')->get();

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

        $size_s = Size::where('size', 's')->first();
            $BufferStock_s = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_s->id
            ])->first();

            if ($BufferStock_s) {
                if ($BufferStock_s->qty_buffer != 0) {
                    if ($BufferStock_s->qty_avaliable >= $request->size_s) {
                        $qty_avaliable = $BufferStock_s->qty_avaliable - $request->size_s;
                        $qty_process = $BufferStock_s->qty_process + $request->size_s;

                        $BufferStock_s->update([
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
                } elseif ($BufferStock_s->qty_buffer == 0) {
                    $qty_process = $BufferStock_s->qty_process + $request->size_s;

                    $BufferStock_s->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_m = Size::where('size', 'm')->first();
            $BufferStock_m = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_m->id
            ])->first();

            if ($BufferStock_m) {
                if ($BufferStock_m->qty_buffer != 0) {
                    if ($BufferStock_m->qty_avaliable >= $request->size_m) {
                        $qty_avaliable = $BufferStock_m->qty_avaliable - $request->size_m;
                        $qty_process = $BufferStock_m->qty_process + $request->size_m;

                        $BufferStock_m->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } else {
                        return response()->json([
                            'status' => 'failed',
                            'message' => 'size m to many request'
                        ], 400);
                    }
                } elseif ($BufferStock_m->qty_buffer == 0) {
                    $qty_process = $BufferStock_m->qty_process + $request->size_m;

                    $BufferStock_m->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_l = Size::where('size', 'l')->first();
            $BufferStock_l = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_l->id
            ])->first();

            if ($BufferStock_l) {
                if ($BufferStock_l->qty_buffer != 0) {
                    dd($BufferStock_l->qty_buffer);
                    if ($BufferStock_l->qty_avaliable >= $request->size_l) {
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
                            'message' => 'size l sold out'
                        ], 400);
                    }
                } elseif ($BufferStock_l->qty_buffer == 0) {
                    $qty_process = $BufferStock_l->qty_process + $request->size_l;

                    $BufferStock_l->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_xl = Size::where('size', 'xl')->first();
            $BufferStock_xl = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_xl->id
            ])->first();

            if ($BufferStock_xl) {
                if ($BufferStock_xl->qty_buffer != 0) {
                    if ($BufferStock_xl->qty_avaliable >= $request->size_xl) {
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
                            'message' => 'size xl sold out'
                        ], 400);
                    }
                } elseif ($BufferStock_xl->qty_buffer == 0) {
                    $qty_process = $BufferStock_xl->qty_process + $request->size_xl;

                    $BufferStock_xl->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_xxl = Size::where('size', 'xxl')->first();
            $BufferStock_xxl = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_xxl->id
            ])->first();

            if ($BufferStock_xxl) {
                if ($BufferStock_xxl->qty_buffer != 0) {
                    if ($BufferStock_xxl->qty_avaliable >= $request->size_xxl) {
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
                            'message' => 'size xxl sold out'
                        ], 400);
                    }
                } elseif ($BufferStock_xxl->qty_buffer == 0) {
                    $qty_process = $BufferStock_xxl->qty_process + $request->size_xxl;

                    $BufferStock_xxl->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_xxxl = Size::where('size', 'xxxl')->first();
            $BufferStock_xxxl = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_xxxl->id
            ])->first();

            if ($BufferStock_xxxl) {
                if ($BufferStock_xxxl->qty_buffer != 0) {
                    if ($BufferStock_xxxl->qty_avaliable >= $request->size_xxxl) {
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
                            'message' => 'size xxxl sold out'
                        ], 400);
                    }
                } elseif ($BufferStock_xxxl->qty_buffer == 0) {
                    $qty_process = $BufferStock_xxxl->qty_process + $request->size_xxxl;

                    $BufferStock_xxxl->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_2 = Size::where('size', '2')->first();
            $BufferStock_2 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_2->id
            ])->first();

            if ($BufferStock_2) {
                if ($BufferStock_2->qty_buffer != 0) {
                    if ($BufferStock_2->qty_avaliable >= $request->size_2) {
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
                            'message' => 'size 2 sold out'
                        ], 400);
                    }
                } elseif ($BufferStock_2->qty_buffer == 0) {
                    $qty_process = $BufferStock_2->qty_process + $request->size_2;

                    $BufferStock_2->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }


            $size_4 = Size::where('size', '4')->first();
            $BufferStock_4 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_4->id
            ])->first();

            if ($BufferStock_4) {
                if ($BufferStock_4->qty_buffer != 0) {
                    if ($BufferStock_4->qty_avaliable >= $request->size_4) {
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
                            'message' => 'size 4 sold out'
                        ], 400);
                    }
                } elseif ($BufferStock_4->qty_buffer == 0) {
                    $qty_process = $BufferStock_4->qty_process + $request->size_4;

                    $BufferStock_4->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_6 = Size::where('size', '6')->first();
            $BufferStock_6 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_6->id
            ])->first();

            if ($BufferStock_6) {
                if ($BufferStock_6->qty_buffer != 0) {
                    if ($BufferStock_6->qty_avaliable >= $request->size_6) {
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
                            'message' => 'size 6 sold out'
                        ], 400);
                    }
                } elseif ($BufferStock_6->qty_buffer == 0) {
                    $qty_process = $BufferStock_6->qty_process + $request->size_6;

                    $BufferStock_6->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_8 = Size::where('size', '8')->first();
            $BufferStock_8 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_8->id
            ])->first();

            if ($BufferStock_8) {
                if ($BufferStock_8->qty_buffer != 0) {
                    if ($BufferStock_8->qty_avaliable >= $request->size_8) {
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
                            'message' => 'size 8 sold out'
                        ], 400);
                    }
                } elseif ($BufferStock_8->qty_buffer == 0) {
                    $qty_process = $BufferStock_8->qty_process + $request->size_8;

                    $BufferStock_8->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_10 = Size::where('size', '10')->first();
            $BufferStock_10 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_10->id
            ])->first();

            if ($BufferStock_10) {
                if ($BufferStock_10->qty_buffer != 0) {
                    if ($BufferStock_10->qty_avaliable >= $request->size_10) {
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
                            'message' => 'size 10 sold out'
                        ], 400);
                    }
                } elseif ($BufferStock_10->qty_buffer == 0) {
                    $qty_process = $BufferStock_10->qty_process + $request->size_10;

                    $BufferStock_10->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_12 = Size::where('size', '12')->first();
            $BufferStock_12 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_12->id
            ])->first();

            if ($BufferStock_12) {
                if ($BufferStock_12->qty_buffer != 0) {
                    if ($BufferStock_12->qty_avaliable >= $request->size_12) {
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
                            'message' => 'size 12 sold out'
                        ], 400);
                    }
                } elseif ($BufferStock_12->qty_buffer == 0) {
                    $qty_process = $BufferStock_12->qty_process + $request->size_12;

                    $BufferStock_12->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_27 = Size::where('size', '27')->first();
            $BufferStock_27 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_27->id
            ])->first();

            if ($BufferStock_27) {
                if ($BufferStock_27->qty_buffer != 0) {
                    if ($BufferStock_27->qty_avaliable >= $request->size_27) {
                        $qty_avaliable = $BufferStock_27->qty_avaliable - $request->size_27;
                        $qty_process = $BufferStock_27->qty_process + $request->size_27;

                        $BufferStock_27->update([
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
                } elseif ($BufferStock_27->qty_buffer == 0) {
                    $qty_process = $BufferStock_27->qty_process + $request->size_27;

                    $BufferStock_27->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_28 = Size::where('size', '28')->first();
            $BufferStock_28 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_28->id
            ])->first();

            if ($BufferStock_28) {
                if ($BufferStock_28->qty_buffer != 0) {
                    if ($BufferStock_28->qty_avaliable >= $request->size_28) {
                        $qty_avaliable = $BufferStock_28->qty_avaliable - $request->size_28;
                        $qty_process = $BufferStock_28->qty_process + $request->size_28;

                        $BufferStock_28->update([
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
                } elseif ($BufferStock_28->qty_buffer == 0) {
                    $qty_process = $BufferStock_28->qty_process + $request->size_28;

                    $BufferStock_28->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_29 = Size::where('size', '29')->first();
            $BufferStock_29 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_29->id
            ])->first();

            if ($BufferStock_29) {
                if ($BufferStock_29->qty_buffer != 0) {
                    if ($BufferStock_29->qty_avaliable >= $request->size_29) {
                        $qty_avaliable = $BufferStock_29->qty_avaliable - $request->size_29;
                        $qty_process = $BufferStock_29->qty_process + $request->size_29;

                        $BufferStock_29->update([
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
                } elseif ($BufferStock_29->qty_buffer == 0) {
                    $qty_process = $BufferStock_29->qty_process + $request->size_29;

                    $BufferStock_29->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_30 = Size::where('size', '30')->first();
            $BufferStock_30 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_30->id
            ])->first();

            if ($BufferStock_30) {
                if ($BufferStock_30->qty_buffer != 0) {
                    if ($BufferStock_30->qty_avaliable >= $request->size_30) {
                        $qty_avaliable = $BufferStock_30->qty_avaliable - $request->size_30;
                        $qty_process = $BufferStock_30->qty_process + $request->size_30;

                        $BufferStock_30->update([
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
                } elseif ($BufferStock_30->qty_buffer == 0) {
                    $qty_process = $BufferStock_30->qty_process + $request->size_30;

                    $BufferStock_30->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_31 = Size::where('size', '31')->first();
            $BufferStock_31 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_31->id
            ])->first();

            if ($BufferStock_31) {
                if ($BufferStock_31->qty_buffer != 0) {
                    if ($BufferStock_31->qty_avaliable >= $request->size_31) {
                        $qty_avaliable = $BufferStock_31->qty_avaliable - $request->size_31;
                        $qty_process = $BufferStock_31->qty_process + $request->size_31;

                        $BufferStock_31->update([
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
                } elseif ($BufferStock_31->qty_buffer == 0) {
                    $qty_process = $BufferStock_31->qty_process + $request->size_31;

                    $BufferStock_31->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_32 = Size::where('size', '32')->first();
            $BufferStock_32 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_32->id
            ])->first();

            if ($BufferStock_32) {
                if ($BufferStock_32->qty_buffer != 0) {
                    if ($BufferStock_32->qty_avaliable >= $request->size_32) {
                        $qty_avaliable = $BufferStock_32->qty_avaliable - $request->size_32;
                        $qty_process = $BufferStock_32->qty_process + $request->size_32;

                        $BufferStock_32->update([
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
                } elseif ($BufferStock_32->qty_buffer == 0) {
                    $qty_process = $BufferStock_32->qty_process + $request->size_32;

                    $BufferStock_32->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_33 = Size::where('size', '33')->first();
            $BufferStock_33 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_33->id
            ])->first();

            if ($BufferStock_33) {
                if ($BufferStock_33->qty_buffer != 0) {
                    if ($BufferStock_33->qty_avaliable >= $request->size_33) {
                        $qty_avaliable = $BufferStock_33->qty_avaliable - $request->size_33;
                        $qty_process = $BufferStock_33->qty_process + $request->size_33;

                        $BufferStock_33->update([
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
                } elseif ($BufferStock_33->qty_buffer == 0) {
                    $qty_process = $BufferStock_33->qty_process + $request->size_33;

                    $BufferStock_33->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_34 = Size::where('size', '34')->first();
            $BufferStock_34 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_34->id
            ])->first();

            if ($BufferStock_34) {
                if ($BufferStock_34->qty_buffer != 0) {
                    if ($BufferStock_34->qty_avaliable >= $request->size_34) {
                        $qty_avaliable = $BufferStock_34->qty_avaliable - $request->size_34;
                        $qty_process = $BufferStock_34->qty_process + $request->size_34;

                        $BufferStock_34->update([
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
                } elseif ($BufferStock_34->qty_buffer == 0) {
                    $qty_process = $BufferStock_34->qty_process + $request->size_34;

                    $BufferStock_34->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_35 = Size::where('size', '35')->first();
            $BufferStock_35 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_35->id
            ])->first();

            if ($BufferStock_35) {
                if ($BufferStock_35->qty_buffer != 0) {
                    if ($BufferStock_35->qty_avaliable >= $request->size_35) {
                        $qty_avaliable = $BufferStock_35->qty_avaliable - $request->size_35;
                        $qty_process = $BufferStock_35->qty_process + $request->size_35;

                        $BufferStock_35->update([
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
                } elseif ($BufferStock_35->qty_buffer == 0) {
                    $qty_process = $BufferStock_35->qty_process + $request->size_35;

                    $BufferStock_35->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_36 = Size::where('size', '36')->first();
            $BufferStock_36 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_36->id
            ])->first();

            if ($BufferStock_36) {
                if ($BufferStock_36->qty_buffer != 0) {
                    if ($BufferStock_36->qty_avaliable >= $request->size_36) {
                        $qty_avaliable = $BufferStock_36->qty_avaliable - $request->size_36;
                        $qty_process = $BufferStock_36->qty_process + $request->size_36;

                        $BufferStock_36->update([
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
                } elseif ($BufferStock_36->qty_buffer == 0) {
                    $qty_process = $BufferStock_36->qty_process + $request->size_36;

                    $BufferStock_36->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_37 = Size::where('size', '37')->first();
            $BufferStock_37 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_37->id
            ])->first();

            if ($BufferStock_37) {
                if ($BufferStock_37->qty_buffer != 0) {
                    if ($BufferStock_37->qty_avaliable >= $request->size_37) {
                        $qty_avaliable = $BufferStock_37->qty_avaliable - $request->size_37;
                        $qty_process = $BufferStock_37->qty_process + $request->size_37;

                        $BufferStock_37->update([
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
                } elseif ($BufferStock_37->qty_buffer == 0) {
                    $qty_process = $BufferStock_37->qty_process + $request->size_37;

                    $BufferStock_37->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_38 = Size::where('size', '38')->first();
            $BufferStock_38 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_38->id
            ])->first();

            if ($BufferStock_38) {
                if ($BufferStock_38->qty_buffer != 0) {
                    if ($BufferStock_38->qty_avaliable >= $request->size_38) {
                        $qty_avaliable = $BufferStock_38->qty_avaliable - $request->size_38;
                        $qty_process = $BufferStock_38->qty_process + $request->size_38;

                        $BufferStock_38->update([
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
                } elseif ($BufferStock_38->qty_buffer == 0) {
                    $qty_process = $BufferStock_38->qty_process + $request->size_38;

                    $BufferStock_38->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_39 = Size::where('size', '39')->first();
            $BufferStock_39 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_39->id
            ])->first();

            if ($BufferStock_39) {
                if ($BufferStock_39->qty_buffer != 0) {
                    if ($BufferStock_39->qty_avaliable >= $request->size_39) {
                        $qty_avaliable = $BufferStock_39->qty_avaliable - $request->size_39;
                        $qty_process = $BufferStock_39->qty_process + $request->size_39;

                        $BufferStock_39->update([
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
                } elseif ($BufferStock_39->qty_buffer == 0) {
                    $qty_process = $BufferStock_39->qty_process + $request->size_39;

                    $BufferStock_39->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_40 = Size::where('size', '40')->first();
            $BufferStock_40 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_40->id
            ])->first();

            if ($BufferStock_40) {
                if ($BufferStock_40->qty_buffer != 0) {
                    if ($BufferStock_40->qty_avaliable >= $request->size_40) {
                        $qty_avaliable = $BufferStock_40->qty_avaliable - $request->size_40;
                        $qty_process = $BufferStock_40->qty_process + $request->size_40;

                        $BufferStock_40->update([
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
                } elseif ($BufferStock_40->qty_buffer == 0) {
                    $qty_process = $BufferStock_40->qty_process + $request->size_40;

                    $BufferStock_40->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_41 = Size::where('size', '41')->first();
            $BufferStock_41 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_41->id
            ])->first();

            if ($BufferStock_41) {
                if ($BufferStock_41->qty_buffer != 0) {
                    if ($BufferStock_41->qty_avaliable >= $request->size_41) {
                        $qty_avaliable = $BufferStock_41->qty_avaliable - $request->size_41;
                        $qty_process = $BufferStock_41->qty_process + $request->size_41;

                        $BufferStock_41->update([
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
                } elseif ($BufferStock_41->qty_buffer == 0) {
                    $qty_process = $BufferStock_41->qty_process + $request->size_41;

                    $BufferStock_41->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

            $size_42 = Size::where('size', '42')->first();
            $BufferStock_42 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_42->id
            ])->first();

            if ($BufferStock_42) {
                if ($BufferStock_42->qty_buffer != 0) {
                    if ($BufferStock_42->qty_avaliable >= $request->size_42) {
                        $qty_avaliable = $BufferStock_42->qty_avaliable - $request->size_42;
                        $qty_process = $BufferStock_42->qty_process + $request->size_42;

                        $BufferStock_42->update([
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
                } elseif ($BufferStock_42->qty_buffer == 0) {
                    $qty_process = $BufferStock_42->qty_process + $request->size_42;

                    $BufferStock_42->update([
                        'qty_process' => $qty_process
                    ]);
                }
            }

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
                    if ($request->size_s && $BufferStock_s->qty_avaliable != 0 && $request->size_s > $temporary_storage->size_s) {
                        $increment = $request->size_s - $temporary_storage->size_s;
                        $qty_avaliable = $BufferStock_s->qty_avaliable - $increment;
                        $qty_process = $BufferStock_s->qty_process + $increment;

                        $BufferStock_s->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);

                    } elseif ($request->size_s < $temporary_storage->size_s) {
                        if ($request->size_s == 0) {
                            $qty_avaliable = $BufferStock_s->qty_avaliable + $temporary_storage->size_s;
                            $qty_process = $BufferStock_s->qty_process - $temporary_storage->size_s;

                            $BufferStock_s->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_s - $request->size_s;
                            $qty_avaliable = $BufferStock_s->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_s->qty_process - $decrement;

                            $BufferStock_s->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_s->qty_buffer == 0) {
                    if ($request->size_s > $temporary_storage->size_s) {
                        $increment = $request->size_s - $temporary_storage->size_s;
                        $qty_process = $BufferStock_s->qty_process + $increment;

                        $BufferStock_s->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_s < $temporary_storage->size_s) {
                        if ($request->size_s == 0) {
                            $qty_process = $BufferStock_s->qty_process - $temporary_storage->size_s;

                            $BufferStock_s->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_s - $request->size_s;
                            $qty_process = $BufferStock_s->qty_process - $decrement;

                            $BufferStock_s->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_m = Size::where('size', 'M')->first();
            $BufferStock_m = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_m->id
            ])->first();

            if ($BufferStock_m) {
                if ($BufferStock_m->qty_buffer != 0) {
                    if ($request->size_m && $BufferStock_m->qty_avaliable != 0 && $request->size_m > $temporary_storage->size_m) {
                        $increment = $request->size_m - $temporary_storage->size_m;
                        $qty_avaliable = $BufferStock_m->qty_avaliable - $increment;
                        $qty_process = $BufferStock_m->qty_process + $increment;

                        $BufferStock_m->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_m < $temporary_storage->size_m) {
                        if ($request->size_m == 0) {
                            $qty_avaliable = $BufferStock_m->qty_avaliable + $temporary_storage->size_m;
                            $qty_process = $BufferStock_m->qty_process - $temporary_storage->size_m;
                            $BufferStock_m->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_m - $request->size_m;
                            $qty_avaliable = $BufferStock_m->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_m->qty_process - $decrement;
                            $BufferStock_m->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_m->qty_buffer == 0) {
                    if ($request->size_m > $temporary_storage->size_m) {
                        $increment = $request->size_m - $temporary_storage->size_m;
                        $qty_process = $BufferStock_m->qty_process + $increment;

                        $BufferStock_m->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_m < $temporary_storage->size_m) {
                        if ($request->size_m == 0) {
                            $qty_process = $BufferStock_m->qty_process - $temporary_storage->size_l;

                            $BufferStock_m->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_m - $request->size_m;
                            $qty_process = $BufferStock_m->qty_process - $decrement;

                            $BufferStock_m->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_l = Size::where('size', 'l')->first();
            $BufferStock_l = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_l->id
            ])->first();

            if ($BufferStock_l) {
                if ($BufferStock_l->qty_buffer != 0) {
                    if ($request->size_l && $BufferStock_l->qty_avaliable != 0 && $request->size_l > $temporary_storage->size_l) {
                        $increment = $request->size_l - $temporary_storage->size_l;
                        $qty_avaliable = $BufferStock_l->qty_avaliable - $increment;
                        $qty_process = $BufferStock_l->qty_process + $increment;

                        $BufferStock_l->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($temporary_storage->size_l < $temporary_storage->size_l) {
                        if ($request->size_l) {
                            $qty_avaliable = $BufferStock_l->qty_avaliable + $temporary_storage->size_l;
                            $qty_process = $BufferStock_l->qty_process - $temporary_storage->size_l;

                            $BufferStock_l->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_l - $request->size_l;
                            $qty_avaliable = $BufferStock_l->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_l->qty_process - $decrement;

                            $BufferStock_l->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_l->qty_buffer == 0) {
                    if ($request->size_l > $temporary_storage->size_l) {
                        $increment = $request->size_l - $temporary_storage->size_l;
                        $qty_process = $BufferStock_l->qty_process + $increment;

                        $BufferStock_l->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_l < $temporary_storage->size_l) {
                        if ($request->size_l == 0) {
                            $qty_process = $BufferStock_l->qty_process - $temporary_storage->size_l;

                            $BufferStock_l->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_l - $request->size_l;
                            $qty_process = $BufferStock_l->qty_process - $decrement;

                            $BufferStock_l->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_xl = Size::where('size', 'xl')->first();
            $BufferStock_xl = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_xl->id
            ])->first();

            if ($BufferStock_xl) {
                if ($BufferStock_xl->qty_buffer != 0) {
                    if ($request->size_xl && $BufferStock_xl->qty_avaliable != 0 && $request->size_xl > $temporary_storage->size_xl) {
                        $increment = $request->size_xl - $temporary_storage->size_xl;
                        $qty_avaliable = $BufferStock_xl->qty_avaliable - $increment;
                        $qty_process = $BufferStock_xl->qty_process + $increment;

                        $BufferStock_xl->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_xl < $temporary_storage->size_xl) {
                        if ($request->size_xl == 0) {
                            $qty_avaliable = $BufferStock_xl->qty_avaliable + $temporary_storage->size_xl;
                            $qty_process = $BufferStock_xl->qty_process - $temporary_storage->size_xl;

                            $BufferStock_xl->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_xl - $request->size_xl;
                            $qty_avaliable = $BufferStock_xl->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_xl->qty_process - $decrement;

                            $BufferStock_xl->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_xl->qty_buffer == 0) {
                    if ($request->size_xl > $temporary_storage->size_xl) {
                        $increment = $request->size_xl - $temporary_storage->size_xl;
                        $qty_process = $BufferStock_xl->qty_process + $increment;

                        $BufferStock_xl->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_xl < $temporary_storage->size_xl) {
                        if ($request->size_xl == 0) {
                            $qty_process = $BufferStock_xl->qty_process - $temporary_storage->size_xl;

                            $BufferStock_xl->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_xl - $request->size_xl;
                            $qty_process = $BufferStock_xl->qty_process - $decrement;

                            $BufferStock_xl->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_xxl = Size::where('size', 'xxl')->first();
            $BufferStock_xxl = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_xxl->id
            ])->first();

            if ($BufferStock_xxl) {
                if ($BufferStock_xxl->qty_buffer != 0) {
                    if ($BufferStock_xxl->qty_avaliable > 0 && $request->size_xxl > $temporary_storage->size_xxl) {
                        $increment = $request->size_xxl - $temporary_storage->size_xxl;

                        $qty_avaliable = $BufferStock_xxl->qty_avaliable - $increment;
                        dd($increment);
                        $qty_process = $BufferStock_xxl->qty_process + $increment;

                        $BufferStock_xxl->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_xxl < $temporary_storage->size_xxl) {
                        if ($request->size_xxl == 0) {
                            $qty_avaliable = $BufferStock_xxl->qty_avaliable + $temporary_storage->size_xxl;
                            $qty_process = $BufferStock_xxl->qty_process - $temporary_storage->size_xxl;

                            $BufferStock_xxl->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_xxl - $request->size_xxl;
                            $qty_avaliable = $BufferStock_xxl->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_xxl->qty_process - $decrement;

                            $BufferStock_xxl->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_xxl->qty_buffer == 0) {
                    if ($request->size_xxl > $temporary_storage->size_xxl) {
                        $increment = $request->size_xxl - $temporary_storage->size_xxl;
                        $qty_process = $BufferStock_xxl->qty_process + $increment;

                        $BufferStock_xxl->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_xxl < $temporary_storage->size_xxl) {
                        if ($request->size_xxl == 0) {
                            $qty_process = $BufferStock_xxl->qty_process - $temporary_storage->size_xxl;

                            $BufferStock_xxl->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_xxl - $request->size_xxl;
                            $qty_process = $BufferStock_xxl->qty_process - $decrement;

                            $BufferStock_xxl->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_xxxl = Size::where('size', 'xxxl')->first();
            $BufferStock_xxxl = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_xxxl->id
            ])->first();

            if ($BufferStock_xxxl) {
                if ($BufferStock_xxxl->qty_buffer != 0) {
                    if ($request->size_xxxl && $BufferStock_xxxl->qty_avaliable != 0 && $request->size_xxxl > $temporary_storage->size_xxxl) {
                        $increment = $request->size_xxxl - $temporary_storage->size_xxxl;
                        $qty_avaliable = $BufferStock_xxxl->qty_avaliable - $increment;
                        $qty_process = $BufferStock_xxxl->qty_process + $increment;

                        $BufferStock_xxxl->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_xxxl < $temporary_storage->size_xxxl) {
                        if ($request->size_xxxl == 0) {
                            $qty_avaliable = $BufferStock_xxxl->qty_avaliable + $temporary_storage->size_xxxl;
                            $qty_process = $BufferStock_xxxl->qty_process - $temporary_storage->size_xxxl;

                            $BufferStock_xxxl->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_xxxl - $request->size_xxxl;
                            $qty_avaliable = $BufferStock_xxxl->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_xxxl->qty_process - $decrement;

                            $BufferStock_xxxl->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_xxxl->qty_buffer == 0) {
                    if ($request->size_xxxl > $temporary_storage->size_xxxl) {
                        $increment = $request->size_xxxl - $temporary_storage->size_xxxl;
                        $qty_process = $BufferStock_xxxl->qty_process + $increment;

                        $BufferStock_xxxl->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_xxxl < $temporary_storage->size_xxxl) {
                        if ($request->size_xxxl == 0) {
                            $qty_process = $BufferStock_xxxl->qty_process - $temporary_storage->size_xxxl;

                            $BufferStock_xxxl->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_xxxl - $request->size_xxxl;
                            $qty_process = $BufferStock_xxxl->qty_process - $decrement;

                            $BufferStock_xxxl->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_2 = Size::where('size', '2')->first();
            $BufferStock_2 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_2->id
            ])->first();

            if ($BufferStock_2) {
                if ($BufferStock_2->qty_buffer != 0) {
                    if ($request->size_2 && $BufferStock_2->qty_avaliable != 0 && $request->size_2 > $temporary_storage->size_2) {
                        $increment = $request->size_2 - $temporary_storage->size_2;
                        $qty_avaliable = $BufferStock_2->qty_avaliable - $increment;
                        $qty_process = $BufferStock_2->qty_process + $increment;

                        $BufferStock_2->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_2 < $temporary_storage->size_2) {
                        if ($request->size_2) {
                            $qty_avaliable = $BufferStock_2->qty_avaliable + $increment;
                            $qty_process = $BufferStock_2->qty_process - $increment;

                            $BufferStock_2->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_2 - $request->size_2;
                            $qty_avaliable = $BufferStock_2->qty_avaliable + $increment;
                            $qty_process = $BufferStock_2->qty_process - $increment;

                            $BufferStock_2->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_2->qty_buffer == 0) {
                    if ($request->size_2 > $temporary_storage->size_2) {
                        $increment = $request->size_2 - $temporary_storage->size_2;
                        $qty_process = $BufferStock_2->qty_process + $increment;

                        $BufferStock_2->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_2 < $temporary_storage->size_2) {
                        if ($request->size_2 == 0) {
                            $qty_process = $BufferStock_2->qty_process - $temporary_storage->size_2;

                            $BufferStock_2->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_2 - $request->size_2;
                            $qty_process = $BufferStock_2->qty_process - $decrement;

                            $BufferStock_2->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_4 = Size::where('size', '4')->first();
            $BufferStock_4 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_4->id
            ])->first();

            if ($BufferStock_4) {
                if ($BufferStock_4->qty_buffer != 0) {
                    if ($request->size_4 && $BufferStock_4->qty_avaliable != 0 && $request->size_4 > $temporary_storage->size_4) {
                        $increment = $request->size_4 - $temporary_storage->size_4;
                        $qty_avaliable = $BufferStock_4->qty_avaliable - $increment;
                        $qty_process = $BufferStock_4->qty_process + $increment;

                        $BufferStock_4->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_4 < $temporary_storage->size_4) {
                        if ($request->size_4 == 0) {
                            $qty_avaliable = $BufferStock_4->qty_avaliable + $temporary_storage->size_4;
                            $qty_process = $BufferStock_4->qty_process - $temporary_storage->size_4;

                            $BufferStock_4->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_4 - $request->size_4;
                            $qty_avaliable = $BufferStock_4->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_4->qty_process - $decrement;

                            $BufferStock_4->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_4->qty_buffer == 0) {
                    if ($request->size_4 > $temporary_storage->size_4) {
                        $increment = $request->size_4 - $temporary_storage->size_4;
                        $qty_process = $BufferStock_4->qty_process + $increment;

                        $BufferStock_4->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_4 < $temporary_storage->size_4) {
                        if ($request->size_4 == 0) {
                            $qty_process = $BufferStock_4->qty_process - $temporary_storage->size_4;

                            $BufferStock_4->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_4 - $request->size_4;
                            $qty_process = $BufferStock_4->qty_process - $decrement;

                            $BufferStock_4->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_6 = Size::where('size', '6')->first();
            $BufferStock_6 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_6->id
            ])->first();

            if ($BufferStock_6) {
                if ($BufferStock_6->qty_buffer != 0) {
                    if ($request->size_6 && $BufferStock_6->qty_avaliable != 0 && $request->size_6 > $temporary_storage->size_6) {
                        $increment = $request->size_6 - $temporary_storage->size_6;
                        $qty_avaliable = $BufferStock_6->qty_avaliable - $increment;
                        $qty_process = $BufferStock_6->qty_process + $increment;

                        $BufferStock_6->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_6 < $temporary_storage->size_6) {
                        if ($temporary_storage->size_6 == 0) {
                            $qty_avaliable = $BufferStock_6->qty_avaliable + $temporary_storage->size_6;
                            $qty_process = $BufferStock_6->qty_process - $temporary_storage->size_6;

                            $BufferStock_6->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_6 - $request->size_6;
                            $qty_avaliable = $BufferStock_6->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_6->qty_process - $decrement;

                            $BufferStock_6->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_6->qty_buffer == 0) {
                    if ($request->size_6 > $temporary_storage->size_6) {
                        $increment = $request->size_6 - $temporary_storage->size_6;
                        $qty_process = $BufferStock_4->qty_process + $increment;

                        $BufferStock_6->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_6 < $temporary_storage->size_6) {
                        if ($request->size_6 == 0) {
                            $qty_process = $BufferStock_6->qty_process - $temporary_storage->size_6;

                            $BufferStock_6->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_6 - $request->size_6;
                            $qty_process = $BufferStock_6->qty_process - $decrement;

                            $BufferStock_6->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_8 = Size::where('size', '8')->first();
            $BufferStock_8 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_8->id
            ])->first();

            if ($BufferStock_8) {
                if ($BufferStock_8->qty_buffer != 0) {
                    if ($request->size_8 && $BufferStock_8->qty_avaliable != 0 && $request->size_8 > $temporary_storage->size_8) {
                        $increment = $request->size_8 - $temporary_storage->size_8;
                        $qty_avaliable = $BufferStock_8->qty_avaliable - $increment;
                        $qty_process = $BufferStock_8->qty_process + $increment;

                        $BufferStock_8->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_8 < $temporary_storage->size_8) {
                        if ($request->size_8 == 0) {
                            $qty_avaliable = $BufferStock_8->qty_avaliable + $temporary_storage->size_8;
                            $qty_process = $BufferStock_8->qty_process - $temporary_storage->size_8;

                            $BufferStock_8->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            return response()->json($request->size_8, 200);
                            $decrement = $temporary_storage->size_8 - $request->size_8;
                            $qty_avaliable = $BufferStock_8->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_8->qty_process - $decrement;

                            $BufferStock_8->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_8->qty_buffer == 0) {
                    if ($request->size_8 > $temporary_storage->size_8) {
                        $increment = $request->size_8 - $temporary_storage->size_8;
                        $qty_process = $BufferStock_8->qty_process + $increment;

                        $BufferStock_8->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_8 < $temporary_storage->size_8) {
                        if ($request->size_8 == 0) {
                            $qty_process = $BufferStock_8->qty_process - $temporary_storage->size_8;

                            $BufferStock_8->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_8 - $request->size_8;
                            $qty_process = $BufferStock_8->qty_process - $decrement;

                            $BufferStock_8->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_10 = Size::where('size', '10')->first();
            $BufferStock_10 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_10->id
            ])->first();

            if ($BufferStock_10) {
                if ($BufferStock_10->qty_buffer != 0) {
                    if ($request->size_10 && $BufferStock_10->qty_avaliable != 0 && $request->size_10 > $temporary_storage->size_10) {
                        $increment = $request->size_10 - $temporary_storage->size_10;
                        $qty_avaliable = $BufferStock_10->qty_avaliable - $request->size_10;
                        $qty_process = $BufferStock_10->qty_process + $request->size_10;

                        $BufferStock_10->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_10 < $temporary_storage->size_10) {
                        if ($request->size_10 == 0) {
                            $qty_avaliable = $BufferStock_10->qty_avaliable + $temporary_storage->size_10;
                            $qty_process = $BufferStock_10->qty_process - $temporary_storage->size_10;

                            $BufferStock_10->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_10 - $request->size_10;
                            $qty_avaliable = $BufferStock_10->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_10->qty_process - $decrement;

                            $BufferStock_10->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_10->qty_buffer == 0) {
                    if ($request->size_10 > $temporary_storage->size_10) {
                        $increment = $request->size_10 - $temporary_storage->size_10;
                        $qty_process = $BufferStock_10->qty_process + $increment;

                        $BufferStock_10->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_10 < $temporary_storage->size_10) {
                        if ($request->size_10 == 0) {
                            $qty_process = $BufferStock_10->qty_process - $temporary_storage->size_10;

                            $BufferStock_10->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_10 - $request->size_10;
                            $qty_process = $BufferStock_10->qty_process - $decrement;

                            $BufferStock_10->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_12 = Size::where('size', '12')->first();
            $BufferStock_12 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_12->id
            ])->first();

            if ($BufferStock_12) {
                if ($BufferStock_12->qty_buffer != 0) {
                    if ($request->size_12 && $BufferStock_12->qty_avaliable != 0 && $request->size_12 > $temporary_storage->size_12) {
                        $increment = $request->size_12 - $temporary_storage->size_12;
                        $qty_avaliable = $BufferStock_12->qty_avaliable - $increment;
                        $qty_process = $BufferStock_12->qty_process + $increment;

                        $BufferStock_12->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_12 < $temporary_storage->size_12) {
                        if ($request->size_12 == 0) {
                            $qty_avaliable = $BufferStock_12->qty_avaliable - $temporary_storage->size_12;
                            $qty_process = $BufferStock_12->qty_process + $temporary_storage->size_12;

                            $BufferStock_12->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->size_12 - $request->size_12;
                            $qty_avaliable = $BufferStock_12->qty_avaliable - $decrement;
                            $qty_process = $BufferStock_12->qty_process + $decrement;

                            $BufferStock_12->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_12->qty_buffer == 0) {
                    if ($request->size_12 > $temporary_storage->size_12) {
                        $increment = $request->size_12 - $temporary_storage->size_12;
                        $qty_process = $BufferStock_12->qty_process + $increment;

                        $BufferStock_12->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_12 < $temporary_storage->size_12) {
                        if ($request->size_12 == 0) {
                            $qty_process = $BufferStock_12->qty_process - $temporary_storage->size_12;

                            $BufferStock_12->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_12 - $request->size_12;
                            $qty_process = $BufferStock_12->qty_process - $decrement;

                            $BufferStock_12->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_27 = Size::where('size', '27')->first();
            $BufferStock_27 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_27->id
            ])->first();

            if ($BufferStock_27) {
                if ($BufferStock_27->qty_buffer != 0) {
                    if ($BufferStock_27->qty_avaliable != 0 && $request->size_27 > $temporary_storage->size_27) {
                        $increment = $request->size_27 - $temporary_storage->size_27;
                        $qty_avaliable = $BufferStock_27->qty_avaliable - $increment;
                        $qty_process = $BufferStock_27->qty_process + $increment;

                        $BufferStock_27->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_27 < $temporary_storage->size_27) {
                        if ($request->size_27 == 0) {
                            $qty_avaliable = $BufferStock_27->qty_avaliable + $temporary_storage->size_27;
                            $qty_process = $BufferStock_27->qty_process - $temporary_storage->size_27;

                            $BufferStock_27->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_27 - $request->size_27;
                            $qty_avaliable = $BufferStock_27->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_27->qty_process - $decrement;

                            $BufferStock_27->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_27->qty_buffer == 0) {
                    if ($request->size_27 > $temporary_storage->size_27) {
                        $increment = $request->size_27 - $temporary_storage->size_27;
                        $qty_process = $BufferStock_27->qty_process + $increment;

                        $BufferStock_27->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_27 < $temporary_storage->size_27) {
                        if ($request->size_27 == 0) {
                            $qty_process = $BufferStock_27->qty_process - $temporary_storage->size_27;

                            $BufferStock_27->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_27 - $request->size_27;
                            $qty_process = $BufferStock_27->qty_process - $decrement;

                            $BufferStock_27->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_28 = Size::where('size', '28')->first();
            $BufferStock_28 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_28->id
            ])->first();

            if ($BufferStock_28) {
                if ($BufferStock_28->qty_buffer != 0) {
                    if ($BufferStock_28->qty_avaliable != 0 && $request->size_28 > $temporary_storage->size_28) {
                        $increment = $request->size_28 - $temporary_storage->size_28;
                        $qty_avaliable = $BufferStock_28->qty_avaliable - $increment;
                        $qty_process = $BufferStock_28->qty_process + $increment;

                        $BufferStock_28->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_28 < $temporary_storage->size_28) {
                        if ($request->size_28 == 0) {
                            $qty_avaliable = $BufferStock_28->qty_avaliable + $temporary_storage->size_28;
                            $qty_process = $BufferStock_28->qty_process - $temporary_storage->size_28;

                            $BufferStock_28->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->size_28 - $request->size_28;
                            $qty_avaliable = $BufferStock_28->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_28->qty_process - $decrement;

                            $BufferStock_28->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_28->qty_buffer == 0) {
                    if ($request->size_28 > $temporary_storage->size_28) {
                        $increment = $request->size_28 - $temporary_storage->size_28;
                        $qty_process = $BufferStock_28->qty_process + $increment;

                        $BufferStock_28->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_28 < $temporary_storage->size_28) {
                        if ($request->size_28 == 0) {
                            $qty_process = $BufferStock_28->qty_process - $temporary_storage->size_28;

                            $BufferStock_28->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_27 - $request->size_27;
                            $qty_process = $BufferStock_28->qty_process - $decrement;

                            $BufferStock_28->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_29 = Size::where('size', '29')->first();
            $BufferStock_29 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_29->id
            ])->first();

            if ($BufferStock_29) {
                if ($BufferStock_29->qty_buffer != 0) {
                    if ($request->size_29 && $BufferStock_29->qty_avaliable != 0 && $request->size_29 > $temporary_storage->size_29) {
                        $increment = $request->size_29 - $temporary_storage->size_29;
                        $qty_avaliable = $BufferStock_29->qty_avaliable - $increment;
                        $qty_process = $BufferStock_29->qty_process + $increment;

                        $BufferStock_29->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_29 < $temporary_storage->size_29) {
                        if ($request->size_29 == 0) {
                            $qty_avaliable = $BufferStock_29->qty_avaliable - $temporary_storage->size_29;
                            $qty_process = $BufferStock_29->qty_process + $temporary_storage->size_29;

                            $BufferStock_29->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->size_29 - $request->size_29;
                            $qty_avaliable = $BufferStock_29->qty_avaliable - $decrement;
                            $qty_process = $BufferStock_29->qty_process + $decrement;

                            $BufferStock_29->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_29->qty_buffer == 0) {
                    if ($request->size_29 > $temporary_storage->size_29) {
                        $increment = $request->size_29 - $temporary_storage->size_29;
                        $qty_process = $BufferStock_29->qty_process + $increment;

                        $BufferStock_29->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_29 < $temporary_storage->size_29) {
                        if ($request->size_29 == 0) {
                            $qty_process = $BufferStock_29->qty_process - $temporary_storage->size_29;

                            $BufferStock_29->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_29 - $request->size_29;
                            $qty_process = $BufferStock_29->qty_process - $decrement;

                            $BufferStock_29->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_30 = Size::where('size', '30')->first();
            $BufferStock_30 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_30->id
            ])->first();

            if ($BufferStock_30) {
                if ($BufferStock_30->qty_buffer != 0) {
                    if ($request->size_30 && $BufferStock_30->qty_avaliable != 0 && $request->size_30 > $temporary_storage->size_30) {
                        $increment = $request->size_30 - $temporary_storage->size_30;
                        $qty_avaliable = $BufferStock_30->qty_avaliable - $increment;
                        $qty_process = $BufferStock_30->qty_process + $increment;

                        $BufferStock_30->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_30 < $temporary_storage->size_30) {
                        if ($request->size_30 == 0) {
                            $qty_avaliable = $BufferStock_30->qty_avaliable + $temporary_storage->size_30;
                            $qty_process = $BufferStock_30->qty_process - $temporary_storage->size_30;

                            $BufferStock_30->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->size_30 - $request->size_30;
                            $qty_avaliable = $BufferStock_30->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_30->qty_process - $decrement;

                            $BufferStock_30->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_30->qty_buffer == 0) {
                    if ($request->size_30 > $temporary_storage->size_30) {
                        $increment = $request->size_30 - $temporary_storage->size_30;
                        $qty_process = $BufferStock_30->qty_process + $increment;

                        $BufferStock_30->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_30 < $temporary_storage->size_30) {
                        if ($request->size_30 == 0) {
                            $qty_process = $BufferStock_30->qty_process - $temporary_storage->size_30;

                            $BufferStock_30->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_30 - $request->size_30;
                            $qty_process = $BufferStock_30->qty_process - $decrement;

                            $BufferStock_30->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_31 = Size::where('size', '31')->first();
            $BufferStock_31 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_31->id
            ])->first();

            if ($BufferStock_31) {
                if ($BufferStock_31->qty_buffer != 0) {
                    if ($request->size_31 && $BufferStock_31->qty_avaliable != 0 && $request->size_31 > $temporary_storage->size_31) {
                        $increment = $request->size_31 - $temporary_storage->size_31;
                        $qty_avaliable = $BufferStock_31->qty_avaliable - $increment;
                        $qty_process = $BufferStock_31->qty_process + $increment;

                        $BufferStock_31->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_31 < $temporary_storage->size_31) {
                        if ($request->size_31 == 0) {
                            $qty_avaliable = $BufferStock_31->qty_avaliable + $temporary_storage->size_31;
                            $qty_process = $BufferStock_31->qty_process - $temporary_storage->size_31;

                            $BufferStock_31->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->size_31 - $request->size_31;
                            $qty_avaliable = $BufferStock_31->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_31->qty_process - $decrement;

                            $BufferStock_31->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_31->qty_buffer == 0) {
                    if ($request->size_31 > $temporary_storage->size_31) {
                        $increment = $request->size_31 - $temporary_storage->size_31;
                        $qty_process = $BufferStock_31->qty_process + $increment;

                        $BufferStock_31->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_31 < $temporary_storage->size_31) {
                        if ($request->size_31 == 0) {
                            $qty_process = $BufferStock_31->qty_process - $temporary_storage->size_31;

                            $BufferStock_31->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_31 - $request->size_31;
                            $qty_process = $BufferStock_31->qty_process - $decrement;

                            $BufferStock_31->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_32 = Size::where('size', '32')->first();
            $BufferStock_32 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_32->id
            ])->first();

            if ($BufferStock_32) {
                if ($BufferStock_32->qty_buffer != 0) {
                    if ($request->size_32 && $BufferStock_32->qty_avaliable != 0 && $request->size_32 > $temporary_storage->size_32) {
                        $increment = $request->size_32 - $temporary_storage->size_32;
                        $qty_avaliable = $BufferStock_32->qty_avaliable - $increment;
                        $qty_process = $BufferStock_32->qty_process + $increment;

                        $BufferStock_32->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_32 < $temporary_storage->size_32) {
                        if ($request->size_32 == 0) {
                            $qty_avaliable = $BufferStock_32->qty_avaliable + $temporary_storage->size_32;
                            $qty_process = $BufferStock_32->qty_process - $temporary_storage->size_32;

                            $BufferStock_32->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->size_32 - $request->size_32;
                            $qty_avaliable = $BufferStock_32->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_32->qty_process - $decrement;

                            $BufferStock_32->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_32->qty_buffer == 0) {
                    if ($request->size_32 > $temporary_storage->size_32) {
                        $increment = $request->size_32 - $temporary_storage->size_32;
                        $qty_process = $BufferStock_32->qty_process + $increment;

                        $BufferStock_32->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_32 < $temporary_storage->size_32) {
                        if ($request->size_32 == 0) {
                            $qty_process = $BufferStock_32->qty_process - $temporary_storage->size_32;

                            $BufferStock_32->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_32 - $request->size_32;
                            $qty_process = $BufferStock_32->qty_process - $decrement;

                            $BufferStock_32->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_33 = Size::where('size', '33')->first();
            $BufferStock_33 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_33->id
            ])->first();

            if ($BufferStock_33) {
                if ($BufferStock_33->qty_buffer != 0) {
                    if ($request->size_33 && $BufferStock_33->qty_avaliable != 0 && $request->size_33 > $temporary_storage->size_33) {
                        $increment = $request->size_33 - $temporary_storage->size_33;
                        $qty_avaliable = $BufferStock_33->qty_avaliable - $increment;
                        $qty_process = $BufferStock_33->qty_process + $increment;

                        $BufferStock_33->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_33 < $temporary_storage->size_33) {
                        if ($request->size_33 == 0) {
                            $qty_avaliable = $BufferStock_33->qty_avaliable + $temporary_storage->size_33;
                            $qty_process = $BufferStock_33->qty_process - $temporary_storage->size_33;

                            $BufferStock_33->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->size_33 - $request->size_33;
                            $qty_avaliable = $BufferStock_33->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_33->qty_process - $decrement;

                            $BufferStock_33->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_33->qty_buffer == 0) {
                    if ($request->size_33 > $temporary_storage->size_33) {
                        $increment = $request->size_33 - $temporary_storage->size_33;
                        $qty_process = $BufferStock_33->qty_process + $increment;

                        $BufferStock_33->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_33 < $temporary_storage->size_33) {
                        if ($request->size_33 == 0) {
                            $qty_process = $BufferStock_33->qty_process - $temporary_storage->size_33;

                            $BufferStock_33->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_33 - $request->size_33;
                            $qty_process = $BufferStock_33->qty_process - $decrement;

                            $BufferStock_33->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_34 = Size::where('size', '34')->first();
            $BufferStock_34 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_34->id
            ])->first();

            if ($BufferStock_34) {
                if ($BufferStock_34->qty_buffer != 0) {
                    if ($request->size_34 && $BufferStock_34->qty_avaliable != 0 && $request->size_34 > $temporary_storage->size_34) {
                        $increment = $request->size_34 - $temporary_storage->size_34;
                        $qty_avaliable = $BufferStock_34->qty_avaliable - $increment;
                        $qty_process = $BufferStock_34->qty_process + $increment;

                        $BufferStock_34->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_34 < $temporary_storage->size_34) {
                        if ($request->size_34 == 0) {
                            $qty_avaliable = $BufferStock_34->qty_avaliable + $temporary_storage->size_34;
                            $qty_process = $BufferStock_34->qty_process - $temporary_storage->size_34;

                            $BufferStock_34->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->size_34 - $request->size_34;
                            $qty_avaliable = $BufferStock_34->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_34->qty_process - $decrement;

                            $BufferStock_34->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_34->qty_buffer == 0) {
                    if ($request->size_34 > $temporary_storage->size_34) {
                        $increment = $request->size_34 - $temporary_storage->size_34;
                        $qty_process = $BufferStock_34->qty_process + $increment;

                        $BufferStock_34->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_34 < $temporary_storage->size_34) {
                        if ($request->size_34 == 0) {
                            $qty_process = $BufferStock_34->qty_process - $temporary_storage->size_34;

                            $BufferStock_34->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_34 - $request->size_34;
                            $qty_process = $BufferStock_34->qty_process - $decrement;

                            $BufferStock_34->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_35 = Size::where('size', '35')->first();
            $BufferStock_35 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_35->id
            ])->first();

            if ($BufferStock_35) {
                if ($BufferStock_35->qty_buffer != 0) {
                    if ($request->size_35 && $BufferStock_35->qty_avaliable != 0 && $request->size_35 > $temporary_storage->size_35) {
                        $increment = $request->size_35 - $temporary_storage->size_35;
                        $qty_avaliable = $BufferStock_35->qty_avaliable - $increment;
                        $qty_process = $BufferStock_35->qty_process + $increment;

                        $BufferStock_35->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_35 < $temporary_storage->size_35) {
                        if ($request->size_35 == 0) {
                            $qty_avaliable = $BufferStock_35->qty_avaliable + $temporary_storage->size_35;
                            $qty_process = $BufferStock_35->qty_process - $temporary_storage->size_35;

                            $BufferStock_35->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->size_35 - $request->size_35;
                            $qty_avaliable = $BufferStock_35->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_35->qty_process - $decrement;

                            $BufferStock_35->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_35->qty_buffer == 0) {
                    if ($request->size_35 > $temporary_storage->size_35) {
                        $increment = $request->size_35 - $temporary_storage->size_35;
                        $qty_process = $BufferStock_35->qty_process + $increment;

                        $BufferStock_35->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_35 < $temporary_storage->size_35) {
                        if ($request->size_35 == 0) {
                            $qty_process = $BufferStock_35->qty_process - $temporary_storage->size_35;

                            $BufferStock_35->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_35 - $request->size_35;
                            $qty_process = $BufferStock_35->qty_process - $decrement;

                            $BufferStock_35->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_36 = Size::where('size', '36')->first();
            $BufferStock_36 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_36->id
            ])->first();

            if ($BufferStock_36) {
                if ($BufferStock_36->qty_buffer != 0) {
                    if ($request->size_36 && $BufferStock_36->qty_avaliable != 0 && $request->size_36 > $temporary_storage->size_36) {
                        $increment = $request->size_36 - $temporary_storage->size_36;
                        $qty_avaliable = $BufferStock_36->qty_avaliable - $increment;
                        $qty_process = $BufferStock_36->qty_process + $increment;

                        $BufferStock_36->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_36 < $temporary_storage->size_36) {
                        if ($request->size_36 == 0) {
                            $qty_avaliable = $BufferStock_36->qty_avaliable + $temporary_storage->size_36;
                            $qty_process = $BufferStock_36->qty_process - $temporary_storage->size_36;

                            $BufferStock_36->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->size_36 - $request->size_36;
                            $qty_avaliable = $BufferStock_36->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_36->qty_process - $decrement;

                            $BufferStock_36->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_36->qty_buffer == 0) {
                    if ($request->size_36 > $temporary_storage->size_36) {
                        $increment = $request->size_36 - $temporary_storage->size_36;
                        $qty_process = $BufferStock_36->qty_process + $increment;

                        $BufferStock_36->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_36 < $temporary_storage->size_36) {
                        if ($request->size_36 == 0) {
                            $qty_process = $BufferStock_36->qty_process - $temporary_storage->size_36;

                            $BufferStock_36->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_36 - $request->size_36;
                            $qty_process = $BufferStock_36->qty_process - $decrement;

                            $BufferStock_36->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_37 = Size::where('size', '37')->first();
            $BufferStock_37 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_37->id
            ])->first();

            if ($BufferStock_37) {
                if ($BufferStock_37->qty_buffer != 0) {
                    if ($request->size_37 && $BufferStock_37->qty_avaliable != 0 && $request->size_37 > $temporary_storage->size_37) {
                        $increment = $request->size_37 - $temporary_storage->size_37;
                        $qty_avaliable = $BufferStock_37->qty_avaliable - $increment;
                        $qty_process = $BufferStock_37->qty_process + $increment;

                        $BufferStock_37->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_37 < $temporary_storage->size_37) {
                        if ($request->size_37 == 0) {
                            $qty_avaliable = $BufferStock_37->qty_avaliable + $temporary_storage->size_37;
                            $qty_process = $BufferStock_37->qty_process - $temporary_storage->size_37;

                            $BufferStock_37->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->size_37 - $request->size_37;
                            $qty_avaliable = $BufferStock_37->qty_avaliable - $decrement;
                            $qty_process = $BufferStock_37->qty_process + $decrement;

                            $BufferStock_37->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_37->qty_buffer == 0) {
                    if ($request->size_37 > $temporary_storage->size_37) {
                        $increment = $request->size_37 - $temporary_storage->size_37;
                        $qty_process = $BufferStock_37->qty_process + $increment;

                        $BufferStock_37->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_37 < $temporary_storage->size_37) {
                        if ($request->size_37 == 0) {
                            $qty_process = $BufferStock_37->qty_process - $temporary_storage->size_37;

                            $BufferStock_37->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_37 - $request->size_37;
                            $qty_process = $BufferStock_37->qty_process - $decrement;

                            $BufferStock_37->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_38 = Size::where('size', '38')->first();
            $BufferStock_38 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_38->id
            ])->first();

            if ($BufferStock_38) {
                if ($BufferStock_38->qty_buffer != 0) {
                    if ($request->size_38 && $BufferStock_38->qty_avaliable != 0 && $request->size_38 > $temporary_storage->size_38) {
                        $increment = $request->size_38 - $temporary_storage->size_38;
                        $qty_avaliable = $BufferStock_38->qty_avaliable - $increment;
                        $qty_process = $BufferStock_38->qty_process + $increment;

                        $BufferStock_38->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_38 < $temporary_storage->size_38) {
                        if ($request->size_38 == 0) {
                            $qty_avaliable = $BufferStock_38->qty_avaliable + $temporary_storage->size_38;
                            $qty_process = $BufferStock_38->qty_process - $temporary_storage->size_38;

                            $BufferStock_38->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->size_38 - $request->size_38;
                            $qty_avaliable = $BufferStock_38->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_38->qty_process - $decrement;

                            $BufferStock_38->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_38->qty_buffer == 0) {
                    if ($request->size_38 > $temporary_storage->size_38) {
                        $increment = $request->size_38 - $temporary_storage->size_38;
                        $qty_process = $BufferStock_38->qty_process + $increment;

                        $BufferStock_38->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_38 < $temporary_storage->size_38) {
                        if ($request->size_38 == 0) {
                            $qty_process = $BufferStock_38->qty_process - $temporary_storage->size_38;

                            $BufferStock_38->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_38 - $request->size_38;
                            $qty_process = $BufferStock_38->qty_process - $decrement;

                            $BufferStock_38->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_39 = Size::where('size', '39')->first();
            $BufferStock_39 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_39->id
            ])->first();

            if ($BufferStock_39) {
                if ($BufferStock_39->qty_buffer != 0) {
                    if ($request->size_39 && $BufferStock_39->qty_avaliable != 0 && $request->size_39 > $temporary_storage->size_39) {
                        $increment = $request->size_39 - $temporary_storage->size_39;
                        $qty_avaliable = $BufferStock_39->qty_avaliable - $increment;
                        $qty_process = $BufferStock_39->qty_process + $increment;

                        $BufferStock_39->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_39 < $temporary_storage->size_39) {
                        if ($request->size_39 == 0) {
                            $qty_avaliable = $BufferStock_39->qty_avaliable + $temporary_storage->size_39;
                            $qty_process = $BufferStock_39->qty_process - $temporary_storage->size_39;

                            $BufferStock_39->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->size_39 - $request->size_39;
                            $qty_avaliable = $BufferStock_39->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_39->qty_process - $decrement;

                            $BufferStock_39->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_39->qty_buffer == 0) {
                    if ($request->size_39 > $temporary_storage->size_39) {
                        $increment = $request->size_39 - $temporary_storage->size_39;
                        $qty_process = $BufferStock_39->qty_process + $increment;

                        $BufferStock_39->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_39 < $temporary_storage->size_39) {
                        if ($request->size_39 == 0) {
                            $qty_process = $BufferStock_39->qty_process - $temporary_storage->size_39;

                            $BufferStock_39->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_39 - $request->size_39;
                            $qty_process = $BufferStock_39->qty_process - $decrement;

                            $BufferStock_39->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_40 = Size::where('size', '40')->first();
            $BufferStock_40 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_40->id
            ])->first();

            if ($BufferStock_40) {
                if ($BufferStock_40->qty_buffer != 0) {
                    if ($request->size_40 && $BufferStock_40->qty_avaliable != 0 && $request->size_40 > $temporary_storage->size_40) {
                        $increment = $request->size_40 - $temporary_storage->size_40;
                        $qty_avaliable = $BufferStock_40->qty_avaliable - $increment;
                        $qty_process = $BufferStock_40->qty_process + $increment;

                        $BufferStock_40->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_40 < $temporary_storage->size_40) {
                        if ($request->size_40 == 0) {
                            $qty_avaliable = $BufferStock_40->qty_avaliable + $temporary_storage->size_40;
                            $qty_process = $BufferStock_40->qty_process - $temporary_storage->size_40;

                            $BufferStock_40->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_40 - $request->size_40;
                            $qty_avaliable = $BufferStock_40->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_40->qty_process - $decrement;

                            $BufferStock_40->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_40->qty_buffer == 0) {
                    if ($request->size_40 > $temporary_storage->size_40) {
                        $increment = $request->size_40 - $temporary_storage->size_40;
                        $qty_process = $BufferStock_40->qty_process + $increment;

                        $BufferStock_40->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_40 < $temporary_storage->size_40) {
                        if ($request->size_40 == 0) {
                            $qty_process = $BufferStock_40->qty_process - $temporary_storage->size_40;

                            $BufferStock_40->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_40 - $request->size_40;
                            $qty_process = $BufferStock_40->qty_process - $decrement;

                            $BufferStock_40->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_41 = Size::where('size', '41')->first();
            $BufferStock_41 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_41->id
            ])->first();

            if ($BufferStock_41) {
                if ($BufferStock_41->qty_buffer != 0) {
                    if ($request->size_41 && $BufferStock_41->qty_avaliable != 0 && $request->size_41 > $temporary_storage->size_41) {
                        $increment = $request->size_41 - $temporary_storage->size_41;
                        $qty_avaliable = $BufferStock_41->qty_avaliable - $increment;
                        $qty_process = $BufferStock_41->qty_process + $increment;

                        $BufferStock_41->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_41 < $temporary_storage->size_41) {
                        if ($request->size_41 == 0) {
                            $qty_avaliable = $BufferStock_41->qty_avaliable + $temporary_storage->size_41;
                            $qty_process = $BufferStock_41->qty_process - $temporary_storage->size_41;

                            $BufferStock_41->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->size_41 - $request->size_41;
                            $qty_avaliable = $BufferStock_41->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_41->qty_process - $decrement;

                            $BufferStock_41->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_41->qty_buffer == 0) {
                    if ($request->size_41 > $temporary_storage->size_41) {
                        $increment = $request->size_41 - $temporary_storage->size_41;
                        $qty_process = $BufferStock_41->qty_process + $increment;

                        $BufferStock_41->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_41 < $temporary_storage->size_41) {
                        if ($request->size_41 == 0) {
                            $qty_process = $BufferStock_41->qty_process - $temporary_storage->size_41;

                            $BufferStock_41->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_41 - $request->size_41;
                            $qty_process = $BufferStock_41->qty_process - $decrement;

                            $BufferStock_41->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

            $size_42 = Size::where('size', '42')->first();
            $BufferStock_42 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_42->id
            ])->first();

            if ($BufferStock_42) {
                if ($BufferStock_42->qty_buffer != 0) {
                    if ($request->size_42 && $BufferStock_42->qty_avaliable != 0 && $request->size_42 > $temporary_storage->size_42) {
                        $increment = $request->size_42 - $temporary_storage->size_42;
                        $qty_avaliable = $BufferStock_42->qty_avaliable - $increment;
                        $qty_process = $BufferStock_42->qty_process + $increment;

                        $BufferStock_42->update([
                            'qty_avaliable' => $qty_avaliable,
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_42 < $temporary_storage->size_42) {
                        if ($request->size_42 == 0) {
                            $qty_avaliable = $BufferStock_42->qty_avaliable + $temporary_storage->size_42;
                            $qty_process = $BufferStock_42->qty_process - $temporary_storage->size_42;

                            $BufferStock_42->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        } else {

                            $decrement = $temporary_storage->size_42 - $request->size_42;
                            $qty_avaliable = $BufferStock_42->qty_avaliable + $decrement;
                            $qty_process = $BufferStock_42->qty_process - $decrement;

                            $BufferStock_42->update([
                                'qty_avaliable' => $qty_avaliable,
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                } elseif ($BufferStock_42->qty_buffer == 0) {
                    if ($request->size_42 > $temporary_storage->size_42) {
                        $increment = $request->size_42 - $temporary_storage->size_42;
                        $qty_process = $BufferStock_42->qty_process + $increment;

                        $BufferStock_42->update([
                            'qty_process' => $qty_process
                        ]);
                    } elseif ($request->size_42 < $temporary_storage->size_42) {
                        if ($request->size_42 == 0) {
                            $qty_process = $BufferStock_42->qty_process - $temporary_storage->size_42;

                            $BufferStock_42->update([
                                'qty_process' => $qty_process
                            ]);
                        } else {
                            $decrement = $temporary_storage->size_42 - $request->size_42;
                            $qty_process = $BufferStock_42->qty_process - $decrement;

                            $BufferStock_42->update([
                                'qty_process' => $qty_process
                            ]);
                        }
                    }
                }
            }

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
            $detailCart = TemporaryStorage::where('id', $id)->first();
            $clothes = Clothes::where('id', $detailCart->clothes_id)->first();

            DB::beginTransaction();

            $size_s = Size::where('size', 's')->first();
            $BufferStock_s = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_s->id
            ])->first();

            if ($BufferStock_s) {
                if ($BufferStock_s->qty_buffer > 0) {
                    $qty_avaliable = $BufferStock_s->qty_avaliable + $detailCart->size_s;
                    $qty_process = $BufferStock_s->qty_process - $detailCart->size_s;

                    $BufferStock_s->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_s->qty_buffer == 0) {
                    $qty_process = $BufferStock_s->qty_process - $detailCart->size_s;

                    $BufferStock_s->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_m = Size::where('size', 'm')->first();
            $BufferStock_m = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_m->id
            ])->first();

            if ($BufferStock_m) {
                if ($BufferStock_m->qty_buffer > 0) {
                    $qty_avaliable = $BufferStock_m->qty_avaliable + $detailCart->size_m;
                    $qty_process = $BufferStock_m->qty_process - $detailCart->size_m;

                    $BufferStock_m->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_m->qty_buffer == 0) {
                    $qty_process = $BufferStock_m->qty_process - $detailCart->size_m;

                    $BufferStock_m->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_l = Size::where('size', 'l')->first();
            $BufferStock_l = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_l->id
            ])->first();

            if ($BufferStock_l) {
                if ($BufferStock_l->qty_buffer > 0) {
                    $qty_avaliable = $BufferStock_l->qty_avaliable + $detailCart->size_l;
                    $qty_process = $BufferStock_l->qty_process - $detailCart->size_l;

                    $BufferStock_l->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_l->qty_buffer == 0) {
                    $qty_process = $BufferStock_l->qty_process - $detailCart->size_l;

                    $BufferStock_l->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_xl = Size::where('size', 'xl')->first();
            $BufferStock_xl = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_xl->id
            ])->first();

            if ($BufferStock_xl) {
                if ($BufferStock_xl->qty_buffer > 0) {
                    $qty_avaliable = $BufferStock_xl->qty_avaliable + $detailCart->size_xl;
                    $qty_process = $BufferStock_xl->qty_process - $detailCart->size_xl;

                    $BufferStock_xl->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_xl->qty_buffer == 0) {
                    $qty_process = $BufferStock_xl->qty_process - $detailCart->size_xl;

                    $BufferStock_xl->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_xxl = Size::where('size', 'xxl')->first();
            $BufferStock_xxl = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_xxl->id
            ])->first();

            if ($BufferStock_xxl) {
                if ($BufferStock_xxl->qty_buffer > 0) {
                    $qty_avaliable = $BufferStock_xxl->qty_avaliable + $detailCart->size_xxl;
                    $qty_process = $BufferStock_xxl->qty_process - $detailCart->size_xxl;

                    $BufferStock_xl->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_xxl->qty_buffer == 0) {
                    $qty_process = $BufferStock_xxl->qty_process - $detailCart->size_xxl;

                    $BufferStock_xl->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_xxxl = Size::where('size', 'xxxl')->first();
            $BufferStock_xxxl = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_xxxl->id
            ])->first();

            if ($BufferStock_xxxl) {
                if ($BufferStock_xxxl->qty_buffer > 0) {
                    
                    $qty_avaliable = $BufferStock_xxxl->qty_avaliable + $detailCart->size_xxxl;
                    $qty_process = $BufferStock_xxxl->qty_process - $detailCart->size_xxxl;

                    $BufferStock_xxxl->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_xxxl->qty_buffer == 0) {
                    $qty_process = $BufferStock_xxxl->qty_process - $detailCart->size_xxxl;

                    $BufferStock_xxxl->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_2 = Size::where('size', '2')->first();
            $BufferStock_2 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_2->id
            ])->first();

            if ($BufferStock_2) {
                if ($BufferStock_2->qty_buffer > 0) {
                    $qty_avaliable = $BufferStock_2->qty_avaliable + $detailCart->size_2;
                    $qty_process = $BufferStock_2->qty_process - $detailCart->size_2;

                    $BufferStock_2->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_2->qty_buffer == 0) {
                    $qty_process = $BufferStock_2->qty_process - $detailCart->size_2;

                    $BufferStock_2->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_4 = Size::where('size', '4')->first();
            $BufferStock_4 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_4->id
            ])->first();

            if ($BufferStock_4) {
                if ($BufferStock_4->qty_buffer > 0) {
                    $qty_avaliable = $BufferStock_4->qty_avaliable + $detailCart->size_4;
                    $qty_process = $BufferStock_4->qty_process - $detailCart->size_4;

                    $BufferStock_4->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_4->qty_buffer == 0) {
                    $qty_process = $BufferStock_4->qty_process - $detailCart->size_4;

                    $BufferStock_4->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_6 = Size::where('size', '6')->first();
            $BufferStock_6 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_6->id
            ])->first();

            if ($BufferStock_6) {
                if ($BufferStock_6->qty_buffer > 0) {
                    $qty_avaliable = $BufferStock_6->qty_avaliable + $detailCart->size_6;
                    $qty_process = $BufferStock_6->qty_process - $detailCart->size_6;

                    $BufferStock_6->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_6->qty_buffer == 0) {
                    $qty_process = $BufferStock_6->qty_process - $detailCart->size_6;

                    $BufferStock_6->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_8 = Size::where('size', '8')->first();
            $BufferStock_8 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_8->id
            ])->first();

            if ($BufferStock_8) {
                if ($BufferStock_8->qty_buffer > 0) {
                    $qty_avaliable = $BufferStock_8->qty_avaliable + $detailCart->size_8;
                    $qty_process = $BufferStock_8->qty_process - $detailCart->size_8;

                    $BufferStock_8->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_8->qty_buffer == 0) {
                    $qty_process = $BufferStock_8->qty_process - $detailCart->size_8;

                    $BufferStock_8->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_10 = Size::where('size', '10')->first();
            $BufferStock_10 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_10->id
            ])->first();

            if ($BufferStock_10) {
                if ($BufferStock_10->qty_buffer > 0) {
                    $qty_avaliable = $BufferStock_10->qty_avaliable + $detailCart->size_10;
                    $qty_process = $BufferStock_10->qty_process - $detailCart->size_10;

                    $BufferStock_10->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_10->qty_buffer == 0) {
                    $qty_process = $BufferStock_10->qty_process - $detailCart->size_10;

                    $BufferStock_10->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_12 = Size::where('size', '12')->first();
            $BufferStock_12 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_12->id
            ])->first();

            if ($BufferStock_12) {
                if ($BufferStock_12->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_12->qty_avaliable + $detailCart->size_12;
                    $qty_process = $BufferStock_12->qty_process - $detailCart->size_12;

                    $BufferStock_12->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_12->qty_buffer == 0) {
                    $qty_process = $BufferStock_12->qty_process - $detailCart->size_12;

                    $BufferStock_12->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_27 = Size::where('size', '27')->first();
            $BufferStock_27 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_27->id
            ])->first();

            if ($BufferStock_27) {
                if ($BufferStock_27->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_27->qty_avaliable + $detailCart->size_27;
                    $qty_process = $BufferStock_27->qty_process - $detailCart->size_27;

                    $BufferStock_27->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_27->qty_buffer == 0) {
                    $qty_process = $BufferStock_27->qty_process - $detailCart->size_27;

                    $BufferStock_27->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_28 = Size::where('size', '28')->first();
            $BufferStock_28 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_28->id
            ])->first();

            if ($BufferStock_28) {
                if ($BufferStock_28->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_28->qty_avaliable + $detailCart->size_28;
                    $qty_process = $BufferStock_28->qty_process - $detailCart->size_28;

                    $BufferStock_28->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_28->qty_buffer == 0) {
                    $qty_process = $BufferStock_28->qty_process - $detailCart->size_28;

                    $BufferStock_28->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_29 = Size::where('size', '29')->first();
            $BufferStock_29 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_29->id
            ])->first();

            if ($BufferStock_29) {
                if ($BufferStock_29->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_29->qty_avaliable + $detailCart->size_29;
                    $qty_process = $BufferStock_29->qty_process - $detailCart->size_29;

                    $BufferStock_29->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_29->qty_buffer == 0) {
                    $qty_process = $BufferStock_29->qty_process - $detailCart->size_29;

                    $BufferStock_29->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_30 = Size::where('size', '30')->first();
            $BufferStock_30 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_30->id
            ])->first();

            if ($BufferStock_30) {
                if ($BufferStock_30->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_30->qty_avaliable + $detailCart->size_30;
                    $qty_process = $BufferStock_30->qty_process - $detailCart->size_30;

                    $BufferStock_30->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_30->qty_buffer == 0) {
                    $qty_process = $BufferStock_30->qty_process - $detailCart->size_30;

                    $BufferStock_30->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_30 = Size::where('size', '30')->first();
            $BufferStock_30 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_30->id
            ])->first();

            if ($BufferStock_30) {
                if ($BufferStock_30->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_30->qty_avaliable + $detailCart->size_30;
                    $qty_process = $BufferStock_30->qty_process - $detailCart->size_30;

                    $BufferStock_30->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_30->qty_buffer == 0) {
                    $qty_process = $BufferStock_30->qty_process - $detailCart->size_30;

                    $BufferStock_30->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_31 = Size::where('size', '31')->first();
            $BufferStock_31 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_31->id
            ])->first();

            if ($BufferStock_31) {
                if ($BufferStock_31->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_31->qty_avaliable + $detailCart->size_31;
                    $qty_process = $BufferStock_31->qty_process - $detailCart->size_31;

                    $BufferStock_31->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_31->qty_buffer == 0) {
                    $qty_process = $BufferStock_31->qty_process - $detailCart->size_31;

                    $BufferStock_31->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_32 = Size::where('size', '32')->first();
            $BufferStock_32 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_32->id
            ])->first();

            if ($BufferStock_32) {
                if ($BufferStock_32->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_32->qty_avaliable + $detailCart->size_32;
                    $qty_process = $BufferStock_32->qty_process - $detailCart->size_32;

                    $BufferStock_32->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_32->qty_buffer == 0) {
                    $qty_process = $BufferStock_32->qty_process - $detailCart->size_32;

                    $BufferStock_32->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_33 = Size::where('size', '33')->first();
            $BufferStock_33 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_33->id
            ])->first();

            if ($BufferStock_33) {
                if ($BufferStock_33->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_33->qty_avaliable + $detailCart->size_33;
                    $qty_process = $BufferStock_33->qty_process - $detailCart->size_33;

                    $BufferStock_33->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_33->qty_buffer == 0) {
                    $qty_process = $BufferStock_33->qty_process - $detailCart->size_33;

                    $BufferStock_33->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_34 = Size::where('size', '34')->first();
            $BufferStock_34 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_34->id
            ])->first();

            if ($BufferStock_34) {
                if ($BufferStock_34->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_34->qty_avaliable + $detailCart->size_34;
                    $qty_process = $BufferStock_34->qty_process - $detailCart->size_34;

                    $BufferStock_34->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_34->qty_buffer == 0) {
                    $qty_process = $BufferStock_34->qty_process - $detailCart->size_34;

                    $BufferStock_34->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_35 = Size::where('size', '35')->first();
            $BufferStock_35 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_35->id
            ])->first();

            if ($BufferStock_35) {
                if ($BufferStock_35->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_35->qty_avaliable + $detailCart->size_35;
                    $qty_process = $BufferStock_35->qty_process - $detailCart->size_35;

                    $BufferStock_35->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_35->qty_buffer == 0) {
                    $qty_process = $BufferStock_35->qty_process - $detailCart->size_35;

                    $BufferStock_35->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_36 = Size::where('size', '36')->first();
            $BufferStock_36 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_36->id
            ])->first();

            if ($BufferStock_36) {
                if ($BufferStock_36->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_36->qty_avaliable + $detailCart->size_36;
                    $qty_process = $BufferStock_36->qty_process - $detailCart->size_36;

                    $BufferStock_36->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_36->qty_buffer == 0) {
                    $qty_process = $BufferStock_36->qty_process - $detailCart->size_36;

                    $BufferStock_36->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_37 = Size::where('size', '37')->first();
            $BufferStock_37 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_37->id
            ])->first();

            if ($BufferStock_37) {
                if ($BufferStock_37->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_37->qty_avaliable + $detailCart->size_37;
                    $qty_process = $BufferStock_37->qty_process - $detailCart->size_37;

                    $BufferStock_37->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_37->qty_buffer == 0) {
                    $qty_process = $BufferStock_37->qty_process - $detailCart->size_37;

                    $BufferStock_37->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_38 = Size::where('size', '38')->first();
            $BufferStock_38 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_38->id
            ])->first();

            if ($BufferStock_38) {
                if ($BufferStock_38->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_38->qty_avaliable + $detailCart->size_38;
                    $qty_process = $BufferStock_38->qty_process - $detailCart->size_38;

                    $BufferStock_38->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_38->qty_buffer == 0) {
                    $qty_process = $BufferStock_38->qty_process - $detailCart->size_38;

                    $BufferStock_38->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_39 = Size::where('size', '39')->first();
            $BufferStock_39 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_39->id
            ])->first();

            if ($BufferStock_39) {
                if ($BufferStock_39->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_39->qty_avaliable + $detailCart->size_39;
                    $qty_process = $BufferStock_39->qty_process - $detailCart->size_39;

                    $BufferStock_39->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_39->qty_buffer == 0) {
                    $qty_process = $BufferStock_39->qty_process - $detailCart->size_39;

                    $BufferStock_39->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_40 = Size::where('size', '40')->first();
            $BufferStock_40 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_40->id
            ])->first();

            if ($BufferStock_40) {
                if ($BufferStock_40->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_40->qty_avaliable + $detailCart->size_40;
                    $qty_process = $BufferStock_40->qty_process - $detailCart->size_40;

                    $BufferStock_40->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_40->qty_buffer == 0) {
                    $qty_process = $BufferStock_40->qty_process - $detailCart->size_40;

                    $BufferStock_40->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_41 = Size::where('size', '41')->first();
            $BufferStock_41 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_41->id
            ])->first();

            if ($BufferStock_41) {
                if ($BufferStock_41->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_41->qty_avaliable + $detailCart->size_41;
                    $qty_process = $BufferStock_41->qty_process - $detailCart->size_41;

                    $BufferStock_41->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_41->qty_buffer == 0) {
                    $qty_process = $BufferStock_41->qty_process - $detailCart->size_41;

                    $BufferStock_41->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

            $size_42 = Size::where('size', '42')->first();
            $BufferStock_42 = BufferProduct::where([
                'clothes_id' => $detailCart->clothes_id,
                'size_id' => $size_42->id
            ])->first();

            if ($BufferStock_42) {
                if ($BufferStock_42->qty_buffer > 0) {    
                    $qty_avaliable = $BufferStock_42->qty_avaliable + $detailCart->size_42;
                    $qty_process = $BufferStock_42->qty_process - $detailCart->size_42;

                    $BufferStock_42->update([
                        'qty_avaliable' => $qty_avaliable,
                        'qty_proccess' => $qty_process
                    ]);
                } elseif ($BufferStock_42->qty_buffer == 0) {
                    $qty_process = $BufferStock_42->qty_process - $detailCart->size_42;

                    $BufferStock_42->update([
                        'qty_proccess' => $qty_process
                    ]);
                }
            }

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
