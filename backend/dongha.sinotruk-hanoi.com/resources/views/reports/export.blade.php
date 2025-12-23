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
		
		#watermark {
		position: fixed;
		top: 40%;
		width: 100%;
		text-align: center;
		opacity: .2;
		transform: rotate(-3deg);
		transform-origin: 50% 50%;
		z-index: -1000;
		}
		.seal {
        position: absolute;
        top: 0;
        left: 0;
        z-index: 999; /* Đặt mức độ ưu tiên cao để hình ảnh con dấu nằm trên cùng */
        transform: rotate(0deg); /* Nghiêng hình ảnh -10 độ */
        }
        .header {
            z-index: -1000;
        }
        
        .table-border {
            border-collapse: collapse;
            width: 100%;
            border: 2px solid black;
            background-color: #F8F8FF;
            border-radius: 3px; /* Đặt bán kính bo tròn */
            overflow: hidden; /* Đảm bảo các phần tử con không vượt ra khỏi phần bo tròn */
        }
        
        .table-css {
            border-collapse: collapse;
            border: 1px solid black;
            width: 100%;
        }
        
        .table-css th,
        .table-css td {
            border: 1px solid black;
            padding: 5px;
        }
        
        .table-css th {
            font-weight: bold;
            text-align: left;
        }
        
        .table-css tr {
            height: auto; /* Đổi giá trị này thành chiều cao mong muốn */
        }
        
        .table-css tr:nth-child(even) {
            background-color: #F0FFF0;
        }



        

    </style>
</head>
<body>
<table width="100%">
<thead>
    <tr>
        <td style="width: 30%; text-align: left">
		@if($seal)
                <img src="../img/seal.png" width="145px" class="seal">
		@endif
            <img src="../img/logo.png" width="120px">
        </td>
        <td>
            <h3 style="width: 70%; text-align: center">{{$options[1]}}</h3>
        </td>
    </tr>
</thead>
</table>
<div class="header">
    <b>Địa chỉ: {{$options[2]}}</b><br>
    <b>VPGD: {{$options[3]}}</b><br>
    <b>Gmail: {{$options[4]}}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Điện thoại: {{$options[5]}}</b><br>
    <b>Website: {{$options[6]}}</b><br>
    <hr>
