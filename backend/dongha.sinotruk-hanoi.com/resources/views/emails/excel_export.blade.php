<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 10px;
			margin:0;
			padding:0; 
        }
    </style>
</head>
<body>
<div style="text-align: center"><h3>THÔNG BÁO V/V XUẤT FILE EXCEL {{ mb_strtoupper($data['type'], 'UTF-8') }} TỪ HỆ THỐNG</h3></div>
<br>
<div>Xin chào! Chúng tôi muốn thông báo đến bạn về việc một người dùng có tên <strong>{{$data['userName']}}</strong> vừa xuất một tệp excel chứa {{$data['type']}} từ hệ thống SINOTRUK vào lúc <strong>{{$data['date']}}</strong></div>
<br>
<br>
<div>Email này được tạo tự động khi có người dùng xuất tệp excel chứa {{$data['type']}} từ hệ thống SINOTRUK. Vui lòng bỏ qua nếu bạn đã biết thông tin!</div>
<br>
<div>Trân trọng!</div>
<b>SINOTRUK TEAM</b>