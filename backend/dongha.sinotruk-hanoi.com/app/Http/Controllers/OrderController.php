<?php

namespace App\Http\Controllers;

use App\Order;
use App\User;
use App\Permission;
use App\Product;
use App\ProductLog;
use App\Role;
use Carbon\Carbon;
use App\Option;
use function GuzzleHttp\Psr7\copy_to_string;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $strFromDate = Input::get('fromdate');
        $strToDate = Input::get('todate');
        $user_name = Input::get('key');
        $nameFilter = Input::get('nameFilter');
        $product_id = Input::get('product');
        $items = Order::with('user', 'user_update')->orderByDesc('id');
        $user = auth()->user();
        // Kiểm tra nếu là admin thì có quyền đọc tất cả
        $have_permission_readall = $user->admin ? true : false;
        $have_permission_thongtinloinhuangia = $user->admin ? true : false;
    
       if (!$user->admin) {
            $permission_readall = Permission::where("name", "order_readall")->first();
            $roles_has_permssions = $permission_readall->roles;
            foreach ($roles_has_permssions as $role_has_permssions) {
                if ($user->roles->contains('id', $role_has_permssions->id)) {
                    $have_permission_readall = true;
                    break;
                }
            }

            // Kiểm tra quyền order_thongtinloinhuangia
            $permission_thongtinloinhuangia = Permission::where("name", "order_thongtinloinhuangia")->first();
            if ($permission_thongtinloinhuangia) {
                $roles_has_permssions = $permission_thongtinloinhuangia->roles;
    
                foreach ($roles_has_permssions as $role_has_permssions) {
                    if ($user->roles->contains('id', $role_has_permssions->id)) {
                        $have_permission_thongtinloinhuangia = true;
                        break;
                    }
                }
            }
  
        }
        if (!$have_permission_readall) {
            $items = $items->where('invisible', 0)
                ->orWhere("user_id", $user->id);
        }
        
        
        $permission_thongtinloinhuangia = Permission::where("name", "order_thongtinloinhuangia")->first();
        if ($permission_thongtinloinhuangia) {
            $roles_has_permssions = $permission_thongtinloinhuangia->roles;
    
            foreach ($roles_has_permssions as $role_has_permssions) {
                if ($user->roles->contains('id', $role_has_permssions->id)) {
                    $have_permission_thongtinloinhuangia = true;
                    break;
                }
            }
        }
        

        if ($strFromDate != '' &&
            $strFromDate != 'Invalid date') {
            $strFromDate = $strFromDate . '000000';
            $fromDate = Carbon::createFromFormat('dmYHis', $strFromDate);
            $items = $items->where('created_at', '>=', $fromDate);
        }
        if ($strToDate != '' &&
            $strToDate != 'Invalid date') {
            $strToDate = $strToDate . '235959';
            $toDate = Carbon::createFromFormat('dmYHis', $strToDate);
            $items = $items->where('created_at', '<', $toDate);
        }
        if ($user_name != '') {
            //dd($user_name);
            $items = $items->whereHas('user', function ($query) use ($user_name) {
                $query->Where('name', '=', $user_name);
            });

        }
        if ($nameFilter != null && $nameFilter != '') {
            //dd($user_name);
            $items = $items->where("tenphieu", 'like', "%$nameFilter%");
        }
        if ($product_id != '') {
            $items = $items->whereHas('products', function ($query) use ($product_id) {
                $query->Where('products.id', '=', $product_id);
            });
        }
        $items = $items->paginate(15);
		$money = $items->sum('money');
        // Kiểm tra quyền hạn và ẩn các trường nếu người dùng không có quyền
       if (!$have_permission_readall || !$have_permission_thongtinloinhuangia) {
            $items->getCollection()->transform(function ($item) {
                return $item->makeHidden(['tygia', 'loinhuan', 'vanchuyen']);
            });
        }
        
        
        if ($request->expectsJson()) {
            return response()->json([
                'items' => $items,
				'money' => $money,
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
        //
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
//          'money' => 'required|numeric',
            'money' => 'required|numeric',
            'items.length' => 'min:1',
            'tenphieu' => 'required',
        ]);
        $order = Order::createOrder($request);
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
                'item' => $order
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        $order = Order::find($id);

        $order->products->map(function($value) {
            $value->price = $value->pivot->price;
            $value->count = $value->pivot->count; //Số lượng thực nhận Received
            $value->base_number = $value->pivot->base_number; //Số lượng đặt hàng ban đầu Order
            $value->note = $value->pivot->note;
			$value->nametq = $value->pivot->nametq; //Ratio
            $value->weight = $value->pivot->weight;
            $value->hscode = $value->pivot->hscode; // Số lượng TQ gửi đi Sent
			$value->suggest_price = $value->pivot->suggest_price;
            $value->image_pivot = $value->pivot->image;
            $value->hightlight = $value->pivot->hightlight == 1 ? true : false;
        });
        if ($request->expectsJson()) {
            return response()->json([
                'items' => $order->products,
            ]);
        }
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
            'money' => 'required|numeric',
            'items.length' => 'min:1',
            'tenphieu' => 'required',
        ]);
        $item = Order::find($id);
        $item->updateOrder($request);
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
        $item = Order::find($id);
        $item->deleteOrder();
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

    public function exportExcel($id)
    {
        $item = Order::find($id);
        $products = $item->products;
        $totalItemPrice = 0;
        $totalItemPriceReality = 0;
        $totalItemWeight = 0;
        $totalItemVND = 0;
        $vanchuyen = $item->vanchuyen;
        $tygia = $item->tygia;
        $loinhuan = $item->loinhuan;
        $orderName = $item->tenphieu;
		$user = Auth::user();
		$hasPermission = $user->hasPermission('order_thongtinloinhuangia') ? true : false;
		$orderCreatorId = $item->user_id; // Lấy user_id của người tạo đơn hàng
        foreach ($products as $product) {
            $product->price = number_format($product->pivot->price, 2, ".", "");
			// Tìm giá thấp nhất từ các đơn đặt hàng trước đó
			$lowestPriceOrder = DB::table('order_product')
				->select('orders.tenphieu as order_name', 'order_product.price')
				->join('orders', 'orders.id', '=', 'order_product.order_id')
				->where('order_product.product_id', $product->id)
				->where('order_product.price', '>', 0)
				->where('orders.user_id', $orderCreatorId) // Thêm điều kiện lọc theo user_id của người tạo
				->orderBy('order_product.price', 'asc')
				->first();
				if ($lowestPriceOrder) {
					$product->lowestPrice = number_format($lowestPriceOrder->price, 2, ".", "");
					$product->lowestPriceOrderName = $lowestPriceOrder->order_name; // Lấy tên đơn đặt hàng
				} else {
					$product->lowestPrice = 0;
					$product->lowestPriceOrderName = null; // Không có đơn đặt hàng thỏa mãn điều kiện
					}
			$product->base_number = $product->pivot->base_number; //SL đặt hàng ban đầu
			$product->hscode = $product->pivot->hscode; //Xuất số lượng TQ gửi đi Sent
			$product->count = $product->pivot->count; //Xuất số lượng nhận thực tế Received
            $product->total = number_format($product->pivot->hscode * $product->pivot->price, 2, ".", "");
            $product->totalReality = number_format($product->pivot->count * $product->pivot->price * $product->pivot->nametq, 2, ".", "");
            $product->note = $product->pivot->note;
            $product->nametq = $product->pivot->nametq; //Xuất tỉ lệ Ratio
            $product->hightlight = $product->pivot->hightlight;
            $product->vnd = number_format((double)($vanchuyen * $tygia * $loinhuan * $product->pivot->price), 2, ".", "");
            $totalItemPrice += $product->total;
            $totalItemPriceReality += $product->totalReality;
            $totalItemWeight += $product->total_weight;
            $totalItemVND += $product->vnd;
            if ($product->image == null || !file_exists('img/products/' . $product->image))
                $product->image = null;
        }
        
        //Ghi vào logs
		
		ProductLog::create([
                'name' => "Xuất Excel Order",
                'value' => "Xuất Excel Order ". $orderName." từ hệ thống SINOTRUK",
                'user_id' => auth()->id(),
            ]);
            
        \Excel::create(Carbon::now()->format('dmY') . '_order_' . str_slug($item->tenphieu),
            function($excel) use ($products, $totalItemPrice,$totalItemPriceReality, $totalItemWeight, $item, $totalItemVND, $hasPermission ){
                $excel->sheet('Thống kê', function($sheet) use ($products, $totalItemPrice,$totalItemPriceReality, $totalItemWeight, $item, $totalItemVND, $hasPermission){
                    $sheet->loadView('reports.order_excel', [
						'hasPermission'	=> $hasPermission,
                        'tenphieu' => $item->tenphieu,
                        'vanchuyen' => $item->vanchuyen,
                        'tygia' => $item->tygia,
                        'loinhuan' => $item->loinhuan,
                        'products' => $products,
                        'totalItemPrice' => number_format($totalItemPrice, 2, ".", ""),
                        'totalItemPriceReality' => number_format($totalItemPriceReality, 2, ".", ""),
                        'totalItemWeight' => number_format($totalItemWeight, 2, ".", ""),
                        'totalItemVND' => number_format($totalItemVND, 2, ".", ""),
                    ]);
                });
            })->download('xlsx');
    }
	
	
	public function exportPDF($id) 
	{
		$item = Order::find($id);
        $products = $item->products;
        $totalItemPrice = 0;
        $totalItemPriceReality = 0;
        $totalItemWeight = 0;
        $totalItemVND = 0;
        $vanchuyen = $item->vanchuyen;
        $tygia = $item->tygia;
        $loinhuan = $item->loinhuan;
        $orderName = $item->tenphieu;
		$partner = User::where('id', $item->user_id)->first();
		$seal = $item->completed?true:false;
		$user = Auth::user();
		$hasPermission = $user->hasPermission('order_thongtinloinhuangia') ? true : false;
		foreach ($products as $product) {
			$product->base_number = $product->pivot->base_number; //SL đặt hàng ban đầu
			$product->hscode = $product->pivot->hscode; //Xuất số lượng TQ gửi đi Sent
			$product->count = $product->pivot->count; //Xuất số lượng nhận thực tế Received
            $product->price = number_format($product->pivot->price, 2, ".", "");
            $product->total = number_format($product->pivot->hscode * $product->pivot->price, 2, ".", "");
            $product->totalReality = number_format($product->pivot->count * $product->pivot->price * $product->pivot->nametq, 2, ".", "");
            $product->note = $product->pivot->note;
            $product->nametq = $product->pivot->nametq; //Xuất tỉ lệ Ratio
            $product->hightlight = $product->pivot->hightlight;
            $product->vnd = number_format((double)($vanchuyen * $tygia * $loinhuan * $product->pivot->price), 2, ".", "");
            $totalItemPrice += $product->total;
            $totalItemPriceReality += $product->totalReality;
			$totalItemVND = $totalItemPriceReality * $vanchuyen * $tygia * $loinhuan;
            $totalItemWeight += $product->total_weight;
            if ($product->image == null || !file_exists('img/products/' . $product->image))
                $product->image = null;
        }
		
		// Khởi tạo mảng options và thêm các option vào mảng
		$options = [];
		foreach(Option::all() as $option)
		{
			$options[$option->id] = $option->value;
		}

		// Khởi tạo dữ liệu cho PDF
		$data = [
			'options' => $options,
			'partner'    => $partner,
			'hasPermission' => $hasPermission,
			'item'    => $item,
			'seal'    => $seal,
			'tenphieu' => $item->tenphieu,
			'vanchuyen' => $item->vanchuyen,
			'tygia' => $item->tygia,
			'loinhuan' => $item->loinhuan,
			'products' => $products,
			'totalItemPrice' => $totalItemPrice,
			'totalItemPriceReality' => $totalItemPriceReality,
			'totalItemWeight' => $totalItemWeight,
			'totalItemVND' => $totalItemVND,
		];
		// Xuất file PDF
		$pdf = \PDF::loadView('reports.order_pdf', $data);
		$pdf->setPaper('A4', 'landscape');
		return $pdf->download(Carbon::now()->format('dmY') . '_' . str_slug($item->tenphieu). '.pdf');
	}
	
	public function exportExcelUpdatePrice($id)
    {
        $item = Order::find($id);
        $products = $item->products;
        $totalItemPrice = 0;
        $totalItemPriceReality = 0;
        $totalItemWeight = 0;
        $totalItemVND = 0;
        $vanchuyen = $item->vanchuyen;
        $tygia = $item->tygia;
        $loinhuan = $item->loinhuan;
		
        foreach ($products as $product) {
			$product->hscode = $product->pivot->hscode; //Xuất số lượng nhận thực tế Received
			$product->hightlight = $product->pivot->hightlight;
			$product->vnd = number_format((double)($vanchuyen * $tygia * $loinhuan * $product->pivot->price), 2, ".", "");
			
			if ($product->total == 0 && $product->count == 0) {
				// Nếu cả total và count đều bằng 0, gán giá trị 0 cho $rawValue
				$rawValue = 0;
			} else {
				// Tính giá trị không làm tròn
				$rawValue = (($product->total * $product->price_bulk + $product->hscode * $product->vnd) / ($product->total + $product->hscode));
			}
			
			// Làm tròn giá trị đến số gần nhất là 5000
			$roundedValue = round($rawValue / 5000) * 5000;

			// Gán giá trị làm tròn vào thuộc tính gia_capnhat
			$product->gia_capnhat = $roundedValue;
		}

		
        \Excel::create(Carbon::now()->format('dmY') . '_' . '_update_gia_theo_order',
            function($excel) use ($products, $item, $totalItemVND){
                $excel->sheet('UpdatePrice', function($sheet) use ($products, $item){
                    $sheet->loadView('reports.order_excel_update_price', [
                        'tenphieu' => $item->tenphieu,
                        'vanchuyen' => $item->vanchuyen,
                        'tygia' => $item->tygia,
                        'loinhuan' => $item->loinhuan,
                        'products' => $products,
                    ]);
                });
            })->download('xlsx');
    }

    public function moneyFormat($value) {
        return preg_replace("/\.?0+$/", "", number_format($value, 2));
    }

    public function importExcel(Request $request) {
        $file_name = time().$request->uploadFile->getClientOriginalName();
        $request->uploadFile->move('uploads/', $file_name);
        $items = collect();
        \Excel::load('uploads/' . $file_name, function($reader) use ($items)
        {
            $results = $reader->get();
            foreach($results as $value)
            {
                try {
                    $product_code = $value->product_code;
                    if ($product_code != null) {
                        $product = Product::where("code", $product_code)->first();
                        if ($product != null) {
                            $product->base_number = $value->base == null ? 0 : $value->base; //SL Order ban đầu
                            $product->hscode = $value->sent == null ? 0 : $value->sent; //SL TQ gửi đi Sent
                            $product->count = $value->received == null ? 0 : $value->received; //SL thực tế Received
                            $product->nametq = $value->ratio; //Tỉ lệ ratio
                            $product->price = $value->price == null ? 0 : $value->price;
                            $product->weight = $value->weight  == null ? $product->weight : $value->weight;
                            $product->note = $value->note == null ? $product->note : $value->note;
                            $product->image_pivot = null;
                            $items->push($product);
                        }
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                }
            }
        });
		File::delete('uploads/' . $file_name);
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
                'items' => $items
            ]);
        }
        
    }

    public function updateLock(Request $request)
    {
        $item = Order::find($request->id);
        $item->lock = $request->lock;
        $item->save();
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

    public function updateInvisible(Request $request)
    {
        $item = Order::find($request->id);
        $item->invisible = $request->invisible;
        $item->save();
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

    public function updateCompleted(Request $request)
    {
        $item = Order::find($request->id);
        $item->updateCompleted($request->value);
        return response(null, 201);
    }

    public function uploadImage_old(Request $request) {
        $today = Carbon::now();
        collect(File::directories('img/orders/'))->each(function ($directory) {
            $directoryDate = basename($directory);
            if (Carbon::createFromFormat("Y", $directoryDate)->year < Carbon::now()->year - 1) {
                File::deleteDirectory($directory);
            }
//            if (Carbon::parse($directoryDate)->year < Carbon::now()->year - 1) {
//                File::deleteDirectory($directory);
//            }
        });
        $file_name = time() . $request->uploadFile->getClientOriginalName();
        $request->uploadFile->move("img/orders/$today->year/$today->month", $file_name);
        $file = "img/orders/$today->year/$today->month/" . $file_name;
        $img = \Image::make($file);
        $img->resize(env('IMG_WIDTH', 150), null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save($file);
        return response($file, 201);
    }

    public function uploadImage(Request $request) {
        if(!File::exists("img/orders/temp/")) {
            File::makeDirectory("img/orders/temp/", 0777, true, true);
        }
        collect(File::allFiles('img/orders/temp/'))->each(function ($file) {
            $lastModified =  File::lastModified($file);
            $lastModified = Carbon::createFromTimestamp($lastModified);
            if (Carbon::now()->gt($lastModified->addDays(2))) {
                File::delete($file);
            }
        });
        $file_name = time() . $request->uploadFile->getClientOriginalName();
        $request->uploadFile->move("img/orders/temp/", $file_name);
        $file = "img/orders/temp/" . $file_name;
        $img = \Image::make($file);
        $img->resize(env('IMG_WIDTH', 150), null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $img->save($file);
        return response($file, 201);
    }
}
