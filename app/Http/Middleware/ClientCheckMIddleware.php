<?php

namespace App\Http\Middleware;

use Closure;
use App\Distributor;

class ClientCheckMIddleware
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
        $phoneNumber = $request->header('phone');

        $checkUser = Distributor::where('phone', '=', $phoneNumber)->first();

        if ($checkUser == null) {
            return response()->json([
                'status' => 'rejected',
                'data' => null,
                'error' => 'unauthorize!'
            ], 300);
        }

        return $next($request);
    }
}
