<?php

namespace App\Http\Controllers\Api\Admin\ResourceAndDevelopment;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\{User, Models\SIP\UserSIP};
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Hash, DB};
use App\Http\Requests\Admin\Voting\CreateVotingEventRequest;
use App\Models\{VotingEvent, VotingMember, VotingSample, VotingScore};

class VotingController extends Controller
{
    public function getAllEvent()
    {
        try {
            $searchName = request()->search;

            $dataEvent = VotingEvent::select('id', 'title')
                                    ->when($searchName, function ($query) use ($searchName) {
                                        $query->where('title', 'like', "%$searchName%");
                                    })
                                    ->get();

            return response()->json([
                'status' => 'success',
                'data' => $dataEvent,
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

    public function createEvent(CreateVotingEventRequest $request)
    {
        try {
            DB::beginTransaction();
                $dataEventVoting = VotingEvent::create([
                    'start_date' => $request->start_date,
                    'title' => $request->title,
                    'description' => $request->description,
                ]);

                $this->inputSample($request->sample_id, $dataEventVoting->id);
                $this->inviteMember($request->member_attendance_id, $dataEventVoting->id);
            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => $dataEventVoting,
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

    private function inputSample($sampleId, $eventId)
    {
        $explodeSampleId = explode(',', $sampleId);

        collect($explodeSampleId)->map(function ($item, $index) use ($eventId) {
            VotingSample::create([
                'voting_event_id' => $eventId,
                'sample_product_id' => $item,
                'queue' => $index + 1
            ]);
        });
    }

    private function inviteMember($memberAttendanceId, $eventId)
    {
        $explodeMemberAttendanceId = explode(',', $memberAttendanceId);

        collect($explodeMemberAttendanceId)->map(function ($item) use ($eventId) {
            $attendanceId = $this->createUser($item);

            VotingMember::create([
                'voting_event_id' => $eventId,
                'attendance_id' => $attendanceId,
            ]);
        });
    }

    private function createUser($attendanceId)
    {
        $dataUser = User::select('name')->where('attendance_id', '=', $attendanceId)->first();

        if($dataUser == null) {
            $userSIP = UserSIP::select('username', 'attendance_id', 'sub_section_id', 'seksi', 'data_karyawans.nip')
                                ->leftJoin('detail_users', 'detail_users.id', '=', 'users.detail_user_id')
                                ->leftJoin('data_karyawans', 'data_karyawans.id', '=', 'detail_users.data_karyawan_id')
                                ->where('users.attendance_id', '=', $attendanceId)
                                ->first();

            User::create([
                'name' => $userSIP->username,
                'email' => "$userSIP->username@mutif.atpo",
                'password' => Hash::make($userSIP->username),
                'attendance_id' => $userSIP->attendance_id,
                'sub_section_id' => $userSIP->sub_section_id,
                'sub_section' => $userSIP->seksi,
                'nip' => $userSIP->nip
            ]);
        }

        return $attendanceId;
    }
}