</div>
<div style="text-align: right">Đông Hà, ngày {{$export->created_at->day}} tháng {{$export->created_at->month}} năm {{$export->created_at->year}}</div>
<div style="text-align: center"><h1>BIÊN BẢN GIAO NHẬN HÀNG HÓA</h1></div>
<table border="1" width="100%" class="table-border">
    <tr>
        <td width="50%">
            <table>
                <tr>
                    <td>
                        Người nhận:
                    </td>
                    <td>
                        <b>{{$customer['name']}}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        Đại diện:
                    </td>
                    <td>
                        {{$customer['person']}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Điện thoại:
                    </td>
                    <td>
                        {{$customer['phone']}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Email:
                    </td>
                    <td>
                        {{$customer['email']}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Địa chỉ:
                    </td>
                    <td>
                        {{$customer['address']}}
                    </td>
                </tr>
            </table>
        </td>
        <td>
            <table>
                <tr>
                    <td>
                        Người gửi:
                    </td>
                    <td>
                        <b>{{$user['full_name']}}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        Điện thoại:
                    </td>
                    <td>
                        {{$user['phone']}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Chức vụ:
                    </td>
                    <td>
                        {{$options[9]}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Email:
                    </td>
                    <td>
                        {{$options[10]}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Địa chỉ:
                    </td>
                    <td>
                        {{$options[11]}}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<b>Kính gửi Quý khách hàng</b><br>
<div>Chúng tôi xin gửi đến Quý khách hàng thông tin đơn hàng phụ tùng Howo như sau: </div>
<div>
    <i>GHI CHÚ:</i>
    <i style="color: red">{{$note}}</i>
</div>
<b>PHIẾU BÁN HÀNG</b><br>
<div id="watermark">
    <img src="../img/logo.png" width=35%>
 </div>
<table border="1" width="100%" class="table-css">
    <thead>
        <tr>
            <td style="text-align: center">
                <b>STT</b>
            </td>
            <td style="text-align: center">
                <b>Tên hàng</b>
            </td>
            <td style="text-align: center">
                <b>Đơn vị tính</b>
            </td>
            <td style="text-align: center">
                <b>Số lượng</b>
            </td>
            <td style="text-align: center">
                <b>Đơn giá</b>
            </td>
            <td style="text-align: right">
                <b>Thành tiền</b>
            </td>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
		<tr style="text-align: center; background-color: {{ strpos($product->code, 'HIGHLIGHT') !== false ? '#eaff7b' : 'transparent' }}">
                <td>
                    {{$loop->index + 1}}
                </td>
                <td style="text-align: left">
                    {{$product->name}}
                </td>
                <td>
                    {{$product->type}}
                </td>
                <td>
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
        <tr>
            <td colspan="5">
                <b>Tổng</b>
            </td>
            <td style="text-align: right;font-weight: bold;">{{number_format($totalItemPrice, 0)}}</td>
        </tr>
		@if($totalDiscount < 0)
        <tr>
            <td colspan="5"><b>Giảm giá đơn hàng (5%)</b></td>
            <td style="text-align: right"><b>{{ number_format($totalDiscount, 0) }}</b></td>
        </tr>
		@endif
        @if($isVAT)
        <tr>
            <td colspan="5">
                <b>VAT</b>
            </td>
            <td style="text-align: right;font-weight: bold;">{{number_format($vat, 0)}}</td>
        </tr>
        @endif
        <tr>
            <td colspan="5">
                <b>Nợ cũ</b>
            </td>
            <td style="text-align: right;font-weight: bold;">{{number_format($export->debt, 0)}}</td>
        </tr>
        <tr>
            <td colspan="5">
                <b>Tổng cộng</b>
            </td>
            <td style="background-color: yellow; text-align: right;font-weight: bold;">{{number_format($export->total, 0)}}</td>
        </tr>
    </tbody>
</table>
<b>ĐIỀU KIỆN THƯƠNG MẠI</b></a><br>
<div>1- Tình trạng hàng hóa: Hàng có sẵn; mới 100% . Đổi trả 30 ngày nếu phát sinh lỗi do nhà sản xuất.</div>
<div>2- Địa điểm giao hàng: Giao hàng tại kho Bên bán.</div>
<div>3- Hiệu lực báo giá: 30 ngày kể từ ngày báo giá.</div>
<div>4- Hình thức thanh toán: Tiền mặt / Chuyển khoản.</div>
<div><b>5- Quý Khách hàng lưu ý:</b> Trường hợp hàng hóa nhận được không đúng số lượng và chủng loại, Quý khách vui lòng phản ánh lại với công ty trong thời gian 2 ngày kể từ khi nhận được hàng. Hàng đổi trả vui lòng gửi về công ty trong thời gian 1 tuần kể từ khi phát sinh đơn hàng. <b>CHÚNG TÔI KHÔNG NHẬN LẠI CÁC SẢN PHẨM KHÔNG CÓ BAO BÌ CÔNG TY HOẶC BAO BÌ KHÔNG CÒN NGUYÊN VẸN</b>. Sau thời gian kể trên, công ty không giải quyết bất kỳ thắc mắc nào liên quan đến đơn hàng.<a href="https://sinotruk-hanoi.com/dich-vu-hau-mai/chinh-sach-bao-hanh/" target="_blank" style="text-decoration:none"> Xem thêm chính sách bảo hạnh tại https://khangthinhphat.com</a></div>
<b>RẤT MONG NHẬN ĐƯỢC SỰ HỢP TÁC CỦA QUÝ KHÁCH HÀNG!</b><br>
<b>TRÂN TRỌNG CẢM ƠN !</b><br><br>
<table width="100%">
	<tr>
        <th style="text-align: center">
            Người lập phiếu
        </th>
        <th  style="text-align: center">
            Xác nhận bên mua
        </th>
	</tr>
	<tr>
		<td style="text-align: center">
		<br><br><br>
		<b>{{$user['full_name']}}</b>
		</td>
		<td style="text-align: center">
		<br><br><br>
		<b>{{$customer['name']}}</b>
		</td>
	</tr>
</table>
</body>
</body>
</html>