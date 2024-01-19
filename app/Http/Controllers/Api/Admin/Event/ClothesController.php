<?php

namespace App\Http\Controllers\Api\Admin\Event;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\{BufferProduct, Clothes, Size, Partnumber};
use App\Http\Requests\Admin\Clothes\{CreateClothesRequest, UpdateClothesRequest};

class ClothesController extends Controller
{
    public function getAllClothes()
    {
        try {
            $clothes = Clothes::select([
                                    'clothes.id',
                                    'clothes.entity_name',
                                    'clothes.article_name',
                                    'clothes.color',
                                    'clothes.material',
                                    'clothes.combo',
                                    'clothes.special_feature',
                                    'clothes.keyword',
                                    'clothes.description',
                                    'clothes.slug',
                                    'clothes.group_article',
                                    'clothes.type_id',
                                    'clothes.is_active',
                                    DB::raw("(SELECT photo FROM images WHERE clothes_id = clothes.id LIMIT 1 OFFSET 0) AS photo"),
                                    DB::raw("CASE WHEN partnumbers.partnumber IS NULL THEN '-' ELSE partnumbers.partnumber END AS partnumber")
                                ])->when(request()->searchname, fn($query) =>
                                    $query->where('article_name', 'LIKE', '%'.request()->searchname.'%')
                                )
                                ->leftJoin('partnumbers', 'partnumbers.clothes_id', '=', 'clothes.id')
                                ->paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $clothes,
                'error' => null
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage()
            ]);
        }
    }

    public function getDetailClothes($id)
    {
        try {
            $clothes = Clothes::select(
                                    'clothes.id',
                                    'clothes.entity_name',
                                    'clothes.article_name',
                                    'clothes.color',
                                    'clothes.material',
                                    'clothes.combo',
                                    'clothes.special_feature',
                                    'clothes.keyword',
                                    'clothes.description',
                                    'clothes.slug',
                                    'clothes.group_article',
                                    DB::raw("CASE WHEN types.type IS NULL THEN '-' ELSE types.type END AS type"),
                                    'partnumbers.partnumber',
                                    'clothes.is_active'
                                )->leftJoin('types', 'types.id', '=', 'clothes.type_id')
                                ->leftJoin('partnumbers', 'partnumbers.clothes_id', '=', 'clothes.id')
                                ->where('clothes.id', '=', $id)
                                ->with([
                                        'Image' => function ($query) {
                                            $query->select('id', 'clothes_id', 'photo');
                                        },
                                        'BufferProduct' => function ($query) {
                                            $query->select(
                                                        'buffer_products.clothes_id',
                                                        'sizes.size',
                                                        'buffer_products.size_id',
                                                        'buffer_products.qty_avaliable',
                                                    )->leftJoin('sizes', 'sizes.id', '=', 'buffer_products.size_id');
                                        }
                                    ])
                                ->first();

            return response()->json([
                'status' => 'success',
                'data' => $clothes,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function store(CreateClothesRequest $request)
    {
        try {
            DB::beginTransaction();
                $slug = implode('-', explode(' ', $request->article_name));

                $clothes = Clothes::create([
                    'entity_name' => $request->entity_name,
                    'article_name' => $request->article_name,
                    'color' => $request->color,
                    'material' => $request->material,
                    'combo' => $request->combo,
                    'special_feature' => $request->special_feature,
                    'keyword' => $request->keyword,
                    'description' => $request->description,
                    'slug' => $slug,
                    'group_article' => $request->group_article,
                    'type_id' => $request->type_id,
                    'is_active' => 1,
                    'price' => $request->price
                ]);

                $this->inputPartnumber($clothes->id, $request->partnumber);
                $this->inputBufferStock($request->stock, $clothes->id);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $clothes,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage()
            ], 400);
        }
    }

    private function inputBufferStock($stock, $clothesId)
    {
        BufferProduct::create([
            'clothes_id' => $clothesId,
            'qty_avaliable' => $stock,
            'qty_process' => 0,
            'qty_buffer' => 0
        ]);
    }

    public function updateClothes(UpdateClothesRequest $request, $id)
    {
        try {
            $req = $this->checkRequest($request, $id);

            Clothes::where('id', '=', $id)->update([
                'entity_name' => $req['entity_name'],
                'article_name' => $req['article_name'],
                'color' => $req['color'],
                'material' => $req['material'],
                'combo' => $req['combo'],
                'special_feature' => $req['special_feature'],
                'keyword' => $req['keyword'],
                'description' => $req['description'],
                'slug' => $req['slug'],
                'group_article' => $req['group_article'],
                'type_id' => $req['type_id'],
                'is_active' => $req['is_active'],
            ]);

            return response()->json([
                'status' => 'success',
                'data' => true,
                'error' => null
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage()
            ]);
        }
    }

    private function checkRequest($request, $id)
    {
        $clothes = Clothes::where('id', '=', $id)->first();
        $slug = implode('-', explode(' ', strtolower(($request->article_name) ? $request->article_name : $clothes->article_name)));

        return [
            "entity_name" => ($request->entity_name) ? $request->entity_name : $clothes->entity_name,
            "article_name" => ($request->article_name) ? $request->article_name : $clothes->article_name,
            "color" => ($request->color) ? $request->color : $clothes->color,
            "material" => ($request->material) ? $request->material : $clothes->material,
            "combo" => ($request->combo) ? $request->combo : $clothes->combo,
            "special_feature" => ($request->special_feature) ? $request->special_feature : $clothes->special_feature,
            "keyword" => ($request->keyword) ? $request->keyword : $clothes->keyword,
            "description" => ($request->description) ? $request->description : $clothes->description,
            "slug" => ($request->article_name) ? $slug : $clothes->slug,
            "group_article" => ($request->group_article) ? $request->group_article : $clothes->group_article,
            "type_id" => ($request->type_id) ? $request->type_id : $clothes->type_id,
            "is_active" => ($request->is_active) ? $request->is_active : $clothes->is_active,
        ];
    }

    private function inputPartnumber($clothesId, $partnumber)
    {
        Partnumber::create([
            'clothes_id' => $clothesId,
            'partnumber' => $partnumber
        ]);
    }

    private function getSizeId($size)
    {
        $sizeId = Size::where('size', '=', $size)->first('id');
        return $sizeId->id;
    }
}
