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
        <td width="10">
            <b>MÃ SP</b>
        </td>
        <td width="50">
            <b>TÊN SP</b>
        </td>
        <td width="8">
            <b>ĐVT</b>
        </td>
        <td width="15">
            <b>TỒN HIỆN TẠI</b>
        </td>
		<td width="15">
            <b>ĐANG ORDER</b>
        </td>
		<td width="15">
            <b>SL MIN</b>
        </td>
		<td width="15">
            <b>ĐỀ XUẤT NHẬP</b>
        </td>
        <td width="18">
            <b>TỔNG SL ĐÃ BÁN</b>
        </td>
		<td width="22" style="text-align: right">
            <b>TỔNG GIÁ TRỊ ĐÃ BÁN</b>
        </td>
	@if($diff!='')
		<td width="22" style="text-align: right">
            <b>TỔNG GIÁ TRỊ THEO GIÁ {{ $diff ? 'SỈ' : 'LẺ' }}</b>
        </td>
	@endif
    </tr>
	<?php $totalValueSum = 0;$totalVarianceSum = 0; ?>
    @foreach($products as $product)
	<?php $totalValueSum += $product['actual_value']; $totalVarianceSum += $product['diff_value']; ?>
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
				{{$product['total']}}
			</td>
			<td style="text-align: center;border: 1px solid #6c757d">
				{{$product['order_pending']}}
			</td>
			<td style="text-align: center;border: 1px solid #6c757d">
				{{$product['min']}}
			</td>
		@if($product['total'] > $product['min'] || $product['order_pending'] != 0)
			<td style="text-align: center;border: 1px solid #6c757d">
						<p>KHÔNG</p>
			</td>
		@else
			<td style="text-align: center;border: 1px solid #6c757d ; background-color: #f0ff00">
						{{$product['min']}}
			</td>
		@endif
			
			<td style="text-align: center;border: 1px solid #6c757d">
				{{$product['quantity']}}
			</td>
			<td style="text-align: right;border: 1px solid #6c757d">
				{{$product['actual_value']}}
			</td>
		@if($diff!='')
			<td style="text-align: right;border: 1px solid #6c757d">
				{{$product['diff_value']}}
			</td>
		@endif
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
				<td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"></td>
				<td style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d"></td>
                <td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d"><strong>{{$totalValueSum}}</strong></td>
				@if($diff!='')
				<td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d"><strong>{{$totalVarianceSum}}</strong></td>
				@endif
        </tr>
    
</table>
</body>
</html>