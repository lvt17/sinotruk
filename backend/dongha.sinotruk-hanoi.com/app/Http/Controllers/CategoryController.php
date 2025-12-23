<?php

namespace App\Http\Controllers;

use App\Category;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $key = Input::get('key');
        $items = Category::orderBy('created_at');
        if ($key != '') {
            $items = $items->where('name', 'like', "%$key%");
        }
        $items = $items->get();
        if ($request->expectsJson()) {
            return response()->json([
                'items' => $items,
            ]);
        }
    }

    public function getallcategories(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'items' => Category::select('id','name')->orderByDesc('created_at')->get(),
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

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
        ]);
        $item = Category::create([
            'name' => mb_strtoupper($request->name, 'UTF-8'),
        ]);
        if ($request->expectsJson()) {
            return response()->json([
                'item' => $item,
                'status' => 1,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        ]);
        $item = Category::find($id);
        $item->name = $request->name;
        $item->save();
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $item = Category::find($id);
        $product = Product::where('category_id', $id)->first();
        if ($product != null)
        {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Còn sản phẩm trong category này, vui lòng xóa hết sản phẩm trước khi xóa category',
                ]);
            }
        }
        $item->delete();
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }

    }

    public function uploadExcel(Request $request) {
        $file_name = time().$request->uploadFile->getClientOriginalName();
        $request->uploadFile->move('uploads/', $file_name);
        $items = collect();
        \Excel::load('uploads/' . $file_name, function($reader) use ($items)
        {
            $results = $reader->get();
            foreach($results as $value)
            {
                try
                {
                    $item = Category::where('name', $value->ten)->first();
                    if ($item != null || $value->ten == null)
                        continue;
                    Category::create([
                        'name' => $value->ten,
                    ]);
                }
                catch (\Illuminate\Database\QueryException $e) {}
            }
        });
        File::delete('uploads/' . $file_name);
    }
}
