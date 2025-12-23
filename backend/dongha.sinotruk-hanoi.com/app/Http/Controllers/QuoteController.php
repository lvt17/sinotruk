<?php

namespace App\Http\Controllers;

use App\Option;
use App\Quote;
use App\Export;
use App\User;
use App\Customer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class QuoteController extends Controller
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
        $items = Quote::with('customer', 'user')->orderByDesc('updated_at');

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
			'customer_id' => 'required',
			'items.length' => 'min:1',
			'importDate' => 'required',
			'isVAT' => 'required',
		]);

		$findPrice = $request->findPrice;

		\DB::beginTransaction();

		$quote = Quote::create([
			'customer_id' => $request->customer_id,
			'created_at' => strtotime($request->importDate),
			'user_id' => auth()->id(),
			'discount' => $request->discount === '' ? null : $request->discount,
			'isVAT' => $request->isVAT,
			'note' => mb_strtoupper($request->note, 'UTF-8'),
		]);

		foreach ($request->items as $item) {
			$price = $item['price'];
			if ($findPrice) {
				$price = $this->getClosestPrice($item['id'], $request->customer_id);
			}

			$quote->products()->attach($item['id'], [
				'price' => $price,
				'count' => $item['count'],
			]);
		}

		\DB::commit();

		if ($request->expectsJson()) {
			return response()->json([
				'status' => 1,
				'item' => $quote
			]);
		}
	}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $quote = Quote::with('customer')->find($id);
        $quote->products->map(function($value) {
            $value->price_init = $value->pivot->price;
            $value->price = $value->pivot->price;
            $value->count = $value->pivot->count;
        });
        if ($request->expectsJson()) {
            return response()->json([
                'quote' => $quote,
                'items' => $quote->products,
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
            'customer_id' => 'required',
            'items.length' => 'min:1',
            'importDate' => 'required',
            'isVAT' => 'required',
        ]);
		$findPrice = $request->findPrice;
        \DB::beginTransaction();
        $quote = Quote::find($id);
        $quote->customer_id = $request->customer_id;
        $quote->created_at = strtotime($request->importDate);
        $quote->user_id = auth()->id();
        $quote->discount = $request->discount === '' ? null : $request->discount;
        $quote->isVAT = $request->isVAT;
        $quote->note = mb_strtoupper($request->note,'UTF-8');
        $quote->save();
        $quote->products()->detach();
        foreach ($request->items as $item) {
			$price = $item['price'];
			if ($findPrice) {
				$price = $this->getClosestPrice($item['id'], $request->customer_id);
			}
            $quote->products()->attach($item['id'], [
                'price' => $price,
                'count' => $item['count'],
            ]);
        }
        \DB::commit();
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
        $item = Quote::find($id);
        \DB::beginTransaction();
        $item->products()->detach();
        $item->delete();
        \DB::commit();
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

    public function exportExcel($id)
    {
        $showMaNSX = Input::get('mansx');
		$customVAT = Input::get('customVAT');
        $item = Quote::find($id);
        $products = $item->products;
        $totalItemPrice = 0;
		$isVAT = false;
        $vat = 0; // Khởi tạo biến vat
		$options = [];
        foreach(Option::all() as $option)
        {
            $options[$option->id] = $option->value;
        }
        foreach ($products as $index => $product) {
			$product->count = $product->pivot->count;
			$product->price = number_format($product->pivot->price, 0);
			$product->tonkho =  $product->total;
			$product->total = number_format($product->pivot->count * $product->pivot->price, 0);
			
			if (strpos($product->code, 'VAT') !== false) {
				// Nếu $product->code chứa từ khóa 'VAT'
				$isVAT = !$isVAT;
				$vat += $product->pivot->count * $product->pivot->price;
				unset($products[$index]); // Loại bỏ sản phẩm này khỏi mảng $products
			} else {
				// Nếu $product->code không chứa từ khóa 'VAT'
				$totalItemPrice += $product->pivot->count * $product->pivot->price;
			}
			
			if ($product->image == null || !file_exists('img/products/' . $product->image))
				$product->image = null;
		}
        if ($vat == 0) {
			$vat = $totalItemPrice * 10 / 100;
		}
        $totalPrice = $totalItemPrice;
        if ($isVAT || $item->isVAT)
            $totalPrice += $vat;

        \Excel::create($item->created_at->format('dmY') . '_' . str_slug($item->customer->code) . '_baogia',
            function($excel) use ($products, $showMaNSX, $customVAT, $vat, $totalItemPrice, $item, $totalPrice, $isVAT, $options){
                $excel->sheet('Thống kê', function($sheet) use ($products, $showMaNSX, $customVAT, $vat, $totalItemPrice, $item, $totalPrice, $isVAT, $options){
                    $sheet->loadView('reports.baogia_excel', [
						'options' => $options,
                        'products' => $products,
						'customer' => $item->customer,
                        'showMaNSX' => $showMaNSX,
						'customVAT' =>$customVAT,
                        'totalItemPrice' => number_format($totalItemPrice, 0),
                        'vat' => number_format($vat, 0),
                        'isVAT' => $isVAT || $item->isVAT,
                        'note' => $item->note,
                        'totalPrice' => number_format($totalPrice, 0),
                    ]);
            });
        })->download('xlsx');
    }

    
	public function exportPDF($id)
{
    $showMaNSX = Input::get('mansx');
    $customVAT = Input::get('customVAT');
    $item = Quote::find($id);
    $customer = Customer::where('id', $item->customer_id)->first();
    $user = User::where('id', $customer->user_id)->first();
    $products = $item->products;
    $seal = false;
    $totalItemPrice = 0;
    $isVAT = false;
    $vat = 0; // Khởi tạo biến vat
    $totalDiscount = 0; // Khởi tạo biến tổng giá trị discount
    
    foreach ($products as $index => $product) {
        $product->count = $product->pivot->count;
        $product->price = number_format($product->pivot->price, 0);
        $product->tonkho = $product->total;
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
        } else {
            // Nếu $product->code không chứa từ khóa 'VAT' hoặc 'DISCOUNT'
            $totalItemPrice += $product->pivot->count * $product->pivot->price;
        }
        
        if ($product->image == null || !file_exists('img/products/' . $product->image)) {
            $product->image = null;
        }
    }

    // Nếu không có sản phẩm nào có $product->code chứa từ khóa 'VAT'
    if ($vat == 0) {
        $vat = $totalItemPrice * 10 / 100;
    }
    
    $options = [];
    foreach(Option::all() as $option) {
        $options[$option->id] = $option->value;
    }
    
    $data = [
        'options' => $options,
        'customer' => $item->customer->toArray(),
        'user' => $user->toArray(),
        'products' => $products,
        'showMaNSX' => $showMaNSX,
        'customVAT' => $customVAT,
        'totalItemPrice' => $totalItemPrice,
        'vat' => $vat,
        'isVAT' => $isVAT || $item->isVAT,
        'note' => $item->note,
        'quote' => $item,
        'seal' => $seal,
        'totalDiscount' => $totalDiscount, // Thêm giá trị discount vào dữ liệu truyền vào view
    ];
    
    $pdf = \PDF::loadView('reports.baogia_pdf', $data);
    return $pdf->stream($item->created_at->format('dmY') . '_' . str_slug($item->customer->code) . '_baogia.pdf');
}


	
	public function exportPDFSeal($id)
    {
        $showMaNSX = Input::get('mansx');
		$customVAT = Input::get('customVAT');
        $item = Quote::find($id);
        $customer = Customer::where('id', $item->customer_id)->first();
        $user = User::where('id', $customer->user_id)->first();
        $products = $item->products;
        $totalItemPrice = 0;
		$seal= true;
		$isVAT = false;
        $vat = 0; // Khởi tạo biến vat
		$totalDiscount = 0; // Khởi tạo biến tổng giá trị discount
		foreach ($products as $index => $product) {
			$product->count = $product->pivot->count;
			$product->price = number_format($product->pivot->price, 0);
			$product->tonkho =  $product->total;
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
			} else {
				// Nếu $product->code không chứa từ khóa 'VAT' hoặc 'DISCOUNT'
				$totalItemPrice += $product->pivot->count * $product->pivot->price;
			}
			
			if ($product->image == null || !file_exists('img/products/' . $product->image))
				$product->image = null;
		}

		// Nếu không có sản phẩm nào có $product->code chứa từ khóa 'VAT'
		if ($vat == 0) {
			$vat = $totalItemPrice * 10 / 100;
		}

        $options = [];
        foreach(Option::all() as $option)
        {
            $options[$option->id] = $option->value;
        }
        $data = [
            'options' => $options,
            'customer' => $item->customer->toArray(),
            'user' => $user->toArray(),
            'products' => $products,
            'showMaNSX' => $showMaNSX,
			'customVAT' =>$customVAT,
            'totalItemPrice' => $totalItemPrice,
            'vat' => $vat,
            'isVAT' => $isVAT || $item->isVAT,
            'note' => $item->note,
            'quote' => $item,
			'seal' => $seal,
			'totalDiscount' => $totalDiscount, // Thêm giá trị discount vào dữ liệu truyền vào view
        ];
        $pdf = \PDF::loadView('reports.baogia_pdf', $data);
        return $pdf->stream($item->created_at->format('dmY') . '_' . str_slug($item->customer->code) . '_baogia_seal.pdf');
    }

    public function bulkdelete(Request $request) {
        $this->validate($request, [
            'ids' => 'required',
        ]);
        \DB::beginTransaction();
        foreach($request->ids as $id) {
            $item = Quote::find($id);
            $item->products()->detach();
            $item->delete();
        }
        \DB::commit();
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }
	
	private function getClosestPrice($product_id, $customer_id) {
        // Tìm khách hàng dựa trên customer_id
        $customer = Customer::find($customer_id);
        if (!$customer) {
            // Xử lý nếu không tìm thấy khách hàng
            return null; // Trả về null nếu không tìm thấy khách hàng
        }
    
        // Tìm sản phẩm dựa trên product_id
        $product = Product::find($product_id);
        if (!$product) {
            // Xử lý nếu không tìm thấy sản phẩm
            return null; // Trả về null nếu không tìm thấy sản phẩm
        }
    
        // Tìm đơn hàng gần nhất của khách hàng có chứa sản phẩm cần tìm
        $export = Export::whereHas('products', function ($query) use ($product_id) {
            $query->where('product_id', $product_id);
        })->where('customer_id', $customer_id)->latest()->first();
    
        // Nếu không tìm thấy đơn hàng, trả về giá mặc định của sản phẩm
        if (!$export) {
            return $customer->bulk_customer ? $product->price_bulk : $product->price;
        }
    
        // Trả về giá của sản phẩm từ đơn hàng gần nhất
        return $export->products()->where('product_id', $product_id)->first()->pivot->price;
    }
}
