<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Clothes;
use App\IsActive;

class EntityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clothess = Clothes::groupBy('entity_name')->get('entity_name');

        $active = IsActive::find(1);


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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($entity)
    {
        $active = IsActive::find(1);

        if (!$active) {
            IsActive::create([
                'name' => $entity
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'status change'
            ]);
        }

        $active->update([
            'name' => $entity
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'status change'
        ]);
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
}
