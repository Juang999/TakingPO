<?php

namespace App\Http\Controllers\Api;

use App\BufferProduct;
use App\Distributor;
use App\Http\Controllers\Controller;
use App\Http\Requests\PreOrderRequest;
use App\Size;
use App\TemporaryStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function StoreClothes(PreOrderRequest $request, $phone)
    {
        $user = Distributor::where('phone', $phone)->with('PartnerGroup')->first();

            if (!$user) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'number '.$phone.' not registered'
                ], 400);
            }

        try {
            $user = Distributor::where('phone', $phone)->with('PartnerGroup')->first();

            if (!$user) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'number '.$phone.' not registered'
                ], 400);
            }

            DB::beginTransaction();

            $size_s = Size::where('size', 's')->first();
            $BufferStock_s = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_s->id
            ])->first();


            if ($BufferStock_s) {
                $qty_process = $BufferStock_s->qty_process + $request->size_s;

                $BufferStock_s->update([
                    'qty_process' => $qty_process
                ]);
            }

            $size_m = Size::where('size', 'm')->first();
            $BufferStock_m = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_m->id
            ])->first();

            if ($BufferStock_m) {
                $qty_process = $BufferStock_m->qty_process + $request->size_m;

                $BufferStock_m->update([
                    'qty_process' => $qty_process
                ]);
            }

            $size_l = Size::where('size', 'l')->first();
            $BufferStock_l = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_l->id
            ])->first();

            if ($BufferStock_l) {
                $qty_process = $BufferStock_l->qty_process + $request->size_l;

                $BufferStock_l->update([
                    'qty_process' => $qty_process
                ]);
            }

            $size_xl = Size::where('size', 'xl')->first();
            $BufferStock_xl = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_xl->id
            ])->first();

            if ($BufferStock_xl) {
                $qty_process = $BufferStock_xl->qty_process + $request->size_xl;

                $BufferStock_xl->update([
                    'qty_process' => $qty_process
                ]);
            }

            $size_xxl = Size::where('size', 'xxl')->first();
            $BufferStock_xxl = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_xxl->id,
                'qty_buffer' => 0
            ])->first();

            if ($BufferStock_xxl) {
                $qty_process = $BufferStock_xxl->qty_process + $request->size_xxl;

                $BufferStock_xxl->update([
                    'qty_process' => $qty_process
                ]);
            }

            $size_xxxl = Size::where('size', 'xxxl')->first();
            $BufferStock_xxxl = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_xxxl->id,
                'qty_buffer' => 0
            ])->first();

            if ($BufferStock_xxxl) {
                $qty_process = $BufferStock_xxxl->qty_process + $request->size_xxxl;

                $BufferStock_xxxl->update([
                    'qty_process' => $qty_process
                ]);
            }

            $size_2 = Size::where('size', '2')->first();
            $BufferStock_2 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_2->id
            ])->first();

            if ($BufferStock_2) {
                $qty_process = $BufferStock_2->qty_process + $request->size_2;

                $BufferStock_2->update([
                    'qty_process' => $qty_process
                ]);
            }

            $size_4 = Size::where('size', '4')->first();
            $BufferStock_4 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_4->id
            ])->first();

            if ($BufferStock_4) {
                $qty_process = $BufferStock_4->qty_process + $request->size_4;

                $BufferStock_4->update([
                    'qty_process' => $qty_process
                ]);
            }

            $size_6 = Size::where('size', '6')->first();
            $BufferStock_6 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_6->id
            ])->first();

            if ($BufferStock_6) {
                $qty_process = $BufferStock_6->qty_process + $request->size_6;

                $BufferStock_6->update([
                    'qty_process' => $qty_process
                ]);
            }

            $size_8 = Size::where('size', '8')->first();
            $BufferStock_8 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_8->id
            ])->first();

            if ($BufferStock_8) {
                $qty_process = $BufferStock_8->qty_process + $request->size_8;

                $BufferStock_8->update([
                    'qty_process' => $qty_process
                ]);
            }

            $size_10 = Size::where('size', '10')->first();
            $BufferStock_10 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_10->id
            ])->first();

            if ($BufferStock_10) {
                $qty_process = $BufferStock_10->qty_process + $request->size_10;

                $BufferStock_10->update([
                    'qty_process' => $qty_process
                ]);
            }

            $size_12 = Size::where('size', '12')->first();
            $BufferStock_12 = BufferProduct::where([
                'clothes_id' => $request->clothes_id,
                'size_id' => $size_12->id
            ])->first();

            if ($BufferStock_12) {
                $qty_process = $BufferStock_12->qty_process + $request->size_12;

                $BufferStock_12->update([
                    'qty_process' => $qty_process
                ]);
            }

            $data = TemporaryStorage::create([
                'distributor_id' => $user->id,
                'clothes_id' => $request->clothes_id,
                'info' => $request->info,
                'veil' => $request->veil,
                'size_s' => $request->size_s,
                'size_m' => $request->size_m,
                'size_l' => $request->size_l,
                'size_xl' => $request->size_xl,
                'size_xxl' => $request->size_xxl,
                'size_xxxl' => $request->size_xxxl,
                'size_2' => $request->size_2,
                'size_4' => $request->size_4,
                'size_6' => $request->size_6,
                'size_8' => $request->size_8,
                'size_10' => $request->size_10,
                'size_12' => $request->size_12,
                'total' => $request->total
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'success input data',
                'data' => $data,
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'status' => 'failed',
                'message' => 'failed to create pre-order',
                'error' => $th->getMessage()
            ]);
        }
    }
}
