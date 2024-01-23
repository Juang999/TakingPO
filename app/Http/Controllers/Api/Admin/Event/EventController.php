<?php

namespace App\Http\Controllers\Api\Admin\Event;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{Product, Event, Session, DetailSession};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function getEvent()
    {
        try {
            $event = Event::select('id', 'event_name', 'start_date', 'end_date', 'is_active')->get();

            return response()->json([
                'status' => 'success',
                'data' => $event,
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

    public function createEvent(Request $request)
    {
        try {
            $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
            $endDate = Carbon::parse($request->end_date)->format('Y-m-d');

            $event = Event::create([
                'event_name' => $request->event_name,
                'event_desc' => $request->event_desc,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => false
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $event,
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

    public function getDetailEvent($id)
    {
        try {
            $event = Event::select('id', 'event_name', 'start_date', 'end_date', 'is_active')->where('id', '=', $id)->with('Session')->first();

            return response()->json([
                'status' => 'success',
                'data' => $event,
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

    public function createSession(Request $request)
    {
        try {
            DB::beginTransaction();
                $session = Session::create([
                    'event_id' => $request->event_id,
                    'session_desc' => $request->session_desc,
                    'is_active' => false
                ]);

                if ($request->product != null) {
                    $this->createDetailSession($request, $session->id);
                }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'data' => $session,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 'failed',
                'data' => null,
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function inputDetailSession(Request $request)
    {
        try {
            $detailSession = DetailSession::create([
                'session_id' => $request->session_id,
                'product_id' => $request->product_id
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $detailSession,
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

    public function deleteSession($id)
    {
        try {
            Session::where('id', '=', $id)->delete();

            return response()->json([
                'status' => 'success',
                'data' => true,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => false,
                'error' => $th->getMessage()
            ], 400);
        }
    }

    public function deleteDetailSession($id)
    {
        try {
            DetailSession::where('id', '=', $id)->delete();

            return response()->json([
                'status' => 'success',
                'data' => true,
                'error' => null
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failed',
                'data' => false,
                'error' => $th->getMessage()
            ], 400);
        }
    }

    private function createDetailSession($request, $sessionId)
    {
        $inputDetailSession = collect($request->product)->map(function ($data) use ($sessionId) {
            $decodedData = json_decode($data, true);

            return [
                'session_id' => $sessionId,
                'product_id' => $decodedData['product_id'],
                'created_at' => Carbon::now()->format('Y-m-d H:m:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:m:s')
            ];
        })->toArray();

        DB::table('detail_sessions')->insert($inputDetailSession);
    }
}
