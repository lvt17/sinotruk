<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<body>
<table border="1" >
    <tr style="text-align: center">
        <th>
            <b>ORDER NAME:</b>
        </th>
        <th>
            {{$tenphieu}}
        </th>
    </tr>
    
    <tr></tr>
    <tr style="text-align: center;border: 1px solid #6c757d;background-color: #ffeeba;">
        <td width="8">
            <b>No</b>
        </td>
        <td width="15">
            <b>Product code</b>
        </td>
        <td width="15">
            <b>Original code</b>
        </td>
        <td width="30">
            <b>Product name</b>
        </td>
        <td width="8">
            <b>Type</b>
        </td>
        <td width="15">
            <b>Order</b>
        </td>
        <td width="10">
            <b>Sent</b>
        </td>
		<td width="10">
            <b>Received</b>
        </td>
        <td width="15">
            <b>Unit price (cny)</b>
        </td>
		@if($hasPermission)
		<td width="15">
			<b>Best price (cny)</b>
		</td>
		<td width="20">
			<b>Order best price (cny)</b>
		</td>
		@endif
		<td width="10">
            <b>Ratio(%)</b>
        </td>
        <td width="20">
            <b>Sent amount (cny)</b>
        </td>
		<td width="20">
            <b>Received amount (cny)</b>
        </td>
        <td width="25">
            <b>Note/Chinese name</b>
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
					{{$product->mansx}}
				</td>
				<td style="text-align: left;border: 1px solid #6c757d">
					{{$product->name}}
				</td>
				<td style="text-align: center;border: 1px solid #6c757d">
					{{$product->type}}
				</td>		
				
				<td style="text-align: center; border: 1px solid #6c757d;">
					{{ $product->base_number }}
				</td>
				<td style="text-align: center;border: 1px solid #6c757d; {{ $product->hscode != $product->base_number ? 'background-color: #00a4ef;' : '' }}">
					{{ $product->hscode }}
				</td>
				<td style="text-align: center; border: 1px solid #6c757d;{{ $product->count != $product->hscode ? 'background-color: #00a4ef;' : '' }}">
					{{$product->count}}
				</td>
				<td style="text-align: right;border: 1px solid #6c757d">
					{{ $product->price }}
				</td>
			@if($hasPermission)
				<td style="text-align: right;border: 1px solid #6c757d ; {{ $product->price > $product->lowestPrice ? 'background-color: #00a4ef;' : '' }}">
					{{$product->lowestPrice}}
				</td>
				<td style="text-align: left;border: 1px solid #6c757d">
					{{$product->lowestPriceOrderName}}
				</td>
		    @endif
		    
           	@if ($product->nametq == 1)
				<td style="text-align: center;border: 1px solid #6c757d" >
					{{$product->nametq}}
				</td>
			@else
				<td style="text-align: center;border: 1px solid #6c757d; background-color: #4ad395">
                {{$product->nametq}}
				</td>
		    @endif
		    
				<td style="text-align: right;border: 1px solid #6c757d">
					{{$product->total}}
				</td>
				<td style="text-align: right;border: 1px solid #6c757d">
					{{$product->totalReality}}
				</td>
				<td style="text-align: left;border: 1px solid #6c757d">
					{{$product->note}}
				</td>
				{{--<td style="text-align: center;border: 1px solid #6c757d">--}}
					{{--{{$product->vnd}}--}}
				{{--</td>--}}
				{{--<td style="text-align: center;border: 1px solid #6c757d">--}}
					{{--{{$product->price_bulk}}--}}
				{{--</td>--}}
        </tr>
    @endforeach
		<tr style="text-align: left"; background-color: #ffeeba>
			@if(auth()->check() && auth()->user()->admin)
				<td colspan="12" style="text-align: left;border: 1px solid #6c757d";>
					<b>TOTAL PRICE (CNY):</b>
				</td>
			@else
				<td colspan="10" style="text-align: left;border: 1px solid #6c757d";>
					<b>TOTAL PRICE (CNY):</b>
				</td>
			@endif
				<td  style="text-align: right;border: 1px solid #6c757d;background-color: #ffff00; background-color: #ffeeba">
					{{$totalItemPrice}} (CNY)
				</td>
				<td  style="text-align: right;border: 1px solid #6c757d;background-color: #ffff00; background-color: #ffeeba">
					{{$totalItemPriceReality}} (CNY)
				</td>
				<td style="text-align: right;border: 1px solid #6c757d">
				</td>
		</tr>
</table>
</body>
</html>