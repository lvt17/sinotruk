<?php

namespace App\Http\Controllers;

use App\Category;
use App\MyUtils;
use App\Product;
use App\User;
use App\ProductLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SentEmail;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request){
		$key = Input::get('key');
		$categoryFilter = Input::get('category');

		$items = Product::with('categories')
			->where(function ($query) use ($key) {
				$query->where('name', 'like', "%$key%")
					->orWhere('code', 'like', "%$key%")
					->orWhere('mansx', 'like', "%$key%");
			})
			->when($categoryFilter, function ($query, $categoryFilter) {
				return $query->whereHas('categories', function ($subQuery) use ($categoryFilter) {
					$subQuery->where('category_id', $categoryFilter);
				});
			})
			->orderByDesc('updated_at')
			->orderByRaw("CAST(code as UNSIGNED) ASC");

		$totalSiQuery = clone $items;
		$totalSi = $totalSiQuery->sum(\DB::raw('total * price_bulk'));
		$totalLe = $totalSiQuery->sum(\DB::raw('total * price'));
		$items = $items->paginate(10);
		if ($request->expectsJson()) {
			return response()->json([
				'items' => $items,
				'totalSi' => $totalSi,
				'totalLe' => $totalLe,
			]);
		}
	}


    public function getallproduct(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'items' => Product::select('id','name')->orderByDesc('created_at')->get(),
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
//        $this->validate($request, [
//            'name' => 'required|unique:products',
//            'type' => 'required',
//            'code' => 'required|unique:products',
//            'price' => 'required|numeric|min:0',
//            'price_bulk' => 'required|numeric|min:0',
//            'total' => 'required|numeric|min:0',
//        ]);

        $rules = [
            'name' => 'required|unique:products',
            'type' => 'required',
            'code' => 'required|unique:products',
            'price' => 'required|numeric|min:0',
            'price_bulk' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ];

        $customMessages = [
            'unique' => ':attribute đã tồn tại.'
        ];

        $this->validate($request, $rules, $customMessages);

        \DB::beginTransaction();
        $product = Product::create([
			'code' => strtoupper($request->code),
            'name' => $request->name,
            'mansx' => strtoupper($request->mansx),
            'type' => $request->type,
            'price' => $request->price == null ? 0 : $request->price,
            'price_bulk' => $request->price_bulk == null ? 0 : $request->price_bulk,
            'total' => $request->total == null ? 0 : $request->total,
            'order_pending' => $request->order_pending == null ? 0 : $request->order_pending,
            'image' => $request->itemImage,
            'weight' => $request->weight == "" || $request->weight == null ? 0 :  $request->weight,
			'note' => $request->note == "" || $request->note == null ? null :  $request->note,
			'min' => $request->min == null ? 0 : $request->min,
            //'category_id' => $request->category == "" ? null : $request->category,
        ]);

        if ($request->categories != null && count($request->categories) > 0) {
            $idCategories = array_column($request->categories, 'id');
            $product->categories()->attach($idCategories);
        }


        //old: $log_content = "$product->name:  <br>";
        $log_content = "";
        $log_content = $log_content. "- [Tồn] 0 -> $product->total  <br>";
        $log_content = $log_content. "- [Giá lẻ] 0 -> $product->price  <br>";
        $log_content = $log_content. "- [Giá sỉ] 0 -> $product->price_bulk  <br>";
        if ($product->order_pending > 0)
            $log_content = $log_content. "- [Đang chờ] 0 -> $product->order_pending  <br>";
        //old
//        ProductLog::create([
//            'name' => 'Thêm sản phẩm',
//            'value' => $log_content,
//            'user_id' => auth()->id(),
//        ]);
        ProductLog::create([
            'name' => 'Thêm sản phẩm',
            'value' => $log_content,
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        if ($request->itemImage != null) {
            $file = 'img/products/temps/' . $request->itemImage;
            $file_new = 'img/products/' . $request->itemImage;
            $img = \Image::make($file);
            $img->resize(env('IMG_WIDTH', 150), null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $img->save($file_new);
            File::delete('img/products/temps/' . $request->itemImage);
        }

        \DB::commit();
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
            'code' => 'required',
            'name' => 'required',
            'type' => 'required',
            'price' => 'required|numeric|min:0',
            'price_bulk' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
        ]);


        \DB::beginTransaction();
        $item = Product::find($id);
        $isWriteLog = false;
        //old: $log_content = "$item->name:  <br>";
        $log_content = "";
        $old_price = $item->price;
        $old_total = $item->total;
        $old_price_bulk = $item->price_bulk;
        $old_image = $item->image;
        $old_order_pending = $item->order_pending;
        $item->code = strtoupper($request->code);
        $item->name = $request->name;
        $item->mansx = strtoupper($request->mansx);
        $item->type = $request->type;
        $item->price = $request->price == null ? 0 : $request->price;
        $item->price_bulk = $request->price_bulk == null ? 0 : $request->price_bulk;
        $item->total = $request->total == null ? 0 : $request->total;
        $item->order_pending = $request->order_pending == null ? 0 : $request->order_pending;
        $item->image =  $request->itemImage;
        $item->weight = $request->weight == null ? 0 : $request->weight;
		$item->note = $request->note == null ? null : $request->note;
		$item->min = $request->min == null ? 0 : $request->min;
        //$item->category_id = $request->category == "" ? null : $request->category;
        $item->save();

        $item->categories()->detach();
        if ($request->categories != null || count($request->categories) > 0) {
            $idCategories = array_column($request->categories, 'id');
            $item->categories()->attach($idCategories);
        }


        if ($old_image != $item->image){
            if ($item->image != null) {
                $file = 'img/products/temps/' . $request->itemImage;
                $file_new = 'img/products/' . $request->itemImage;
                $img = \Image::make($file);
                $img->resize(env('IMG_WIDTH', 150), null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $img->save($file_new);
                File::delete('img/products/temps/' . $request->itemImage);
            }
            File::delete('img/products/' . $old_image);
        }

        if ($old_total != $item->total){
            $log_content = $log_content. "- [Tồn] $old_total -> $item->total  <br>";
            $isWriteLog = true;
        }

        if ($old_price != $item->price) {
            $log_content = $log_content. "- [Giá lẻ] $old_price -> $item->price  <br>";
            $isWriteLog = true;
        }

        if ($old_price_bulk != $item->price_bulk) {
            $log_content = $log_content. "- [Giá sỉ] $old_price_bulk -> $item->price_bulk  <br>";
            $isWriteLog = true;
        }

        if ($old_order_pending != $item->order_pending) {
            $log_content = $log_content. "- [Đang chờ] $old_order_pending -> $item->order_pending  <br>";
            $isWriteLog = true;
        }

        if ($isWriteLog) {
            //New
            ProductLog::create([
                'name' => 'Cập nhật sản phẩm',
                'value' => $log_content,
                'user_id' => auth()->id(),
                'product_id' => $id,
            ]);
        }
        \DB::commit();
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

    public function bulkDelete(Request $request) {

        $this->validate($request, [
            'ids' => 'required',
        ]);
        foreach($request->ids as $id) {
            $product = Product::find($id);
            //old: $log_content = "$product->name <br>";
            $product->code = $product->code . '_x_' . date("dmyHis");
            $product->name = $product->name . '_x_' . date("dmyHis");
            $product->save();
            $product->categories()->detach();
            $product->delete();
            File::delete('img/products/' . $product->image);
            //old
//            ProductLog::create([
//                'name' => 'Xóa sản phẩm',
//                'value' => $log_content,
//                'user_id' => auth()->id(),
//            ]);
            ProductLog::create([
                'name' => 'Xóa sản phẩm',
                'value' => "",
                'user_id' => auth()->id(),
                'product_id' => $id,
            ]);
        }
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

	public function uploadExcel(Request $request) {
    $file_name = time() . $request->uploadFile->getClientOriginalName();
    $request->uploadFile->move('uploads/', $file_name);
    \Excel::filter('chunk')->load('uploads/' . $file_name)->chunk(30, function ($results) {
        \DB::beginTransaction();
        foreach ($results as $value) {
            try {
				$item = Product::where('code', strtoupper($value->ma))->first();
				$categories = [];
				// Kiểm tra file excel có chứa cột category hay không
                if (isset($value->category)) {
                    $categoryNames = explode(',', $value->category);
                    foreach ($categoryNames as $categoryName) {
                        // Kiểm tra xem danh mục đã tồn tại trong cơ sở dữ liệu hay chưa
                        $existingCategory = Category::where('name', $categoryName)->first();

                        if ($existingCategory) {
                            // Sử dụng danh mục đã tồn tại
                            $categories[] = $existingCategory;
                        } else {
                            // Tạo mới danh mục
                            $newCategory = new Category();
                            $newCategory->name = $categoryName;
                            $newCategory->save();
                            $categories[] = $newCategory;
                        }
                    }
                } else {
                    // Nếu file excel không có cột category, sử dụng danh mục của sản phẩm nếu có
                    if ($item && $item->categories->count() > 0) {
                        $categories = $item->categories;
                    }   
                }
                if ($item) {
					$item->name = (isset($value->ten) && !empty($value->ten)) ? $value->ten : $item->name;
                    $item->type = isset($value->don_vi_tinh) ? $value->don_vi_tinh : $item->type;
                    $item->order_pending = isset($value->dang_cho) ? $value->dang_cho : $item->order_pending;
                    $item->price = isset($value->gia_le) ? $value->gia_le : $item->price;
                    $item->price_bulk = isset($value->gia_si) ? $value->gia_si : $item->price_bulk;
                    $item->total = isset($value->ton) ? $value->ton : $item->total;
                    $item->weight = isset($value->can_nang) ? $value->can_nang : $item->weight;
                    $item->note = isset($value->ten_tq) ? $value->ten_tq : $item->note;
					$item->min = isset($value->toi_thieu) ? $value->toi_thieu : $item->min;
					$item->mansx = isset($value->ma_nsx) ? strtoupper($value->ma_nsx) : $item->mansx;
                    $item->image = isset($value->hinh) ? $value->hinh : ($value->ma . '.jpg');
					$item->categories()->sync(collect($categories)->pluck('id'));
                    $item->save();
                } else {
                    $newProduct = new Product();
                    $newProduct->code = strtoupper($value->ma);
                    $newProduct->name = isset($value->ten) ? $value->ten : null;
                    $newProduct->type = isset($value->don_vi_tinh) ? $value->don_vi_tinh : null;
                    $newProduct->order_pending = isset($value->dang_cho) ? $value->dang_cho : 0;
                    $newProduct->price = isset($value->gia_le) ? $value->gia_le : 0;
                    $newProduct->price_bulk = isset($value->gia_si) ? $value->gia_si : 0;
                    $newProduct->total = isset($value->ton) ? $value->ton : 0;
                    $newProduct->weight = isset($value->can_nang) ? $value->can_nang : null;
                    $newProduct->note = isset($value->ten_tq) ? $value->ten_tq : null;
					$newProduct->min = isset($value->toi_thieu) ? $value->toi_thieu : 0;
                    $newProduct->mansx = isset($value->ma_nsx) ? strtoupper($value->ma_nsx) : null;
                    $newProduct->image = isset($value->hinh) ? $value->hinh : ($value->ma . '.jpg');
                    $newProduct->save();
                    $newProduct->categories()->saveMany($categories);
                }

            } catch (\Illuminate\Database\QueryException $e) {
                // Xử lý lỗi nếu cần thiết
            }
        }
        \DB::commit();
    });
    File::delete('uploads/' . $file_name);
}


    public function filter(Request $request) {
        $products = Product::where('name', 'like', '%' . $request->text . "%")->get();
        if ($request->expectsJson()) {
            return response()->json([
                'products' => $products
            ]);
        }
    }

    public function exportton()
    {
        $key = Input::get('key');
        $categoryFilter = Input::get('category');

        if ($key == null || $key == '')
            $items = Product::with('categories')->orderByDesc('updated_at')->orderByRaw("CAST(code as UNSIGNED) ASC");
        else
            $items = Product::with('categories')->where('name', 'like', "%$key%")
                ->orWhere('code', 'like', "%$key%")
                ->orWhere('mansx', 'like', "%$key%")
                ->orderByDesc('updated_at')->orderByRaw("CAST(code as UNSIGNED) ASC");

        //lọc category
        if ($categoryFilter != '') {
            $items = $items->whereHas('categories', function ($query) use ($categoryFilter) {
                $query->where('category_id', $categoryFilter);
            });
        }
        $items = $items->get();
		
		//Gửi email thông báo
		if (env('ENABLE_SENT_EMAIL', false) === true) {
		$emailType = 'excel_export'; // Loại email cụ thể
		$receivedEmail = env('RECEIVED_EMAILS', 'chienguyenkhac@gmail.com'); // Lấy giá trị hoặc giá trị mặc định
		$data = [			
            'userName'=>auth()->user()->name,
            'date' => Carbon::now()->format('Y-m-d H:i:s'),
			'type' =>'danh sách sản phẩm',
			'email'=>$receivedEmail,
		];
		dispatch(new SentEmail($emailType, $data));
		}
		//Ghi vào logs
		
		   ProductLog::create([
                'name' => "Xuất Excel SP",
                'value' => "Xuất Excel chứa danh sách sản phẩm từ hệ thống SINOTRUK",
                'user_id' => auth()->id(),
            ]);
		
        \Excel::create(Carbon::now()->format('dmY') . '_' . 'Ton', function($excel) use($items){
            $excel->sheet('Tồn', function($sheet) use($items){
                $sheet->row(1, array(
                    'STT', 'Mã', 'Tên', 'Category', 'Tồn', 'Đang chờ', 'Đơn vị tính', 'Cân nặng (kg)', 'Giá lẻ', 'Giá sỉ', 'Hình', 'Hình ảnh', 'Tên TQ', 'Tối thiểu', 'Mã NSX'
                ));
                $sheet->row(1, function($row) {
                    $row->setBackground('#000000');
                    $row->setFontColor('#ffffff');
                });

                foreach ($items as $key=>$item) {
                    $sheet->row($key + 2, array(
                        $key + 1,
                        $item->code,
                        $item->name,
                        $item->categories->count() <= 0 ? "" : $item->categories->implode('name', ','),
                        $item->total,
                        $item->order_pending,
                        $item->type,
                        $item->weight,
                        $item->price,
                        $item->price_bulk,
                        $item->image,
                        $item->image != null && file_exists('img/products/' . $item->image) ? 1 : 0,
						$item->note, //Tên Trung Quốc
						$item->min, //Tối thiểu
                        $item->mansx,
                    ));
                }
            });

        })->download('xlsx');
		
		
    }

    public function moneyFormat($value) {
        return preg_replace("/\.?0+$/", "", number_format($value, 2));
    }

    public function logs(Request $request)
    {
        $strFromDate = Input::get('tungay');
        $strToDate = Input::get('toingay');
        $nameFilter = Input::get('phuongthuc');
        $customerFilter = Input::get('khachhang');
        $productFilter = Input::get('sanpham');
        $valueFilter = Input::get('trangthai');

        $items = ProductLog::with('user', 'customer', 'product');
        if (!($strToDate == null || $strToDate == '')) {
            $strToDate = $strToDate . '235959';
            $toDate = Carbon::createFromFormat('dmYHis', $strToDate);
            $items = $items
                ->where('created_at', '<', $toDate);
        }
        if (!($strFromDate == null || $strFromDate == '')) {
            $strFromDate = $strFromDate . '000000';
            $fromDate = Carbon::createFromFormat('dmYHis', $strFromDate);
            $items = $items
                ->where('created_at', '>=', $fromDate);
        }
        if ($valueFilter != '') {
            $items = $items
                ->where('value', 'like', "%$valueFilter%");
//            $items = $items->whereHas('user', function ($query) use ($customerFilter) {
//                $query->where('name', 'like', "%$customerFilter%");
//            });
        }
        if ($customerFilter != '') {
            $items = $items->whereHas('customer', function ($query) use ($customerFilter) {
                $query->where('id', $customerFilter);
            });
        }
        if ($productFilter != '') {
            $items = $items->whereHas('product', function ($query) use ($productFilter) {
                $query->where('id', $productFilter);
            });
        }
        if ($nameFilter != '') {
            $items = $items->where('name', $nameFilter);
        }
        $items = $items->orderByDesc('created_at')->paginate(10);
        if ($request->expectsJson()) {
            return response()->json([
                'items' => $items,
            ]);
        }
    }

    public function uploadimage(Request $request) {
        $file_name = time().$request->uploadFile->getClientOriginalName();
        $request->uploadFile->move('img/products/temps/', $file_name);
        if ($request->expectsJson()) {
            return response()->json([
                'itemImage' => $file_name,
                'itemImageUrl' => asset('img/products/temps/' . $file_name),
            ]);
        }
    }

    public function exportexcellogs()
    {
        $strFromDate = Input::get('tungay');
        $strToDate = Input::get('toingay');
        $nameFilter = Input::get('phuongthuc');
        $customerFilter = Input::get('khachhang');
        $productFilter = Input::get('sanpham');
        $valueFilter = Input::get('trangthai');

        $items = ProductLog::with('user', 'customer', 'product');
        if (!($strToDate == null || $strToDate == '')) {
            $strToDate = $strToDate . '235959';
            $toDate = Carbon::createFromFormat('dmYHis', $strToDate);
            $items = $items
                ->where('created_at', '<', $toDate);
        }
        if (!($strFromDate == null || $strFromDate == '')) {
            $strFromDate = $strFromDate . '000000';
            $fromDate = Carbon::createFromFormat('dmYHis', $strFromDate);
            $items = $items
                ->where('created_at', '>=', $fromDate);
        }
        if ($valueFilter != '') {
            $items = $items
                ->where('value', 'like', "%$valueFilter%");
//            $items = $items->whereHas('user', function ($query) use ($customerFilter) {
//                $query->where('name', 'like', "%$customerFilter%");
//            });
        }
        if ($customerFilter != '') {
            $items = $items->whereHas('customer', function ($query) use ($customerFilter) {
                $query->where('id', $customerFilter);
            });
        }
        if ($productFilter != '') {
            $items = $items->whereHas('product', function ($query) use ($productFilter) {
                $query->where('id', $productFilter);
            });
        }
        if ($nameFilter != '') {
            $items = $items->where('name', $nameFilter);
        }

        $items = $items->get();

        \Excel::create(Carbon::now()->format('dmY') . '_' . 'NhatKySanPham', function($excel) use($items){
            $excel
                ->sheet('Nhật ký sản phẩm', function($sheet) use($items){
                $sheet->row(1, array(
                    'STT', 'Phương thức', 'Khách hàng', 'Sản phẩm', 'Trạng thái', 'Tên', 'Ngày'
                ));
                $sheet->row(1, function($row) {
                    // call cell manipulation methods
                    $row->setBackground('#000000');
                    $row->setFontColor('#ffffff');
                });

                foreach ($items as $key=>$item) {

                    $sheet->row($key + 2, array(
                        $key + 1,
                        $item->name,
                        $item->customer == null ? "" : $item->customer->name,
                        $item->product == null ? "" : $item->product->name,
                        str_replace('<br>', PHP_EOL, $item->value),
                        $item->user->name,
                        $item->updated_at->format('d/m/Y H:i:s'),
                    ));
                }
            });

        })->download('xlsx');;
    }
}
