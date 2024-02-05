<?php

namespace App\Http\Controllers\Api\Client\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function getProduct()
    {
        try {
            $searchName = request()->search_name;

            $products = Product::select(
                                'id',
                                'entity_name',
                                'article_name',
                                'color',
                                'combo',
                                'material',
                                'keyword',
                                'description',
                                'price'
                            )->where('group_article', '=', function ($query) {
                                $query->select('id')
                                    ->from('events')
                                    ->where('is_active', '=', true)
                                    ->first();
                            })
                            ->when($searchName, function ($query) use ($searchName) {
                                $query->where('article_name', 'like', "%$searchName%");
                            })
                            ->with(['Photo' => function ($query) {
                                $query->select('id', 'product_id', 'photo');
                            }])
                            ->paginate(30);

            foreach ($products as $product) {
                $product->combo = explode(', ', $product->combo);
            }

            return response()->json([
                'status' => 'success',
                'data' => $products,
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
}
