<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WilayahController extends Controller
{
    protected $url = 'http://www.emsifa.com/api-wilayah-indonesia/api';

    public function index()
    {
        try {
            $responseProvince = Http::get('http://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
            $lastResponseProvince = $responseProvince->json();

            $data = [];
            for ($i=0; $i < count($lastResponseProvince); $i++) {
                $data[$i]['province_id'] = $lastResponseProvince[$i]['id'];
                $data[$i]['province_name'] = $lastResponseProvince[$i]['name'];

                $responseRegency = Http::get('http://www.emsifa.com/api-wilayah-indonesia/api/regencies/'.$lastResponseProvince[$i]['id'].'.json');
                $lastResponseRegency = $responseRegency->json();

                for ($j=0; $j < count($lastResponseRegency); $j++) {
                    $data[$i]['regencies'][$j]['id_province'] = $lastResponseRegency[$j]['province_id'];
                    $data[$i]['regencies'][$j]['regency_name'] = $lastResponseRegency[$j]['name'];

                    $responseDistrict = Http::get('http://www.emsifa.com/api-wilayah-indonesia/api/districts/'.$lastResponseRegency[$j]['id'].'.json');
                    $lastResponseDistrict = $responseDistrict->json();

                    $data[$i]['regencies'][$j]['districts'] = $lastResponseDistrict;
                    // for ($k=0; $k < count($lastResponseDistrict); $k++) {
                    //     $data[$i]['regencies'][$j]['districts'][$k]['id_regency'] = $lastResponseDistrict[$k]['regency_id'];
                    //     $data[$i]['regencies'][$j]['districts'][$k]['district_name'] = $lastResponseDistrict[$k]['name'];
                    // }
                }

            }

            dd($data);

            // return response()->json([
            //     'data' => $lastResponse['name']
            // ]);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
