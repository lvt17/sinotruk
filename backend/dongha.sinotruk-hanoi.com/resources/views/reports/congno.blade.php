<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
<table>
    <tr>
        <td colspan="10"><b>{{$options[1]}}</b></td>
    </tr>
    <tr>
        <td>
			<b>Địa chỉ:</b>
		</td>
		<td colspan="9">
			<b>{{$options[2]}}</b>	
		</td>
    </tr>
    <tr>
        <td>
			<b>EMAIL:</b>
            
		</td>
		<td colspan="9">
			<b>{{$options[4]}}</b>
        </td>
    </tr>
	<tr>
        <td>
			<b>HOTLINE:</b>
            
		</td>
		<td colspan="9">
			<b>{{$options[5]}}</b>
        </td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr>
        <td colspan="12" style="text-align: center">
            <h2><b>ĐỐI SOÁT CÔNG NỢ
            </b></h2>
        </td>
    </tr>
    <tr>
        <td colspan="12" style="text-align: center">
            (Từ ngày {{$fromDate->day}} tháng {{$fromDate->month}} năm {{$fromDate->year}} đến ngày {{$toDate->day}} tháng {{$toDate->month}} năm {{$toDate->year}})
        </td>
    </tr>
    <tr></tr>
    <tr>
        <td colspan="12">
            <b>Kính gửi:</b>
        </td>
    </tr>
    <tr>
        <td style="border: 1px solid #6c757d">
            <b>Khách hàng:</b>
        </td>
        <td colspan="4" style="border: 1px solid #6c757d">
            <b>{{$customer->name}}</b>
        </td>
        <td colspan="3" style="border: 1px solid #6c757d">
            <b>Kỳ thứ:</b>
        </td>
        <td colspan="4" style="border: 1px solid #6c757d">

        </td>
    </tr>
    <tr>
        <td style="border: 1px solid #6c757d">
            <b>Địa chỉ:
                </b>
        </td>
        <td colspan="4" style="border: 1px solid #6c757d">
            {{$customer->address}}
        </td>
        <td colspan="3" style="border: 1px solid #6c757d">
            <b>Năm:
            </b>
        </td>
        <td colspan="4" style="border: 1px solid #6c757d">
            <b>{{$fromDate->year}}</b>
        </td>
    </tr>
    <tr>
        <td style="border: 1px solid #6c757d">
            <b>Mã số thuế:
            </b>
        </td>
        <td colspan="4" style="border: 1px solid #6c757d"></td>
        <td colspan="3" style="border: 1px solid #6c757d">
            <b>Số dư cuối kỳ:
            </b>
        </td>
        <td colspan="4" style="background-color: #ffb900 ; border: 1px solid #6c757d">
            <b>{{number_format($du_cuoi_ky, 0)}}</b>
        </td>
    </tr>
    <tr>
        <td style="border: 1px solid #6c757d">
            <b>Đại diện:
            </b>
        </td>
        <td colspan="4" style="border: 1px solid #6c757d">
            {{$customer->person}}
        </td>
        <td colspan="3" style="border: 1px solid #6c757d">
            <b>Số dư đầu kỳ:
            </b>
        </td>
        <td colspan="4" style="background-color: #a7ff48; border: 1px solid #6c757d">
            <b>{{number_format($du_dau_ky, 0)}}</b>
        </td>
    </tr>
    <tr>
        <td style="border: 1px solid #6c757d">
            <b>SĐT:
            </b>
        </td>
        <td colspan="4" style="border: 1px solid #6c757d">
            {{$customer->phone}}
        </td>
        <td colspan="3" style="border: 1px solid #6c757d">
        </td>
        <td colspan="4" style="border: 1px solid #6c757d">

        </td>
    </tr>
    <tr></tr>
    <tr>
        <td colspan="10">
            <i>
                Chúng tôi xin gửi đến Quý khách hàng bảng đối soát công nợ từ ngày {{$fromDate->day}} tháng {{$fromDate->month}} năm {{$fromDate->year}} đến ngày {{$toDate->day}} tháng {{$toDate->month}} năm {{$toDate->year}} như sau:
            </i>
        </td>
    </tr>
    <tr>
        <td colspan="10">
            <i>
                Lưu ý: Đơn giá chưa bao gồm thuế GTTT 10%
            </i>
        </td>
    </tr>
    <tr >
        <td style="border: 1px solid #6c757d"><b>Ngày
            </b></td>
        <td style="border: 1px solid #6c757d"><b>STT
            </b></td>
        <td style="border: 1px solid #6c757d"><b>Nội dung

            </b></td>
        <td style="border: 1px solid #6c757d"><b>Mã SP

            </b></td>
        <td style="border: 1px solid #6c757d"><b>ĐVT

            </b></td>
        <td style="border: 1px solid #6c757d"><b>Số lượng

            </b></td>
        <td style="border: 1px solid #6c757d"><b>Đơn giá

            </b></td>
        <td style="border: 1px solid #6c757d"><b>Thành tiền

            </b></td>
        <td style="border: 1px solid #6c757d"><b>Tổng

            </b></td>
        <td style="border: 1px solid #6c757d"><b>VAT

            </b></td>
        <td style="border: 1px solid #6c757d"><b>Số dư

            </b></td>
        <td style="border: 1px solid #6c757d"><b>Ghi chú

            </b></td>
    </tr>
    <tr>
        <td style="border: 1px solid #6c757d">
            {{$fromDate->format('d/m/Y H:i:s')}}
        </td>
        <td style="border: 1px solid #6c757d"><b>0</b></td>
        <td style="border: 1px solid #6c757d" colspan="8"><b>Dư đầu kỳ</b></td>
        <td style="text-align: right;border: 1px solid #6c757d"><b>{{number_format($du_dau_ky, 0)}}</b></td>
        <td style="text-align: right;border: 1px solid #6c757d"><b></b></td>
    </tr>
    @foreach($items as $item)
        @if ($item['details'] != '')
            @foreach($item['details'] as $detail)
                <tr style="{{ $item['loai'] == 'import' ? 'background-color: #a7ff48;' : '' }}">
                    @if ($loop->index == 0)
                        <td style="text-align: left; vertical-align: middle;border: 1px solid #6c757d" rowspan="{{count($item['details'])}}">{{$item['ngay']->format('d/m/Y H:i:s')}}</td>
                        <td style="text-align: left;border: 1px solid #6c757d">{{$loop->index + 1}}</td>
                    @else
                        <td></td>
                        <td  style="text-align: left;border: 1px solid #6c757d">{{$loop->index + 1}}</td>
                    @endif
                    <td style="text-align: left;border: 1px solid #6c757d">{{$detail->name}}</td>
                    <td style="text-align: left;border: 1px solid #6c757d">{{$detail->code}}</td>
                    <td style="text-align: center;border: 1px solid #6c757d">{{$detail->type}}</td>
                    <td style="text-align: center;border: 1px solid #6c757d">{{number_format($detail->pivot->count, 0)}}</td>
                    <td style="text-align: right;border: 1px solid #6c757d">{{number_format($detail->pivot->price, 0)}}</td>
                    <td style="text-align: right;border: 1px solid #6c757d">{{number_format($detail->pivot->count * $detail->pivot->price, 0)}}</td>
                    @if ($loop->index == 0)
                        <td rowspan="{{count($item['details'])}}" style="text-align: right; vertical-align: middle;border: 1px solid #6c757d">{{number_format($item['tong'], 0)}}</td>
                        <td rowspan="{{count($item['details'])}}" style="text-align: right; vertical-align: middle;border: 1px solid #6c757d">{{number_format($item['vat'], 0)}}</td>
                        <td rowspan="{{count($item['details'])}}" style="text-align: right; vertical-align: middle;border: 1px solid #6c757d"><b>{{number_format($item['sodu'], 0)}}</b></td>
                        <td rowspan="{{count($item['details'])}}" style="text-align: left; vertical-align: middle;border: 1px solid #6c757d">{{$item['note']}}</td>
                    @endif
                </tr>
            @endforeach
        @else
				<tr style="background-color: #a7ff48;">
					<td  style="text-align: left;border: 1px solid #6c757d">{{$item['ngay']->format('d/m/Y H:i:s')}}</td>
					<td  style="text-align: left;border: 1px solid #6c757d">1</td>
					<td colspan="6" style="text-align: left;border: 1px solid #6c757d">{{$item['noidung']}}</td>
					<td style="text-align: right;border: 1px solid #6c757d">{{number_format($item['tong'], 0)}}</td>
					<td style="text-align: right;border: 1px solid #6c757d">{{number_format($item['vat'], 0)}}</td>
					<td style="text-align: right;border: 1px solid #6c757d"><b>{{number_format($item['sodu'], 0)}}</b></td>
					<td style="text-align: right;border: 1px solid #6c757d"></td>
				</tr>
        @endif
    @endforeach
    <tr></tr>
    <tr>
        <td colspan="10">
            <b>RẤT MONG NHẬN ĐƯỢC SỰ HỢP TÁC TỪ QUÝ KHÁCH HÀNG!
            </b>
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <b>TRÂN TRỌNG CẢM ƠN!
            </b>
        </td>
        <td colspan="4" style="text-align: right">
            Đông Hà, ngày {{Carbon\Carbon::now()->day}} tháng {{Carbon\Carbon::now()->month}} năm {{Carbon\Carbon::now()->year}}
        </td>
    </tr>
    <tr>
        <td colspan="6"></td>
        <td colspan="4">
            <b>Người lập
            </b>
        </td>
    </tr>
</table>
</body>
</html>