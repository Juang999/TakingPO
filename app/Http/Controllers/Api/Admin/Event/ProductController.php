<?php

namespace App\Http\Controllers\Api\Admin\Event;

use App\Models\{Product, PartnumberProduct, BufferProduct};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Clothes\{CreateClothesRequest, UpdateClothesRequest};
// use App\Models\;

class ProductController extends Controller
{
    public function getAllProduct()
    {
        try {
            $clothes = Product::select([
                                    'products.id',
                                    'products.entity_name',
                                    'products.article_name',
                                    'products.color',
                                    'products.material',
                                    'products.combo',
                                    'products.special_feature',
                                    'products.keyword',
                                    'products.description',
                                    'products.slug',
                                    'products.group_article',
                                    'products.type_id',
                                    'products.is_active',
                                    DB::raw("(SELECT photo FROM images WHERE clothes_id = products.id LIMIT 1 OFFSET 0) AS photo"),
                                    DB::raw("CASE WHEN partnumber_products.partnumber IS NULL THEN '-' ELSE partnumber_products.partnumber END AS partnumber")
                                ])->when(request()->searchname, fn($query) =>
                                    $query->where('article_name', 'LIKE', '%'.request()->searchname.'%')
                                )
                                ->leftJoin('partnumber_products', 'partnumber_products.product_id', '=', 'products.id')
                                ->paginate(10);

            foreach ($clothes as $clothing) {
                $clothing->combo = explode(', ', $clothing->combo);
            }

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

    public function getDetailProduct($id)
    {
        try {
            $clothes = Product::select(
                                    'products.id',
                                    'products.entity_name',
                                    'products.article_name',
                                    'products.color',
                                    'products.material',
                                    'products.combo',
                                    'products.special_feature',
                                    'products.keyword',
                                    'products.description',
                                    'products.slug',
                                    'products.group_article',
                                    DB::raw("CASE WHEN types.type IS NULL THEN '-' ELSE types.type END AS type"),
                                    'partnumber_products.partnumber',
                                    'products.is_active'
                                )->leftJoin('types', 'types.id', '=', 'products.type_id')
                                ->leftJoin('partnumber_products', 'partnumber_products.product_id', '=', 'products.id')
                                ->where('products.id', '=', $id)
                                ->with([
                                        'Photo' => function ($query) {
                                            $query->select('id', 'product_id', 'photo');
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

    public function storeProduct(CreateClothesRequest $request)
    {
        try {
            DB::beginTransaction();
                $slug = implode('-', explode(' ', $request->article_name));

                $clothes = Product::create([
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

    public function updateProduct(UpdateClothesRequest $request, $id)
    {
        try {
            $req = $this->checkRequest($request, $id);

            Product::where('id', '=', $id)->update([
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
        $clothes = Product::where('id', '=', $id)->first();
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
        PartnumberProduct::create([
            'product_id' => $clothesId,
            'partnumber' => $partnumber
        ]);
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
}
