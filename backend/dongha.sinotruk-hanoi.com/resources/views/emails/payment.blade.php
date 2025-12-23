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
<div style="text-align: center"><h3>THÔNG BÁO V/V THAY ĐỔI THANH TOÁN CỦA KHÁCH HÀNG</h3></div>
<br>
<div>Xin chào! Chúng tôi muốn thông báo đến bạn một thanh toán của khách hàng {{$data['customerName']}} vừa {{$data['type']}} hệ thống bởi người dùng <strong>{{$data['userName']}}</strong>. Thông tin phiếu thanh toán như sau:</div>
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
                <b>Giá trị thanh toán</b>
            </td>
			<td style="text-align: center">
                <b>Số dư</b>
            </td>
            <td style="text-align: center">
                <b>Người tạo</b>
            </td>
            <td style="text-align: center">
                <b>Ngày tạo</b>
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
                 {{$data['payment']}}
            </td>
			<td style="text-align: center">
                 {{$data['money']}}
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
<div>Email này được tạo tự động khi thanh toán của khách hàng {{$data['type']}} hệ thống SINOTRUK. Vui lòng bỏ qua nếu bạn đã biết thông tin!</div>
<br>
<div>Trân trọng!</div>
<b>SINOTRUK TEAM</b>