<?php

namespace App\Http\Controllers\Api\Admin\ResourceAndDevelopment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{SampleProduct, SampleProductPhoto};
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\SampleProduct\{SampleProductRequest, UpdateSampleProductRequest};
use Carbon\Carbon;

class SampleProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $requestArticle = request()->article;
            $requestEntity = request()->entity;

            $sampleProduct = SampleProduct::select(
                'id',
                'date',
                'article_name',
                'entity_name',
            )->with(['PhotoSampleProduct' => function ($query) {
                $query->select('sample_product_id', 'sequence', 'photo')->first();
            }])->when($requestArticle, function ($query) use ($requestArticle) {
                $query->where('article_name', 'like', "%$requestArticle%");
            })->when($requestEntity, function ($query) use ($requestEntity) {
                $query->where('entity_name', '=', $requestEntity);
            })->get();

            return response()->json([
                'status' => 'success',
                'data' => $sampleProduct,
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SampleProductRequest $request)
    {
        try {
            DB::beginTransaction();

                $sampleProduct = SampleProduct::create([
                    'date' => $request->date,
                    'article_name' => $request->article_name,
                    'entity_name' => $request->entity_name,
                    'material' => $request->material,
                    'size' => $request->size,
                    'accessories' => $request->accessories,
                    'designer_id' => $request->designer_id,
                    'md_id' => $request->md_id,
                    'leader_designer_id' => $request->leader_designer_id,
                ]);

                $this->inputSamplePhoto(['sp_id' => 1, 'photo' => $request->photo]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $sampleProduct,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage(),
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $sampleProduct = SampleProduct::select(
                'id',
                'date',
                'article_name',
                'entity_name',
                'material',
                'size',
                'accessories',
                'designer',
                'md',
                'leader_designer'
            )->with(['PhotoSampleProduct' => function ($query) {
                $query->select('sample_product_id', 'sequence', 'photo')
                    ->orderBy('sequence', 'ASC');
            }])->find($id);

            return response()->json([
                'status' => 'success',
                'data' => $sampleProduct,
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSampleProductRequest $request, $id)
    {
        try {
            $requestSampleProduct = $this->requestUpdateSampelProduct($request, $id);

            $requests = $requestSampleProduct['requests'];
            $sampleProduct = $requestSampleProduct['sampleProduct'];

            $sampleProduct->update([
                'date' => $requests['date'],
                'article_name' => $requests['article_name'],
                'entity_name' => $requests['entity_name'],
                'material' => $requests['material'],
                'size' => $requests['size'],
                'accessories' => $requests['accessories'],
                'designer_id' => $requests['designer_id'],
                'md_id' => $requests['md_id'],
                'leader_designer_id' => $requests['leader_designer_id'],
            ]);

            return response()->json([
                'status' => 'success',
                'data' => true,
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function deletePhoto($id, $sampleProductId)
    {
        try {
            SampleProductPhoto::where([['id', '=', $id], ['sample_product_id', '=', $sampleProductId]])->destroy();

            return response()->json([
                'status' => 'successs',
                'data' => true,
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

    private function inputSamplePhoto(array $request)
    {
        $photos = explode(',', $request['photo']);
        $sequence = $this->getSequencePhoto($request['sp_id']) + 1;

        $array = [];

        foreach ($photos as $photo) {
            array_push($array, [
                'sample_product_id' => $request['sp_id'],
                'sequence' => $sequence,
                'photo' => $photo,
                'created_at' => Carbon::now()->format('Y-m-d H:m:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:m:s')
            ]);

            $sequence += 1;
        }

        DB::table('sample_product_photos')->insert($array);
    }

    private function getSequencePhoto($sp_id)
    {
        $dataSequence = SampleProductPhoto::select('sequence')
                                        ->where('sample_product_id', '=', $sp_id)
                                        ->latest()
                                        ->first();

        return ($dataSequence) ? $dataSequence->sequence : 0;
    }

    private function requestUpdateSampelProduct($request, $id)
    {
        $sampleProduct = SampleProduct::select('id','date','article_name', 'entity_name','material','size','accessories','designer_id','md_id','leader_designer_id',)
                                    ->where('id', '=', $id)
                                    ->first();

        $requests = [
            'date' => ($request->date) ? $request->date : $sampleProduct->date,
            'article_name' => ($request->article_name) ? $request->article_name : $sampleProduct->article_name,
            'entity_name' => ($request->entity_name) ? $request->entity_name : $sampleProduct->entity_name,
            'material' => ($request->material) ? $request->material : $sampleProduct->material,
            'size' => ($request->size) ? $request->size : $sampleProduct->size,
            'accessories' => ($request->accessories) ? $request->accessories : $sampleProduct->accessories,
            'designer_id' => ($request->designer_id) ? $request->designer_id : $sampleProduct->designer_id,
            'md_id' => ($request->md_id) ? $request->md_id : $sampleProduct->md_id,
            'leader_designer_id' => ($request->leader_designer_id) ? $request->leader_designer_id : $sampleProduct->leader_designer_id,
        ];

        $data = compact('sampleProduct', 'requests');

        return $data;
    }
}
