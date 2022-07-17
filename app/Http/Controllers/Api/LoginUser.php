<?php

namespace App\Http\Controllers\Api;

use App\IsActive;
use App\Distributor;
use App\TemporaryStorage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginUser extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke($phone)
    {
        if ($phone == 0 || $phone == NULL) {
            return response()->json([
                'message' => 'please enter your phone number'
            ], 400);
        }

        $user = Distributor::where('phone', $phone)->with('PartnerAddress', 'MutifStoreMaster.MutifStoreAddress')->first();

        if (!$user) {
            return response()->json([
                'status' => 'failed',
                'message' => 'user '.$phone.' not registered'
            ], 400);
        }

        $activate = IsActive::find(1);

        if (!$activate || $activate->name == 'NON-ACTIVE') {
            // when the web is active
            return response()->json([
                'status' => 'closed',
                'message' => 'the web is being closed'
            ], 400);
        } elseif ($activate && $activate->name == 'ACTIVE') {
            // when web is being closed
            return response()->json([
                'status' => 'successs',
                'message' => 'hello '.$user->name,
                'account' => $user
            ], 200);
        } elseif ($activate && $activate->name == 'DONE') {
            // when logging into the final session
            try {
                $data = TemporaryStorage::where('distributor_id', $user->id)->with('Clothes')->get();

                return response()->json([
                    'status' => 'success',
                    'message' => 'success to get data',
                    'user' => $user,
                    'final_data' => $data
                ], 200);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'failed to get data',
                    'error' => $th->getMessage()
                ], 400);
            }
        }
    }
}
