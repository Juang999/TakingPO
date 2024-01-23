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
                            )->when($searchName, function ($query) use ($searchName) {
                                $query->where('article_name', 'like', "%$searchName%");
                            })
                            ->with('Photo')
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
