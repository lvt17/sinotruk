<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class UserController extends Controller
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
                'items' => User::orderByDesc('created_at')->paginate(10),
            ]);
        }
    }

    public function getalluser(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'items' => User::orderByDesc('created_at')->get(),
            ]);
        }
    }

    public function find(Request $request)
    {
        $q = Input::get('q');
        if ($request->expectsJson()) {
            return response()->json([
                'items' => User::where('full_name', 'like', "%$q%")->orderByDesc('created_at')->get(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required|unique:users|max:255',
            'password' => 'required',
            'admin' => 'required',
            'phone' => 'required',
            'full_name' => 'required',
            'money' => 'required',
        ]);

        User::create([
            'name' => $request->name,
            'password' => bcrypt($request->password),
            'admin' => $request->admin,
            'phone' => $request->phone,
            'full_name' => $request->full_name,
            'money' => $request->money,
        ]);
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
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

        $this->validate($request, [
            'name' => 'required|max:255',
            'password' => 'required',
            'admin' => 'required',
            'phone' => 'required',
            'full_name' => 'required',
            'money' => 'required',
        ]);
        $item = User::find($id);
        $item->name = $request->name;
        if ($request->password != $request->oldpassword)
            $item->password = bcrypt($request->password);
        $item->admin = $request->admin;
        $item->phone = $request->phone;
        $item->full_name = $request->full_name;
        $item->money = $request->money;
        $item->save();
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

    public function destroy(Request $request, $id)
    {

        $item = User::find($id);
        $item->name = $item->name . '_x_' . date("dmyHis");
        $item->save();
        $item->delete();
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

    public function filter(Request $request) {
        $items = User::where('name', 'like', '%' . $request->text . "%")
            ->orWhere('full_name', 'like', '%' . $request->text . "%")
            ->get();
        if ($request->expectsJson()) {
            return response()->json([
                'items' => $items
            ]);
        }
    }
}
