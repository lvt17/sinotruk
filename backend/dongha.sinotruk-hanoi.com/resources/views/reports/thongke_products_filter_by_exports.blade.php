<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
<table border="1" >
    <tr style="text-align: left">
        <th>
            <b>BẢNG KÊ CHI TIẾT SẢN PHẨM THEO ĐƠN HÀNG</b>
        </th>
    </tr>
	<tr style="text-align: left"><p>Bảng kê này được lọc theo đơn hàng từ ngày {{$fromDate->day}} tháng {{$fromDate->month}} năm {{$fromDate->year}} đến ngày {{$toDate->day}} tháng {{$toDate->month}} năm {{$toDate->year}}</p>
	</tr>
	<tr>
	</tr>
    <tr style="text-align: center;border: 1px solid #6c757d;background-color: #ffeeba;">
        <td width="8">
            <b>STT</b>
        </td>
		<td width="20">
            <b>NGÀY TẠO</b>
        </td>
        <td width="15">
            <b>MÃ KH</b>
        </td>
        <td width="25">
            <b>TÊN KH</b>
        </td>
        <td width="15">
            <b>MÃ SP</b>
        </td>
        <td width="50">
            <b>TÊN SP</b>
        </td>
		<td width="8">
            <b>ĐVT</b>
        </td>
		<td width="8">
            <b>SL</b>
        </td>
		<td width="10">
            <b>ĐƠN GIÁ</b>
        </td>
		<td width="15">
            <b>SỐ TIỀN</b>
        </td>
		<td width="30">
            <b>GHI CHÚ</b>
        </td>
		
    </tr>
	<?php 
	$totalValueSum = 0;
	$totalValuePrice = 0;
	?>
    @foreach($result as $itemProductInfo)
	<?php 
	$totalValueSum += $itemProductInfo['product_info']['quantity'];
	$totalValuePrice += $itemProductInfo['product_info']['totalValue'];
	?>
		<tr >
			<td style="text-align: center;border: 1px solid #6c757d">
				{{$loop->index + 1}}
			</td>
			<td style="text-align: left;border: 1px solid #6c757d">
				{{$itemProductInfo['item_info']['created_at']}}
			</td>
			<td style="text-align: left;border: 1px solid #6c757d">
				{{$itemProductInfo['item_info']['customer_code']}}
			</td>
			<td style="text-align: left;border: 1px solid #6c757d">
				{{$itemProductInfo['item_info']['customer_name']}}
			</td>			
			<td style="text-align: left;border: 1px solid #6c757d">
				{{$itemProductInfo['product_info']['code']}}
			</td>
			<td style="text-align: left;border: 1px solid #6c757d">
				{{$itemProductInfo['product_info']['name']}}
			</td>
			<td style="text-align: center;border: 1px solid #6c757d">
				{{$itemProductInfo['product_info']['type']}}
			</td>
			<td style="text-align: center;border: 1px solid #6c757d">
				{{$itemProductInfo['product_info']['quantity']}}
			</td>
			<td style="text-align: right;border: 1px solid #6c757d">
				{{$itemProductInfo['product_info']['price']}}
			</td>
			<td style="text-align: right;border: 1px solid #6c757d">
				{{$itemProductInfo['product_info']['totalValue']}}
			</td>
			<td style="text-align: left;border: 1px solid #6c757d">
				{{$itemProductInfo['item_info']['note']}}
			</td>
		</tr>
    @endforeach
		<tr>
                <td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"><strong>Tổng giá trị:</strong></td>
				<td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"></td>
				<td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"></td>
				<td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"></td>
				<td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"></td>
				<td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"></td>
				<td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"></td>
                <td style="text-align: center;background-color: #ffeeba; border: 1px solid #6c757d"><strong>{{$totalValueSum}}</strong></td>
				<td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"></td>
				<td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d"><strong>{{$totalValuePrice}}</strong></td>
				<td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"></td>
				
        </tr>
    
</table>
</body>
</html>