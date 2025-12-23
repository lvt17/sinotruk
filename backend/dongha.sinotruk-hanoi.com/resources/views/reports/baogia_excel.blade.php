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
<tr style="text-align: left">
        <th>
            <h2>BẢNG BÁO GIÁ VẬT TƯ PHỤ TÙNG</h2>
        </th>
</tr>
<tr style="text-align: left"><p>Kính gửi {{$customer->name}}, chúng tôi xin gửi tới Quý khách bảng báo giá vật tư phụ tùng như sau:</p>
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
        <td width="40">
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
		@if($customVAT != "false")
		<th width="20" style="text-align: center">
            <b>THUẾ SUẤT (%)</b>
        </th>
	@endif
    </tr>
    @foreach($products as $product)
        <tr>
            <td style="text-align: center ; border: 1px solid #6c757d">
                {{$loop->index + 1}}
            </td>
            <td style="text-align: left; border: 1px solid #6c757d">
                {{$product->code}}
            </td>
            <td style="text-align: left; border: 1px solid #6c757d">
                {{$product->name}}
            </td>
            <td style="text-align: center; border: 1px solid #6c757d">
                {{$product->type}}
            </td>
            <td style="text-align: center; border: 1px solid #6c757d">
                {{$product->count}}
            </td>
            <td style="text-align: right; border: 1px solid #6c757d">
                {{$product->price}}
            </td>
            <td style="text-align: right; border: 1px solid #6c757d">
                {{$product->total}}
            </td>
			@if($customVAT != "false")
			<td style="text-align: center; border: 1px solid #6c757d">
            </td>
			@endif
        </tr>
    @endforeach
    <tr>
            <td colspan="6" style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d">
                <b>Tổng</b>
            </td>
			<td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d">{{$totalItemPrice}}</td>
			@if($customVAT != "false")
			<td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d"></td>
			@endif
    </tr>
    @if($isVAT)
        <tr>   
			<td colspan="6" style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d">
				<b>VAT</b>
			</td>
			<td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d">{{$vat}}</td>
			@if($customVAT != "false")
			<td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d"></td>
			@endif
        </tr>
        <tr>
			<td colspan="6" style="text-align: left;background-color: #ffeeba; border: 1px solid #6c757d">
				<b>Tổng cộng</b>
			</td>
            <td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d">{{$totalPrice}}</td>
			@if($customVAT != "false")
			<td style="text-align: right;background-color: #ffeeba; border: 1px solid #6c757d"></td>
			@endif
        </tr>
    @endif
<tr></tr>
<tr style="text-align: left"><p>Ghi chú: Đơn giá chưa bao gồm thuế giá trị gia tăng VAT ; Báo giá có hiệu lực 30 ngày kể từ ngày báo giá!</p></tr>
</table>
</body>
</html>