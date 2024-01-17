<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClothesRequest;
use App\{Size, Type, Clothes, BufferProduct};

class ClothesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clothes = Clothes::select([
                                    'id',
                                    'entity_name','article_name',
                                    'color','material',
                                    'combo','special_feature',
                                    'keyword','description','slug',
                                    'group_article','type_id','is_active',
                                ])
                            ->when(request()->search, fn ($query) =>
                                $query->where('article_name', 'LIKE', "%".request()->search."%")
                            )
                            ->with([
                                    'Partnumber' => fn ($query) =>
                                        $query->select('clothes_id', 'partnumber'),
                                    'Image' => fn ($query) =>
                                        $query->select('clothes_id', 'photo')
                                ])
                            ->orderBy('id', 'ASC')
                            ->paginate(10);

        foreach ($clothes as $singleClothes) {
            $singleClothes->combo = explode(', ', $singleClothes->combo);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'success get data',
            'data' => $clothes
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
                    'type_id' => $type->id
                ]);

                $dataPartnumbers = explode(', ', $request->partnumber);

                $bulkPartnumber = collect($dataPartnumbers)->map(function ($query) use ($request, $clothes) {
                    return [
                        "clothes_id" => $clothes->id,
                        "image_id" => $request->image_id,
                        "partnumber" => $query
                    ];
                })->toArray();

                DB::table('partnumbers')->insert($bulkPartnumber);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'success create data',
                'data' => $clothes,
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

        $clothes = Clothes::select(['id','entity_name','article_name','color','material','combo','special_feature','keyword','description','slug','group_article','type_id','is_active'])
                            ->with([
                                    'Partnumber' => fn ($query) =>
                                        $query->select('clothes_id', 'partnumber'),
                                    'Image' => fn ($query) =>
                                        $query->select('clothes_id', 'photo')
                                ])
                            ->where('id', '=', $clothes)
                            ->first();

        $clothes->combo = explode(', ', $clothes->combo);


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

            $req = [
                'entity_name' => ($request->entity_name) ? $request->entity_name : $clothes->entity_name,
                'article_name' => ($request->article_name) ? $request->article_name : $clothes->article_name,
                'color' => ($request->color) ? $request->color : $clothes->color,
                'material' => ($request->material) ? $request->material : $clothes->material,
                'combo' => ($request->combo) ? $request->combo : $clothes->combo,
                'special_feature' => ($request->special_feature) ? $request->special_feature : $clothes->special_feature,
                'keyword' => ($request->keyword) ? $request->keyword : $clothes->keyword,
                'description' => ($request->description) ? $request->description : $clothes->description,
                'slug' => ($request->product_name) ? $request->product_name : $clothes->slug,
                'type_id' => ($request->type) ? $type->id : $clothes->type_id
            ];

            $clothes->update([
                'entity_name' => $req['entity_name'],
                'article_name' => $req['article_name'],
                'color' => $req['color'],
                'material' => $req['material'],
                'combo' => $req['combo'],
                'special_feature' => $req['special_feature'],
                'keyword' => $req['keyword'],
                'description' => $req['description'],
                'slug' => $req['slug'],
                'type_id' => $req['type_id']
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
    public function destroy(Clothes $clothes)
    {
        try {
            $clothes->delete();

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

    public function totalOrder()
    {
        try {
            $theData = Clothes::with('BufferProduct.Size')->get();

            foreach ($theData as $data) {
                $data['total'] = BufferProduct::where('clothes_id', $data->id)->sum('qty_process');
            }

            return response()->json([
                'status' => 'success',
                'message' => 'success to get data',
                'data' => $theData
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get data',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
