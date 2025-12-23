<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerPay;
use App\Export;
use App\Import;
use Carbon\Carbon;
use App\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class DebtController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $key = Input::get('key');
        $user_id = Input::get('user_id');
        if ($key == null || $key == '')
            $items = Customer::with('user')->orderByDesc('money');
        else
            $items = Customer::with('user')->where('code', '=', $key)
                ->orderByDesc('code');

        if ($user_id != null || $user_id != '')
            $items = $items->where('user_id', $user_id);

        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now();
//        $items = $items->leftJoin(
//            \DB::raw('(SELECT customer_id, MAX(pay) TotalPay
//               FROM `customer_pays`
//               WHERE created_at >= "' . $start . '" and created_at < "' . $end .
//               '" GROUP BY customer_id)
//               TotalPayS'),
//            function($join)
//            {
//                $join->on('id', '=', 'TotalPayS.customer_id');
//            }
//        );
        $items = $items->leftJoin(
            \DB::raw('(SELECT customer_id, MAX(created_at) TotalPay
               FROM `customer_pays`
               GROUP BY customer_id)
               TotalPayS'),
            function($join)
            {
                $join->on('id', '=', 'TotalPayS.customer_id');
            }
        );
        $totalQuery = clone $items;
        $total_debt = $totalQuery->sum('money');

        $items = $items->paginate(20);
        if ($request->expectsJson()) {
            return response()->json([
                'items' => $items,
                'total_debt' => $total_debt
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function exportExcel()
    {
        $key = Input::get('key');
        $user_id = Input::get('user_id');
        if ($key == null || $key == '')
            $items = Customer::with('user')->orderBy('code');
        else
            $items = Customer::with('user')->where('code', '=', $key)
                ->orderBy('code');
        if ($user_id != null || $user_id != '')
            $items = $items->where('user_id', $user_id);
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now();

        $items = $items->leftJoin(
            \DB::raw('(SELECT customer_id, MAX(created_at) TotalPay
               FROM `customer_pays`
               GROUP BY customer_id)
               TotalPayS'),
            function($join)
            {
                $join->on('id', '=', 'TotalPayS.customer_id');
            }
        );
        $items = $items->get();
        \Excel::create(Carbon::now()->format('dmY') . '_' . 'Công nợ', function($excel) use($items){
            $excel->sheet('Thống kê', function($sheet) use($items){
//                $sheet->row(1, array(
//                    'STT', 'Mã KH', 'Tên KH', 'Nợ cũ', 'Đã thanh toán', 'Công nợ'
//                ));
                $sheet->row(1, array(
                    'STT', 'Mã KH', 'Tên KH', 'SĐT', 'NV chăm sóc', 'Ngày thanh toán gần hất', 'Công nợ'
                ));
                $sheet->row(1, function($row) {
                    $row->setBackground('#000000');
                    $row->setFontColor('#ffffff');
                });
                foreach ($items as $key=>$item) {
                    $money = $item->money === null ? 0 : $item->money;
                    $total_pay = $item->TotalPay === null ? '' : (new Carbon($item->TotalPay))->format('d/m/Y H:i:s');
                    $sheet->row($key + 2, array(
                        $key + 1,
                        $item->code,
                        $item->name,
                        $item->phone,
                        $item->user == null ? "" : $item->user->name,
                        $total_pay,
                        number_format($money),
                    ));
                }
            });
        })->download('xlsx');
    }

    public function exportExcelTotal()
    {
        $customer_id = Input::get('khachhang');
        $strFromDate = Input::get('tungay');
        $strToDate = Input::get('toingay');
        $strFromDate = $strFromDate . '000000';
        $strToDate = $strToDate . '235959';
        $fromDate = Carbon::createFromFormat('dmYHis', $strFromDate);
        $toDate = Carbon::createFromFormat('dmYHis', $strToDate);
		// Khởi tạo mảng options và thêm các option vào mảng
		$options = [];
		foreach(Option::all() as $option)
		{
			$options[$option->id] = $option->value;
		}

        $pays = CustomerPay::where('customer_id', '=', $customer_id)
            ->where('created_at', '>=', $fromDate)
            ->where('created_at', '<', $toDate)->orderBy('created_at')->get();

        $exports = Export::with('products')->where('customer_id', '=', $customer_id)
            ->where('created_at', '>=', $fromDate)
            ->where('created_at', '<', $toDate)->orderBy('created_at')->get();

        $imports = Import::with('products')->where('customer_id', '=', $customer_id)
            ->where('created_at', '>=', $fromDate)
            ->where('created_at', '<', $toDate)->orderBy('created_at')->get();


        //Gộp 2 cái thanh toán và phiếu bán hàng vào chung 1 mãng
        $items = [];
        foreach($pays as $pay)
        {
            $item = [];
            $item['ngay'] = $pay->created_at;
            $item['noidung'] = $pay->content;
            $item['tong'] = -$pay->pay;
            $item['vat'] = 0;
            $item['sodu'] = 0;
            $item['details'] = '';
            $item['nocu'] = '';
            $item['loai'] = 'pay';
            $items[] = $item;
        }
        foreach($exports as $export)
        {
            $item = [];
            $item['ngay'] = $export->created_at;
            $item['noidung'] = '';
            $item['tong'] = $export->money;
            $item['vat'] = $export->isVAT == true ? $export->money * 10 / 100 : 0;
            $item['sodu'] = 0;
            $item['details'] = $export->products;
            $item['nocu'] = $export->debt;
            $item['loai'] = 'export';
            $item['note'] = $export->note;
            $items[] = $item;
        }

        foreach($imports as $import)
        {
            $item = [];
            $item['ngay'] = $import->created_at;
            $item['noidung'] = '';
            $item['tong'] = -$import->money;
            $item['vat'] = -($import->isVAT == true ? $import->money * 10 / 100 : 0);
            $item['sodu'] = 0;
            $item['details'] = $import->products;
            $item['nocu'] = $import->debt;
            $item['loai'] = 'import';
            $item['note'] = $import->content;
            $items[] = $item;
        }
        $du_dau_ky = 0;
        $du_hien_tai = 0;
        $items_new = [];
        if (count($items) != 0) {
            //Sắp xếp mãng theo ngày
            $items = array_values(array_sort($items, function($value)
            {
                return $value['ngay'];
            }));


            //Tính số dư đầu kỳ.
            $first_item = $items[0];
            //Nếu item đầu tiên là phiếu xuất.Lấy nợ cũ của phía xuất làm dư đầu kỳ
            if ($first_item['loai'] == 'export' || $first_item['loai'] == 'import') {
                $du_dau_ky = $first_item['nocu'];
            }
            
            //Nếu item đầu tiên là thanh toán
            else {
                    $contain_export_import = false;
            //Kiểm tra có giao dịch xuất nhập nào trong khoảng thời gian đã lọc hay không.Nếu có tính toán số dư đầu kỳ theo nợ cũ của mỗi giao dịch
                    foreach ($items as $item) {
                        if ($item['loai'] == 'export' || $item['loai'] == 'import') {
                            $du_dau_ky = $du_dau_ky + $item['nocu'];
                            $contain_export_import = true;
                            break;
                        } else
                            $du_dau_ky = $du_dau_ky - $item['tong'];
                    }
    
                    if ($contain_export_import == false) {
        					$last_export = Export::with('products')
        						->where('customer_id', $customer_id)
        						->where('created_at', '<', $fromDate)
        						->orderByDesc('created_at')
        						->first();
        
        					$last_import = Import::with('products')
        						->where('customer_id', $customer_id)
        						->where('created_at', '<', $fromDate)
        						->orderByDesc('created_at')
        						->first();
        
        					// Chọn giao dịch gần nhất từ cả hai kết quả
        					$last_transaction = ($last_export && $last_import) ? 
        						($last_export->created_at > $last_import->created_at ? $last_export : $last_import) :
        						($last_export ?: $last_import);
        
        					if ($last_transaction) {
        						$du_dau_ky = $last_transaction->total;
        						// Tìm tất cả thanh toán của khách hàng từ ngày phát sinh giao dịch gần nhất đến ngày lọc $fromDate:
        						$last_pays = CustomerPay::where('customer_id', $customer_id)
        							->where('created_at', '>=', $last_transaction->created_at)
        							->where('created_at', '<', $fromDate)
        							->orderBy('created_at')
        							->get();
        
        						foreach ($last_pays as $last_pay) {
        							$du_dau_ky = $du_dau_ky - $last_pay->pay;
        						}
        					}
        
        				}
                }
                $du_hien_tai = $du_dau_ky;
    
                //Tính số dư cho từng item
                foreach ($items as $item) {
                    $du_hien_tai = $du_hien_tai + $item['tong'] + $item['vat'];
                    $item['sodu'] = $du_hien_tai;
                    $items_new[] = $item;
                }
        }
		
		$customer = Customer::find($customer_id);
		$keyword = "PARTNER";
		$view = stripos($customer->code, $keyword) !== false ? 'reports.reconcile_debts' : 'reports.congno';
		\Excel::create(Carbon::now()->format('dmY') . '_' . 'doisoatcongno'.'_'.$customer->code, function($excel) use ($customer, $fromDate, $toDate, $items_new, $du_dau_ky, $du_hien_tai,$options, $view){
			$excel->sheet('Thống kê', function($sheet) use ($customer, $fromDate, $toDate, $items_new, $du_dau_ky, $du_hien_tai, $options, $view){
				$sheet->loadView($view, [
					'options' => $options,
					'customer' => $customer,
					'fromDate' => $fromDate,
					'toDate' => $toDate,
					'items' => $items_new,
					'du_dau_ky' => $du_dau_ky,
					'du_cuoi_ky' => $du_hien_tai,
				]);
			});
		})->download('xlsx');
    }
}
