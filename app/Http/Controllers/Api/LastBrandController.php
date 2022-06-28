<?php

namespace App\Http\Controllers\Api;

use App\Clothes;
use App\Http\Controllers\Controller;
use App\LastBrand;
use Illuminate\Http\Request;

class LastBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clothess = Clothes::groupBy('entity_name')->get('entity_name');

        $active = LastBrand::find(1);

        if (!$active) {
            foreach ($clothess as $clothes) {
                $clothes['status'] = false;
            }

            return response()->json([
                'data' => $clothess
            ]);
        }

        foreach ($clothess as $clothes) {
            if ($clothes->entity_name == $active->name) {
                $clothes['status'] = true;
            } else {
                $clothes['status'] = false;
            }
        }

        return response()->json([
            'data' => $clothess
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\LastBrand  $lastBrand
     * @return \Illuminate\Http\Response
     */
    public function show(LastBrand $lastBrand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LastBrand  $lastBrand
     * @return \Illuminate\Http\Response
     */
    public function update($entity)
    {
        $active = LastBrand::find(1);

        if (!$active) {
            LastBrand::create([
                'name' => $entity
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'entity active'
            ]);
        }

        $active->update([
            'name' => $entity
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'entity active'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LastBrand  $lastBrand
     * @return \Illuminate\Http\Response
     */
    public function destroy($entity)
    {
        try {
            LastBrand::where('name', $entity)->update([
                'name' => null
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'last brand deleted',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to delete last brand',
                'error' => $th->getMessage()
            ]);
        }
    }
}
