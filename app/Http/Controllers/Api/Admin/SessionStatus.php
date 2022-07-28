<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\IsActive;
use Illuminate\Http\Request;

class SessionStatus extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        $is_active = IsActive::find(1);

        return response()->json([
            'status' => 'success',
            'message' => 'success to get session status',
            'data' => $is_active
        ], 200);
    }
}
