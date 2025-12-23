<?php

use Illuminate\Database\Seeder;

class JobsTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('jobs')->delete();

        $jobs = [
            ['name' => '01. Bán hàng'],
            ['name' => '02. Nhập kho'],
            ['name' => '03. Báo giá'],
            ['name' => '04. Order'],
            ['name' => '05. Thống kê'],
            ['name' => '06. Sản phẩm'],
            ['name' => '07. Khách hàng'],
            ['name' => '08. Thanh toán'],
            ['name' => '09. Công nợ'],
            ['name' => '10. Nhật ký'],
            ['name' => '11. Người dùng'],
            ['name' => '12. Nhóm'],
            ['name' => '13. Quyền'],
            ['name' => '14. Cấu hình'],
			['name' => '15. Category'],
			['name' => '16. Dashboard']
			
        ];

        foreach ($jobs as $job) {
            \App\Job::create($job);
        }
    }
}
