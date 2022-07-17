<?php

namespace App\Http\Controllers\Api;

use App\Distributor;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\PartnerGroup;
use Illuminate\Http\Request;

class SingleRegister extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(RegisterRequest $request)
    {
        try {
            $partner_group = PartnerGroup::where('id', $request->partner_group_id)->first();

            Distributor::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'distributor_id' => $request->distributor_id,
                'group_code' => $partner_group->prtnr_code,
                'partner_group_id' => $partner_group->id,
                'level' => 'bronze'
            ]);

            return response()->json([
                'success' => 'success',
                'message' => 'register successfully',
            ], 200);

        } catch (\Throwable $th) {

            return response()->json([
                'status' => 'failed',
                'message' => 'failed to register',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
