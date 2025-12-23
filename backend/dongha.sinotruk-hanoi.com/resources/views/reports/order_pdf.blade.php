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
		top: 45%;
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
            width: 100%;
            border: 2px solid black;
            background-color: #F8F8FF;
            border-radius: 3px; /* Đặt bán kính bo tròn */
            overflow: hidden; /* Đảm bảo các phần tử con không vượt ra khỏi phần bo tròn */
        }
        
        .table-css {
            border-collapse: collapse;
            border: 1px solid black;
			border-radius: 3px; /* Đặt bán kính bo tròn */
			overflow: hidden; /* Đảm bảo các phần tử con không vượt ra khỏi phần bo tròn */
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
            <h3 style="width: 70%; text-align: center">SINOTRUK HANOI JOINT STOCK COMPANY</h3>
        </td>
    </tr>
</thead>
</table>
<div class="header">
    <b>Address: {{$options[2]}}</b><br>
    <b>Office: {{$options[3]}}</b><br>
    <b>Email: {{$options[4]}}</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Phone: {{$options[5]}}</b><br>
    <b>Website: {{$options[6]}}</b><br>
    <hr>
</div>
<div style="text-align: right">Ha Noi, {{$item->created_at->day}} - {{$item->created_at->month}} - {{$item->created_at->year}}</div>
<div style="text-align: center"><h1>ORDER DETAILS</h1></div>
<table border="1" width="100%" class="table-border">
    <tr>
        <td width="50%">
            <table>
                <tr>
                    <td>
                        Order Name:
                    </td>
                    <td>
                        <b>{{$item->tenphieu}}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        Partner:
                    </td>
                    <td>
                        {{$partner->full_name}}
                    </td>
                </tr>
				<tr>
                    <td>
                        Status:
                    </td>
                    <td>
                        {{$item->completed?'Complete':'Pending'}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Sent Amount:
                    </td>
                    <td>
                        {{number_format($totalItemPrice, 0)}} ¥
                    </td>
                </tr>
                <tr>
                    <td>
                        Received amount:
                    </td>
                    <td>
                        {{number_format($totalItemPriceReality, 0)}} ¥
                    </td>
                </tr>
                <tr>
                    <td>
                        Variance:
                    </td>
                    <td>
                        {{number_format($totalItemPriceReality - $totalItemPrice, 0)}} ¥
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br>
<div>
    <b><u>PACKING LIST:</u></b>
</div>
<br>
<div id="watermark">
    <img src="../img/logo.png" width=30%>
 </div>
<table border="1" width="100%" class="table-css">
    <thead>
        <tr style="text-align: center;border: 1px solid #6c757d;background-color: #ffeeba;">
			<td>
				<b>No</b>
			</td>
			<td>
				<b>Code</b>
			</td>
			<td>
				<b>Name</b>
			</td>
			<td>
				<b>Type</b>
			</td>
			<td>
				<b>Order</b>
			</td>
			<td>
				<b>Sent</b>
			</td>
			<td>
				<b>Received</b>
			</td>
			<td>
				<b>Unit price</b>
			</td>
			<td>
				<b>Ratio</b>
			</td>
			<td>
				<b>Sent amount</b>
			</td>
			<td>
				<b>Received amount</b>
			</td>
			<td>
				<b>Note/Chinese name</b>
			</td>
		@if($hasPermission)
			<td>
				<b>VNĐ</b>
			</td>
			<td>
				<b>GSHT</b>
			</td>
		@endif
    </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        @if ($product->hightlight)
            <tr style="background-color: #ffff00">
        @else
            <tr >
        @endif
					<td style="text-align: center;border: 1px solid #6c757d;">
						{{$loop->index + 1}}
					</td>
					<td style="text-align: left;border: 1px solid #6c757d ;">
						{{$product->mansx}}
					</td>
					<td style="text-align: left;border: 1px solid #6c757d ;">
						{{$product->name}}
					</td>
					<td style="text-align: center;border: 1px solid #6c757d ;">
						{{$product->type}}
					</td>			
					<td style="text-align: center;border: 1px solid #6c757d;">
					{{$product->base_number}}
					</td>
					<td style="text-align: right;border: 1px solid #6c757d ; {{ $product->hscode != $product->base_number ? 'background-color: #00a4ef;' : '' }}">
						{{$product->hscode}}
					</td>
					<td style="text-align: center;border: 1px solid #6c757d; {{ $product->count != $product->hscode ? 'background-color: #00a4ef;' : '' }}">
					{{$product->count}}
					</td>
					<td style="text-align: right;border: 1px solid #6c757d ;">
						{{$product->price}} ¥
					</td>
					<td style="text-align: center;border: 1px solid #6c757d ; {{ $product->nametq != 1 ? 'background-color: #4ad395;' : '' }}" >
						{{$product->nametq}}
					</td>
					<td style="text-align: right;border: 1px solid #6c757d">
						{{number_format($product->total,0)}} ¥ 
					</td>
					<td style="text-align: right;border: 1px solid #6c757d">
						{{number_format($product->totalReality,0)}} ¥
					</td>
					<td style="text-align: left;border: 1px solid #6c757d">
						{{$product->note}}
					</td>
			@if($hasPermission)
					<td style="text-align: right; border: 1px solid #6c757d; {{ $product->vnd > $product->price_bulk ? 'background-color: #fe624e;' : '' }}">
						{{ number_format($product->vnd, 0) }}
					</td>
					<td style="text-align: right; border: 1px solid #6c757d">
						{{ number_format($product->price_bulk, 0) }}
					</td>
			@endif

			</tr>
    @endforeach
        <tr style='background-color: #ffeeba;'>
            <td colspan="9">
                <b>Total</b>
            </td>
            <td style="text-align: right;font-weight: bold;">{{number_format($totalItemPrice, 0)}} ¥</td>
			<td style="text-align: right;font-weight: bold;">{{number_format($totalItemPriceReality, 0)}} ¥</td>
		@if($hasPermission)
			<td style="text-align: right;font-weight: bold;"></td>
			<td style="text-align: right;font-weight: bold;">{{number_format($totalItemVND, 0)}}</td>
			<td style="text-align: right;font-weight: bold;"></td>
		@else
			<td style="text-align: right;font-weight: bold;"></td>
		@endif
        </tr>
		<tr style='background-color: #ffeeba;'>
            <td colspan="9">
                <b>
				Variance (Reality amount - Order amount)
				</b>
            </td>
			<td colspan="2" style="text-align: right;font-weight: bold;">
                <b>{{number_format($totalItemPriceReality - $totalItemPrice, 0)}} ¥</b>
            </td>
		@if($hasPermission)
            <td></td>
			<td></td>
			<td></td>
		@else
			<td></td>
		@endif
        </tr>
    </tbody>
</table>
<br>
<b>WE LOOK FORWARD TO RECEIVING COOPERATION FROM OUR ESTEEMED PARTNERS! </b><br>
<b>THANK YOU VERY MUCH!</b><br><br>
<table width="100%">
	<tr>
        <th style="text-align: center">
            Creator
        </th>
        <th  style="text-align: center">
            Partner
        </th>
	</tr>
	<tr>
		<td style="text-align: center">
		<br><br><br>
		<b>Chien Nguyen Khac</b>
		</td>
		<td style="text-align: center">
		<br><br><br>
		<b>{{$partner->full_name}}</b>
		</td>
	</tr>
</table>
</body>
</html>