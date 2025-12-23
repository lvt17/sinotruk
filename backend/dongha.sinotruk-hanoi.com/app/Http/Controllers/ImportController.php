<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerLog;
use App\CustomerPay;
use App\Export;
use App\Import;
use App\Product;
use App\ProductLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;

class ImportController extends Controller
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
        $product_id = Input::get('product');
        $isError = Input::get('isError');



        $items = Import::with('customer', 'user')->orderByDesc('id');

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
        if ($product_id != '') {
            $items = $items->whereHas('products', function ($query) use ($product_id) {
                $query->Where('products.id', '=', $product_id);
            });
        }
        if ($isError == 'true') {
            $items = $items->where('isProductError', true);
        }
        $items = $items->paginate(10);
        $last_item = Import::orderByDesc('id')->first();

        if ($request->expectsJson()) {
            return response()->json([
                'items' => $items,
                'last_id' => $last_item == null ? 1 : $last_item->id,
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
    // Validation: Yêu cầu các trường cần thiết từ request
    $this->validate($request, [
        'customer_id' => 'required',
        'total' => 'required|integer',
        'items' => 'required|array|min:1',
        'importDate' => 'required',
        'cash' => 'required',
        'isVAT' => 'required',
        'isProductError' => 'required',
        'content' => 'required',
    ]);

    // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
    \DB::beginTransaction();

    try {
        // Khởi tạo biến để tính toán
        $totalBulk = 0;
        $totalRetail = 0;

        // Tìm thông tin khách hàng
        $customer = Customer::find($request->customer_id);

        // Kiểm tra nợ khách hàng có khớp với yêu cầu nhập kho hay không
        if ($customer->money != $request->debt) {
            \DB::rollBack();
            return response()->json([
                'status' => -2,
                'debt' => $customer->money,
                'message' => "Nợ khách hàng đã thay đổi. Vui lòng kiểm tra lại phiếu nhập!",
            ]);
        }
		
		$import = null; // Khởi tạo biến $import
        // Mảng để lưu trữ thông tin các sản phẩm
        $productData = [];

        // Duyệt qua từng sản phẩm trong danh sách items từ request
        foreach ($request->items as $item) {
            // Validate: Giá sản phẩm phải là số nguyên
            if ($item['price']<0) {
                \DB::rollBack();
                return response()->json([
                    'status' => -2,
                    'message' => "Đơn giá phải lớn hơn hoặc bằng 0",
                    'debt' => $customer->money,
                ]);
            }

            
            $product = Product::find($item['id']);
            
            //Nếu nhập hàng TQ (copy từ order) lấy theo giá trị thực tế Sent; 
			//Nếu nhập hàng bình thường lấy theo giá trị count nhập vào
			$quantity = isset($item['hscode']) ? $item['hscode'] : $item['count']; 
			
			// Tính toán tổng giá sỉ và giá lẻ của các sản phẩm
            $totalBulk += $quantity * ($product->price_bulk == 0 ? $item['price'] : $product->price_bulk);
            $totalRetail += $quantity * ($product->price == 0 ? $item['price'] : $product->price);
           
			// Cập nhật số lượng tồn kho sản phẩm
            if (!$request->isProductError) {
                $product->total += $quantity;
                $product->save();
                // Ghi nhật ký thay đổi tồn kho sản phẩm
    			ProductLog::create([
    				'name' => 'Nhập kho',
    				'value' => "- [Tồn]: {$product->total} -> " . ($product->total + $quantity),
    				'user_id' => auth()->id(),
    				'customer_id' => $customer->id,
    				'product_id' => $product->id,
    			]);
            }
			// Thêm thông tin sản phẩm vào mảng productData
            $productData[] = [
                'product_id' => $item['id'],
                'price' => $item['price'],
                'count' => $quantity,
            ];
                
        }

        // Tạo đối tượng Import và lưu vào database
        $import = Import::create([
            'customer_id' => $request->customer_id,
            'money' => $request->money,
            'debt' => $request->debt,
            'total' => $request->total,
            'created_at' => strtotime($request->importDate),
            'cash' => $request->cash,
            'user_id' => auth()->id(),
            'isVAT' => $request->isVAT,
            'money_with_vat' => $request->isVAT ? $request->money + $request->money * 10 / 100 : $request->money,
            'content' => mb_strtoupper($request->content, 'UTF-8'),
            'isProductError' => $request->isProductError,
            'discount' => $request->discount === '' ? null : $request->discount,
            'diff_bulk' => $totalBulk - $request->money,
            'diff_retail' => $totalRetail - $request->money,
        ]);

        // Đính kèm các sản phẩm vào đối tượng Import
        foreach ($productData as $data) {
            $import->products()->attach($data['product_id'], [
                'price' => $data['price'],
                'count' => $data['count'],
            ]);
        }

        // Nếu không thanh toán bằng tiền mặt, cập nhật lại nợ của khách hàng
        if (!$request->cash) {
            $logContentCustomer = "[Nợ] {$customer->money} -> {$request->total}  <br>";
            $customer->money = $request->total;
            $customer->save();
            CustomerLog::create([
                'name' => 'Nhập kho',
                'value' => $logContentCustomer,
                'user_id' => auth()->id(),
                'customer_id' => $customer->id,
            ]);
        }

        // Commit transaction
        \DB::commit();

        // Trả về phản hồi dưới dạng JSON
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
                'item' => $import
            ]);
        }
    } catch (\Exception $e) {
        // Rollback transaction nếu có lỗi xảy ra
        \DB::rollBack();
        return response()->json([
            'status' => -1,
            'message' => $e->getMessage(),
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
        $import = Import::with('customer', 'user')->find($id);
        $customer_pays = CustomerPay::where('customer_id', '=', $import->customer_id)
            ->where('created_at', '>', $import->created_at)
            ->count();

        $exports = Export::where('customer_id', '=', $import->customer_id)
            ->where('created_at', '>', $import->created_at)
            ->count();

        $imports = Import::where('customer_id', '=', $import->customer_id)
            ->where('created_at', '>', $import->created_at)
            ->count();

        $canReCreate = true;
        if ($customer_pays >0 ||
            $exports > 0 ||
            $imports > 0)
            $canReCreate = false;

        $import->products->map(function($value) {
            $value->price = $value->pivot->price;
            $value->count = $value->pivot->count;
        });
        if ($request->expectsJson()) {
            return response()->json([
                'item' => $import,
                'items' => $import->products,
                'canReCreate' => $canReCreate,
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $item = Import::find($id);
        \DB::beginTransaction();
        $customer = Customer::find($item->customer_id);
        $log_content = "Khách hàng: $customer->name  <br>";
        if ($item->isProductError !== true) {
            foreach ($item->products as $product) {
                $new_count = $product->total - $product->pivot->count;
//                if ($new_count < 0)
//                    $new_count = 0;
                //$log_content = $log_content."Sản phẩm: $product->name  <br>";
                //$log_content = $log_content."- [Tồn]: $product->total ->  $new_count<br>";
                if ($item->isProductError !== true) {
                    ProductLog::create([
                        'name' => 'Xóa nhập kho',
                        'value' => "- [Tồn]: $product->total ->  $new_count",
                        'user_id' => auth()->id(),
                        'customer_id' => $customer->id,
                        'product_id' => $product->id,
                    ]);
                }
                $product->total = $new_count;
                $product->save();
            }
        }


        if ($customer != null && !$item->cash) {
            $new_money = $customer->money + $item->money_with_vat;
            $log_content_customer = "[Nợ] $customer->money -> $new_money  <br>";
            $customer->money = $new_money;
            $customer->save();
            CustomerLog::create([
                'name' => 'Xóa nhập kho',
                'value' => $log_content_customer,
                'user_id' => auth()->id(),
                'customer_id' => $customer->id,
            ]);
        }
        $item->products()->detach();
        $item->delete();
//        if ($item->isProductError !== true) {
//            ProductLog::create([
//                'name' => 'Xóa nhập kho',
//                'value' => $log_content,
//                'user_id' => auth()->id(),
//            ]);
//        }
        \DB::commit();
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

    public function recreate(Request $request, $id)
    {
        $item = Import::find($id);
        \DB::beginTransaction();
        $products = collect();
        $customer = Customer::find($item->customer_id);
        //$log_content = "Khách hàng: $customer->name  <br>";
        foreach ($item->products as $product) {
            if ($item->isProductError !== true) {
                $new_count = $product->total - $product->pivot->count;
//                if ($new_count < 0)
//                    $new_count = 0;
                //$log_content = $log_content."Sản phẩm: $product->name  <br>";
                //$log_content = $log_content."- [Tồn]: $product->total ->  $new_count<br>";
                if ($item->isProductError !== true) {
                    ProductLog::create([
                        'name' => 'Tạo mới (xóa) nhập kho',
                        'value' => "- [Tồn]: $product->total ->  $new_count",
                        'user_id' => auth()->id(),
                        'customer_id' => $customer->id,
                        'product_id' => $product->id,
                    ]);
                }
                $product->total = $new_count;
                $product->save();
            }
            $product->count = $product->pivot->count;
            $product->price_init = $product->price;
            $product->price = $product->pivot->price;
            $products->push($product);
        }


        if ($customer != null && !$item->cash) {
            $new_money = $customer->money + $item->money_with_vat;
            $log_content_customer = "[Nợ] $customer->money -> $new_money  <br>";
            $customer->money = $new_money;
            $customer->save();
            CustomerLog::create([
                'name' => 'Tạo mới (xóa) nhập kho',
                'value' => $log_content_customer,
                'user_id' => auth()->id(),
                'customer_id' => $customer->id,
            ]);
        }
        $item->products()->detach();
        $item->delete();

//        if ($item->isProductError !== true) {
//            ProductLog::create([
//                'name' => 'Tạo mới (xóa) nhập kho',
//                'value' => $log_content,
//                'user_id' => auth()->id(),
//            ]);
//        }
        \DB::commit();
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
                'customer' => $customer,
                'products' => $products,
            ]);
        }
    }

    public function exportreport()
    {
        $strFromDate = Input::get('fromdate');
        $strToDate = Input::get('todate');
        $customer_code = Input::get('key');
        $product_id = Input::get('product');
        $isError = Input::get('isError');

        $items = Import::with('customer', 'user', 'products')->orderByDesc('updated_at');

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
        if ($product_id != '') {
            $items = $items->whereHas('products', function ($query) use ($product_id) {
                $query->Where('products.id', '=', $product_id);
            });
        }
        if ($isError == 'true') {
            $items = $items->where('isProductError', true);
        }
        $items = $items->get();
        \Excel::create(Carbon::now()->format('dmY') . '_' . 'ThongKe', function($excel) use($items){
            $excel->sheet('Thống kê', function($sheet) use($items){

                $sheet->row(1, array(
                    'Ngày', 'Khách hàng', 'Nội dung', 'Sản phẩm', 'Tình trạng', 'Số lượng', 'Đơn giá', 'Số tiền'
                ));
                $sheet->row(1, function($row) {
                    // call cell manipulation methods
                    $row->setBackground('#ffeeba');
                    $row->setFontColor('#282828');
                });
                $row_index = 2;
                $total = 0;
                foreach ($items as $key=>$item) {
                    $row_first = $row_index;
                    foreach ($item->products as $key_product=>$product) {

                        if ($key_product == 0) {
                            $sheet->row($row_index, array(
                                $item->created_at->format('d/m/Y'),
                                $item->customer->name,
                                $item->content,
                                $product->name,
                                $item->isProductError == null ? "" : $item->isProductError == 1 ? "Lỗi" : "",
                                $product->pivot->count,
                                number_format($product->pivot->price),
                                number_format($product->pivot->price * $product->pivot->count),
                            ));
                        }
                        else {
                            $sheet->row($row_index, array(
                                "",
                                "",
                                "",
                                $product->name,
                                $item->isProductError == null ? "" : $item->isProductError == 1 ? "Lỗi" : "",
                                $product->pivot->count,
                                number_format($product->pivot->price),
                                number_format($product->pivot->price * $product->pivot->count),
                            ));
                        }
                        $total += $product->pivot->price * $product->pivot->count;
                        $row_index++;

                    }
                    $row_last = $row_index - 1;
                    if ($row_first != $row_last) {
                        $sheet->setMergeColumn(array(
                            'columns' => array('A', 'B', 'C'),
                            'rows' => array(
                                array($row_first, $row_last),
                            )
                        ));
                    }
                }
                $sheet->row($row_index, array(
                    'Tổng cộng', '', '', '', '', '', '', number_format($total)
                ));
                $sheet->row($row_index, function($row) {
                    // call cell manipulation methods
                    $row->setBackground('#ffff00');
                });
            });

        })->download('xlsx');;
    }
	
	    public function exportreportdetail()
    {
        $strFromDate = Input::get('fromdate');
        $strToDate = Input::get('todate');
        $customer_code = Input::get('key');
        $product_id = Input::get('product');
        $isError = Input::get('isError');
		
		// Kiểm tra nếu cả "fromdate" và "todate" đều trống, hiển thị thông báo lỗi và dừng quá trình xuất file
		if (empty($strFromDate) && empty($strToDate)) {
        return back()->with('error', 'Vui lòng nhập vào khoảng thời gian!');
		}
        $items = Import::with('customer', 'user', 'products')->orderByDesc('updated_at');

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
        if ($product_id != '') {
            $items = $items->whereHas('products', function ($query) use ($product_id) {
                $query->Where('products.id', '=', $product_id);
            });
        }
        if ($isError == 'true') {
            $items = $items->where('isProductError', true);
        }
        $items = $items->get();

		$products = []; // Mảng để lưu thông tin sản phẩm và tổng số lượng
		foreach ($items as $item) {
			foreach ($item->products as $product) {
				$productCode = $product->code;
				$productName = $product->name;
				$productType = $product->type;
				$quantity = $product->pivot->count; // Sử dụng 'count' là tên của trường số lượng trong bảng trung gian
				$price = $product->pivot->price; // Sử dụng 'price' là tên của trường giá tiền trong bảng trung gian

				// Tính tổng số tiền mà sản phẩm đã được bán
				$totalValue = $quantity * $price;

				// Check if the product exists in the products array and update its quantity, value, code, and total
				if (isset($products[$productName])) {
					$products[$productName]['quantity'] += $quantity;
					$products[$productName]['value'] += $totalValue;
				} else {
					// If the product doesn't exist, add it to the products array with its quantity, value, code, and total
					$products[$productName] = [
						'code' => $productCode,
						'name' => $productName,
						'type' => $productType,
						'quantity' => $quantity,
						'value' => $totalValue,
					];
				}
			}
		}

		// Generate the Excel file with product information, total quantities, total values, code, and total
		\Excel::create(Carbon::now()->format('dmY') . '_' . 'products_detail', function ($excel) use ($products, $fromDate, $toDate) {
			$excel->sheet('bangke', function ($sheet) use ($products, $fromDate, $toDate) {
				$sheet->setTitle('ListDetail');
				$sheet->loadView('reports.nhapkho_products_detail', [
					'products' => $products,
					'fromDate' => $fromDate,
                    'toDate' => $toDate
				]);
			});
		})->download('xlsx');
    }

	public function uploadexcelnhapkhodssanpham(Request $request) {
    $file_name = time() . $request->uploadFile->getClientOriginalName();
    $request->uploadFile->move('uploads/', $file_name);
    $items = collect();

    \Excel::load('uploads/' . $file_name, function ($reader) use ($items) {
        $results = $reader->get();
        foreach ($results as $value) {
            try {
                $item = Product::where('code', $value->ma)->first();
                if ($item == null) {
                    continue;
                }

                $don_gia = $value->don_gia;
                if ($don_gia === 'sỉ') {
                    $don_gia = $item->price_bulk;
                } elseif ($don_gia === 'lẻ') {
                    $don_gia = $item->price;
                } elseif (strpos($don_gia, 'gannhat-') !== false) {
                    // Nếu đơn giá có dạng 'gannhat-A'
                    $customer_code = substr($don_gia, strpos($don_gia, '-') + 1);
                    $don_gia = $this->getClosestPrice($item->id, $customer_code);
                }

                $item->count = $value->so_luong;
                $item->price = is_numeric($don_gia) ? $don_gia : 0;
                $item->price_init = is_numeric($don_gia) ? $don_gia : 0;
                $items->push($item);
            } catch (\Illuminate\Database\QueryException $e) {
                // Handle exception if needed
            }
        }
    });

    File::delete('uploads/' . $file_name);

    if ($request->expectsJson()) {
        return response()->json([
            'items' => $items,
        ]);
    }
}
	
	// Hàm để lấy đơn giá gần nhất cho sản phẩm dựa trên mã khách hàng
	private function getClosestPrice($product_id, $customer_code) {
    // Tìm khách hàng
    $customer = Customer::where('code', $customer_code)->first();

    if (!$customer) {
        // Xử lý nếu không tìm thấy khách hàng
        return 0;
    }

    // Tìm đơn hàng của khách hàng đã mua chứa sản phẩm cần xử lý, sắp xếp theo thời gian từ gần nhất đến xa nhất
    $export = Export::whereHas('products', function ($query) use ($product_id) {
        $query->where('product_id', $product_id);
    })->where('customer_id', $customer->id)->latest()->first();

    if (!$export) {
        // Nếu không tìm thấy đơn hàng, trả về 0
        return 0;
    }

    // Trả về giá của sản phẩm từ đơn hàng gần nhất
    return $export->products()->where('product_id', $product_id)->first()->pivot->price;
	}

}
