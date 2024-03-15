<?php

namespace App\Http\Controllers\Api\Admin\ResourceAndDevelopment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Style;

class StyleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $style = Style::select('id', 'style_name')->get();

            return response()->json([
                'status' => 'success',
                'data' => $style,
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
    public function store(Request $request)
    {
        try {
            $checkExistanceData = $this->checkStyleName($request->style_name);

            if ($checkExistanceData == false) {
                $style = Style::create([
                    'style_name' => ucwords($request->style_name)
                ]);
            }

            return response()->json([
                'status' => 'success',
                'data' => ($checkExistanceData == false) ? $style : $checkExistanceData,
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
    public function update(Request $request, $id)
    {
        //
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

    private function checkStyleName($styleName)
    {
        $explode = explode(' ', $styleName);
        $implode = implode(' ', $explode);

        $style = Style::where('style_name', '=', ucwords($implode))->first();

        return ($style) ? $style : false;
    }
}
