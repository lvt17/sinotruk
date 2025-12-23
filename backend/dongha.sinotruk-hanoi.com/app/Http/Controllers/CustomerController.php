<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerLog;
use App\ProductLog;
use App\MyUtils;
use Carbon\Carbon;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SentEmail;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $key = Input::get('key');
        if ($key == null || $key == '')
            $items = Customer::with('user')->orderByDesc('created_at')->paginate(10);
        else
            $items = Customer::with('user')->where('name', 'like', "%$key%")
                ->orWhere('code', 'like', '%' . $key . "%")
                ->orWhere('phone', 'like', '%' . $key . "%")
                ->orderByDesc('created_at')->paginate(10);
        if ($request->expectsJson()) {
            return response()->json([
                'items' => $items,
            ]);
        }
    }

    public function getallcustomer(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'items' => Customer::select('id','name')->orderByDesc('created_at')->get(),
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

        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:customers',
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Mã đã tồn tại',
            ]);
        }
        try {
            
            $customer = Customer::create([
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'money' => ($request->money == null || $request->money == '') ? 0 : $request->money,
				'monthly_discount' => ($request->monthly_discount == null || $request->monthly_discount == '') ? 0 : $request->monthly_discount,
                'bulk_customer' => $request->bulk_customer != null &&  $request->bulk_customer == true ? $request->bulk_customer : false,
                'phone' => $request->phone,
                'address' => $request->address,
                'person' => $request->person,
                'email' => $request->email,
                'user_id' => $request->user_id,
            ]);
            $log_content = "[Nợ] 0 -> $customer->money  <br>";
            CustomerLog::create([
                'name' => 'Thêm khách hàng',
                'value' => $log_content,
                'user_id' => auth()->id(),
                'customer_id' => $customer->id,
            ]);
        } catch (QueryException $ex) {
            return response()->json([
                'status' => 0,
                'message' => 'Thông tin bạn nhập bị lỗi khi thêm vào hệ thống, vui lòng liên hệ admin.',
            ]);
        }
        if ($request->expectsJson()) {
            return response()->json([
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
            'code' => 'required',
            'name' => 'required',
        ]);
		
		
        try {
            $item = Customer::find($id);
            //$old_money = $item->money;
            $item->code = strtoupper($request->code);
            $item->name = $request->name;
            //$item->money = ($request->money === null || $request->money === '') ? 0 : $request->money; //Update số dư công nơ
            $item->bulk_customer = $request->bulk_customer != null &&  $request->bulk_customer == true ? $request->bulk_customer : false;
            $item->phone = $request->phone;
            $item->address = $request->address;
            $item->email = $request->email;
            $item->person = $request->person;
			$item->monthly_discount = ($request->monthly_discount === null || $request->monthly_discount === '') ? 0 : $request->monthly_discount;
            $item->user_id = $request->user_id;
            $item->save();
        }
        catch (QueryException $ex) {

            return response()->json([
                'status' => 0,
                'message' => 'Thông tin bạn nhập bị lỗi khi thêm vào hệ thống, vui lòng liên hệ admin.',
            ]);
        }
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

        $item = Customer::find($id);
        $item->code = $item->code . '_x_' . date("dmyHis");
        $item->save();
        $item->delete();
        CustomerLog::create([
            'name' => 'Xóa khách hàng',
            'value' => "",
            'user_id' => auth()->id(),
            'customer_id' => $item->id,
        ]);

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
            $item = Customer::find($id);
            $item->code = $item->code . '_x_' . date("dmyHis");
            $item->save();
            $item->delete();
            CustomerLog::create([
                'name' => 'Xóa khách hàng',
                'value' => "",
                'user_id' => auth()->id(),
                'customer_id' => $item->id,
            ]);
        }
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }
	
   public function uploadExcel(Request $request)
{
    $file_name = time() . $request->uploadFile->getClientOriginalName();
    $request->uploadFile->move('uploads/', $file_name);
    \Excel::filter('chunk')->load('uploads/' . $file_name)->chunk(30, function ($results) {
        \DB::beginTransaction();
        foreach ($results as $value) {
            try {
                $item = Customer::where('code', strtoupper($value->ma))->first();
                if ($item) {
					// Các cột khác chỉ được cập nhật nếu có giá trị trong file Excel
                    $item->name = isset($value->ten) ? $value->ten : $item->name;
					$item->bulk_customer = isset($value->sile) ? (($value->sile == 'sỉ') ? 1 : 0) : $item->bulk_customer;
					$item->phone = isset($value->dien_thoai) ? $value->dien_thoai : $item->phone;
					$item->address = isset($value->dia_chi) ? $value->dia_chi : $item->address;
					$item->email = isset($value->email) ? $value->email : $item->email;
					$item->person = isset($value->dai_dien) ? $value->dai_dien : $item->person;
					$item->money = isset($value->no) ? $value->no : $item->money;
					$item->monthly_discount = isset($value->chiet_khau) ? $value->chiet_khau : $item->monthly_discount;
					$item->user_id = isset($value->nv_id) ? $value->nv_id : $item->user_id;
					$item->save();
                } else {
                    $newCustomer = new Customer();
                    $newCustomer->code = $value->ma;
                    $newCustomer->name = $value->ten;
                    $newCustomer->bulk_customer = isset($value->sile) ? ($value->sile == 'sỉ' ? 1 : 0) : 1;
                    $newCustomer->phone = isset($value->dien_thoai) ? $value->dien_thoai : null;
                    $newCustomer->address = isset($value->dia_chi) ? $value->dia_chi : null;
                    $newCustomer->email = isset($value->email) ? $value->email : null;
                    $newCustomer->person = isset($value->dai_dien) ? $value->dai_dien : null;
                    $newCustomer->money = isset($value->no) ? $value->no : 0;
                    $newCustomer->monthly_discount = isset($value->chiet_khau) ? $value->chiet_khau : 0;
                    $newCustomer->user_id = isset($value->nv_id) ? $value->nv_id : null;
                    $newCustomer->save();
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
        $customers = Customer::where('name', 'like', '%' . $request->text . "%")
            ->orWhere('code', 'like', '%' . $request->text . "%")
            ->get();
        if ($request->expectsJson()) {
            return response()->json([
                'customers' => $customers
            ]);
        }
    }

    public function logs(Request $request)
    {
        $strFromDate = Input::get('tungay');
        $strToDate = Input::get('toingay');
        $customerFilter = Input::get('khachhang');

        $items = CustomerLog::with('user', 'customer');
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
        if ($customerFilter != '') {
            $items = $items->whereHas('customer', function ($query) use ($customerFilter) {
                $query->where('name', 'like', "%$customerFilter%")
                    ->orWhere('code', 'like', "%$customerFilter%");
            });
        }
        $items = $items->orderByDesc('created_at')->paginate(10);

        if ($request->expectsJson()) {
            return response()->json([
                'items' => $items,
            ]);
        }
    }
public function exportExcel()
{	
	$key = Input::get('key');
        if ($key == null || $key == '')
            $items = Customer::with('user')->orderByDesc('created_at')->get();
        else
            $items = Customer::with('user')->where('name', 'like', "%$key%")
                ->orWhere('code', 'like', '%' . $key . "%")
                ->orWhere('phone', 'like', '%' . $key . "%")
                ->orderByDesc('created_at')->get();
	//Gửi email thông báo
		if (env('ENABLE_SENT_EMAIL', false) === true) {
		$emailType = 'excel_export'; // Loại email cụ thể
		$receivedEmail = env('RECEIVED_EMAIL', 'chienguyenkhac@gmail.com'); // Lấy giá trị hoặc giá trị mặc định
		$data = [			
            'userName'=>auth()->user()->name,
            'date' => Carbon::now()->format('Y-m-d H:i:s'),
			'type' =>'danh sách khách hàng',
			'email'=>$receivedEmail,
		];
		dispatch(new SentEmail($emailType, $data));
		}
	// Ghi vào log
	
	ProductLog::create([
                'name' => "Xuất Excel KH",
                'value' => "Xuất Excel chứa danh sách khách hàng từ hệ thống SINOTRUK",
                'user_id' => auth()->id(),
            ]);
	
    \Excel::create(Carbon::now()->format('dmY') . '_' . 'KhachHang', function($excel) use($items){
        $excel->sheet('Tồn', function($sheet) use($items){
            $sheet->row(1, array(
                'STT', 'Mã', 'Tên', 'Sỉ/Lẻ', 'Nợ', 'Chiết khấu', 'Đại diện', 'Điện thoại', 'Email', 'Địa chỉ', 'Nhân viên chăm sóc', 'NV Id'
            ));
            $sheet->row(1, function($row) {
                $row->setBackground('#000000');
                $row->setFontColor('#ffffff');
            });

            foreach ($items as $key => $item) {
                $sheet->row($key + 2, array(
                    $key + 1,
                    $item->code,
                    $item->name,
                    $item->bulk_customer == 0 ? 'lẻ' : 'sỉ',
                    $item->money,
                    $item->monthly_discount,
                    $item->person,
                    $item->phone,
                    $item->email,
                    $item->address,
                    $item->user == null ? null : $item->user->name,
                    $item->user == null ? null : $item->user->id
                ));
            }
        });

    })->download('xlsx');
}

}


