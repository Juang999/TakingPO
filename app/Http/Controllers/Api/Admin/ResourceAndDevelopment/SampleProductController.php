<?php

namespace App\Http\Controllers\Api\Admin\ResourceAndDevelopment;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\{SampleProduct, SampleProductPhoto};
use App\Http\Requests\Admin\SampleProduct\{SampleProductRequest, UpdateSampleProductRequest, InsertSamplePhotoRequest};

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
            )->with(['SinglePhotoProduct' => function ($query) {
                $query->select('sample_product_id', 'sequence', 'photo');
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

                $this->inputSamplePhoto(['sp_id' => $sampleProduct->id, 'photo' => $request->photo]);

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
                $query->select('id', 'sample_product_id', 'sequence', 'photo')
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
            DB::beginTransaction();
                $this->loggerDeletePhoto($id);

                SampleProductPhoto::where([['id', '=', $id], ['sample_product_id', '=', $sampleProductId]])->delete();
            DB::commit();

            return response()->json([
                'status' => 'successs',
                'data' => true,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function insertSamplePhoto(InsertSamplePhotoRequest $request)
    {
        try {
            $this->inputSamplePhoto(['sp_id' => $request->sample_product_id, 'photo' => $request->photo]);

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

    private function inputSamplePhoto(array $request)
    {
        $photos = explode(',', $request['photo']);
        $sequence = $this->getSequencePhoto($request['sp_id']) + 1;

        foreach ($photos as $photo) {
            SampleProductPhoto::create([
                'sample_product_id' => $request['sp_id'],
                'sequence' => $sequence,
                'photo' => $photo
            ]);

            $sequence += 1;
        }
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

    private function loggerFunction($log, $activity, $performedOn, $causedBy)
    {
        DB::table('activity_log')->insert([
            'log_name' => 'system',
            'description' => $activity,
            'subject_type' => 'App\Models\SampleProductPhoto',
            'subject_id' => $performedOn->id,
            'causer_type' => 'App\User',
            'causer_id' => $causedBy->id,
            'properties' => json_encode($log),
            'created_at' => Carbon::now()->format('Y-m-d H:m:s'),
            'updated_at' => Carbon::now()->format('Y-m-d H:m:s')
        ]);
    }

    private function loggerDeletePhoto($id)
    {
        $modelSampleProductPhoto = new SampleProductPhoto();
        $data = $modelSampleProductPhoto->where('id', '=', $id)->first();
        $performedOn = $data;
        $causerBy = auth()->user();

        $this->loggerFunction(['attributes' => $data], 'deleted', $performedOn, $causerBy);
    }
}
