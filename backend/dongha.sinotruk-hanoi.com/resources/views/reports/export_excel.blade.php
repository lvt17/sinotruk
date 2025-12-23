<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
<table>
<tr style="text-align: left">
        <th>
            <b>{{$options[1]}}</b>
        </th>
</tr>
<tr style="text-align: center">
        <th colspan="7" style="text-align: center;">
            <h2>BIÊN BẢN GIAO NHẬN HÀNG HÓA</h2>
        </th>
</tr>
<tr>
	<th colspan="7" style="text-align: right">Hà nội, ngày {{$export->created_at->day}} tháng {{$export->created_at->month}} năm {{$export->created_at->year}}</th>
</tr>
<tr style="text-align: left"><p>Kính gửi {{$customer->name}}, chúng tôi xin gửi tới Quý khách bảng kê đơn hàng vật tư phụ tùng như sau:</p>
</tr>
<tr>
</tr>
<tr style="text-align: left">
    <th>GHI CHÚ:</th>
    <th style="text-align: left;">{{$note}}</th>
</tr>
<tr style="text-align: center;border: 1px solid #6c757d;background-color: #ffeeba;">
        <td width="8">
            <b>STT</b>
        </td>
        <td width="10">
            <b>MÃ SP</b>
        </td>
        <td width="25">
            <b>TÊN SP</b>
        </td>
        <td width="8">
            <b>ĐVT</b>
        </td>
        <td width="10">
            <b>SỐ LƯỢNG</b>
        </td>
		<td width="15">
            <b>ĐƠN GIÁ</b>
        </td>
		<td width="15">
            <b>THÀNH TIỀN</b>
        </td>
    </tr>
    @foreach($products as $product)
        <tr>
            <td style="text-align: center">
                {{$loop->index + 1}}
            </td>
            <td style="text-align: left">
                {{$product->code}}
            </td>
            <td style="text-align: left">
                {{$product->name}}
            </td>
            <td style="text-align: center">
                {{$product->type}}
            </td>
            <td style="text-align: center">
                {{$product->count}}
            </td>
            <td style="text-align: right">
                {{$product->price}}
            </td>
            <td style="text-align: right">
                {{$product->total}}
            </td>
        </tr>
    @endforeach
	@if($isVAT==false)
    <tr>
            <td colspan="6" style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d">
                <b>Tổng</b>
            </td>
			<td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d">{{$totalItemPrice}}</td>
			
    </tr>
	<tr>
            <td colspan="6" style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d">
                <b>Nợ cũ</b>
            </td>
			<td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d">{{$debt}}</td>
			
    </tr>
	<tr>
            <td colspan="6" style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d">
                <b>Tổng cộng</b>
            </td>
			<td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d">{{$totalPrice}}</td>
			
    </tr>
    @else
		<tr>
            <td colspan="6" style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d">
                <b>Tổng</b>
            </td>
			<td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d">{{$totalItemPrice}}</td>
			
		</tr>
        <tr>   
			<td colspan="6" style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d">
				<b>VAT</b>
			</td>
			<td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d">{{$vat}}</td>
        </tr>
		<tr>
				<td colspan="6" style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d">
					<b>Nợ cũ</b>
				</td>
				<td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d">{{$debt}}</td>
				
		</tr>
		<tr>
				<td colspan="6" style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d">
					<b>Tổng cộng</b>
				</td>
				<td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d">{{$totalPrice}}</td>
				
		</tr>
    @endif
<tr></tr>
<tr style="text-align: left"><p>Ghi chú: Đơn giá chưa bao gồm thuế giá trị gia tăng VAT ; Báo giá có hiệu lực 30 ngày kể từ ngày báo giá!</p></tr>
</table>
</body>
</html>