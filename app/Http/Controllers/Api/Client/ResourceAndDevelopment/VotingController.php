<?php

namespace App\Http\Controllers\Api\Client\ResourceAndDevelopment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{DB, Auth};
use App\Http\Requests\Admin\Voting\VoteSampleRequest;
use App\Models\{VotingEvent, VotingScore, SampleProduct};

class VotingController extends Controller
{
    public function showSampleForClient()
    {
        try {
            $dataSample = SampleProduct::select(DB::raw('voting_events.id AS voting_event_id'), DB::raw('voting_samples.id AS sample_id'), DB::raw('sample_products.id AS id'), 'date','article_name','style_id','entity_name','material','size','accessories','note_and_description')
                                ->leftJoin('voting_samples', 'voting_samples.sample_product_id', '=', 'sample_products.id')
                                ->leftJoin('voting_events', 'voting_events.id', '=', 'voting_samples.voting_event_id')
                                ->with([
                                    'PhotoSampleProduct' => fn ($query) => $query->select('id', 'sample_product_id', 'photo'),
                                    'FabricTexture' => fn ($query) => $query->select('id', 'sample_product_id', 'photo', 'description')
                                ])->where('sample_products.id', '=', function ($query) {
                                    $query->select('sample_product_id')
                                        ->from('voting_samples')
                                        ->where([
                                            ['show', '=', true],
                                            ['voting_event_id', '=', function ($query) {
                                                $query->select('id')
                                                    ->from('voting_events')
                                                    ->where('is_activate', '=', true)
                                                    ->first();
                                            }]
                                        ])->first();
                                })->first();

            $dataEvent = VotingEvent::select('id', 'start_date', 'title', 'description')->where('is_activate', '=', true)->first();

            return response()->json([
                'status' => 'success',
                'data' => compact('dataSample', 'dataEvent'),
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
            $user = Auth::user();

            $dataVoting = VotingScore::create([
                'voting_event_id' => $request->voting_event_id,
                'sample_id' => $request->sample_id,
                'sample_product_id' => $request->product_id,
                'attendance_id' => $user->attendance_id,
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

    public function getHistoryVote()
    {
        try {
            $user = Auth::user();
            $eventId = request()->event_id;

            $history = VotingScore::select(
                                        'voting_scores.id',
                                        DB::raw('voting_events.title'),
                                        DB::raw('voting_events.start_date'),
                                        DB::raw('sample_products.article_name'),
                                        DB::raw('sample_products.entity_name'),
                                        'voting_scores.score',
                                        'voting_scores.created_at'
                                    )->leftJoin('voting_events', 'voting_events.id', '=', 'voting_scores.voting_event_id')
                                ->leftJoin('sample_products', 'sample_products.id', '=', 'voting_scores.sample_product_id')
                                ->where('voting_scores.attendance_id', '=', $user->attendance_id)
                                ->when($eventId, fn ($query) => $query->where('voting_event_id', '=', $eventId))
                                ->orderByDesc('voting_scores.created_at')
                                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $history,
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
