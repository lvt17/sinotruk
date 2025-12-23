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
<div style="text-align: center"><h3>THÔNG BÁO V/V XÓA ĐƠN HÀNG KHỎI HỆ THỐNG</h3></div>
<br>
<div>Xin chào! Chúng tôi rất tiếc phải thông báo đến bạn một đơn hàng vừa bị {{$data['type']}} khỏi hệ thống bởi người dùng <strong>{{$data['userName']}}</strong>. Thông tin đơn hàng như sau:</div>
<br>
<table border="1" width="100%">
    <thead>
        <tr>
            <td style="text-align: center">
                <b>Mã KH</b>
            </td>
            <td style="text-align: center">
                <b>Tên KH</b>
            </td>
            <td style="text-align: center">
                <b>Giá trị ĐH</b>
            </td>
            <td style="text-align: center">
                <b>Người xóa</b>
            </td>
            <td style="text-align: center">
                <b>Ngày xóa</b>
            </td>
        </tr>
    </thead>
    <tbody>
		<tr>
			<td style="text-align: center">
                {{$data['customerCode']}}
            </td>
			<td style="text-align: center">
                {{$data['customerName']}}
            </td>
			<td style="text-align: center">
                 {{$data['exportMoney']}}
            </td>
			<td style="text-align: center">
                 {{$data['userName']}}
            </td>
			<td style="text-align: center">
                {{$data['date']}}
            </td>
		</tr>
	</tbody>
</table>
<br>
<div>Email này được tạo tự động khi có người dùng {{$data['type']}} đơn hàng. Vui lòng bỏ qua nếu bạn đã biết thông tin!</div>
<br>
<div>Trân trọng!</div>
<b>SINOTRUK TEAM</b>