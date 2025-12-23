<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\CustomerPay;
use App\Export;
use App\Import;
use App\Order;
use App\User;
use App\Product;
use App\LaravelChart;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class DashBoardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $today = Carbon::now()->toDateString();

        // Lấy tên của người dùng
        $users = User::pluck('name', 'id')->all();

        // Tính toán các thông số
        $totalMoneyOrders = $this->getTotalMoneyOrders($today);
        $totalCustomerPayments = $this->getTotalCustomerPayments($today);
        $totalCosts = $this->getTotalCosts($today);
        $totalDebts = $this->getTotalDebts();
        $totalOrders = $this->getTotalOrders($today);
        $totalImports = $this->getTotalImports($today);
        $totalNewProducts = $this->getTotalNewProducts($today);
        $totalNewCustomers = $this->getTotalNewCustomers($today);

        // Biểu đồ đường thống kê bán hàng và thanh toán 30 ngày gần nhất
        $export_payment_chart = $this->getExportAndPaymentChartOptions();

        // Biểu đồ cột thanh toán trong tháng theo nhân viên
        $payment_chart = $this->getPaymentChartOptions($users);

        // Biểu đồ Order hàng trong năm
        $order_chart = $this->getOrderChartOptions();

        // Biểu đồ công nợ theo nhân viên
        $debt_chart = $this->getDebtChartOptions($users);

        $user = Auth::user();

        return view('dashboard', compact(
            'totalMoneyOrders',
            'totalCustomerPayments',
            'totalCosts',
            'totalDebts',
            'totalOrders',
            'totalImports',
            'totalNewProducts',
            'totalNewCustomers',
            'export_payment_chart',
            'payment_chart',
            'debt_chart',
            'order_chart',
            'user'
        ));
    }

    // Các hàm tính toán thông số
    private function getTotalMoneyOrders($today)
    {
        return Export::whereDate('created_at', $today)->sum('money_with_vat');
    }

    private function getTotalCustomerPayments($today)
    {
        return CustomerPay::whereDate('created_at', $today)->sum('pay');
    }

    private function getTotalCosts($today)
    {
        return Export::whereDate('exports.created_at', $today)
            ->join('customers', 'exports.customer_id', '=', 'customers.id')
            ->where('customers.code', 'like', '%guihang%')
            ->sum('money_with_vat');
    }

    private function getTotalDebts()
    {
        return Customer::sum('money');
    }

    private function getTotalOrders($today)
    {
        return Export::whereDate('created_at', $today)->count();
    }

    private function getTotalImports($today)
    {
        return Import::whereDate('created_at', $today)->count();
    }

    private function getTotalNewProducts($today)
    {
        return Product::whereDate('created_at', $today)->count();
    }

    private function getTotalNewCustomers($today)
    {
        return Customer::whereDate('created_at', $today)->count();
    }


    // Các hàm cấu hình biểu đồ
    private function getExportAndPaymentChartOptions()
    {
        $export_chart_options = [
			'chart_title' => 'Tổng bán hàng',
			'report_type' => 'group_by_date',
			'model' => 'App\Export',
			'group_by_field' => 'created_at',
			'group_by_period' => 'day',
			'chart_type' => 'line',
			'filter_field' => 'created_at',
			'filter_days' => 30,
			'aggregate_function' => 'sum',
			'aggregate_field' => 'money_with_vat',
			'chart_height' => 400, // Set the desired chart height
			'date_format' => 'Y-m-d', // Set the date format
			'chart_color' => '30, 144, 255',
			'labels' => [
				'old' => 'money_with_vat', // The field in the database
				'new' => 'Bán hàng', // The label to be displayed
			],
			'number_format' => [
				'style' => 'decimal',
				'locale' => 'vi-VN',
			],
		];

        $payment_chart_options = [
			'chart_title' => 'Tổng thanh toán',
			'report_type' => 'group_by_date',
			'model' => 'App\CustomerPay',
			'group_by_field' => 'created_at',
			'group_by_period' => 'day',
			'chart_type' => 'line',
			'filter_field' => 'created_at',
			'filter_days' => 30,
			'aggregate_function' => 'sum',
			'aggregate_field' => 'pay',
			'chart_height' => 400, // Set the desired chart height
			'date_format' => 'Y-m-d', // Set the date format
			'chart_color' => '7, 255, 64',
			'labels' => [
				'old' => 'pay', // The field in the database
				'new' => 'Doanh thu', // The label to be displayed
			],
			'number_format' => [
				'style' => 'decimal',
				'locale' => 'vi-VN',
			],
		];

        return new LaravelChart($export_chart_options, $payment_chart_options);
    }

    private function getPaymentChartOptions($users)
    {
        $payment_chart_options = [
		'chart_title' => 'Thanh toán trong tháng theo nhân viên',
		'chart_type' => 'bar',
		'model' => 'App\CustomerPay',
		'report_type' => 'group_by_relationship',
		'relationship_name' => 'customer',
		'group_by_field' => 'user_id', // Nhóm theo id của người quản lý
		'aggregate_function' => 'sum',
		'aggregate_field' => 'pay',
		'filter_field' => 'created_at',
		'chart_height' => 400,
        'filter_period' => 'month', // show users only registered this month
		'date_format' => 'Y-m-d H:i:s',
		'chart_color' => '0, 230, 64',
		'labels' => $users,
		'number_format' => [
			'style' => 'decimal',
			'locale' => 'vi-VN',
			],
	];

        return new LaravelChart($payment_chart_options);
    }

    private function getOrderChartOptions()
    {
        $order_chart_options = [
			'chart_title' => 'Nhập hàng TQ',
			'report_type' => 'group_by_date',
			'model' => 'App\Order',
			'group_by_field' => 'updated_at',
			'group_by_period' => 'month',
			'chart_type' => 'bar',
			'filter_field' => 'updated_at',
			'filter_period' => 'year',
			'aggregate_function' => 'sum',
			'aggregate_field' => 'money',
			'chart_height' => 400, 
			'date_format' => 'Y-m-d',
			'chart_color' => '3255, 193, 7',
			'labels' => [
				'old' => 'money', // The field in the database
				'new' => 'Order', // The label to be displayed
			],
			'number_format' => [
				'style' => 'decimal',
				'locale' => 'vi-VN',
			],
		];

        return new LaravelChart($order_chart_options);
    }

    private function getDebtChartOptions($users)
    {
        $debt_chart_options = [
		'chart_title' => 'Công nợ theo nhân viên',
		'chart_type' => 'bar',
		'report_type' => 'group_by_string',
		'model' => 'App\Customer',
		'group_by_field' => 'user_id', // Nhóm theo id của người quản lý
		'aggregate_function' => 'sum',
		'aggregate_field' => 'money',
		'filter_field' => 'created_at',
		'chart_height' => 400,
		'date_format' => 'Y-m-d H:i:s',
		'chart_color' => '255, 0, 0',
		'labels' => $users,
		'number_format' => [
			'style' => 'decimal',
			'locale' => 'vi-VN',
			],
		];

        return new LaravelChart($debt_chart_options);
    }
}
