<?php

namespace App\Http\Controllers\Api;

use App\BufferProduct;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Clothes;
use App\Http\Requests\ClothesRequest;
use App\Size;
use App\Type;
use Illuminate\Support\Facades\DB;

class ClothesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clothess = Clothes::orderBy('type_id', 'DESC')->with('Type', 'Image', 'BufferProduct.Size')->get();

        foreach ($clothess as $clothes) {
            $clothes->combo = explode(',', $clothes->combo);
            $clothes->size_2 = explode(',', $clothes->size_2);
            $clothes->size_4 = explode(',', $clothes->size_4);
            $clothes->size_6 = explode(',', $clothes->size_6);
            $clothes->size_8 = explode(',', $clothes->size_8);
            $clothes->size_10 = explode(',', $clothes->size_10);
            $clothes->size_12 = explode(',', $clothes->size_12);
            $clothes->BufferProduct->makeHidden(['created_at', 'updated_at', 'qty_avaliable', 'qty_process']);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'success get data',
            'data' => $clothess
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClothesRequest $request)
    {
        try {
            $type = Type::firstOrCreate([
                'type' => $request->type
            ]);

            DB::beginTransaction();
                $clothes = Clothes::create([
                    'entity_name' => $request->entity_name,
                    'article_name' => $request->article_name,
                    'color' => $request->color,
                    'material' => $request->material,
                    'combo' => $request->combo,
                    'special_feature' => $request->special_feature,
                    'keyword' => $request->keyword,
                    'description' => $request->description,
                    'slug' => $request->article_name,
                    'group_article' => $request->group_article,
                    'type_id' => $type->id,
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
                ]);

                if ($clothes->size_s > 0) {
                    $size_s = Size::firstOrCreate([
                        'size' => strtoupper('s')
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_s->id,
                        'qty_avaliable' => $request->bs_size_s,
                        'qty_buffer' => $request->bs_size_s
                    ]);
                }

                if ($clothes->size_m > 0) {
                    $size_m = Size::firstOrCreate([
                        'size' => strtoupper('m')
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_m->id,
                        'qty_avaliable' => $request->bs_size_m,
                        'qty_buffer' => $request->bs_size_m
                    ]);
                }

                if ($clothes->size_l > 0) {
                    $size_l = Size::firstOrCreate([
                        'size' => strtoupper('l')
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_l->id,
                        'qty_avaliable' => $request->bs_size_l,
                        'qty_buffer' => $request->bs_size_l
                    ]);
                }

                if ($clothes->size_xl > 0) {
                    $size_xl = Size::firstOrCreate([
                        'size' => strtoupper('xl')
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_xl->id,
                        'qty_avaliable' => $request->bs_size_xl,
                        'qty_buffer' => $request->bs_size_xl
                    ]);
                }

                if ($clothes->size_xxl > 0) {
                    $size_xxl = Size::firstOrCreate([
                        'size' => strtoupper('xxl')
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_xxl->id,
                        'qty_avaliable' => $request->bs_size_xxl,
                        'qty_buffer' => $request->bs_size_xxl
                    ]);
                }

                if ($clothes->size_xxxl > 0) {
                    $size_xxxl = Size::firstOrCreate([
                        'size' => strtoupper('xxxl')
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_xxxl->id,
                        'qty_avaliable' => $request->bs_size_xxxl,
                        'qty_buffer' => $request->bs_size_xxxl
                    ]);
                }

                if ($clothes->size_2 > 0) {
                    $size_2 = Size::firstOrCreate([
                        'size' => '2'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_2->id,
                        'qty_avaliable' => $request->bs_size_2,
                        'qty_buffer' => $request->bs_size_2
                    ]);
                }

                if ($clothes->size_4 > 0) {
                    $size_4 = Size::firstOrCreate([
                        'size' => '4'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_4->id,
                        'qty_avaliable' => $request->bs_size_4,
                        'qty_buffer' => $request->bs_size_4
                    ]);
                }

                if ($clothes->size_6 > 0) {
                    $size_6 = Size::firstOrCreate([
                        'size' => '6'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_6->id,
                        'qty_avaliable' => $request->bs_size_6,
                        'qty_buffer' => $request->bs_size_6
                    ]);
                }

                if ($clothes->size_8 > 0) {
                    $size_8 = Size::firstOrCreate([
                        'size' => '8'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_8->id,
                        'qty_avaliable' => $request->bs_size_8,
                        'qty_buffer' => $request->bs_size_8
                    ]);
                }

                if ($clothes->size_10 > 0) {
                    $size_10 = Size::firstOrCreate([
                        'size' => '10'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_10->id,
                        'qty_avaliable' => $request->bs_size_10,
                        'qty_buffer' => $request->bs_size_10
                    ]);
                }

                if ($clothes->size_12 > 0) {
                    $size_12 = Size::firstOrCreate([
                        'size' => '12'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_12->id,
                        'qty_avaliable' => $request->bs_size_12,
                        'qty_buffer' => $request->bs_size_12
                    ]);
                }

                if ($clothes->size_27 > 0) {
                    $size_27 = Size::firstOrCreate([
                        'size' => '27'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_27->id,
                        'qty_avaliable' => $request->bs_size_27,
                        'qty_buffer' => $request->bs_size_27
                    ]);
                }

                if ($clothes->size_28 > 0) {
                    $size_28 = Size::firstOrCreate([
                        'size' => '28'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_28->id,
                        'qty_avaliable' => $request->bs_size_28,
                        'qty_buffer' => $request->bs_size_28
                    ]);
                }

                if ($clothes->size_29 > 0) {
                    $size_29 = Size::firstOrCreate([
                        'size' => '29'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_29->id,
                        'qty_avaliable' => $request->bs_size_29,
                        'qty_buffer' => $request->bs_size_29
                    ]);
                }

                if ($clothes->size_30 > 0) {
                    $size_30 = Size::firstOrCreate([
                        'size' => '30'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_30->id,
                        'qty_avaliable' => $request->bs_size_30,
                        'qty_buffer' => $request->bs_size_30
                    ]);
                }

                if ($clothes->size_31 > 0) {
                    $size_31 = Size::firstOrCreate([
                        'size' => '31'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_31->id,
                        'qty_avaliable' => $request->bs_size_31,
                        'qty_buffer' => $request->bs_size_31
                    ]);
                }

                if ($clothes->size_32 > 0) {
                    $size_32 = Size::firstOrCreate([
                        'size' => '32'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_32->id,
                        'qty_avaliable' => $request->bs_size_32,
                        'qty_buffer' => $request->bs_size_32
                    ]);
                }

                if ($clothes->size_33 > 0) {
                    $size_33 = Size::firstOrCreate([
                        'size' => '33'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_33->id,
                        'qty_avaliable' => $request->bs_size_33,
                        'qty_buffer' => $request->bs_size_33
                    ]);
                }

                if ($clothes->size_34 > 0) {
                    $size_34 = Size::firstOrCreate([
                        'size' => '34'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_34->id,
                        'qty_avaliable' => $request->bs_size_34,
                        'qty_buffer' => $request->bs_size_34
                    ]);
                }

                if ($clothes->size_35 > 0) {
                    $size_35 = Size::firstOrCreate([
                        'size' => '35'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_35->id,
                        'qty_avaliable' => $request->bs_size_35,
                        'qty_buffer' => $request->bs_size_35
                    ]);
                }

                if ($clothes->size_36 > 0) {
                    $size_36 = Size::firstOrCreate([
                        'size' => '36'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_36->id,
                        'qty_avaliable' => $request->bs_size_36,
                        'qty_buffer' => $request->bs_size_36
                    ]);
                }

                if ($clothes->size_37 > 0) {
                    $size_37 = Size::firstOrCreate([
                        'size' => '37'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_37->id,
                        'qty_avaliable' => $request->bs_size_37,
                        'qty_buffer' => $request->bs_size_37
                    ]);
                }

                if ($clothes->size_38 > 0) {
                    $size_38 = Size::firstOrCreate([
                        'size' => '38'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_38->id,
                        'qty_avaliable' => $request->bs_size_38,
                        'qty_buffer' => $request->bs_size_38
                    ]);
                }

                if ($clothes->size_39 > 0) {
                    $size_39 = Size::firstOrCreate([
                        'size' => '39'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_39->id,
                        'qty_avaliable' => $request->bs_size_39,
                        'qty_buffer' => $request->bs_size_39
                    ]);
                }

                if ($clothes->size_40 > 0) {
                    $size_40 = Size::firstOrCreate([
                        'size' => '40'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_40->id,
                        'qty_avaliable' => $request->bs_size_40,
                        'qty_buffer' => $request->bs_size_40
                    ]);
                }

                if ($clothes->size_41 > 0) {
                    $size_41 = Size::firstOrCreate([
                        'size' => '41'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_41->id,
                        'qty_avaliable' => $request->bs_size_41,
                        'qty_buffer' => $request->bs_size_41
                    ]);
                }

                if ($clothes->size_42 > 0) {
                    $size_42 = Size::firstOrCreate([
                        'size' => '42'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $size_42->id,
                        'qty_avaliable' => $request->bs_size_42,
                        'qty_buffer' => $request->bs_size_42
                    ]);
                }

                if ($clothes->other > 0) {
                    $other = Size::firstOrCreate([
                        'size' => 'other'
                    ]);
                    BufferProduct::create([
                        'clothes_id' => $clothes->id,
                        'size_id' => $other->id,
                        'qty_avaliable' => $request->other,
                        'qty_buffer' => $request->other
                    ]);
                }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'success create data',
                'clothes_id' => $clothes->id
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'error create data',
                'data' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($clothes)
    {
        if (!Clothes::find($clothes)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'data not found!'
            ], 404);
        }

        $clothes = Clothes::where('id', $clothes)->with('Type', 'Image', 'BufferProduct.Size')->first();

        $clothes->combo = explode(',', $clothes->combo);
        $clothes->size_2 = explode(',', $clothes->size_2);
        $clothes->size_4 = explode(',', $clothes->size_4);
        $clothes->size_6 = explode(',', $clothes->size_6);
        $clothes->size_8 = explode(',', $clothes->size_8);
        $clothes->size_10 = explode(',', $clothes->size_10);
        $clothes->size_12 = explode(',', $clothes->size_12);


        return response()->json([
            'status' => 'success',
            'message' => 'success get data',
            'data' => $clothes
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Clothes $clothes)
    {
        try {
            $type = Type::where('type', $request->type)->first();

            DB::beginTransaction();

            $clothes->update([
                'entity_name' => ($request->entity_name) ? $request->entity_name : $clothes->entity_name,
                'article_name' => ($request->article_name) ? $request->article_name : $clothes->article_name,
                'color' => ($request->color) ? $request->color : $clothes->color,
                'material' => ($request->material) ? $request->material : $clothes->material,
                'combo' => ($request->combo) ? $request->combo : $clothes->combo,
                'special_feature' => ($request->special_feature) ? $request->special_feature : $clothes->special_feature,
                'keyword' => ($request->keyword) ? $request->keyword : $clothes->keyword,
                'description' => ($request->description) ? $request->description : $clothes->description,
                'slug' => ($request->product_name) ? $request->product_name : $clothes->slug,
                'type_id' => ($request->type) ? $type->id : $clothes->type_id,
                'size_s' => ($request->size_s) ? $request->size_s : $clothes->size_s,
                'size_m' => ($request->size_m) ? $request->size_m : $clothes->size_m,
                'size_l' => ($request->size_l) ? $request->size_l : $clothes->size_l,
                'size_xl' => ($request->size_xl) ? $request->size_xl : $clothes->size_xl,
                'size_xxl' => ($request->size_xxl) ? $request->size_xxl : $clothes->size_xxl,
                'size_xxxl' => ($request->size_xxxl) ? $request->size_xxxl : $clothes->size_xxxl,
                'size_2' => ($request->size_2) ? $request->size_2 : $clothes->size_2,
                'size_4' => ($request->size_4) ? $request->size_4 : $clothes->size_4,
                'size_6' => ($request->size_6) ? $request->size_6 : $clothes->size_6,
                'size_8' => ($request->size_6) ? $request->size_6 : $clothes->size_6,
                'size_10' => ($request->size_10) ? $request->size_10 : $clothes->size_10,
                'size_12' => ($request->size_12) ? $request->size_12 : $clothes->size_12,
                'size_27' => ($request->size_27) ? $request->size_27 : $clothes->size_27,
                'size_28' => ($request->size_28) ? $request->size_28 : $clothes->size_28,
                'size_29' => ($request->size_29) ? $request->size_29 : $clothes->size_29,
                'size_30' => ($request->size_30) ? $request->size_30 : $clothes->size_30,
                'size_31' => ($request->size_31) ? $request->size_31 : $clothes->size_31,
                'size_32' => ($request->size_32) ? $request->size_32 : $clothes->size_32,
                'size_33' => ($request->size_33) ? $request->size_33 : $clothes->size_33,
                'size_34' => ($request->size_34) ? $request->size_34 : $clothes->size_34,
                'size_35' => ($request->size_35) ? $request->size_35 : $clothes->size_35,
                'size_36' => ($request->size_36) ? $request->size_36 : $clothes->size_36,
                'size_37' => ($request->size_37) ? $request->size_37 : $clothes->size_37,
                'size_38' => ($request->size_38) ? $request->size_38 : $clothes->size_38,
                'size_39' => ($request->size_39) ? $request->size_39 : $clothes->size_39,
                'size_40' => ($request->size_40) ? $request->size_40 : $clothes->size_40,
                'size_41' => ($request->size_41) ? $request->size_41 : $clothes->size_41,
                'size_42' => ($request->size_42) ? $request->size_42 : $clothes->size_42,
                'other' => ($request->other) ? $request->other : $clothes->other,
                'created_at' => $clothes->created_at,
                'updated_at' => now(),
            ]);

            $size_s = Size::where('size', 'S')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_s->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_s) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_s;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_s;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_m = Size::where('size', 'M')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_m->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_m) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_m;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_m;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_l = Size::where('size', 'L')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_l->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_l) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_l;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_l;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_xl = Size::where('size', 'XL')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_xl->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_xl) {

                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_xl;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_xl;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_xxl = Size::where('size', 'XXL')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_xxl->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_xxl) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_xxl;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_xxl;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_xxxl = Size::where('size', 'XXXL')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_xxxl->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_xxxl) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_s;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_s;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_2 = Size::where('size', '2')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_2->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_2) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_2;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_2;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_4 = Size::where('size', '4')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_4->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_4) {
                $avaliable = $BufferStock->qty_avaliable + $request->bs_size_4;
                $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_4;

                $BufferStock->update([
                    'qty_avaliable' => $avaliable,
                    'qty_buffer' => $buffer_stock
                ]);
                }
            }

            $size_6 = Size::where('size', '6')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_6->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_6) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_6;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_6;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_8 = Size::where('size', '8')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_8->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_8) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_8;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_8;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_10 = Size::where('size', '10')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_10->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_10) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_10;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_10;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_12 = Size::where('size', '12')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_12->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_12) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_12;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_12;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_27 = Size::where('size', '27')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_27->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_27) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_27;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_27;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_28 = Size::where('size', '28')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_28->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_28) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_28;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_28;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_29 = Size::where('size', '29')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_29->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_29) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_29;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_29;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_30 = Size::where('size', '30')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_30->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_30) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_30;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_30;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_31 = Size::where('size', '31')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_31->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_31) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_31;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_31;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_32 = Size::where('size', '32')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_32->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_32) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_32;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_32;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_33 = Size::where('size', '33')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_33->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_33) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_33;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_33;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_34 = Size::where('size', '34')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_34->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_34) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_34;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_34;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_35 = Size::where('size', '35')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_35->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_35) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_35;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_35;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_36 = Size::where('size', '36')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_36->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_36) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_36;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_36;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_37 = Size::where('size', '37')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_37->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_37) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_37;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_37;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_38 = Size::where('size', '38')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_38->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_38) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_38;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_38;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_39 = Size::where('size', '39')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_39->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_39) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_39;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_39;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_40 = Size::where('size', '40')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_40->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_40) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_40;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_40;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_41 = Size::where('size', '41')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_41->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_41) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_41;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_41;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $size_42 = Size::where('size', '42')->first();
            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
                'size_id' => $size_42->id
            ])->first();

            if ($BufferStock) {
                if ($request->bs_size_42) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_size_42;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_size_42;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            $BufferStock = BufferProduct::where([
                'clothes_id' => $clothes->id,
            ])->first();

            if ($BufferStock) {
                if ($request->bs_other) {
                    $avaliable = $BufferStock->qty_avaliable + $request->bs_other;
                    $buffer_stock = $BufferStock->qty_buffer + $request->bs_other;

                    $BufferStock->update([
                        'qty_avaliable' => $avaliable,
                        'qty_buffer' => $buffer_stock
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'data updated!',
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'update data fail',
                'error' => $th->getMessage()
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
        try {
            Clothes::find($id)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'data deleted'
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
