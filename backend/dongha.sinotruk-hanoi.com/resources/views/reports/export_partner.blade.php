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
<div style="text-align: right">Ha Noi, {{$export->created_at->day}} - {{$export->created_at->month}} - {{$export->created_at->year}}</div>
<div style="text-align: center"><h1>RECONCILE THE DEBTS OF PURCHASING PARTNERS</h1></div>
<table border="1" width="100%" class="table-border">
    <tr>
        <td width="50%">
            <table>
                <tr>
                    <td>
                        Partner:
                    </td>
                    <td>
                        <b>{{$customer['name']}}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        Representative:
                    </td>
                    <td>
                        {{$customer['person']}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Phone:
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
                        Address:
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
                        Creator:
                    </td>
                    <td>
                        <b>{{$user['full_name']}}</b>
                    </td>
                </tr>
                <tr>
                    <td>
                        Phone:
                    </td>
                    <td>
                        {{$user['phone']}}
                    </td>
                </tr>
                <tr>
                    <td>
                        Position:
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
                        Address:
                    </td>
                    <td>
                        {{$options[11]}}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<b>Dear {{$customer['name']}} Partner</b><br>
<div>We would like to send our Partner a debt reconciliation statement of {{$export->note}}</div>
<b>DETAILED TABLE:</b><br>
<div id="watermark">
    <img src="../img/logo.png" width=50%>
 </div>
<table border="1" width="100%" class="table-css">
    <thead>
        <tr>
            <td style="text-align: center">
                <b>No</b>
            </td>
            <td style="text-align: center">
                <b>Name</b>
            </td>
            <td style="text-align: center">
                <b>Type</b>
            </td>
            <td style="text-align: center">
                <b>Number</b>
            </td>
            <td style="text-align: center">
                <b>Unit Price</b>
            </td>
            <td style="text-align: right">
                <b>Total Amount</b>
            </td>
        </tr>
    </thead>
    <tbody>
        @foreach($export->products as $product)
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
                    {{$product->pivot->count}}
                </td>
                <td style="text-align: right">
                    {{number_format($product->pivot->price, 0)}}
                </td>
                <td style="text-align: right">
                    {{number_format($product->pivot->count * $product->pivot->price, 0)}}
                </td>
		</tr>
        @endforeach
        <tr>
            <td colspan="5">
                <b>Total</b>
            </td>
            <td style="text-align: right;font-weight: bold;">{{number_format($totalItemPrice, 0)}}</td>
        </tr>
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
                <b>Old Debt</b>
            </td>
            <td style="text-align: right;font-weight: bold;">{{number_format($export->debt, 0)}}</td>
        </tr>
        <tr>
            <td colspan="5">
                <b>Total Amount</b>
            </td>
            <td style="background-color: yellow; text-align: right;font-weight: bold;">{{number_format($export->total, 0)}}</td>
        </tr>
    </tbody>
</table>
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
		<br><br><br><br><br><br><br><br><br>
		<b>{{$user['full_name']}}</b>
		</td>
		<td style="text-align: center">
		<br><br><br><br><br><br><br><br><br>
		<b>{{$customer['name']}}</b>
		</td>
	</tr>
</table>
</body>
</body>
</html>