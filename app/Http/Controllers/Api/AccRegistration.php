<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Distributor;

class AccRegistration extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $users = Distributor::where('partner_group_id', '!=', '1')->with('PartnerAddress','MutifStoreMaster','PartnerGroup')->get();

        $new_member = [];

        foreach ($users as $user) {
            if (empty($user->MutifStoreMaster[0]->mutif_store_master)) {
                $new_member = $user;
            }
        }

        return response()->json([
            'data' => $new_member
        ]);
    }
}
