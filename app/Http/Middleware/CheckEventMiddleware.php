<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Event;

class CheckEventMiddleware
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
        $existanceEvent = Event::where('is_active', '=', true)->first();

        // check active event
        if ($existanceEvent == null) {
            return response()->json([
                'status' => 'rejected',
                'data' => null,
                'error' => 'event noc active!'
            ]);
        }

        return $next($request);
    }
}
