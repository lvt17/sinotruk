<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('permissions')->delete();
        \DB::table('role_has_permissions')->delete();

        $jobs = [
		'01. Bán hàng' => [
			['name' => 'export_create', 'description' => 'Tạo'],
			['name' => 'export_read', 'description' => 'Xem'],
			['name' => 'export_delete', 'description' => 'Xóa'],
			['name' => 'export_report', 'description' => 'Báo cáo'],
			['name' => 'export_lock', 'description' => 'Khóa'],
			['name' => 'export_recreate', 'description' => 'Tạo mới'],
			['name' => 'export_date', 'description' => 'Chọn ngày'],
			['name' => 'export_editpriceproduct', 'description' => 'Cập nhật đơn giá'],
			['name' => 'export_uploadexceldssanpham', 'description' => 'Nhập từ Excel'],
			['name' => 'export_dautreo', 'description' => 'Đóng dấu treo'],
			['name' => 'export_excel', 'description' => 'Xuất Excel đơn hàng']
		],
		'02. Nhập kho' => [
			['name' => 'import_create', 'description' => 'Tạo'],
			['name' => 'import_read', 'description' => 'Xem'],
			['name' => 'import_delete', 'description' => 'Xóa'],
			['name' => 'import_report', 'description' => 'Báo cáo'],
			['name' => 'import_recreate', 'description' => 'Tạo mới']
		],
		'03. Báo giá' => [
			['name' => 'quote_create', 'description' => 'Tạo'],
			['name' => 'quote_read', 'description' => 'Xem'],
			['name' => 'quote_update', 'description' => 'Cập nhật'],
			['name' => 'quote_delete', 'description' => 'Xóa'],
			['name' => 'quote_report', 'description' => 'Báo cáo'],
			['name' => 'quote_copyexport', 'description' => 'Copy sang bán hàng'],
			['name' => 'quote_copyimport', 'description' => 'Copy sang nhập kho'],
			['name' => 'quote_date', 'description' => 'Chọn ngày'],
			['name' => 'quote_editpriceproduct', 'description' => 'Cập nhật đơn giá'],
			['name' => 'quote_uploadexceldssanpham', 'description' => 'Nhập từ Excel'],
			['name' => 'quote_dautreo', 'description' => 'Đóng dấu treo']
		],
		'04. Order' => [
			['name' => 'order_create', 'description' => 'Tạo'],
			['name' => 'order_read', 'description' => 'Xem'],
			['name' => 'order_update', 'description' => 'Cập nhật'],
			['name' => 'order_delete', 'description' => 'Xóa'],
			['name' => 'order_lock', 'description' => 'Khóa'],
			['name' => 'order_readall', 'description' => 'Xem tất cả (kể cả ẩn)'],
			['name' => 'order_copyimport', 'description' => 'Copy sang nhập kho'],
			['name' => 'order_creatbyexcel', 'description' => 'Tạo bằng excel'],
			['name' => 'order_export', 'description' => 'Báo cáo'],
			['name' => 'order_invisible', 'description' => 'Ẩn'],
			['name' => 'order_completed', 'description' => 'Hoàn thành order'],
			['name' => 'order_updatesuggestprice', 'description' => 'Cập nhật số lượng đặt hàng'],
			['name' => 'order_hightlight', 'description' => 'Hightlight order'],
			['name' => 'order_thongtinloinhuangia', 'description' => 'Hiển thị thông tin lợi nhuận, giá..'],
			['name' => 'order_new_price', 'description' => 'Xuất Excel giá cập nhật'],
			['name' => 'order_exportpdf', 'description' => 'Xuất PDF Order']
		],
		'05. Thống kê' => [
			['name' => 'report_read', 'description' => 'Xem'],
			['name' => 'report_report', 'description' => 'Báo cáo']
		],
		'06. Sản phẩm' => [
			['name' => 'product_create', 'description' => 'Tạo'],
			['name' => 'product_createall', 'description' => 'Tạo hàng loạt'],
			['name' => 'product_read', 'description' => 'Xem'],
			['name' => 'product_update', 'description' => 'Cập nhật'],
			['name' => 'product_updateimage', 'description' => 'Cập nhật hình'],
			['name' => 'product_delete', 'description' => 'Xóa'],
			['name' => 'product_report', 'description' => 'Báo cáo'],
			['name' => 'product_editcategory', 'description' => 'Cập nhật category'],
			
		],
		'07. Khách hàng' => [
			['name' => 'customer_create', 'description' => 'Tạo'],
			['name' => 'customer_createall', 'description' => 'Tạo hàng loạt'],
			['name' => 'customer_read', 'description' => 'Xem'],
			['name' => 'customer_update', 'description' => 'Cập nhật'],
			['name' => 'customer_delete', 'description' => 'Xóa'],
			['name' => 'customer_report', 'description' => 'Báo cáo'],
			
		],
		'08. Thanh toán' => [
			['name' => 'customerpay_create', 'description' => 'Tạo'],
			['name' => 'customerpay_read', 'description' => 'Xem tất cả ngày'],
			['name' => 'customerpay_readnow', 'description' => 'Xem ngày hiện tại'],
			['name' => 'customerpay_update', 'description' => 'Cập nhật'],
			['name' => 'customerpay_createnew', 'description' => 'Tạo mới'],
			['name' => 'customerpay_delete', 'description' => 'Xóa'],
			['name' => 'customerpay_report', 'description' => 'Báo cáo'],
			['name' => 'customerpay_pdf', 'description' => 'Xuất PDF'],
			['name' => 'customerpay_filterbynhanvien', 'description' => 'Lọc theo nhân viên']
		],
		'09. Công nợ' => [
			['name' => 'debtmanagement_read', 'description' => 'Xem'],
			['name' => 'debtmanagement_report', 'description' => 'Xuất Excel danh sách công nợ'],
			['name' => 'debtmanagement_reportnoti', 'description' => 'Xuất Excel đối soát công nợ']
		],
		'10. Nhật ký' => [
			['name' => 'log_read', 'description' => 'Xem']
		],
		'11. Người dùng' => [
			['name' => 'user_create', 'description' => 'Tạo'],
			['name' => 'user_read', 'description' => 'Xem'],
			['name' => 'user_update', 'description' => 'Cập nhật'],
			['name' => 'user_delete', 'description' => 'Xóa']
		],
		'12. Nhóm' => [
			['name' => 'role_create', 'description' => 'Tạo'],
			['name' => 'role_read', 'description' => 'Xem'],
			['name' => 'role_update', 'description' => 'Cập nhật'],
			['name' => 'role_delete', 'description' => 'Xóa']
		],
		'13. Quyền' => [
			['name' => 'permission_create', 'description' => 'Tạo'],
			['name' => 'permission_read', 'description' => 'Xem'],
			['name' => 'permission_update', 'description' => 'Cập nhật'],
			['name' => 'permission_delete', 'description' => 'Xóa']
		],
		'14. Cấu hình' => [
			['name' => 'option_read', 'description' => 'Xem'],
			['name' => 'option_update', 'description' => 'Cập nhật']
		],
		'15. Category' => [
			['name' => 'category_create', 'description' => 'Tạo'],
			['name' => 'category_read', 'description' => 'Xem'],
			['name' => 'category_update', 'description' => 'Cập nhật'],
			['name' => 'category_delete', 'description' => 'Xóa']
		],
		'16. Dashboard' => [
			['name' => 'dashboard_read', 'description' => 'Xem Dashboard']
    ]
];

	// Tạo các permission từ mảng $jobs
	foreach ($jobs as $jobName => $permissions) {
            $job = \App\Job::where('name', $jobName)->first();
            foreach ($permissions as $permData) {
                $permission = \App\Permission::create($permData);
                $job->permissions()->save($permission);
            }
	}

}
}
