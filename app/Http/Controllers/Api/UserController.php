<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use Illuminate\Http\Request;
use App\{User, Models\SIP\UserSIP};
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\User\CreateUserRequest;
use Illuminate\Support\Facades\{DB, Auth, Hash, Validator};

class UserController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $user = User::where([
            ['email', '=', $request->email]
        ])->first();

        activity()->log($user->name . ' has logged in');

        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user','token'),201);
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        return response()->json(compact('user'));
    }

    public function logout()
    {
        try {

            activity()->log(Auth::user()->jame . ' logged out');

            JWTAuth::parseToken()->invalidate(true);

            return response()->json([
                'status' => 'success',
                'message' => 'logged out!'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to logout',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function getUserName()
    {
        try {
            $name = DB::table('users')->select('name')->where('id', Auth::user()->id)->first();

            return response()->json([
                'name' => $name->name
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'message' => 'failed to get data',
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function createUser(CreateUserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'attendance_id' => $request->attendance_id,
                'sub_section_id' => $request->sub_section_id,
                'sub_section' => $request->seksi,
                'nip' => $request->nip
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $user,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function getUserSIP()
    {
        try {
            $searchName = request()->name;

            $userSIP = UserSIP::select('users.id', 'username', 'attendance_id', 'sub_section_id', 'seksi', 'data_karyawans.nip')
                                ->leftJoin('detail_users', 'detail_users.id', '=', 'users.detail_user_id')
                                ->leftJoin('data_karyawans', 'data_karyawans.id', '=', 'detail_users.data_karyawan_id')
                                ->where('attendance_id', '<>', 0)
                                ->when($searchName, fn ($query) =>
                                    $query->where('username', 'LIKE', "%$searchName%")
                                )->get();

            return response()->json([
                'status' => 'success',
                'data' => $userSIP,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function checkUser($attendanceId)
    {
        try {
            $user = User::select('name', 'email', 'attendance_id')
                        ->where('attendance_id', '=', $attendanceId)
                        ->first();

            return response()->json([
                'status' => 'success',
                'data' => $user,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage()
            ], 400);
        }
    }
}
