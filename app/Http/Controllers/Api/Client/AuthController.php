<?php

namespace App\Http\Controllers\Api\Client;

use JWTAuth;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Client\{ClientLoginRequest};

class AuthController extends Controller
{
    public function login (Request $request) {
        try {
            $user = $this->checkUser($request);

            dd($user);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    private function checkUser($request)
    {
        $checkUser = User::where([
            ['email', '=', $request->email]
        ])->first();

        $checkPassword = Hash::check($request->password, $checkUser->password);

        

        if ($checkUser != null) {
            return $checkUser;
        } else {
            return response()->json([
                'status' => 'failed',
                'message' => 'invalidate username or password!'
            ]);
        }
    }
}
