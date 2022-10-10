<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoopFunction extends Controller
{
    public function totalClient($tableName, $entityName)
    {
        $data = DB::table($tableName)->whereIn('clothes_id', function ($query)  use ($entityName) {
            $query->select('id')
                ->from('clothes')
                ->where('entity_name', $entityName)
                ->get();
        })->sum(DB::raw('size_s + size_m + size_l + size_xl + size_xxl + size_xxxl + size_2 + size_4 + size_6 + size_8 + size_10 + size_12 + size_27 + size_28 + size_29 + size_30 + size_31 + size_32 + size_33 + size_34 + size_35 + size_36 + size_37 + size_38 + size_39 + size_40 + size_41 + size_42'));

        return $data;
    }
}
