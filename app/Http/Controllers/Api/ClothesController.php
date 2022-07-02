<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Clothes;
use App\Http\Requests\ClothesRequest;
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
                DB::table('clothes')->insert([
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
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

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
