<!DOCTYPE html>
<html>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style>
		body {
			font-family: DejaVu Sans;
			font-size: 28px;
			}
		#watermark {
    		position: fixed;
    		top: 20%;
    		width: 100%;
    		text-align: center;
    		opacity: .2;
    		transform: rotate(-5deg);
    		transform-origin: 50% 50%;
    		z-index: -1000;
    		}
    	.table-border {
            border-collapse: collapse;
            border: 2px solid black;
            width: 100%;
            border-radius: 4px; /* Đặt bán kính bo tròn */
            overflow: hidden; /* Đảm bảo các phần tử con không vượt ra khỏi phần bo tròn */
        }
	</style>
        <table border="1" width="100%" class="table-border">
            <tr>
                <td width="50%">
                    <table>
                        <tr>
                            <head>
                                
            				</head>
            				<body>
            					<div style="text-align: center">
            						<b><i>Người gửi: {{$user['full_name']}}</i></b>
            						<br>
            						 <i>ĐT: {{$user['phone']}}</i>
            						<br>
            						 <i>Địa chỉ: {{$options[11]}}</i>
            						<br>
            						<br>
            						<b><i>Người nhận:  {{$customer['name']}}</i></b>  
            						<br>
            						 <i>ĐT: {{$customer['phone']}}</i>
            						<br>
            						 <i>Địa chỉ: {{$customer['address']}}</i>
            					</div>
            				</body>
        				</tr>
        			</table>
        		</td>
        	</tr>
        </table>
</html>