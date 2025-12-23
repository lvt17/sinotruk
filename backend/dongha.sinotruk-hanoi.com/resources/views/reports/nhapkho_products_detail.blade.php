<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
<table border="1" >
    <tr style="text-align: left">
        <th>
            <b>BẢNG KÊ CHI TIẾT SẢN PHẨM THEO PHIẾU NHẬP KHO</b>
        </th>
    </tr>
	<tr style="text-align: left"><p>Bảng kê này được lọc theo phiếu nhập từ ngày {{$fromDate->day}} tháng {{$fromDate->month}} năm {{$fromDate->year}} đến ngày {{$toDate->day}} tháng {{$toDate->month}} năm {{$toDate->year}}</p>
	</tr>
	<tr>
	</tr>
    <tr style="text-align: center;border: 1px solid #6c757d;background-color: #ffeeba;">
        <td width="8">
            <b>STT</b>
        </td>
        <td width="10">
            <b>MÃ SP</b>
        </td>
        <td width="50">
            <b>TÊN SP</b>
        </td>
        <td width="8">
            <b>ĐVT</b>
        </td>
        <td width="18">
            <b>TỔNG SL ĐÃ NHẬP</b>
        </td>
		<td width="22" style="text-align: right">
            <b>TỔNG GIÁ TRỊ ĐÃ NHẬP</b>
        </td>
    </tr>
	<?php $totalValueSum = 0; ?>
    @foreach($products as $product)
	<?php $totalValueSum += $product['value']; ?>
		<tr >
			<td style="text-align: center;border: 1px solid #6c757d">
				{{$loop->index + 1}}
			</td>
			<td style="text-align: left;border: 1px solid #6c757d">
				{{$product['code']}}
			</td>
			<td style="text-align: left;border: 1px solid #6c757d">
				{{$product['name']}}
			</td>
			<td style="text-align: center;border: 1px solid #6c757d">
				{{$product['type']}}
			</td>			
			<td style="text-align: center;border: 1px solid #6c757d">
				{{$product['quantity']}}
			</td>
			<td style="text-align: right;border: 1px solid #6c757d">
				{{$product['value']}}
			</td>
		</tr>
    @endforeach
		<tr>
                <td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"><strong>Tổng giá trị:</strong></td>
				<td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"></td>
				<td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"></td>
				<td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"></td>
				<td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"></td>
		        <td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d"><strong>{{$totalValueSum}}</strong></td>
        </tr>
    
</table>
</body>
</html>