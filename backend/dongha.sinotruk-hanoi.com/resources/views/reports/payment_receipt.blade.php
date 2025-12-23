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
            top: 65%;
            width: 100%;
            text-align: center;
            opacity: .2;
            transform: rotate(-5deg);
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
            background-color: #F8F8FF;
            border: 2px solid black;
            width: 100%;
            border-radius: 3px; /* Đặt bán kính bo tròn */
            overflow: hidden; /* Đảm bảo các phần tử con không vượt ra khỏi phần bo tròn */
        }
        
        .table-css {
            border-collapse: collapse;
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
            background-color: #F0FFF0;
        }
        
        .table-css tr {
            height: auto; /* Đổi giá trị này thành chiều cao mong muốn */
        }
        
        .product-image-column {
            width: 120px; /* Độ rộng cố định cho cột hình ảnh */
        }
    </style>
</head>
<body>
	<table width="100%">
		<thead>
			<tr>
				<td style="width: 30%; text-align: left">
						<img src="../img/seal.png" width="145px" class="seal">
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
	</div>
	<hr>
	<div style="text-align: right">Đông Hà, ngày {{$payPDF->created_at->day}} tháng {{$payPDF->created_at->month}} năm {{$payPDF->created_at->year}}</div>
	<div style="text-align: center"><h1>PHIẾU XÁC NHẬN THANH TOÁN</h1></div>
	<table border="1" width="100%" class="table-border">
    <tr>
        <td width="50%">
            <table>
                <tr>
                    <td>Người nhận:</td>
                    <td><b>{{$customer['name']}}</b></td>
                </tr>
                <tr>
                    <td>Đại diện:</td>
                    <td>{{$customer['person']}}</td>
                </tr>
                <tr>
                    <td>Số điện thoại:</td>
                    <td>{{$customer['phone']}}</td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>{{$customer['email']}}</td>
                </tr>
                <tr>
                    <td>Địa chỉ:</td>
                    <td>{{$customer['address']}}</td>
                </tr>
            </table>
        </td>
        <td>
            <table>
                <tr>
                    <td>Người gửi:</td>
                    <td><b>{{$user['full_name']}}</b></td>
                </tr>
                <tr>
                    <td>Số điện thoại:</td>
                    <td>{{$user['phone']}}</td>
                </tr>
                <tr>
                    <td>Chức vụ:</td>
                    <td>{{$options[9]}}</td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td>{{$options[10]}}</td>
                </tr>
                <tr>
                    <td>Địa chỉ:</td>
                    <td>{{$options[11]}}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>
	<b>Kính gửi Quý khách hàng</b><br>
	<div>Chúng tôi xin gửi đến Quý khách hàng phiếu xác nhận số tiền thanh toán công nợ như sau:</div>
	<b><u>CHI TIẾT THANH TOÁN:</u></b>
	<div id="watermark">
		<img src="../img/logo.png" width=30%>
	</div>
	<br>
	<table border="1" width="100%" class="table-css">
        <thead>
    		<tr style="text-align: center">
    			<th style="width: 200px"><b>Ngày</b></th>
    			<th style="text-align: center"><b>Nội dung</b></th>
    			<th style="width: 150px; text-align: center">Số tiền</th>
    		</tr>
    		
        </thead>
        <tbody>
    		<tr>
    			<td>
    				{{$payPDF->created_at->format('d/m/Y H:i:s')}}
    			</td>
    			<td style="text-align: left">Công nợ trước thanh toán</td>
    			<td style="text-align: right"><b>{{number_format($debt,0)}}</b></td>
    		</tr>
    		<tr style="background-color: #95ff8f;">
    			<td>
    				{{$payPDF->created_at->format('d/m/Y H:i:s')}}
    			</td>
    			<td style="text-align: left">Thanh toán {{$payPDF->content}}</td>
    			<td style="text-align: right"><b>-{{number_format($payPDF->pay,0)}}</b></td>
    		</tr>
    		<tr>
    			<td>
    				{{$payPDF->created_at->format('d/m/Y H:i:s')}}
    			</td>
    			<td style="text-align: left">Công nợ sau thanh toán tính đến {{$payPDF->created_at->format('d/m/Y H:i:s')}}</td>
    			<td style="text-align: right; background-color: yellow;"><b>{{number_format($new_debt,0)}}</b></td>
    		</tr>
        </tbody>
    </table>
<br>
<b><u>QUÝ KHÁCH HÀNG LƯU Ý:</u></b><br>
<div><span style="color: red">1- Phiếu xác nhận được nhân viên kinh doanh gửi tới Quý khách hàng sau khi Quý khách hàng thanh toán công nợ. Nếu không nhận được phiếu này, vui lòng liên hệ Hotline: {{$options[5]}}</span></div>
<div>2- Không phát sinh giao dịch thanh toán công nợ với số tài khoản cá nhân của nhân viên Kinh doanh. Trường hợp phát sinh, Quý khách yêu cầu nhận được phiếu xác nhận thanh toán này.</div>
<b>RẤT MONG NHẬN ĐƯỢC SỰ HỢP TÁC CỦA QUÝ KHÁCH HÀNG!</b>
<br>
<b>TRÂN TRỌNG CẢM ƠN !</b><br><br><br>
<table width="100%">
	<tr>
        <th style="text-align: right" width="70%">
        </th>
        <th  style="text-align: left" width="30%">
          <h3>{{$options[1]}}</h3>
        </th>
	</tr>
</table>
    
</body>
</html>
