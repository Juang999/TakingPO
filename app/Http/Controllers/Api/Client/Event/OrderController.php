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
                            ->paginate(3);

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
            $dataClient = $this->getDataClient(request()->header('phone'));

            $inputChart = Chart::create([
                'client_id' => $dataClient->id,
                'event_id' => $request->event_id,
                'session_id' => null,
                'product_id' => $request->product_id,
                'qty' => $request->qty,
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
                'products.description',
                'products.price',
                'charts.qty'
            )->join('products', 'products.id', '=', 'charts.product_id')
            ->where('charts.client_id', '=', function($query) {
                $phoneNumber = request()->header('phone');
                $query->select('id')
                    ->from('distributors')
                    ->where('phone', '=', $phoneNumber)
                    ->first();
            })->where('event_id', '=', $eventId)
            ->when($searchProduct, function ($query) use ($searchProduct) {
                $query->where('products.article_name', 'LIKE', "%$searchProduct%");
            })
            ->get();

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
            Chart::where([
                ['id', '=', $id],
                ['client_id', '=', function ($query) {
                    $query->select('id')
                        ->from('distributors')
                        ->where('phone', '=', request()->header('phone'))
                        ->first();
                }]
            ])->update([
                'qty' => $request->qty
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

    private function dataChart($eventId)
    {
        $rawDataChart = Chart::select(
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
        ->get();

        $dataChart = collect($rawDataChart)->map(function ($data) {
            return [
                'client_id' => $data->client_id,
                'event_id' => $data->event_id,
                'session_id' => $data->session_id,
                'product_id' => $data->product_id,
                'qty' => $data->qty,
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
