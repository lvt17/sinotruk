<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerLog;
use App\CustomerPay;
use App\Export;
use App\Exports\MoneyExport;
use App\Import;
use App\Option;
use App\Options;
use App\Product;
use App\ProductLog;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class ExportController extends Controller
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
        $customer_code = Input::get('key');
        $items = Export::with('customer', 'user')->orderByDesc('id');

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
        if ($customer_code != '') {
            $items = $items->whereHas('customer', function ($query) use ($customer_code) {
                $query->Where('code', '=', "$customer_code");
            });
        }
        $items = $items->paginate(10);
        if ($request->expectsJson()) {
            return response()->json([
                'items' => $items,
            ]);
        }
    }

	public function showstatus(Request $request)
{
		$strFromDate = $request->input('tungay');
		$strToDate = $request->input('toingay');
		$customerFilter = $request->input('khachhang');
		$productFilter = $request->input('sanpham');
		$userFilter = $request->input('nguoidung');
		$noteFilter = $request->input('note');
		$vatFilter = $request->input('vat');
		$diff = $request->input('diff') === "true" ? true : ($request->input('diff') === "false" ? false : null);

		$money = 0;
		$debt = 0;
		$total = 0;
		$allDiff = 0;
		$strFromDate .= '000000';
		$strToDate .= '235959';
		$fromDate = Carbon::createFromFormat('dmYHis', $strFromDate);
		$toDate = Carbon::createFromFormat('dmYHis', $strToDate);
		
		//Lấy export và import
		$expors = Export::with('customer', 'user')
			->whereBetween('created_at', [$fromDate, $toDate]);
		$imports = Import::with('customer', 'user')
			->whereBetween('created_at', [$fromDate, $toDate]);
			
		//Thực hiện lọc theo điều kiện
		if (!empty($customerFilter)) {
			$expors = $expors->whereHas('customer', function ($query) use ($customerFilter) {
				$query->where('name', 'like', "%$customerFilter%")
					->orWhere('code', 'like', "%$customerFilter%");
			});
			$imports = $imports->whereHas('customer', function ($query) use ($customerFilter) {
				$query->where('name', 'like', "%$customerFilter%")
					->orWhere('code', 'like', "%$customerFilter%");
			});
		}

		if (!empty($noteFilter)) {
			$expors = $expors->where('note', 'like', "%$noteFilter%");
			$imports = $imports->where('note', 'like', "%$noteFilter%");
		}

		if (!empty($productFilter)) {
			$expors = $expors->whereHas('products', function ($query) use ($productFilter) {
				$query->where('product_id', $productFilter);
			});
			$imports = $imports->whereHas('products', function ($query) use ($productFilter) {
				$query->where('product_id', $productFilter);
			});
		}

		if (!empty($vatFilter)) {
			$vatFilter = $vatFilter === "true" ? true : false;
			$expors = $expors->where('isVAT', $vatFilter);
			$imports = $imports->where('isVAT', $vatFilter);
		}

		if (!empty($userFilter)) {
			$expors = $expors->whereHas('customer', function ($customer) use ($userFilter) {
				$customer->whereHas('user', function($query) use ($userFilter) {
					$query->where('id', $userFilter);
				});
			});
			$imports = $imports->whereHas('customer', function ($customer) use ($userFilter) {
				$customer->whereHas('user', function($query) use ($userFilter) {
					$query->where('id', $userFilter);
				});
			});
		}


		$expors = $expors->select(\DB::raw('1 as loai, id, user_id, customer_id, money, isVAT, money_with_vat, debt, total, cash, discount, updated_at, created_at, note,diff_bulk, diff_retail, 1'));
		$imports = $imports->select(\DB::raw('2 as loai, id, user_id, customer_id, money, isVAT, money_with_vat * -1 as money_with_vat, debt, total, cash, discount, updated_at, created_at, note,diff_bulk, diff_retail, 1'));

		$items = $expors->union($imports)->orderByDesc('updated_at')->get();
		$money = $items->sum('money_with_vat');
		$totalQuery = clone $items;
		$khachhangs = $totalQuery->groupBy('customer_id');
		$total = 0;

		foreach ($khachhangs as $khachhang) {
			$total += $khachhang->sortByDesc('updated_at')->first()->total;
		}

		if ($diff !== null) {
			foreach ($items as $item) {
				$item->diffValue=0;
				$item->diffValue = $diff ? $item->diff_bulk : $item->diff_retail;
				$allDiff += $item->diffValue;
			}
		} else {
			foreach ($items as $item) {
				$item->diffValue = 0;
			}
			$allDiff = 0;
		}
    if ($request->expectsJson()) {
        return response()->json([
            'items' => $items,
            'money' => $money,
            'debt' => $debt,
            'total' => $total,
            'allDiff' => $allDiff,
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
	public function store(Request $request) {
	$this->validate($request, [
            'customer_id' => 'required',
            'total' => 'required|integer',
            'items' => 'required|array|min:1',
            'importDate' => 'required',
            'cash' => 'required',
            'isVAT' => 'required',
    ]);
	
    \DB::beginTransaction();

    try {
		
        $totalBulk = 0;
        $totalRetail = 0;

        $customer = Customer::find($request->customer_id);
        if ($customer->money != $request->debt)
        {
            return response()->json([
                'status' => -2,
                'debt' => $customer->money,
                'message' => "Nợ khách hàng đã bị thay đổi. Vui lòng xem lại phiếu bán!",
            ]);
        }

        // Tính toán công nợ hiện tại của khách hàng
        $congnohientai = Customer::where('user_id', $customer->user_id)->sum("money");
        // Lấy số tiền tối đa mà nhân viên quản lý khách hàng có thể quản lý
        $congnotoida = User::find($customer->user_id)->money;

        if ($congnohientai + $request->money > $congnotoida) {
            \DB::rollBack();
            return response()->json([
                'status' => -2,
                'debt' => $customer->money,
                'message' => "Công nợ nhân viên vượt ngưỡng, Vui lòng thông báo khách hàng thanh toán",
            ]);
        }

        $export = null; // Khởi tạo biến $export

        // Mảng để lưu trữ thông tin sản phẩm đính kèm
        $productData = [];

        foreach ($request->items as $item) {
            $product = Product::find($item['id']);
            $quantity = $item['count'];
            // Tính toán $totalBulk và $totalRetail sử dụng toán tử 3 ngôi
            $totalBulk += $quantity * ($product->price_bulk == 0 ? $item['price'] : $product->price_bulk);
            $totalRetail += $quantity * ($product->price == 0 ? $item['price'] : $product->price);

            // Kiểm tra tồn kho sản phẩm
            if ($product->total < $quantity) {
                \DB::rollBack();
                return response()->json([
                    'status' => -2,
                    'debt' => $customer->money,
                    'message' => "Sản phẩm {$product->name} không đủ tồn kho. Chỉ còn {$product->total} sản phẩm.",
                ]);
            }

            // Tính toán số tiền có VAT
            $moneyWithVAT = $request->isVAT ? $request->money + $request->money * 10 / 100 : $request->money;
			
			// Ghi nhật ký thay đổi tồn kho sản phẩm
            ProductLog::create([
                'name' => "Bán hàng",
                'customer_id' => $customer->id,
                'value' => "- [Tồn]: {$product->total} -> " . ($product->total - $quantity),
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ]);
			
			// Cập nhật số lượng tồn kho sản phẩm
            $product->total -= $quantity;
            $product->save();

            // Chuẩn bị dữ liệu đính kèm sản phẩm
            $productData[] = [
                'product_id' => $product->id,
                'price' => $item['price'],
                'count' => $quantity,
            ];
        }

        // Tạo đối tượng Export
        $export = Export::create([
            'customer_id' => $request->customer_id,
            'money' => $request->money,
            'debt' => $request->debt,
            'total' => $request->total,
            'created_at' => strtotime($request->importDate),
            'cash' => $request->cash,
            'user_id' => auth()->id(),
            'isVAT' => $request->isVAT,
            'money_with_vat' => $moneyWithVAT,
            'discount' => $request->discount === '' ? null : $request->discount,
            'note' => mb_strtoupper($request->note, 'UTF-8'),
            'diff_bulk' => $request->money - $totalBulk,
            'diff_retail' => $request->money - $totalRetail,
        ]);

        // Đính kèm sản phẩm vào đơn xuất
        foreach ($productData as $data) {
            $export->products()->attach($data['product_id'], [
                'price' => $data['price'],
                'count' => $data['count'],
            ]);
        }

        // Nếu không thanh toán bằng tiền mặt, cập nhật công nợ khách hàng
        if (!$request->cash) {
            $log_content_customer = "[Nợ] {$customer->money} -> {$request->total}  <br>";
            $customer->money = $request->total;
            $customer->save();
            CustomerLog::create([
                'name' => 'Bán hàng',
                'value' => $log_content_customer,
                'user_id' => auth()->id(),
                'customer_id' => $customer->id,
            ]);
        }

        \DB::commit();

        // Trả về phản hồi JSON
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
                'item' => $export
            ]);
        }
    } catch (\Exception $e) {
        \DB::rollBack();
        return response()->json([
            'status' => -1,
            'message' => $e->getMessage(),
        ]);
    }
}

    public function show(Request $request, $id) {
        $export = Export::with('customer', 'user')->find($id);
        $customer_pays = CustomerPay::where('customer_id', '=', $export->customer_id)
                                    ->where('created_at', '>', $export->created_at)
                                    ->count();
        $exports = Export::where('customer_id', '=', $export->customer_id)
            ->where('created_at', '>', $export->created_at)
            ->count();

        $imports = Import::where('customer_id', '=', $export->customer_id)
            ->where('created_at', '>', $export->created_at)
            ->count();

        $canReCreate = true;
        if ($customer_pays >0 ||
            $exports > 0 ||
            $imports > 0)
            $canReCreate = false;

        $export->products->map(function($value) {
            $value->price = $value->pivot->price;
            $value->count = $value->pivot->count;
        });
        if ($request->expectsJson()) {
            return response()->json([
                'item' => $export,
                'items' => $export->products,
                'canReCreate' => $canReCreate,
            ]);
        }
    }

    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy(Request $request, $id)
    {
        $item = Export::find($id);
        \DB::beginTransaction();
        $customer = Customer::find($item->customer_id);
        $log_content = "Khách hàng: $customer->name  <br>";
        foreach ($item->products as $product) {
            $new_count = $product->total + $product->pivot->count;
            ProductLog::create([
                'name' => "Xóa bán hàng",
                'customer_id' => $customer->id,
                'value' => "- [Tồn]: $product->total ->  $new_count",
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ]);
            $product->total = $new_count;
            $product->save();
        }
        if ($customer != null && !$item->cash) {
            $new_money = $customer->money - $item->money_with_vat;
            $log_content_customer = "[Nợ] $customer->money -> $new_money  <br>";
            $customer->money = $new_money;
            $customer->save();
            CustomerLog::create([
                'name' => 'Xóa bán hàng',
                'value' => $log_content_customer,
                'user_id' => auth()->id(),
                'customer_id' => $customer->id,
            ]);
        }
        $item->products()->detach();
        $item->delete();
        \DB::commit();
		
		if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
		
    }

    public function recreate(Request $request, $id)
    {
        $item = Export::find($id);
        \DB::beginTransaction();
        $products = collect();
        $customer = Customer::find($item->customer_id);
        foreach ($item->products as $product) {
            $new_count = $product->total + $product->pivot->count;
            ProductLog::create([
                'name' => "Tạo lại (xóa) bán hàng",
                'customer_id' => $customer->id,
                'value' => "- [Tồn]: $product->total ->  $new_count",
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ]);
            $product->total = $new_count;
            $product->save();
            $product->count = $product->pivot->count;
            $product->price_init = $product->price;
            if ($customer->bulk_customer) {
                $product->price_init = $product->price_bulk;
            }
            $product->price = $product->pivot->price;
            $products->push($product);
        }
        if ($customer != null && !$item->cash) {
            $new_money = $customer->money - $item->money_with_vat;
            $log_content_customer = "[Nợ] $customer->money -> $new_money  <br>";
            $customer->money = $new_money;
            $customer->save();
            CustomerLog::create([
                'name' => 'Tạo lại (xóa) bán hàng',
                'value' => $log_content_customer,
                'user_id' => auth()->id(),
                'customer_id' => $customer->id,
            ]);
        }
        $item->products()->detach();
        $item->delete();
        \DB::commit();
		
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
                'customer' => $customer,
                'products' => $products,
            ]);
        }
    }

    public function exportpdf($id) {

    $export = Export::find($id);
    $customer = Customer::where('id', $export->customer_id)->first();
    $user = User::where('id', $customer->user_id)->first();
	$seal=false;
    $products = $export->products;
    $totalItemPrice = 0;
	$isVAT = false;
    $vat = 0;
	$totalDiscount = 0; // Khởi tạo biến tổng giá trị discount
   
    foreach ($products as $index => $product) {
			$product->count = $product->pivot->count;
			$product->price = number_format($product->pivot->price, 0);
			$product->total = number_format($product->pivot->count * $product->pivot->price, 0);
			if (strpos($product->code, 'VAT') !== false) {
				// Nếu $product->code chứa từ khóa 'VAT'
				$vat += $product->pivot->count * $product->pivot->price;
				$isVAT = !$isVAT;
				unset($products[$index]); // Loại bỏ sản phẩm này khỏi mảng $products
			} 
			elseif (strpos($product->code, 'DISCOUNT') !== false) {
				// Nếu $product->code chứa từ khóa 'DISCOUNT'
				$totalDiscount += $product->pivot->count * $product->pivot->price;
				unset($products[$index]); // Loại bỏ sản phẩm này khỏi mảng $products		
			}
			else {
				// Nếu $product->code không chứa từ khóa 'VAT'
				$totalItemPrice += $product->pivot->count * $product->pivot->price;
			}
		}

    // Nếu không có sản phẩm nào là VAT, tính VAT dựa trên tổng giá sản phẩm
    if ($vat==0) {
        $vat = $totalItemPrice * 10 / 100;
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
        'customer' => $customer->toArray(),
        'user' => $user->toArray(),
        'note' => $export->note,
        'export' => $export,
        'products' => $products,
        'totalItemPrice' => $totalItemPrice,
		'seal' => $seal,
        'vat' => $vat,
        'isVAT' => $isVAT || $export->isVAT,
		'totalDiscount' => $totalDiscount, // Thêm giá trị discount vào dữ liệu truyền vào view
    ];

    // Kiểm tra xem có phải là đối tác export hay không để chọn view tương ứng
    $keyword = "PARTNER";
    $view = stripos($export->customer->code, $keyword) !== false ? 'reports.export_partner' : 'reports.export';
    $pdf = \PDF::loadView($view, $data);
    return $pdf->stream($export->created_at->format('dmY') . '_' . str_slug($export->customer->code) . '_' . str_slug($export->user->name) . '.pdf');
}

	
	public function exportpdfseal($id) {
		$export = Export::find($id);
		$customer = Customer::where('id', $export->customer_id)->first();
		$user = User::where('id', $customer->user_id)->first();
		$seal=true;
		$products = $export->products;
		$totalItemPrice = 0;
		$isVAT = false;
		$vat = 0;
		$totalDiscount = 0; // Khởi tạo biến tổng giá trị discount
	   
		foreach ($products as $index => $product) {
				$product->count = $product->pivot->count;
				$product->price = number_format($product->pivot->price, 0);
				$product->total = number_format($product->pivot->count * $product->pivot->price, 0);
				if (strpos($product->code, 'VAT') !== false) {
					// Nếu $product->code chứa từ khóa 'VAT'
					$vat += $product->pivot->count * $product->pivot->price;
					$isVAT = !$isVAT;
					unset($products[$index]); // Loại bỏ sản phẩm này khỏi mảng $products
				} elseif (strpos($product->code, 'DISCOUNT') !== false) {
					// Nếu $product->code chứa từ khóa 'DISCOUNT'
					$totalDiscount += $product->pivot->count * $product->pivot->price;
					unset($products[$index]); // Loại bỏ sản phẩm này khỏi mảng $products
				}
				else {
					// Nếu $product->code không chứa từ khóa 'VAT'
					$totalItemPrice += $product->pivot->count * $product->pivot->price;
				}
			}

		// Nếu không có sản phẩm nào là VAT, tính VAT dựa trên tổng giá sản phẩm
		if ($vat==0) {
			$vat = $totalItemPrice * 10 / 100;
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
			'customer' => $customer->toArray(),
			'user' => $user->toArray(),
			'note' => $export->note,
			'export' => $export,
			'products' => $products,
			'totalItemPrice' => $totalItemPrice,
			'seal' => $seal,
			'vat' => $vat,
			'isVAT' => $isVAT || $export->isVAT,
			'totalDiscount' => $totalDiscount, // Thêm giá trị discount vào dữ liệu truyền vào view
		];

		// Kiểm tra xem có phải là đối tác export hay không để chọn view tương ứng
		$keyword = "PARTNER";
		$view = stripos($export->customer->code, $keyword) !== false ? 'reports.export_partner' : 'reports.export';
		$pdf = \PDF::loadView($view, $data);
		return $pdf->stream($export->created_at->format('dmY') . '_' . str_slug($export->customer->code) . '_' . str_slug($export->user->name) . '.pdf');
    }
	
	public function exportexcel($id)
    {
        $export = Export::find($id);
        $products = $export->products;
		$totalItemPrice = 0;
		$isVAT = false;
		$vat = 0;
		// Khởi tạo mảng options và thêm các option vào mảng
		$options = [];
		foreach(Option::all() as $option)
		{
			$options[$option->id] = $option->value;
		}
        foreach ($products as $index => $product) {
				$product->count = $product->pivot->count;
				$product->price = number_format($product->pivot->price, 0);
				$product->total = number_format($product->pivot->count * $product->pivot->price, 0);
				if (strpos($product->code, 'VAT') !== false) {
					// Nếu $product->code chứa từ khóa 'VAT'
					$vat += $product->pivot->count * $product->pivot->price;
					$isVAT = !$isVAT;
					unset($products[$index]); // Loại bỏ sản phẩm này khỏi mảng $products
				} else {
					// Nếu $product->code không chứa từ khóa 'VAT'
					$totalItemPrice += $product->pivot->count * $product->pivot->price;
				}
			}
        if ($vat==0) {
			$vat = $totalItemPrice * 10 / 100;
		}
		$debt = $export->debt;
        $totalPrice = $debt + $totalItemPrice;
        if ($isVAT || $export->isVAT)
            $totalPrice += $vat;
        \Excel::create($export->created_at->format('dmY') . '_' . str_slug($export->customer->code) . '_donhang',
            function($excel) use ($products, $vat, $totalItemPrice, $export, $totalPrice, $isVAT, $options){
                $excel->sheet('Thống kê', function($sheet) use ($products, $vat, $totalItemPrice, $export, $totalPrice, $isVAT, $options){
                    $sheet->loadView('reports.export_excel', [
						'options' => $options,
                        'products' => $products,
						'note' => $export->note,
						'export' => $export,
						'customer' => $export->customer,
                        'totalItemPrice' => number_format($totalItemPrice, 0),
                        'vat' => number_format($vat, 0),
                        'isVAT' => $isVAT || $export->isVAT,
						'debt' => number_format($export->debt, 0),
                        'totalPrice' => number_format($totalPrice, 0),
                    ]);
            });
        })->download('xlsx');
    }
	
	public function exporta5($id) {
        $export = Export::find($id);
        $customer = Customer::where('id', $export->customer_id)->first();
        $user = User::where('id', $customer->user_id)->first();
        $options = [];
        foreach(Option::all() as $option)
        {
            $options[$option->id] = $option->value;
        }
        $data = [
            'options' => $options,
            'customer' => $export->customer->toArray(),
            'user' => $user->toArray(),
        ];
        $pdf = \PDF::loadView('reports.a5', $data);
        $pdf->setPaper('a5', 'landscape');
        return $pdf->download(Carbon::now()->format('dmY') . '_' . str_slug($export->customer->code) . '_' . str_slug($export->user->name).'.pdf');
    }

	public function exportreport(Request $request)
	{
		// Lấy các giá trị từ request
		$strFromDate = $request->input('tungay');
		$strToDate = $request->input('toingay');
		$customerFilter = $request->input('khachhang');
		$userFilter = $request->input('nguoidung');
		$productFilter = $request->input('sanpham');
		$vatFilter = $request->input('vat');
		$noteFilter = $request->input('note');
		$diff = $request->input('diff') === "true" ? true : ($request->input('diff') === "false" ? false : null);
		
		$allDiff = 0;
		// Xử lý chuỗi ngày tháng
		if ($strFromDate) {
			$strFromDate .= '000000';
		}
		if ($strToDate) {
			$strToDate .= '235959';
		}

		if ($strFromDate && $strToDate) {
			$fromDate = Carbon::createFromFormat('dmYHis', $strFromDate);
			$toDate = Carbon::createFromFormat('dmYHis', $strToDate);
		} else {
			return back()->withErrors(['message' => 'Ngày bắt đầu và kết thúc không hợp lệ.']);
		}

		// Truy vấn dữ liệu
		$expors = Export::with('customer', 'user')
			->whereBetween('created_at', [$fromDate, $toDate]);

		$imports = Import::with('customer', 'user')
			->whereBetween('created_at', [$fromDate, $toDate]);

		if ($customerFilter) {
			$expors->whereHas('customer', function ($query) use ($customerFilter) {
				$query->where('name', 'like', "%$customerFilter%")
					  ->orWhere('code', 'like', "%$customerFilter%");
			});

			$imports->whereHas('customer', function ($query) use ($customerFilter) {
				$query->where('name', 'like', "%$customerFilter%")
					  ->orWhere('code', 'like', "%$customerFilter%");
			});
		}

		if ($productFilter) {
			$expors->whereHas('products', function ($query) use ($productFilter) {
				$query->where('product_id', $productFilter);
			});

			$imports->whereHas('products', function ($query) use ($productFilter) {
				$query->where('product_id', $productFilter);
			});
		}

		if ($noteFilter) {
			$expors->where('note', 'like', "%$noteFilter%");
			$imports->where('note', 'like', "%$noteFilter%");
		}

		if ($vatFilter !== null) {
			$vatFilter = $vatFilter === 'true';
			$expors->where('isVAT', $vatFilter);
			$imports->where('isVAT', $vatFilter);
		}

		if ($userFilter) {
			$expors->whereHas('customer.user', function ($query) use ($userFilter) {
				$query->where('id', $userFilter);
			});

			$imports->whereHas('customer.user', function ($query) use ($userFilter) {
				$query->where('id', $userFilter);
			});
		}

		$expors = $expors->select(\DB::raw('1 as loai, id, user_id, customer_id, money, isVAT, money_with_vat, debt, total, cash, discount, updated_at, created_at, note,diff_bulk, diff_retail, 1'));
		$imports = $imports->select(\DB::raw('2 as loai, id, user_id, customer_id, money, isVAT, money_with_vat * -1 as money_with_vat, debt, total, cash, discount, updated_at, created_at, note,diff_bulk, diff_retail, 1'));

		$items = $expors->union($imports)->orderByDesc('updated_at')->get();
		$money = $items->sum('money_with_vat');
		$total = $items->groupBy('customer_id')->map->sortByDesc('updated_at')->map->first()->sum('total');
		
		if ($diff !== null) {
			foreach ($items as $item) {
				$item->diffValue=0;
				$item->diffValue = $diff ? $item->diff_bulk : $item->diff_retail;
				$allDiff += $item->diffValue;
			}
		} else {
			foreach ($items as $item) {
				$item->diffValue = 0;
			}
			$allDiff = 0;
		}

		\Excel::create(Carbon::now()->format('dmY') . '_ThongKe', function($excel) use($items, $money, $total, $allDiff) {
			$excel->sheet('Thống kê', function($sheet) use($items, $money, $total, $allDiff) {
				$sheet->row(1, ['Ngày', 'Người tạo', 'Mã KH', 'Khách hàng', 'Loại', 'Nợ cũ', 'Tiền đơn', 'Nợ mới','Chênh (Không tính VAT)']);
				$sheet->row(1, function($row) {
					$row->setBackground('#000000');
					$row->setFontColor('#ffffff');
				});

				foreach ($items as $key => $item) {
					$sheet->row($key + 2, [
						$item->created_at->format('d/m/Y H:i:s'),
						$item->user->name,
						$item->customer->code,
						$item->customer->name,
						$item->loai == 1 ? 'Bán hàng' : 'Nhập kho',
						number_format($item->debt, 0),
						number_format($item->money_with_vat, 0),
						number_format($item->total, 0),
						number_format($item->diffValue, 0),
					]);
				}

				$sheet->row(count($items) + 2, ['Tổng cộng', '', '', '', '', '', number_format($money, 0), number_format($total, 0),number_format($allDiff, 0)]);
				$sheet->row(count($items) + 2, function($row) {
					$row->setBackground('#ffff00');
				});
			});
		})->download('xlsx');
	}


    public function updateLock(Request $request)
    {
        $xuat = Export::find($request->id);
        $xuat->lock = $request->lock;
        $xuat->save();
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }
	

	public function exportreportdetail(Request $request)
	{
		// Lấy các giá trị từ request
		$strFromDate = $request->input('tungay');
		$strToDate = $request->input('toingay');
		$customerFilter = $request->input('khachhang');
		$userFilter = $request->input('nguoidung');
		$productFilter = $request->input('sanpham');
		$vatFilter = $request->input('vat');
		$noteFilter = $request->input('note');
		$diff = $request->input('diff') === "true" ? true : ($request->input('diff') === "false" ? false : null);

		// Xử lý chuỗi ngày tháng
		if ($strFromDate) {
			$strFromDate .= '000000';
		}
		if ($strToDate) {
			$strToDate .= '235959';
		}

		if ($strFromDate && $strToDate) {
			$fromDate = Carbon::createFromFormat('dmYHis', $strFromDate);
			$toDate = Carbon::createFromFormat('dmYHis', $strToDate);
		} else {
			return back()->withErrors(['message' => 'Ngày bắt đầu và kết thúc không hợp lệ.']);
		}

		// Truy vấn dữ liệu
		$expors = Export::with(['customer', 'user', 'products'])
			->whereBetween('created_at', [$fromDate, $toDate]);

		if ($customerFilter) {
			$expors->whereHas('customer', function ($query) use ($customerFilter) {
				$query->where('name', 'like', "%$customerFilter%")
					  ->orWhere('code', 'like', "%$customerFilter%");
			});
		}

		if ($noteFilter) {
			$expors->where('note', 'like', "%$noteFilter%");
		}

		if ($vatFilter !== null) {
			$vatFilter = $vatFilter === 'true';
			$expors->where('isVAT', $vatFilter);
		}

		if ($userFilter) {
			$expors->whereHas('customer.user', function ($query) use ($userFilter) {
				$query->where('id', $userFilter);
			});
		}

		if (!$productFilter) {
			$items = $expors->get();
			$products = [];

			foreach ($items as $item) {
				foreach ($item->products as $product) {
					$productCode = $product->code;
					$productName = $product->name;
					$productType = $product->type;
					$productTotal = $product->total;
					$productPending = $product->order_pending;
					$productMin = $product->min;
					$quantity = $product->pivot->count;
					$actualPrice = $product->pivot->price;
					$actualValue = $quantity * $actualPrice;
					$price = $diff ? $product->price_bulk : $product->price;
					$diffValue = $quantity * $price;

					if (isset($products[$productCode])) {
						$products[$productCode]['quantity'] += $quantity;
						$products[$productCode]['actual_value'] += $actualValue;
						$products[$productCode]['diff_value'] += $diffValue;
					} else {
						$products[$productCode] = [
							'code' => $productCode,
							'name' => $productName,
							'type' => $productType,
							'total' => $productTotal,
							'quantity' => $quantity,
							'actual_value' => $actualValue,
							'diff_value' => $diffValue,
							'order_pending' => $productPending,
							'min' => $productMin,
						];
					}
				}
			}

			\Excel::create(Carbon::now()->format('dmY') . '_products_detail', function ($excel) use ($products, $fromDate, $toDate, $diff) {
				$excel->sheet('bangke', function ($sheet) use ($products, $fromDate, $toDate, $diff) {
					$sheet->setTitle('ListDetail');
					$sheet->loadView('reports.thongke_products_detail', [
						'products' => $products,
						'fromDate' => $fromDate,
						'toDate' => $toDate,
						'diff' => $diff,
					]);
				});
			})->download('xlsx');
		} else {
			$expors->whereHas('products', function ($query) use ($productFilter) {
				$query->where('product_id', $productFilter);
			});

			$items = $expors->get();
			$result = [];

			foreach ($items as $item) {
				$filteredProducts = $item->products->filter(function ($product) use ($productFilter) {
					return $product->id == $productFilter;
				});

				foreach ($filteredProducts as $product) {
					$productCode = $product->code;
					$productName = $product->name;
					$productType = $product->type;
					$quantity = $product->pivot->count;
					$price = $product->pivot->price;
					$totalValue = $quantity * $price;

					$result[] = [
						'item_info' => [
							'customer_code' => $item->customer->code,
							'customer_name' => $item->customer->name,
							'created_at' => $item->created_at,
							'note' => $item->note,
						],
						'product_info' => [
							'code' => $productCode,
							'name' => $productName,
							'type' => $productType,
							'quantity' => $quantity,
							'price' => $price,
							'totalValue' => $totalValue,
						],
					];
				}
			}

			\Excel::create(Carbon::now()->format('dmY') . '_products_detail', function ($excel) use ($result, $fromDate, $toDate) {
				$excel->sheet('bangke', function ($sheet) use ($result, $fromDate, $toDate) {
					$sheet->setTitle('ListDetail');
					$sheet->loadView('reports.thongke_products_filter_by_exports', [
						'result' => $result,
						'fromDate' => $fromDate,
						'toDate' => $toDate,
					]);
				});
			})->download('xlsx');
		}
	}

}
