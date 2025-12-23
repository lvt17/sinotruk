<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerLog;
use App\Option;
use App\User;
use App\Export;
use App\Import;
use App\CustomerPay;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Jobs\SentEmail;

class CustomerPayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $key = Input::get('key');
        $strFromDate = Input::get('fromdate');
        $strToDate = Input::get('todate');
        $userFilter = Input::get('nguoidung');
        $nhanvienFilter = Input::get('nv');

        $items = CustomerPay::with('customer', 'user', 'customer.user');

        if ($key != '')
        {
            $items = $items->whereHas('customer', function ($query) use ($key) {
                    $query->where('code', '=', "$key");
                });
        }
        if ($strFromDate != 'Invalid date') {
            $strFromDate = $strFromDate . '000000';
            $fromDate = Carbon::createFromFormat('dmYHis', $strFromDate);
            $items = $items->where('created_at', '>=', $fromDate);
        }
        if ($strToDate != 'Invalid date') {
            $strToDate = $strToDate . '235959';
            $toDate = Carbon::createFromFormat('dmYHis', $strToDate);
            $items = $items->where('created_at', '<', $toDate);
        }
        if ($userFilter != '') {
            $items = $items->where('user_id', $userFilter);
        }
        if ($nhanvienFilter != '') {
            $items = $items->whereHas('customer', function ($query) use ($nhanvienFilter) {
                $query->where('user_id', $nhanvienFilter);
            });
            //$items = $items->where('user_id', $userFilter);
        }
        if (!auth()->user()->hasPermission("customerpay_filterbynhanvien")) {
            $items = $items->whereHas('customer', function ($query) use ($nhanvienFilter) {
                $query->where('user_id', auth()->id());
            });
        }

        $totalQuery = clone $items;
        $total_pay = $totalQuery->sum('pay');
		$total_discount_value = $totalQuery->sum('discount_value');
        $items = $items->orderByDesc('created_at')->paginate(20);
        if ($request->expectsJson()) {
            return response()->json([
                'items' => $items,
                'total_pay' => $total_pay,
				'total_discount_value'=>$total_discount_value
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
            'pay' => 'required|integer',
            'content' => 'required',
            'payDate' => 'required',
        ]);
		$customer = Customer::find($request->customer_id);
        \DB::beginTransaction();
        $pay = CustomerPay::create([
            'customer_id' => $request->customer_id,
            'pay' => $request->pay,
			'discount_value' => $request->pay - $request->pay/(1+$customer->monthly_discount/100),
            'content' => mb_strtoupper($request['content'],'UTF-8'),
            'created_at' => strtotime($request->payDate),
            'user_id' => auth()->id(),
        ]);

        //$customer = Customer::find($request->customer_id);
        $new_money = $customer->money - $request->pay;
        $log_content_customer = "[Nợ] $customer->money -> $new_money  <br>";

        $customer->money = $new_money;
        $customer->save();
        CustomerLog::create([
            'name' => 'Tạo thanh toán',
            'value' => $log_content_customer,
            'user_id' => auth()->id(),
            'customer_id' => $customer->id,
        ]);
        \DB::commit();
	
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
                'item' => $pay
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
            'content' => 'required',
            'pay' => 'required',
        ]);
        \DB::beginTransaction();
        $item = CustomerPay::find($id);

        $customer = Customer::find($item->customer_id);
        $new_money = $customer->money + $item->pay - $request->pay;
        $log_content_customer = "[Nợ] $customer->money -> $new_money  <br>";

        $customer->money = $new_money;
        $customer->save();
        $item->pay = $request->pay;
		$item->discount_value = $request->pay - $request->pay/(1+$customer->monthly_discount/100);
        $item->content = $request['content'];
        $item->save();

        CustomerLog::create([
            'name' => 'Cập nhật thanh toán',
            'value' => $log_content_customer,
            'user_id' => auth()->id(),
            'customer_id' => $customer->id,
        ]);

        \DB::commit();
		
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
                'item' =>$item,
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
        \DB::beginTransaction();
        $item = CustomerPay::find($id);

        $customer = Customer::find($item->customer_id);
        $new_money = $customer->money + $item->pay;
        $log_content_customer = "[Nợ] $customer->money -> $new_money  <br>";
        $customer->money = $new_money;
        $customer->save();

        $item->delete();
        CustomerLog::create([
            'name' => 'Xóa thanh toán',
            'value' => $log_content_customer,
            'user_id' => auth()->id(),
            'customer_id' => $customer->id,
        ]);
        \DB::commit();
		
		//Gửi email thông báo
		if (env('ENABLE_SENT_EMAIL', false) === true) {
		$emailType = 'payment'; // Loại email cụ thể
		$receivedEmail = env('RECEIVED_EMAILSS', 'chienguyenkhac@gmail.com'); // Lấy giá trị hoặc giá trị mặc định
		$data = [
			'customerCode'=>$customer->code,
            'customerName'=>$customer->name,
            'payment' => $request->pay,
			'money' => $new_money,
            'userName'=>auth()->user()->name,
            'date' => Carbon::now()->format('Y-m-d H:i:s'),
			'type' =>'bị xóa khỏi',
			'email'=>$receivedEmail,
		];
		dispatch(new SentEmail($emailType, $data));
		}
		
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

    
    public function checknew(Request $request, $id)
    {
        $pay = CustomerPay::find($id);
		$customer_pays = CustomerPay::where('customer_id', '=', $pay->customer_id)
							->where('created_at', '>', $pay->created_at)
							->count();
		$exports = Export::where('customer_id', '=', $pay->customer_id)
			->where('created_at', '>', $pay->created_at)
			->count();

		$imports = Import::where('customer_id', '=', $pay->customer_id)
			->where('created_at', '>', $pay->created_at)
			->count();

		$canUpdate = true;
		if ($customer_pays >0 ||
			$exports > 0 ||
			$imports > 0)
			$canUpdate = false;
			
        if ($request->expectsJson()) {
            return response()->json([
                'canUpdate' => $canUpdate,
            ]);
        }
    }
    
    

    public function exportExcel(Request $request)
    {
        $key = Input::get('key');
        $strFromDate = Input::get('fromdate');
        $strToDate = Input::get('todate');
        $items = CustomerPay::with('customer', 'user');
        if ($key != '')
        {
            $items = $items->whereHas('customer', function ($query) use ($key) {
                $query->where('code', '=', "$key");
            });
        }
        if ($strFromDate != 'Invalid date') {
            $strFromDate = $strFromDate . '000000';
            $fromDate = Carbon::createFromFormat('dmYHis', $strFromDate);
            $items = $items->where('created_at', '>=', $fromDate);
        }
        if ($strToDate != 'Invalid date') {
            $strToDate = $strToDate . '235959';
            $toDate = Carbon::createFromFormat('dmYHis', $strToDate);
            $items = $items->where('created_at', '<', $toDate);
        }
        $items = $items->orderByDesc('created_at')->get();


        \Excel::create(Carbon::now()->format('dmY') . '_' . 'ThanhToan', function($excel) use($items){
            $excel->sheet('Thống kê', function($sheet) use($items){
                $sheet->row(1, array(
                    'Ngày', 'Mã KH', 'Tên KH', 'Nội dung thanh toán', 'Số tiền thanh toán'
                ));
                $sheet->row(1, function($row) {
                    $row->setBackground('#000000');
                    $row->setFontColor('#ffffff');
                });
                foreach ($items as $key=>$item) {
                    $sheet->row($key + 2, array(
                        $item->created_at->format('d/m/y H:i:s'),
                        $item->customer->code,
                        $item->customer->name,
                        $item->content,
                        number_format($item->pay, 0),
                    ));
                }
            });
        })->download('xlsx');
    }
    
    public function exportpdf($id)
    {
		//Lấy thông tin thanh toán & khách hàng
		$payPDF = CustomerPay::find($id);
        $customer = Customer::find($payPDF->customer_id);
		$customer_id = $customer->id;
		$user = User::where('id', $customer->user_id)->first();
		$new_debt = 0;
	
		// Ngày thanh toán
		$paymentDate = $payPDF ->created_at;
		
		// Tìm các giao dịch trước thời điểm thanh toán để tính nơ khách hàng thời điểm đó
		$pays = CustomerPay::where('customer_id', '=', $customer_id)
            ->where('created_at', '<', $paymentDate)->orderByDesc('created_at')->get();

        $exports = Export::with('products')->where('customer_id', '=', $customer_id)
            ->where('created_at', '<', $paymentDate)->orderByDesc('created_at')->get();

        $imports = Import::with('products')->where('customer_id', '=', $customer_id)
            ->where('created_at', '<', $paymentDate)->orderByDesc('created_at')->get();							 
		
		
		//Gộp 2 cái thanh toán và phiếu bán hàng vào chung 1 mãng
        $items = [];
        foreach($pays as $pay)
        {
            $item = [];
            $item['ngay'] = $pay->created_at;
            $item['tong'] = -$pay->pay;
            $item['nocu'] = '';
            $item['loai'] = 'pay';
            $items[] = $item;
        }
        foreach($exports as $export)
        {
            $item = [];
            $item['ngay'] = $export->created_at;
            $item['tong'] = $export->total;
            $item['nocu'] = $export->debt;
            $item['loai'] = 'export';
            $items[] = $item;
        }

        foreach($imports as $import)
        {
            $item = [];
            $item['ngay'] = $import->created_at;
            $item['tong'] = -$import->total;
            $item['nocu'] = $import->debt;
            $item['loai'] = 'import';
            $items[] = $item;
        }
		$du_dau_ky = 0;
		
		if (count($items) != 0) {
			// Sắp xếp mảng theo ngày giảm dần
			usort($items, function ($a, $b) {
				return $b['ngay']->timestamp - $a['ngay']->timestamp;
			});
			//Tính số dư đầu kỳ.
			$first_item = $items[0];
			//Nếu item đầu tiên là phiếu xuất.Lấy tổng đơn của phía xuất làm dư cuối kỳ
			if ($first_item['loai'] == 'export' || $first_item['loai'] == 'import') {
			$du_dau_ky = abs($first_item['tong']);
			} else { // Nếu giao dịch đầu tiên là pay, tính số dư đầu kỳ theo các giao dịch trước đó, sau đó tính số dư cuối kỳ bằng cách trừ đi thanh toán.
				foreach ($items as $item) {
                        if ($item['loai'] == 'export' || $item['loai'] == 'import') {
                            $du_dau_ky = $du_dau_ky + abs($item['tong']);
                            break;
                        } else
                            $du_dau_ky = $du_dau_ky + $item['tong'];
                    }
			}
			
			
        }		
		$new_debt = $du_dau_ky - $payPDF->pay;					 
		
		
		$options = [];
		foreach(Option::all() as $option) {
			$options[$option->id] = $option->value;
		}
		
		$data = [
			'options' => $options,
			'customer' => $customer->toArray(),
			'user' => $user->toArray(),
			'payPDF' => $payPDF,
			'debt' => $du_dau_ky,
			'new_debt'=> $new_debt,
		];
		
		$pdf = \PDF::loadView('reports.payment_receipt', $data);
		$pdf->setPaper('a4', 'landscape');
		return $pdf->stream($paymentDate->format('dmY') . '_' . str_slug($payPDF->customer->code) . '_payment_receipt.pdf');
									 
	}
}
