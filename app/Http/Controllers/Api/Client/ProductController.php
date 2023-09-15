<?php

namespace App\Http\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\{Clothes, Image};

class ProductController extends Controller
{
    public function show ($partnumber) {
        try {
            $description = Clothes::select('clothes.id', 'clothes.entity_name', 'clothes.article_name', 'clothes.color', 'clothes.material', 'clothes.combo', 'clothes.special_feature', 'clothes.keyword', 'clothes.description', 'clothes.slug', 'clothes.group_article', 'clothes.type_id', 'images.photo')
                                    ->leftJoin('images', 'images.clothes_id', '=', 'clothes.id')
                                    ->where('clothes.id', '=', function ($query) use ($partnumber) {
                                        $query->select('clothes_id')
                                            ->from('partnumbers')
                                            ->where('partnumber', '=', $partnumber)
                                            ->first();
                                    })
                                    ->first();

            if ($description == null) {
                $description = [
                    "id" => 0,
                    "entity_name" => "-",
                    "article_name" => "-",
                    "color" => "-",
                    "material" => "-",
                    "combo" => "-",
                    "special_feature" => "-",
                    "keyword" => "-",
                    "description" => "-",
                    "slug" => "-",
                    "group_article" => 0,
                    "type_id" => 0,
                    "photo" => '-'
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => $description,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage()
            ]);
        }
    }
}
