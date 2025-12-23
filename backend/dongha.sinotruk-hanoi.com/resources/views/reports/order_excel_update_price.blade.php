<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
<table border="1" >
    <tr style="text-align: left">
        <th>
            <b>{{$tenphieu}}</b>
        </th>
    </tr>
	<tr style="text-align: left"><p>Lưu ý: Do tồn kho của sản phẩm thay đổi từng ngày nên bảng tính chỉ có giá trị tham khảo khi hàng mới về</p></tr>
	<tr>
	</tr>
    <tr style="text-align: center;border: 1px solid #6c757d;background-color: #ffeeba;">
        <td width="8">
            <b>STT</b>
        </td>
        <td width="15">
            <b>MÃ SP</b>
        </td>
        <td width="30">
            <b>TÊN SP</b>
        </td>
        <td width="8">
            <b>ĐVT</b>
        </td>
        <td width="8">
            <b>TỒN</b>
        </td>
        <td width="15">
            <b>TQ GỬI</b>
        </td>
		<td width="15" style="text-align: right">
            <b>GIÁ HIỆN TẠI</b>
        </td>
        <td width="15" style="text-align: right">
            <b>GIÁ LO MỚI</b>
        </td>
        <td width="18" style="text-align: right">
            <b>GIÁ ĐIỀU CHỈNH</b>
        </td>
		<td width="15">
            <b>CẬP NHẬT GIÁ</b>
        </td>
        <td width="15">
            <b>TRẠNG THÁI</b>
        </td>
    </tr>
    @foreach($products as $product)
        @if ($product->hightlight)
            <tr style="background-color: #ffff00;">
        @else
            <tr >
        @endif
                <td style="text-align: center;border: 1px solid #6c757d">
                    {{$loop->index + 1}}
                </td>
                <td style="text-align: left;border: 1px solid #6c757d">
                    {{$product->code}}
                </td>
                <td style="text-align: left;border: 1px solid #6c757d">
                    {{$product->name}}
                </td>
                <td style="text-align: center;border: 1px solid #6c757d">
                    {{$product->type}}
                </td>			
    			<td style="text-align: center;border: 1px solid #6c757d">
    				{{$product->total}}
    			</td>
    			<td style="text-align: center;border: 1px solid #6c757d">
    				{{$product->hscode}}
    			</td>
                <td style="text-align: right;border: 1px solid #6c757d">
                    {{$product->price_bulk}}
                </td>
                <td style="text-align: right;border: 1px solid #6c757d">
                    {{$product->vnd}}
                </td>
                <td style="text-align: right; border: 1px solid #6c757d">
    				{{$product->gia_capnhat}}
    			</td>
    			<td style="text-align: center; border: 1px solid #6c757d; {{ abs($product->gia_capnhat - $product->price_bulk) >= 10000 ? 'background-color: #10ff00;' : '' }}">
    				{{ abs($product->gia_capnhat - $product->price_bulk) >= 10000 ? 'CÓ' : 'KHÔNG' }}
    			</td>
    			<td style="text-align: center; border: 1px solid #6c757d ; {{ $product->gia_capnhat > $product->price_bulk ? 'background-color: #f29141;' : 'background-color: #47f241;'}}">
    				{{ $product->gia_capnhat > $product->price_bulk ? "TĂNG" : "GIẢM" }}
    			</td>

            </tr>
    @endforeach
    
</table>
</body>
</html>