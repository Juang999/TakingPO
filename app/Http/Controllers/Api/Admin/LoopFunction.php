<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\{Size, BufferProduct, PriceList};

class LoopFunction extends Controller
{
    public function totalClient($tableName, $entityName)
    {
        $data = DB::table($tableName)->whereIn('clothes_id', function ($query)  use ($entityName) {
            $query->select('id')
                ->from('clothes')
                ->where('entity_name', $entityName)
                ->get();
        })->sum(DB::raw('size_s + size_m + size_l + size_xl + size_xxl + size_xxxl + size_2 + size_4 + size_6 + size_8 + size_10 + size_12 + size_27 + size_28 + size_29 + size_30 + size_31 + size_32 + size_33 + size_34 + size_35 + size_36 + size_37 + size_38 + size_39 + size_40 + size_41 + size_42 + other'));

        return $data;
    }

    public function totalNominalClient($tableName, $entityName, $discount)
    {
        $data = DB::table($tableName)->selectRaw('(
        (('.$tableName.'.size_s * clothes.size_s) - ('.$tableName.'.size_s * clothes.size_s * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_m * clothes.size_m) - ('.$tableName.'.size_m * clothes.size_m * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_l * clothes.size_l) - ('.$tableName.'.size_l * clothes.size_l * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_xl * clothes.size_xl) - ('.$tableName.'.size_xl * clothes.size_xl * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_xxl * clothes.size_xxl) - ('.$tableName.'.size_xxl * clothes.size_xxl * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_xxxl * clothes.size_xxxl) - ('.$tableName.'.size_xxxl * clothes.size_xxxl * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_2 * clothes.size_2) - ('.$tableName.'.size_2 * clothes.size_2 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_4 * clothes.size_4) - ('.$tableName.'.size_4 * clothes.size_4 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_6 * clothes.size_6) - ('.$tableName.'.size_6 * clothes.size_6 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_8 * clothes.size_8) - ('.$tableName.'.size_8 * clothes.size_8 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_10 * clothes.size_10) - ('.$tableName.'.size_10 * clothes.size_10 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_12 * clothes.size_12) - ('.$tableName.'.size_12 * clothes.size_12 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_27 * clothes.size_27) - ('.$tableName.'.size_27 * clothes.size_27 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_28 * clothes.size_28) - ('.$tableName.'.size_28 * clothes.size_28 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_29 * clothes.size_29) - ('.$tableName.'.size_29 * clothes.size_29 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_30 * clothes.size_30) - ('.$tableName.'.size_30 * clothes.size_30 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_31 * clothes.size_31) - ('.$tableName.'.size_31 * clothes.size_31 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_32 * clothes.size_32) - ('.$tableName.'.size_32 * clothes.size_32 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_33 * clothes.size_33) - ('.$tableName.'.size_33 * clothes.size_33 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_34 * clothes.size_34) - ('.$tableName.'.size_34 * clothes.size_34 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_35 * clothes.size_35) - ('.$tableName.'.size_35 * clothes.size_35 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_36 * clothes.size_36) - ('.$tableName.'.size_36 * clothes.size_36 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_37 * clothes.size_37) - ('.$tableName.'.size_37 * clothes.size_37 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_38 * clothes.size_38) - ('.$tableName.'.size_38 * clothes.size_38 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_39 * clothes.size_39) - ('.$tableName.'.size_39 * clothes.size_39 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_40 * clothes.size_40) - ('.$tableName.'.size_40 * clothes.size_40 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_41 * clothes.size_41) - ('.$tableName.'.size_41 * clothes.size_41 * '.$discount->discount.' / 100)) +
        (('.$tableName.'.size_42 * clothes.size_42) - ('.$tableName.'.size_42 * clothes.size_42 * '.$discount->discount.' / 100))
        ) AS total')->join('clothes', $tableName.'.clothes_id', '=', 'clothes.id')
        ->whereIn('clothes_id', function ($query)  use ($entityName) {
            $query->select('id')
                ->from('clothes')
                ->where('entity_name', $entityName)
                ->get();
        })->sum('total');

        return $data;
    }

    public function createClothes($theSize, $areaId, $clothesId, $productPrice, $bufferStock)
    {
        if ($productPrice > 0) {
            $size = Size::firstOrCreate([
                'size' => strtoupper($theSize)
            ]);

            PriceList::create([
                'area_id' => $areaId,
                'clothes_id' => $clothesId,
                'size_id' => $size->id,
                'price' => $productPrice
            ]);

            BufferProduct::create([
                'clothes_id' => $clothesId,
                'size_id' => $size->id,
                'qty_avaliable' => $bufferStock,
                'qty_buffer' => $bufferStock
            ]);
        }
    }
}
