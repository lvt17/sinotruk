<?php

namespace App\Http\Controllers;

use App\Option;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        if ($request->expectsJson()) {
            return response()->json([
                'items' => Option::all(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $item = Option::find($id);
        $item->value = $request->value == null ? '' : $request->value;
        $item->save();
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cập nhật thành công',
            ]);
        }
    }

    public function thongbao(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'thongbao' => Option::where('name', 'Thông báo')->first(),
            ]);
        }
    }
}
