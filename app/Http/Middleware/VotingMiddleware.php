<?php

namespace App\Http\Middleware;

use App\Models\VotingMember;
use Closure;
use Illuminate\Support\Facades\Auth;

class VotingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        $data = VotingMember::where([
            ['attendance_id', '=', $user->attendance_id],
            ['voting_event_id', '=', function ($query) {
                $query->select('id')
                    ->from('voting_events')
                    ->where('is_activate', '=', true)
                    ->first();
            }]
        ])->first();

        if (!$data) {
            return response()->json([
                'message' => 'you are not invited!',
            ], 403);
        }

        return $next($request);
    }
}
