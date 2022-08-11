<?php

namespace App\Http\Controllers\Api\Admin;

use App\Distributor;
use App\Http\Controllers\Controller;
use App\Phone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PhoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $phone = Phone::where('approved', 0)->with('Distributor')->orderBy('created_at', 'DESC')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'success to get not-approved phone',
                'phone' => $phone
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get not approved phone',
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Phone  $phone
     * @return \Illuminate\Http\Response
     */
    public function show(Phone $phone)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Phone  $phone
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Phone $phone)
    {
        try {
        DB::beginTransaction();
            $latestPhoneNumber = Phone::where([
                ['distributor_id', '=', $phone->distributor_id,],
                ['is_active', '=', 1],
                ['approved', '=', 1]
                ])->latest();

            $distributor = Distributor::where('id', $phone->distributor_id)->first();

            $latestPhoneNumber->update([
                'is_active' => 0
            ]);

            $notApprovePhone = Phone::find($phone->id);

            $phone->update([
                'is_active' => 1,
                'approved' => 1
            ]);

            $distributor->update([
                'phone' => $phone->phone_number
            ]);

            $approvedPhone = Phone::find($phone->id);

            activity()->causedBy(Auth::user())
                        ->performedOn($phone)
                        ->withProperties([
                                    'old' => [
                                        'phone_number' => $notApprovePhone->phone_number,
                                        'is_active' => $notApprovePhone->is_active,
                                        'approved' => $notApprovePhone->approved
                                    ],
                                    'attributes' => [
                                        'phone_number' => $approvedPhone->phone_number,
                                        'is_active' => $approvedPhone->is_active,
                                        'approved' => $approvedPhone->approved
                                    ]
                        ])->log('updated');

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'phone approved'
            ], 200);
        } catch (\Throwable $th) {

            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to approve',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Phone  $phone
     * @return \Illuminate\Http\Response
     */
    public function destroy(Phone $phone)
    {
        try {
            $phone->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'success to reject phone number'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to reject phone number',
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
