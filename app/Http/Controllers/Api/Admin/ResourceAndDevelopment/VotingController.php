<?php

namespace App\Http\Controllers\Api\Admin\ResourceAndDevelopment;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\{User, Models\SIP\UserSIP};
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Hash, DB, Auth};
use App\Http\Requests\Admin\Voting\{
    CreateVotingEventRequest,
    InviteMemberRequest,
    AddSampleRequest,
    LoginVotingRequest,
    VoteSampleRequest,
    UpdateEventRequest
};
use App\Models\{
    VotingEvent,
    VotingMember,
    VotingSample,
    VotingScore,
    SampleProduct
};

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
                                    ->paginate(10);

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

    public function getDetailEvent($id)
    {
        try {
            $event = VotingEvent::query()->select('voting_events.id', 'start_date', 'title', 'description', 'voting_events.created_by', 'voting_events.updated_by', 'voting_events.created_at')
                                ->with([
                                    'Member' => fn ($query) =>
                                                        $query->select(
                                                                    'voting_members.id',
                                                                    'voting_event_id',
                                                                    DB::raw('users.attendance_id'),
                                                                    DB::raw('users.name'),
                                                                    'users.nip',
                                                                    'users.photo'
                                                                )->leftJoin('users', 'users.attendance_id', '=', 'voting_members.attendance_id'),
                                    'Sample' => fn ($query) =>
                                                        $query->select(
                                                                    'voting_event_id',
                                                                    'sample_product_id',
                                                                    DB::raw('sample_products.id'),
                                                                    DB::raw('sample_products.article_name'),
                                                                    DB::raw('sample_products.entity_name')
                                                                )->leftJoin('sample_products', 'sample_products.id', '=', 'voting_samples.sample_product_id')
                                                                ->with(['Thumbnail' => fn ($query) =>
                                                                    $query->select('sample_product_id', 'photo')
                                                            ])
                                    // 'Sample.Thumbnail'
                                ])->find($id);

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

    public function createEvent(CreateVotingEventRequest $request)
    {
        try {
            $userId = Auth::user()->id;
            $user = DB::table('usres')->select('name')->where('id', '=', $userId)->first();
            DB::beginTransaction();
                $dataEventVoting = VotingEvent::create([
                    'start_date' => $request->start_date,
                    'title' => $request->title,
                    'description' => $request->description,
                    'created_by' => $user->name
                ]);

                $this->inputSample($request->sample_id, $dataEventVoting->id);
                $this->inputMember($request->member_attendance_id, $dataEventVoting->id);
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

    public function inviteMember(InviteMemberRequest $request)
    {
        try {
            $this->inputMember($request->attendance_id, $request->event_id);

            return response()->json([
                'status' => 'success',
                'data' => true,
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

    public function addNewSample(AddSampleRequest $request)
    {
        try {
            $this->inputSample($request->sample_id, $request->event_id);

            return response()->json([
                'status' => 'success',
                'data' => true,
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

    public function getSample()
    {
        try {
            $querySearch = request()->search_article;

            $dataSample = SampleProduct::query()->select('id', 'article_name', 'entity_name')
                                    ->with(['Thumbnail' => function ($query) {
                                        $query->select('sample_product_id', 'sequence', 'photo');
                                    }])
                                    ->when($querySearch, function ($query) use ($querySearch) {
                                        $query->where('article_name', 'LIKE', "%$querySearch%");
                                    })
                                    ->get();

            return response()->json([
                'status' => 'success',
                'data' => $dataSample,
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

    public function voteSample(VoteSampleRequest $request)
    {
        try {
            $dataVoting = VotingScore::create([
                'voting_event_id' => $request->voting_event_id,
                'sample_product_id' => $request->sample_product_id,
                'attendance_id' => $request->attendance_id,
                'score' => $request->score,
                'note' => $request->note,
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $dataVoting,
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

    public function updateEvent(UpdateEventRequest $request, $id)
    {
        try {
            $updateRequest = $this->updateRequestEvent($request, $id);

            $dataEvent = $updateRequest['votingEvent'];
            $requests = $updateRequest['requests'];

            $dataEvent->update([
                'start_date' => $requests['start_date'],
                'title' => $requests['title'],
                'description' => $requests['description'],
                'updated_by' => $requests['updated_by'],
            ]);

            return response()->json([
                'status' => 'success',
                'data' => true,
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

    public function deleteEvent($id)
    {
        try {
            DB::beginTransaction();
                $this->deleteInvitation($id);
                $this->deleteSample($id);

                $dataEvent = VotingEvent::find($id);

                if($dataEvent) {
                    $dataEvent->delete();
                }
            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => true,
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

    public function removeInvitation($id, $attendanceId)
    {
        try {
            $dataInvitation = VotingMember::where([['id', '=', $id],['attendance_id', '=', $id]])->first();

            $dataInvitation->delete();

            return response()->json([
                'status' => 'success',
                'data' => true,
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

    public function removeSample($id)
    {
        try {
            $dataSample = VotingSample::find($id);

            $dataSample->delete();

            return response()->json([
                'status' => 'success',
                'data' => true,
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

    private function inputMember($memberAttendanceId, $eventId)
    {
        $explodeMemberAttendanceId = explode(',', $memberAttendanceId);

        collect($explodeMemberAttendanceId)->map(function ($item) use ($eventId) {
            $attendanceId = $this->checkUser($item);

            VotingMember::create([
                'voting_event_id' => $eventId,
                'attendance_id' => $attendanceId,
            ]);
        });
    }

    private function checkUser($attendanceId)
    {
        $dataUser = User::select('name')->where('attendance_id', '=', $attendanceId)->first();

        if($dataUser == null) {
            $userSIP = UserSIP::select('username', 'attendance_id', 'sub_section_id', 'seksi', 'data_karyawans.nip', 'data_karyawans.img_karyawan')
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
                'nip' => $userSIP->nip,
                'photo' => $userSIP->img_karyawan
            ]);
        }

        return $attendanceId;
    }

    private function updateRequestEvent($request, $id)
    {
        $userId = Auth::user()->id;
        $user = DB::table('users')->select('name')->where('id', '=', $userId)->first();

        $votingEvent = VotingEvent::find($id);

        $requests = [
            'start_date' => ($request->start_date) ? $request->start_date : $votingEvent->start_date,
            'title' => ($request->title) ? $request->title : $votingEvent->title,
            'description' => ($request->description) ? $request->description : $votingEvent->description,
            'updated_by' => $user->name,
        ];

        $data = compact('votingEvent', 'requests');

        return $data;
    }

    private function deleteInvitation($id)
    {
        $dataInvitation = VotingMember::select('id')->where('voting_event_id', '=', $id)->get();

        if ($dataInvitation) {
            collect($dataInvitation)->map(function ($index, $item) {
                $invitation = VotingMember::find($index->id);

                $invitation->delete();
            });
        }
    }

    private function deleteSample($id)
    {
        $dataSample = VotingSample::select('id')->where('voting_event_id', '=', $id)->get();

        if ($dataSample) {
            collect($dataSample)->map(function ($index, $item) {
                $sample = VotingSample::find($index->id);

                $sample->delete();
            });
        }
    }
}
