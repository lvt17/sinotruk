<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/themify-icons.css') }}">
</head>

<body>

    <input type="checkbox" id="sidebar-toggle">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3 class="brand">
                <span class="ti-unlink"></span>
                <span>KHANG THỊNH PHÁT</span>
            </h3>
            <label for="sidebar-toggle" class="ti-menu-alt"></label>
        </div>

        <div class="sidebar-menu">
            <ul>
                <li>
                    <a href="">
                        <span class="ti-home"></span>
                        <a href="https://noibo.sinotruk-hanoi.com/#">Bán hàng</a>
                    </a>
                </li>
                <li>
                    <a href="">
                        <span class="ti-face-smile"></span>
                        <a href="https://noibo.sinotruk-hanoi.com/#/nhapkho">Nhập kho</a>
                    </a>
                </li>
                <li>
                    <a href="">
                        <span class="ti-agenda"></span>
                        <a href="https://noibo.sinotruk-hanoi.com/#/baogia">Báo giá</a>
                    </a>
                </li>
                <li>
                    <a href="">
                        <span class="ti-clipboard"></span>
                        <a href="https://noibo.sinotruk-hanoi.com/#/order">Order</a>
                    </a>
                </li>
                <li>
                    <a href="">
                        <span class="ti-folder"></span>
                        <a href="https://noibo.sinotruk-hanoi.com/#/dashboard">Dashboard</a>
                    </a>
                </li>
                <li>
                    <a href="">
                        <span class="ti-time"></span>
                        <a href="https://noibo.sinotruk-hanoi.com/#/thongke">Thống kê</a>
                    </a>
                </li>
                <li>
                    <a href="">
                        <span class="ti-book"></span>
                        <a href="https://noibo.sinotruk-hanoi.com/#/sanpham">Sản phẩm</a>
                    </a>
                </li>
                <li>
                    <a href="">
                        <span class="ti-settings"></span>
                        <a href="https://noibo.sinotruk-hanoi.com/#/category">Category</a>
                    </a>
                </li>
                <li>
                    <a href="">
                        <span class="ti-settings"></span>
                        <a href="https://noibo.sinotruk-hanoi.com/#/khachhang">Khách hàng</a>
                    </a>
                </li>
                <li>
                    <a href="">
                        <span class="ti-settings"></span>
                        <a href="https://noibo.sinotruk-hanoi.com/#/thanhtoan">Thanh toán</a>
                    </a>
                </li>
                <li>
                    <a href="">
                        <span class="ti-settings"></span>
                        <a href="https://noibo.sinotruk-hanoi.com/#/congno">Công nợ</a>
                    </a>
                </li>
            </ul>
        </div>
    </div>


    <div class="main-content">

        <header>
            <div>
                <a href="http://noibo.sinotruk-hanoi.com/dashboard">Trang chủ / Dashboard</a>
            </div>
            <div>
                <span>Xin chào {{$user->full_name}}!</span>
            </div>
        </header>

        <main>
            <div class="dash-cards">
                <div class="card-single" style="background-color: #1E90FF;">
                    <div class="card-body">
                        <span class="ti-briefcase"></span>
                        <div>
                            <h5>Bán hàng hôm nay</h5>
                            <h4>{{ number_format($totalMoneyOrders, 0, '.', ',') }}</h4>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="">Chi tiết</a>
                    </div>
                </div>

                <div class="card-single" style="background-color: #07ff40;">
                    <div class="card-body">
                        <span class="ti-briefcase"></span>
                        <div>
                            <h5>Thanh toán hôm nay</h5>
                            <h4>{{number_format($totalCustomerPayments, 0, '.', ',') }}</h4>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="">Chi tiết</a>
                    </div>
                </div>

                <div class="card-single" style="background-color: #ffc107;">
                    <div class="card-body">
                        <span class="ti-briefcase"></span>
                        <div>
                            <h5>Chi phí hôm nay</h5>
                            <h4>{{ number_format($totalCosts, 0, '.', ',') }}</h4>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="">Chi tiết</a>
                    </div>
                </div>

                <div class="card-single" style="background-color: #FF0000">
                    <div class="card-body">
                        <span class="ti-reload"></span>
                        <div>
                            <h5>Công nợ hôm nay</h5>
                            <h4>{{ number_format($totalDebts, 0, '.', ',') }}</h4>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="">Chi tiết</a>
                    </div>
                </div>

                <div class="card-single" style="background-color:#f4d8c24d">
                    <div class="card-body">
                        <span class="ti-check-box"></span>
                        <div>
                            <h1>{{$totalOrders}}</h1>
                            <h5>Đơn hàng hôm nay</h5>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="">Chi tiết</a>
                    </div>
                </div>
                <div class="card-single" style="background-color:#f4d8c24d">
                    <div class="card-body">
                        <span class="ti-briefcase"></span>
                        <div>
                            <h1>{{$totalImports}}</h1>
                            <h5>Phiếu nhập hàng hôm nay</h5>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="">Chi tiết</a>
                    </div>
                </div>
                <div class="card-single" style="background-color:#f4d8c24d">
                    <div class="card-body">
                        <span class="ti-briefcase"></span>
                        <div>
                            <h1>{{$totalNewProducts}}</h1>
                            <h5>Sản phẩm mới</h5>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="">Chi tiết</a>
                    </div>
                </div>
                <div class="card-single" style="background-color:#f4d8c24d">
                    <div class="card-body">
                        <span class="ti-briefcase"></span>
                        <div>
                            <h1>{{$totalNewCustomers}}</h1>
                            <h5>Khách hàng mới</h5>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="">Chi tiết</a>
                    </div>
                </div>
            </div>
            <br>
            <div class="dash-cards-chart">
                <div class="card-single">
                    {!! $export_payment_chart->renderHtml() !!}
                </div>
                <div class="card-single">
                    {!! $payment_chart->renderHtml() !!}
                </div>
                <div class="card-single">
                    {!! $order_chart->renderHtml() !!}
                </div>
                <div class="card-single">
                    {!! $debt_chart->renderHtml() !!}
                </div>
            </div>


        </main>

    </div>

</body>
{!! $export_payment_chart->renderChartJsLibrary() !!}
{!! $export_payment_chart->renderJs() !!}
{!! $payment_chart->renderChartJsLibrary() !!}
{!! $payment_chart->renderJs() !!}
{!! $order_chart->renderChartJsLibrary() !!}
{!! $order_chart->renderJs() !!}
{!! $debt_chart->renderChartJsLibrary() !!}
{!! $debt_chart->renderJs() !!}

</html>