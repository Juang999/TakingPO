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
    protected function input_buffer($clothes_id, $size_id, $buffer)
    {
        BufferProduct::create([
            'clothes_id' => $clothes_id,
            'size_id' => $size_id,
            'qty_avaliable' => $buffer,
            'qty_buffer' => $buffer
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clothess = Clothes::orderBy('type_id', 'DESC')->with('Type', 'Image')->get();

        foreach ($clothess as $clothes) {
            $clothes->combo = explode(',', $clothes->combo);
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
                ]);

                if ($clothes->size_s > 0) {
                    $size_s = Size::firstOrCreate([
                        'size' => 's'
                    ]);
                    $this->input_buffer($clothes->id, $size_s->id, $request->buffer);
                }

                if ($clothes->size_m > 0) {
                    $size_m = Size::firstOrCreate([
                        'size' => 'm'
                    ]);
                    $this->input_buffer($clothes->id, $size_m->id, $request->buffer);
                }

                if ($clothes->size_l > 0) {
                    $size_l = Size::firstOrCreate([
                        'size' => 'l'
                    ]);
                    $this->input_buffer($clothes->id, $size_l->id, $request->buffer);
                }

                if ($clothes->size_xl > 0) {
                    $size_xl = Size::firstOrCreate([
                        'size' => 'xl'
                    ]);
                    $this->input_buffer($clothes->id, $size_xl->id, $request->buffer);
                }

                if ($clothes->size_xxl > 0) {
                    $size_xxl = Size::firstOrCreate([
                        'size' => 'xxl'
                    ]);
                    $this->input_buffer($clothes->id, $size_xxl->id, $request->buffer);
                }

                if ($clothes->size_xxxl > 0) {
                    $size_xxxl = Size::firstOrCreate([
                        'size' => 'xxxl'
                    ]);
                    $this->input_buffer($clothes->id, $size_xxxl->id, $request->buffer);
                }

                if ($clothes->size_2 > 0) {
                    $size_2 = Size::firstOrCreate([
                        'size' => '2'
                    ]);
                    $this->input_buffer($clothes->id, $size_2->id, $request->buffer);
                }

                if ($clothes->size_4 > 0) {
                    $size_4 = Size::firstOrCreate([
                        'size' => '4'
                    ]);
                    $this->input_buffer($clothes->id, $size_4->id, $request->buffer);
                }

                if ($clothes->size_6 > 0) {
                    $size_6 = Size::firstOrCreate([
                        'size' => '6'
                    ]);
                    $this->input_buffer($clothes->id, $size_6->id, $request->buffer);
                }

                if ($clothes->size_8 > 0) {
                    $size_8 = Size::firstOrCreate([
                        'size' => '8'
                    ]);
                    $this->input_buffer($clothes->id, $size_8->id, $request->buffer);
                }

                if ($clothes->size_10 > 0) {
                    $size_10 = Size::firstOrCreate([
                        'size' => '10'
                    ]);
                    $this->input_buffer($clothes->id, $size_10->id, $request->buffer);
                }

                if ($clothes->size_12 > 0) {
                    $size_12 = Size::firstOrCreate([
                        'size' => '10'
                    ]);
                    $this->input_buffer($clothes->id, $size_12->id, $request->buffer);
                }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'success create data',
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

        return response()->json([
            'status' => 'success',
            'message' => 'success get data',
            'data' => Clothes::where('id', $clothes)->with('Type', 'Image')->first()
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ClothesRequest $request, $id)
    {
        try {
            $clothes = Clothes::findOrFail($id);

            $type = Type::where('type', $request->type)->firstOrFail();

            $clothes->update([
                'entity_name' => $request->entity_name,
                'article_name' => $request->article_name,
                'color' => $request->color,
                'material' => $request->material,
                'combo' => $request->combo,
                'special_feature' => $request->special_feature,
                'keyword' => $request->keyword,
                'description' => $request->description,
                'slug' => $request->product_name,
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
                'created_at' => now(),
                'updated_at' => now(),
            ]);

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
