<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<table>
    <tr>
        <td><b>SINOTRUK HANOI JOINT STOCK COMPANY</b></td>
    </tr>
	<tr>
        <td>
            <h2><b>RECONCILE THE DEBTS OF PURCHASING PARTNERS</b></h2>
        </td>
    </tr>
    <tr></tr>
    <tr>
        <td>
            <b>Dear:</b>
        </td>
    </tr>
    <tr>
        <td>
            <b>Partner:</b>
        </td>
        <td style="text-align: left">
            <b>{{$customer->name}}</b>
        </td>
    </tr>
    <tr>
        <td>
            <b>Address:
                </b>
        </td>
        <td style="text-align: left">
            {{$customer->address}}
        </td>
    </tr>
	<tr>
        <td>
            <b>Opening balance:
            </b>
        </td>
        <td style="background-color: #bbcb4f; text-align: left">
            <b>{{number_format($du_dau_ky, 0)}}</b>
        </td>
    </tr>
    <tr>
        <td>
            <b>Ending balance:</b>
        </td>
        <td style="text-align: left ; background-color: #ffeeba">
            <b>{{number_format($du_cuoi_ky, 0)}}</b>
        </td>
    </tr>
    <tr></tr>
    <tr>
        <td>
            <i>
                We would like to send our customers debt reconciliation statement from {{$fromDate->month}}-{{$fromDate->day}}-{{$fromDate->year}} to {{$toDate->month}}-{{$toDate->day}}-{{$toDate->year}} as follows:
            </i>
        </td>
    </tr>
    <tr></tr>
    <tr style="text-align: center;border: 1px solid #6c757d;background-color: #ffeeba">
        <td style="border: 1px solid #6c757d" width="20"><b>DATE
            </b></td>
        <td style="border: 1px solid #6c757d" width="10"><b>NO
            </b></td>
        <td style="border: 1px solid #6c757d" width="60"><b>ITEMS
            </b></td>
        <td style="border: 1px solid #6c757d" width="8"><b>TYPE

            </b></td>
        <td style="border: 1px solid #6c757d" width="8"><b>NUMBER

            </b></td>
        <td style="border: 1px solid #6c757d" width="15"><b>UNIT PRICE

            </b></td>
        <td style="border: 1px solid #6c757d" width="15"><b>TOTAL ITEMS

            </b></td>
        <td style="border: 1px solid #6c757d" width="15"><b>TOTAL AMOUNT

            </b></td>
        <td style="border: 1px solid #6c757d" width="15"><b>BALANCE

            </b></td>
        <td style="border: 1px solid #6c757d" width="30"><b>EXPLANATION

            </b></td>
    </tr>
    <tr>
        <td style="border: 1px solid #6c757d">
            {{$fromDate->format('d/m/Y H:i:s')}}
        </td>
        <td style="text-align: center;border: 1px solid #6c757d"><b>0</b></td>
        <td style="border: 1px solid #6c757d" colspan="6"><b>Opening balance</b></td>
        <td style="text-align: right;background-color: #bbcb4f;border: 1px solid #6c757d"><b>{{number_format($du_dau_ky, 0)}}</b></td>
        <td style="text-align: right;border: 1px solid #6c757d"><b></b></td>
    </tr>
    @foreach($items as $item)
        @if ($item['details'] != '')
            @foreach($item['details'] as $detail)
                <tr>
                    @if ($loop->index == 0)
                        <td  style="text-align: left; vertical-align: middle;border: 1px solid #6c757d" rowspan="{{count($item['details'])}}">{{$item['ngay']->format('d/m/Y H:i:s')}}</td>
                        <td style="text-align: center;border: 1px solid #6c757d">{{$loop->index + 1}}</td>
                    @else
                        <td></td>
                        <td  style="text-align: center;border: 1px solid #6c757d">{{$loop->index + 1}}</td>
                    @endif
                    <td style="text-align: left;border: 1px solid #6c757d">{{$detail->name}}</td>
                    <td style="text-align: center;border: 1px solid #6c757d">{{$detail->type}}</td>
                    <td style="text-align: center;border: 1px solid #6c757d">{{number_format($detail->pivot->count, 0)}}</td>
                    <td style="text-align: right;border: 1px solid #6c757d">{{number_format($detail->pivot->price, 0)}}</td>
                    <td style="text-align: right;border: 1px solid #6c757d">{{number_format($detail->pivot->count * $detail->pivot->price, 0)}}</td>
                    @if ($loop->index == 0)
                        <td rowspan="{{count($item['details'])}}" style="text-align: right; vertical-align: middle;border: 1px solid #6c757d">{{number_format($item['tong'], 0)}}</td>
                        <td rowspan="{{count($item['details'])}}" style="text-align: right; vertical-align: middle;border: 1px solid #6c757d"><b>{{number_format($item['sodu'], 0)}}</b></td>
                        <td rowspan="{{count($item['details'])}}" style="text-align: left; vertical-align: middle;border: 1px solid #6c757d">{{$item['note']}}</td>
                    @endif
                </tr>
            @endforeach
        @else
            <tr>
                <td  style="text-align: left;border: 1px solid #6c757d">{{$item['ngay']->format('d/m/Y H:i:s')}}</td>
                <td  style="text-align: left;border: 1px solid #6c757d">1</td>
                <td colspan="6" style="text-align: left;border: 1px solid #6c757d">{{$item['noidung']}}</td>
                <td style="text-align: right;border: 1px solid #6c757d">{{number_format($item['tong'], 0)}}</td>
                <td style="text-align: right;border: 1px solid #6c757d">{{number_format($item['vat'], 0)}}</td>
                <td style="text-align: right;border: 1px solid #6c757d"><b>{{number_format($item['sodu'], 0)}}</b></td>
                <td style="text-align: right;border: 1px solid #6c757d"><b></b></td>
            </tr>
        @endif
    @endforeach
    <tr></tr>
    <tr>
        <td colspan="10">
            <b>WE LOOK FORWARD TO RECEIVING COOPERATION FROM OUR ESTEEMED PARTNERS!</b>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <b>THANK YOU VERY MUCH!</b>
        </td>
        <td colspan="4" style="text-align: right">
            Ha Noi, {{Carbon\Carbon::now()->month}}- {{Carbon\Carbon::now()->day}}- {{Carbon\Carbon::now()->year}}
        </td>
    </tr>
    <tr>
        <td colspan="6"></td>
        <td colspan="4">
            <b>Creator
            </b>
        </td>
    </tr>
</table>
</body>
</html>