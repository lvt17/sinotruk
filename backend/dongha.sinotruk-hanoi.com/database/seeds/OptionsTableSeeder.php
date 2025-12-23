<?php

use Illuminate\Database\Seeder;

class OptionsTableSeeder extends Seeder
{
    public function run()
    {
        \DB::table('options')->delete();

        $options = [
            ['name' => 'Company', 'value' => 'CÔNG TY CỔ PHẦN SINOTRUK HÀ NỘI'],
            ['name' => 'Address', 'value' => 'NO17-LK17-57, Phường La Khê, Quận Hà Đông, TP Hà Nội'],
            ['name' => 'Sales office', 'value' => 'Số 16, Đường Thị Cấm, Xuân Phương, Nam Từ Liêm, Hà Nội'],
            ['name' => 'Email', 'value' => 'hnsinotruk@gmail.com & hanoi.sinotruk@gmail.com'],
            ['name' => 'Hotline', 'value' => '0382.890.990'],
            ['name' => 'Website', 'value' => 'www.sinotruk-hanoi.com'],
            ['name' => 'Position', 'value' => 'NV Kinh Doanh'],
            ['name' => 'Sender address', 'value' => 'Mỹ Đình - Hà Nội'],
            ['name' => 'Thông báo', 'value' => ''],
        ];

        foreach ($options as $option) {
            \App\Option::create($option);
        }
    }
}
