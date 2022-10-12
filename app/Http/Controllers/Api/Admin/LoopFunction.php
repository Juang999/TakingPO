<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\{Size, BufferProduct};

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

    public function totalNominalClient($tableName, $entityName, $clothes, $discount)
    {
        $data = DB::table($tableName)->selectRaw('(
        ('.$tableName.'.size_s * clothes.size_s * '.$discount.' / 100) +
        ('.$tableName.'.size_m * clothes.size_m * '.$discount.' / 100) +
        ('.$tableName.'.size_l * clothes.size_l * '.$discount.' / 100) +
        ('.$tableName.'.size_xl * clothes.size_xl * '.$discount.' / 100) +
        ('.$tableName.'.size_xxl * clothes.size_xxl * '.$discount.' / 100) +
        ('.$tableName.'.size_xxxl * clothes.size_xxxl * '.$discount.' / 100) +
        ('.$tableName.'.size_2 * clothes.size_2 * '.$discount.' / 100) +
        ('.$tableName.'.size_4 * clothes.size_4 * '.$discount.' / 100) +
        ('.$tableName.'.size_6 * clothes.size_6 * '.$discount.' / 100) +
        ('.$tableName.'.size_8 * clothes.size_8 * '.$discount.' / 100) +
        ('.$tableName.'.size_10 * clothes.size_10 * '.$discount.' / 100) +
        ('.$tableName.'.size_12 * clothes.size_12 * '.$discount.' / 100) +
        ('.$tableName.'.size_27 * clothes.size_27 * '.$discount.' / 100) +
        ('.$tableName.'.size_s * clothes.size_s * '.$discount.' / 100))')->whereIn('clothes_id', function ($query)  use ($entityName) {
            $query->select('id')
                ->from('clothes')
                ->where('entity_name', $entityName)
                ->get();
        });

        return $data;
    }

    public function createClothes($id, $theSize, $clothesSize, $bufferStock)
    {
        if ($clothesSize > 0) {
            $size = Size::firstOrCreate([
                'size' => strtoupper($theSize)
            ]);
            BufferProduct::create([
                'clothes_id' => $id,
                'size_id' => $size->id,
                'qty_avaliable' => $bufferStock,
                'qty_buffer' => $bufferStock
            ]);
        }
    }
}
