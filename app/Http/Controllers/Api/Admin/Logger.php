<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class Logger extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        if (request()->start_date && request()->end_date) {
            $logs = Activity::whereBetween('created_at', [request()->start_date, request()->end_date])
            ->with('causer', 'subject')
            ->orderBy('created_at', 'DESC')
            ->get()->toArray();

            return response()->json([
                'status' => 'success',
                'message' => 'success to get logs',
                'logs' => $logs
            ], 200);
        }

        $logs = Activity::whereBetween('created_at', [\Carbon\Carbon::now()->subDays(7), \Carbon\Carbon::now()])
        ->with('causer', 'subject')
        ->orderBy('created_at', 'DESC')
        ->get()->toArray();

        return response()->json([
            'status' => 'success',
            'message' => 'success to get logs',
            'logs' => $logs
        ], 200);
    }
}
