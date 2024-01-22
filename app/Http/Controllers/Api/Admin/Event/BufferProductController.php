<?php

namespace App\Http\Controllers\Api\Admin\Event;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BufferProduct;
use App\Http\Requests\Admin\BufferProduct\CreateBufferProductRequest;

class BufferProductController extends Controller
{
    public function createBufferProduct(CreateBufferProductRequest $request)
    {
        try {
            $bufferProduct = BufferProduct::create([
                'clothes_id' => $request->clothes_id,
                'qty_avaliable' => $request->qty,
                'qty_process' => 0,
                'qty_buffer' => $request->qty
            ]);

            return response()->json([
                'status ' => 'success',
                'data' => $bufferProduct,
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

    public function increaseAmount(Request $request, BufferProduct $buffer_product)
    {
        try {
            $updateBuffer = $buffer_product->update([
                'qty_avaliable' => $buffer_product->qty_avaliable + $request->qty,
                'qty_buffer' => $buffer_product->qty_buffer + $request->qty
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $updateBuffer,
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
