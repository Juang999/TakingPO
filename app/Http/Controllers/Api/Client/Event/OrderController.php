<?php

namespace App\Http\Controllers\Api\Client\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Product, Chart, Order, Distributor};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Requests\Client\Order\{InputOrderRequest, UpdateOrderRequest};

class OrderController extends Controller
{
    public function getProduct()
    {
        try {
            $searchName = request()->search_name;

            $products = Product::select(
                                'products.id',
                                'entity_name',
                                'article_name',
                                'color',
                                'combo',
                                'material',
                                'type_id',
                                'types.type as type_name',
                                'keyword',
                                'description',
                                'price'
                            )->leftJoin('types', 'types.id', '=', 'products.type_id')
                            ->where('products.group_article', '=', function ($query) {
                                $query->select('id')
                                    ->from('events')
                                    ->where('is_active', '=', true)
                                    ->first();
                            })
                            ->when($searchName, function ($query) use ($searchName) {
                                $query->where('products.article_name', 'like', "%$searchName%");
                            })
                            ->with(['Photo' => function ($query) {
                                $query->select('id', 'product_id', 'photo');
                            }])
                            ->paginate(1);

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

    public function inputIntoChart(InputOrderRequest $request)
    {
        try {
            $headerPhone = request()->header('phone');
            $request = $this->checkRequestCreate($request, $headerPhone);

            $inputChart = Chart::create([
                'client_id' => $request['client_id'],
                'event_id' => $request['event_id'],
                'session_id' => $request['session_id'],
                'product_id' => $request['product_id'],
                'size_S' => $request['size_S'],
                'size_M' => $request['size_M'],
                'size_L' => $request['size_L'],
                'size_XL' => $request['size_XL'],
                'size_XXL' => $request['size_XXL'],
                'size_XXXL' => $request['size_XXXL'],
                'size_2' => $request['size_2'],
                'size_4' => $request['size_4'],
                'size_6' => $request['size_6'],
                'size_8' => $request['size_8'],
                'size_10' => $request['size_10'],
                'size_12' => $request['size_12'],
                'size_27' => $request['size_27'],
                'size_28' => $request['size_28'],
                'size_29' => $request['size_29'],
                'size_30' => $request['size_30'],
                'size_31' => $request['size_31'],
                'size_32' => $request['size_32'],
                'size_33' => $request['size_33'],
                'size_34' => $request['size_34'],
                'size_35' => $request['size_35'],
                'size_36' => $request['size_36'],
                'size_37' => $request['size_37'],
                'size_38' => $request['size_38'],
                'size_39' => $request['size_39'],
                'size_40' => $request['size_40'],
                'size_41' => $request['size_41'],
                'size_42' => $request['size_42'],
                'size_other' => $request['size_other']
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $inputChart,
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

    public function getDataChart($eventId)
    {
        try {
            $searchProduct = request()->searchproduct;

            $dataChart = Chart::select(
                'charts.id',
                'products.entity_name',
                'products.article_name',
                'products.color',
                'products.combo',
                'products.material',
                'products.keyword',
                'products.type_id',
                'types.type',
                'products.description',
                'products.price',
                'charts.size_S',
                'charts.size_M',
                'charts.size_L',
                'charts.size_XL',
                'charts.size_XXL',
                'charts.size_XXXL',
                'charts.size_2',
                'charts.size_4',
                'charts.size_6',
                'charts.size_8',
                'charts.size_10',
                'charts.size_12',
                'charts.size_27',
                'charts.size_28',
                'charts.size_29',
                'charts.size_30',
                'charts.size_31',
                'charts.size_32',
                'charts.size_33',
                'charts.size_34',
                'charts.size_35',
                'charts.size_36',
                'charts.size_37',
                'charts.size_38',
                'charts.size_39',
                'charts.size_40',
                'charts.size_41',
                'charts.size_42',
                'charts.created_at'

            )->join('products', 'products.id', '=', 'charts.product_id')
            ->join('types', 'types.id', '=', 'products.type_id')
            ->where('charts.client_id', '=', function($query) {
                $phoneNumber = request()->header('phone');
                $query->select('id')
                    ->from('distributors')
                    ->where('phone', '=', $phoneNumber)
                    ->first();
            })->where('event_id', '=', $eventId)
            ->when($searchProduct, function ($query) use ($searchProduct) {
                $query->where('products.article_name', 'LIKE', "%$searchProduct%");
            })->get();

            return response()->json([
                'status' => 'success',
                'data' => $dataChart,
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

    public function countDataChart($eventId)
    {
        try {
            $searchProduct = request()->searchproduct;

            $dataChart = Chart::select(
                'charts.id',
                'products.entity_name',
                'products.article_name',
                'products.color',
                'products.combo',
                'products.material',
                'products.keyword',
                'products.type_id',
                'types.type',
                'products.description',
                'products.price',
                'charts.size_S',
                'charts.size_M',
                'charts.size_L',
                'charts.size_XL',
                'charts.size_XXL',
                'charts.size_XXXL',
                'charts.size_2',
                'charts.size_4',
                'charts.size_6',
                'charts.size_8',
                'charts.size_10',
                'charts.size_12',
                'charts.size_27',
                'charts.size_28',
                'charts.size_29',
                'charts.size_30',
                'charts.size_31',
                'charts.size_32',
                'charts.size_33',
                'charts.size_34',
                'charts.size_35',
                'charts.size_36',
                'charts.size_37',
                'charts.size_38',
                'charts.size_39',
                'charts.size_40',
                'charts.size_41',
                'charts.size_42',
                'charts.created_at'

            )->join('products', 'products.id', '=', 'charts.product_id')
            ->join('types', 'types.id', '=', 'products.type_id')
            ->where('charts.client_id', '=', function($query) {
                $phoneNumber = request()->header('phone');
                $query->select('id')
                    ->from('distributors')
                    ->where('phone', '=', $phoneNumber)
                    ->first();
            })->where('event_id', '=', $eventId)
            ->when($searchProduct, function ($query) use ($searchProduct) {
                $query->where('products.article_name', 'LIKE', "%$searchProduct%");
            })->count();

            return response()->json([
                'status' => 'success',
                'data' => $dataChart,
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

    public function updateDataChart(UpdateOrderRequest $request, $id)
    {
        try {
            $request = $this->checkRequestUpdate($request, $id);

            Chart::where([
                ['id', '=', $id],
                ['client_id', '=', function ($query) {
                    $query->select('id')
                        ->from('distributors')
                        ->where('phone', '=', request()->header('phone'))
                        ->first();
                }]
            ])->update([
                'size_S' => $request['S'],
                'size_M' => $request['M'],
                'size_L' => $request['L'],
                'size_XL' => $request['XL'],
                'size_XXL' => $request['XXL'],
                'size_XXXL' => $request['XXXL'],
                'size_2' => $request['2'],
                'size_4' => $request['4'],
                'size_6' => $request['6'],
                'size_8' => $request['8'],
                'size_10' => $request['10'],
                'size_12' => $request['12'],
                'size_27' => $request['27'],
                'size_28' => $request['28'],
                'size_29' => $request['29'],
                'size_30' => $request['30'],
                'size_31' => $request['31'],
                'size_32' => $request['32'],
                'size_33' => $request['33'],
                'size_34' => $request['34'],
                'size_35' => $request['35'],
                'size_36' => $request['36'],
                'size_37' => $request['37'],
                'size_38' => $request['38'],
                'size_39' => $request['39'],
                'size_40' => $request['40'],
                'size_41' => $request['41'],
                'size_42' => $request['42'],
                'size_other' => $request['other']
            ]);

            return response()->json([
                'status' => 'success',
                'data' => true,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => false,
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function deleteDataChart($id)
    {
        try {
            Chart::where([
                ['id', '=', $id],
                ['client_id', '=', function ($query) {
                    $query->select('id')
                        ->from('distributors')
                        ->where('phone', '=', request()->header('phone'))
                        ->first();
                }]
            ])->delete();

            return response()->json([
                'status' => 'success',
                'data' => true,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => false,
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function createOrder($eventId)
    {
        try {
            $dataChart = $this->dataChart($eventId);

            DB::beginTransaction();
                DB::table('orders')->insert($dataChart);
                $this->deleteChart($eventId);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => true,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'success',
                'data' => false,
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function historyOrder()
    {

    }

    private function checkRequestCreate($request, $headerPhone)
    {
        $dataClient = $this->getDataClient($headerPhone);
        $request = collect($request)->toArray();

        return [
            'client_id' => $dataClient->id,
            'event_id' => $request['event_id'],
            'session_id' => null,
            'product_id' => $request['product_id'],
            'size_S' => ($request['S']) ? $request['S'] : 0,
            'size_M' => ($request['M']) ? $request['M'] : 0,
            'size_L' => ($request['L']) ? $request['L'] : 0,
            'size_XL' => ($request['XL']) ? $request['XL'] : 0,
            'size_XXL' => ($request['XXL']) ? $request['XXL'] : 0,
            'size_XXXL' => ($request['XXXL']) ? $request['XXXL'] : 0,
            'size_2' => ($request['2']) ? $request['2'] : 0,
            'size_4' => ($request['4']) ? $request['4'] : 0,
            'size_6' => ($request['6']) ? $request['6'] : 0,
            'size_8' => ($request['8']) ? $request['8'] : 0,
            'size_10' => ($request['10']) ? $request['10'] : 0,
            'size_12' => ($request['12']) ? $request['12'] : 0,
            'size_27' => ($request['27']) ? $request['27'] : 0,
            'size_28' => ($request['28']) ? $request['28'] : 0,
            'size_29' => ($request['29']) ? $request['29'] : 0,
            'size_30' => ($request['30']) ? $request['30'] : 0,
            'size_31' => ($request['31']) ? $request['31'] : 0,
            'size_32' => ($request['32']) ? $request['32'] : 0,
            'size_33' => ($request['33']) ? $request['33'] : 0,
            'size_34' => ($request['34']) ? $request['34'] : 0,
            'size_35' => ($request['35']) ? $request['35'] : 0,
            'size_36' => ($request['36']) ? $request['36'] : 0,
            'size_37' => ($request['37']) ? $request['37'] : 0,
            'size_38' => ($request['38']) ? $request['38'] : 0,
            'size_39' => ($request['39']) ? $request['39'] : 0,
            'size_40' => ($request['40']) ? $request['40'] : 0,
            'size_41' => ($request['41']) ? $request['41'] : 0,
            'size_42' => ($request['42']) ? $request['42'] : 0,
            'size_other' => ($request['other']) ? $request['other'] : 0,
        ];
    }

    private function checkRequestUpdate($request, $chartId)
    {
        $dataChart = Chart::where('id', '=', $chartId)->first();
        $request = collect($request)->toArray();

        return [
            'S' => ($request['S'] !== NULL) ? $request['S'] : $dataChart->size_S,
            'M' => ($request['M'] !== NULL) ? $request['M'] : $dataChart->size_M,
            'L' => ($request['L'] !== NULL) ? $request['L'] : $dataChart->size_L,
            'XL' => ($request['XL'] !== NULL) ? $request['XL'] : $dataChart->size_XL,
            'XXL' => ($request['XXL'] !== NULL) ? $request['XXL'] : $dataChart->size_XXL,
            'XXXL' => ($request['XXXL'] !== NULL) ? $request['XXXL'] : $dataChart->size_XXXL,
            '2' => ($request['2'] !== NULL) ? $request['2'] : $dataChart->size_2,
            '4' => ($request['4'] !== NULL) ? $request['4'] : $dataChart->size_4,
            '6' => ($request['6'] !== NULL) ? $request['6'] : $dataChart->size_6,
            '8' => ($request['8'] !== NULL) ? $request['8'] : $dataChart->size_8,
            '10' => ($request['10'] !== NULL) ? $request['10'] : $dataChart->size_10,
            '12' => ($request['12'] !== NULL) ? $request['12'] : $dataChart->size_12,
            '27' => ($request['27'] !== NULL) ? $request['27'] : $dataChart->size_27,
            '28' => ($request['28'] !== NULL) ? $request['28'] : $dataChart->size_28,
            '29' => ($request['29'] !== NULL) ? $request['29'] : $dataChart->size_29,
            '30' => ($request['30'] !== NULL) ? $request['30'] : $dataChart->size_30,
            '31' => ($request['31'] !== NULL) ? $request['31'] : $dataChart->size_31,
            '32' => ($request['32'] !== NULL) ? $request['32'] : $dataChart->size_32,
            '33' => ($request['33'] !== NULL) ? $request['33'] : $dataChart->size_33,
            '34' => ($request['34'] !== NULL) ? $request['34'] : $dataChart->size_34,
            '35' => ($request['35'] !== NULL) ? $request['35'] : $dataChart->size_35,
            '36' => ($request['36'] !== NULL) ? $request['36'] : $dataChart->size_36,
            '37' => ($request['37'] !== NULL) ? $request['37'] : $dataChart->size_37,
            '38' => ($request['38'] !== NULL) ? $request['38'] : $dataChart->size_38,
            '39' => ($request['39'] !== NULL) ? $request['39'] : $dataChart->size_39,
            '40' => ($request['40'] !== NULL) ? $request['40'] : $dataChart->size_40,
            '41' => ($request['41'] !== NULL) ? $request['41'] : $dataChart->size_41,
            '42' => ($request['42'] !== NULL) ? $request['42'] : $dataChart->size_42,
            'other' => ($request['other'] !== NULL) ? $request['other'] : $dataChart->size_other
        ];
    }

    private function dataChart($eventId)
    {
        $rawDataChart = Chart::select(
            'client_id',
            'event_id',
            'session_id',
            'product_id',
            'size_S',
            'size_M',
            'size_L',
            'size_XL',
            'size_XXL',
            'size_XXXL',
            'size_2',
            'size_4',
            'size_6',
            'size_8',
            'size_10',
            'size_12',
            'size_27',
            'size_28',
            'size_29',
            'size_30',
            'size_31',
            'size_32',
            'size_33',
            'size_34',
            'size_35',
            'size_36',
            'size_37',
            'size_38',
            'size_39',
            'size_40',
            'size_41',
            'size_42',
            'size_other'
        )->where('charts.client_id', '=', function($query) {
            $phoneNumber = request()->header('phone');
            $query->select('id')
                ->from('distributors')
                ->where('phone', '=', $phoneNumber)
                ->first();
        })->where('event_id', '=', $eventId)
        ->get();

        $dataChart = collect($rawDataChart)->map(function ($data) {
            return [
                'client_id' => $data->client_id,
                'event_id' => $data->event_id,
                'session_id' => $data->session_id,
                'product_id' => $data->product_id,
                'size_S' => $data->size_S,
                'size_M' => $data->size_M,
                'size_L' => $data->size_L,
                'size_XL' => $data->size_XL,
                'size_XXL' => $data->size_XXL,
                'size_XXXL' => $data->size_XXXL,
                'size_2' => $data->size_2,
                'size_4' => $data->size_4,
                'size_6' => $data->size_6,
                'size_8' => $data->size_8,
                'size_10' => $data->size_10,
                'size_12' => $data->size_12,
                'size_27' => $data->size_27,
                'size_28' => $data->size_28,
                'size_29' => $data->size_29,
                'size_30' => $data->size_30,
                'size_31' => $data->size_31,
                'size_32' => $data->size_32,
                'size_33' => $data->size_33,
                'size_34' => $data->size_34,
                'size_35' => $data->size_35,
                'size_36' => $data->size_36,
                'size_37' => $data->size_37,
                'size_38' => $data->size_38,
                'size_39' => $data->size_39,
                'size_40' => $data->size_40,
                'size_41' => $data->size_41,
                'size_42' => $data->size_42,
                'size_other' => $data->size_other,
                'created_at' => Carbon::now()->format('Y-m-d H:m:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:m:s'),
            ];
        })->toArray();

        return $dataChart;
    }

    private function deleteChart($eventId)
    {
        $dataChart = Chart::select(
            'client_id',
            'event_id',
            'session_id',
            'product_id',
            'qty',
        )->join('products', 'products.id', '=', 'charts.product_id')
        ->where('charts.client_id', '=', function($query) {
            $phoneNumber = request()->header('phone');
            $query->select('id')
                ->from('distributors')
                ->where('phone', '=', $phoneNumber)
                ->first();
        })->where('event_id', '=', $eventId)
        ->delete();
    }

    private function getDataClient($clientPhoneNumber)
    {
        $distributor = Distributor::where('phone', '=', $clientPhoneNumber)->first();

        return $distributor;
    }
}
