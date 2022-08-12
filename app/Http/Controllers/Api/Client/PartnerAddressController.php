<?php

namespace App\Http\Controllers\Api\Client;

use App\Distributor;
use App\District;
use App\Http\Controllers\Controller;
use App\PartnerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartnerAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $phone)
    {
        $distributor = Distributor::where('phone', $phone)->first();

        if (!$distributor) {
            return response()->json([
                'status' => 'failed',
                'message' => 'number '.$phone.' not register'
            ], 400);
        }
        try {

        DB::beginTransaction();

        $address = PartnerAddress::create([
            'distributor_id' => $distributor->id,
            'address' => $request->address,
            'district' => $request->district,
            'regency' => $request->regency,
            'province' => $request->province,
            'phone_1' => $phone,
            'phone_2' => ($request->phone_2) ? $request->phone_2 : '-',
            'fax_1' => ($request->fax_1) ? $request->fax_1 : '-',
            'addr_type' => ($request->addr_type) ? $request->addr_type : 'Bill To',
            'zip' => ($request->zip) ? $request->zip : '-',
        ]);

        activity()->causedBy($distributor)
                            ->performedOn($address)
                            ->withProperties([
                                'attributes' => [
                                    'address' => $address->address,
                                    'zip' => $address->zip
                                ]
                            ])->log('created!');

        DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'success to register address',
                'data' => $address,
            ], 200);
        } catch (\Throwable $th) {
        DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to register address',
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
    public function update(Request $request, $phone)
    {
        $user = Distributor::where('phone', $phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'number '.$phone.' not registered'
            ], 400);
        }

        // dd($request->all());
        $oldAddress = PartnerAddress::where('distributor_id', $user->id)->first();

        try {

            DB::beginTransaction();
            PartnerAddress::where('distributor_id', $user->id)->update([
                'distributor_id' => $user->id,
                'address' => $request->address,
                'district' => $request->district,
                'regency' => $request->regency,
                'province' => $request->province,
                'phone_1' => $phone,
                'phone_2' => $request->phone_2,
                'fax_1' => $request->fax_1,
                'addr_type' => $request->addr_type,
                'zip' => $request->zip,
            ]);

            $address = PartnerAddress::where('distributor_id', $user->id)->first();

            activity()->causedBy($user)
            ->performedOn($address)
            ->withProperties([
                'old' => [
                    'address' => $oldAddress->address,
                    'zip' => $oldAddress->zip
                ],
                'attributes' => [
                    'address' => $address->address,
                    'zip' => $address->zip
                ]
            ])->log('created!');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'success to update address',
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to update address',
                'error' => $th->getMessage()
            ]);
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
}
