<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderStatus;
use App\Models\OrderItemStatus;
use App\Models\OrdersLog;
use App\Models\OrdersProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;

class OrderController extends Controller
{
    public function orders()
    {
        Session::put('page', 'orders');
        $adminType = Auth::guard('admin')->user()->type;
        $vendor_id = Auth::guard('admin')->user()->vendor_id;
        if ($adminType == "vendor") {
            $vendorStatus = Auth::guard('admin')->user()->status;
            if ($vendorStatus == 0) {
                return redirect("admin/update-vendor-details/personal")->with('error_message', 'Tài khoản của bạn chưa được cấp phép, vui lòng liên hệ quản trị viên');
            }
        }
        if ($adminType == "vendor") {
            $orders = Order::with(['orders_products' => function ($query) use ($vendor_id) {
                $query->where('vendor_id', $vendor_id);
            }])->orderBy('id', 'Desc')->get()->toArray();
        } else {
            $orders = Order::with('orders_products')->orderBy('id', 'Desc')->get()->toArray();
        }

        // dd($orders);
        return view('admin.orders.orders')->with(compact('orders'));
    }

    public function orderDetails($id)
    {
        Session::put('page', 'orders');
        $adminType = Auth::guard('admin')->user()->type;
        $vendor_id = Auth::guard('admin')->user()->vendor_id;
        if ($adminType == "vendor") {
            $vendorStatus = Auth::guard('admin')->user()->status;
            if ($vendorStatus == 0) {
                return redirect("admin/update-vendor-details/personal")->with('error_message', 'Tài khoản của bạn chưa được cấp phép, vui lòng liên hệ quản trị viên');
            }
        }

        if ($adminType == "vendor") {
            $orderDetails = Order::with(['orders_products' => function ($query) use ($vendor_id) {
                $query->where('vendor_id', $vendor_id);
            }])->where('id', $id)->first()->toArray();
        } else {
            $orderDetails = Order::with('orders_products')->where('id', $id)->first()->toArray();
        }

        $userDetails = User::where('id', $orderDetails['user_id'])->first()->toArray();
        $orderStatuses = OrderStatus::where('status', 1)->get()->toArray();
        $orderItemStatuses = OrderItemStatus::where('status', 1)->get()->toArray();
        $orderLog = OrdersLog::with('orders_products')->where('order_id', $id)->orderBy('id', 'Desc')->get()->toArray();
        return view('admin.orders.order_details')->with(compact('orderDetails', 'userDetails', 'orderStatuses', 'orderItemStatuses', 'orderLog'));
    }
    public function updateOrderStatus(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            //update status
            Order::where('id', $data['order_id'])->update(['order_status' => $data['order_status']]);
            //update dvvc 
            if (!empty($data['courier_name']) && !empty($data['tracking_number'])) {
                Order::where('id', $data['order_id'])->update(['courier_name' => $data['courier_name'], 'tracking_number' => $data['tracking_number']]);
            }
            //update log
            $log = new OrdersLog();
            $log->order_id = $data['order_id'];
            $log->order_status = $data['order_status'];
            $log->save();

            $message = "Cập nhật trạng thái đơn hàng thành công !";
            return redirect()->back()->with('success_message', $message);
        }
    }

    public function updateOrderItemStatus(Request $request)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            //update status
            OrdersProduct::where('id', $data['order_item_id'])->update(['item_status' => $data['item_status']]);
            //update dvvc 
            if (!empty($data['item_courier_name']) && !empty($data['item_tracking_number'])) {
                OrdersProduct::where('id', $data['order_item_id'])->update(['courier_name' => $data['item_courier_name'], 'tracking_number' => $data['item_tracking_number']]);
            }
            $getOrderId = OrdersProduct::select('order_id')->where('id', $data['order_item_id'])->first()->toArray();
            //update log
            $log = new OrdersLog();
            $log->order_id = $getOrderId['order_id'];
            $log->order_item_id = $data['order_item_id'];
            $log->order_status = $data['item_status'];
            $log->save();

            $message = "Cập nhật trạng thái sản phẩm thành công !";
            return redirect()->back()->with('success_message', $message);
        }
    }
    public function viewOrderInvoice($order_id)
    {
        $orderDetails = Order::with('orders_products')->where('id', $order_id)->first()->toArray();
        $userDetails = User::where('id', $orderDetails['user_id'])->first()->toArray();
        return view('admin.orders.order_invoice')->with(compact('orderDetails', 'userDetails'));
    }
    public function printPDFInvoice($order_id)
    {
        $orderDetails = Order::with('orders_products')->where('id', $order_id)->first()->toArray();
        $userDetails = User::where('id', $orderDetails['user_id'])->first()->toArray();
        $invoiceHTML = '<!DOCTYPE html>
        <html>
        <head>
            <title>Hóa đơn thanh toán</title>
            <meta content="width=device-width, initial-scale=1.0" name="viewport">
            <meta http-equiv="content-type" content="text-html; charset=utf-8">
            <style type="text/css">
                html, body, div, span, applet, object, iframe,
                h1, h2, h3, h4, h5, h6, p, blockquote, pre,
                a, abbr, acronym, address, big, cite, code,
                del, dfn, em, img, ins, kbd, q, s, samp,
                small, strike, strong, sub, sup, tt, var,
                b, u, i, center,
                dl, dt, dd, ol, ul, li,
                fieldset, form, label, legend,
                table, caption, tbody, tfoot, thead, tr, th, td,
                article, aside, canvas, details, embed,
                figure, figcaption, footer, header, hgroup,
                menu, nav, output, ruby, section, summary,
                time, mark, audio, video {
                    margin: 0;
                    padding: 0;
                    border: 0;
                    font: inherit;
                    font-size: 100%;
                    vertical-align: baseline;
                }
        
                html {
                    line-height: 1;
                }
        
                ol, ul {
                    list-style: none;
                }
        
                table {
                    border-collapse: collapse;
                    border-spacing: 0;
                }
        
                caption, th, td {
                    text-align: left;
                    font-weight: normal;
                    vertical-align: middle;
                }
        
                q, blockquote {
                    quotes: none;
                }
                q:before, q:after, blockquote:before, blockquote:after {
                    content: "";
                    content: none;
                }
        
                a img {
                    border: none;
                }
        
                article, aside, details, figcaption, figure, footer, header, hgroup, main, menu, nav, section, summary {
                    display: block;
                }
        
                body {
                    font-family: "Source Sans Pro", sans-serif;
                    font-weight: 300;
                    font-size: 12px;
                    margin: 0;
                    padding: 0;
                }
                body a {
                    text-decoration: none;
                    color: inherit;
                }
                body a:hover {
                    color: inherit;
                    opacity: 0.7;
                }
                body .container {
                    min-width: 500px;
                    margin: 0 auto;
                    padding: 0 20px;
                }
                body .clearfix:after {
                    content: "";
                    display: table;
                    clear: both;
                }
                body .left {
                    float: left;
                }
                body .right {
                    float: right;
                }
                body .helper {
                    display: inline-block;
                    height: 100%;
                    vertical-align: middle;
                }
                body .no-break {
                    page-break-inside: avoid;
                }
        
                header {
                    margin-top: 20px;
                    margin-bottom: 50px;
                }
                header figure {
                    float: left;
                    width: 60px;
                    height: 60px;
                    margin-right: 10px;
                    background-color: #8BC34A;
                    border-radius: 50%;
                    text-align: center;
                }
                header figure img {
                    margin-top: 13px;
                }
                header .company-address {
                    float: left;
                    max-width: 150px;
                    line-height: 1.7em;
                }
                header .company-address .title {
                    color: #8BC34A;
                    font-weight: 400;
                    font-size: 1.5em;
                    text-transform: uppercase;
                }
                header .company-contact {
                    float: right;
                    height: 60px;
                    padding: 0 10px;
                    background-color: #8BC34A;
                    color: white;
                }
                header .company-contact span {
                    display: inline-block;
                    vertical-align: middle;
                }
                header .company-contact .circle {
                    width: 20px;
                    height: 20px;
                    background-color: white;
                    border-radius: 50%;
                    text-align: center;
                }
                header .company-contact .circle img {
                    vertical-align: middle;
                }
                header .company-contact .phone {
                    height: 100%;
                    margin-right: 20px;
                }
                header .company-contact .email {
                    height: 100%;
                    min-width: 100px;
                    text-align: right;
                }
        
                section .details {
                    margin-bottom: 55px;
                }
                section .details .client {
                    width: 50%;
                    line-height: 20px;
                }
                section .details .client .name {
                    color: #8BC34A;
                }
                section .details .data {
                    width: 50%;
                    text-align: right;
                }
                section .details .title {
                    margin-bottom: 15px;
                    color: #8BC34A;
                    font-size: 3em;
                    font-weight: 400;
                    text-transform: uppercase;
                }
                section table {
                    width: 100%;
                    border-collapse: collapse;
                    border-spacing: 0;
                    font-size: 0.9166em;
                }
                section table .qty, section table .unit, section table .total {
                    width: 15%;
                }
                section table .desc {
                    width: 55%;
                }
                section table thead {
                    display: table-header-group;
                    vertical-align: middle;
                    border-color: inherit;
                }
                section table thead th {
                    padding: 5px 10px;
                    background: #8BC34A;
                    border-bottom: 5px solid #FFFFFF;
                    border-right: 4px solid #FFFFFF;
                    text-align: right;
                    color: white;
                    font-weight: 400;
                    text-transform: uppercase;
                }
                section table thead th:last-child {
                    border-right: none;
                }
                section table thead .desc {
                    text-align: left;
                }
                section table thead .qty {
                    text-align: center;
                }
                section table tbody td {
                    padding: 10px;
                    background: #E8F3DB;
                    color: #777777;
                    text-align: right;
                    border-bottom: 5px solid #FFFFFF;
                    border-right: 4px solid #E8F3DB;
                }
                section table tbody td:last-child {
                    border-right: none;
                }
                section table tbody h3 {
                    margin-bottom: 5px;
                    color: #8BC34A;
                    font-weight: 600;
                }
                section table tbody .desc {
                    text-align: left;
                }
                section table tbody .qty {
                    text-align: center;
                }
                section table.grand-total {
                    margin-bottom: 45px;
                }
                section table.grand-total td {
                    padding: 5px 10px;
                    border: none;
                    color: #777777;
                    text-align: right;
                }
                section table.grand-total .desc {
                    background-color: transparent;
                }
                section table.grand-total tr:last-child td {
                    font-weight: 600;
                    color: #8BC34A;
                    font-size: 1.18181818181818em;
                }
        
                footer {
                    margin-bottom: 20px;
                }
                footer .thanks {
                    margin-bottom: 40px;
                    color: #8BC34A;
                    font-size: 1.16666666666667em;
                    font-weight: 600;
                }
                footer .notice {
                    margin-bottom: 25px;
                }
                footer .end {
                    padding-top: 5px;
                    border-top: 2px solid #8BC34A;
                    text-align: center;
                }
            </style>
        </head>
        
        <body>
            <header class="clearfix">
                <div class="container">
                   
                    <div class="company-address">
                        <h2 class="title">NGUYEN HUU HUY</h2>
                        <p>
                            755 - Nguyen Kiem,<br>
                            Phuong 3, Go Vap, Ho Chi Minh
                        </p>
                    </div>
                    <div class="company-contact">
                        <div class="phone left">
                            <span class="circle"><img src="data:image/svg+xml;charset=utf-8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxNS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zOnNrZXRjaD0iaHR0cDovL3d3dy5ib2hlbWlhbmNvZGluZy5jb20vc2tldGNoL25zIg0KCSB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjkuNzYycHgiIGhlaWdodD0iOS45NThweCINCgkgdmlld0JveD0iLTQuOTkyIDAuNTE5IDkuNzYyIDkuOTU4IiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IC00Ljk5MiAwLjUxOSA5Ljc2MiA5Ljk1OCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8dGl0bGU+RmlsbCAxPC90aXRsZT4NCjxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPg0KPGcgaWQ9IlBhZ2UtMSIgc2tldGNoOnR5cGU9Ik1TUGFnZSI+DQoJPGcgaWQ9IklOVk9JQ0UtMSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTMwMS4wMDAwMDAsIC01NC4wMDAwMDApIiBza2V0Y2g6dHlwZT0iTVNBcnRib2FyZEdyb3VwIj4NCgkJPGcgaWQ9IlpBR0xBVkxKRSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMzAuMDAwMDAwLCAxNS4wMDAwMDApIiBza2V0Y2g6dHlwZT0iTVNMYXllckdyb3VwIj4NCgkJCTxnIGlkPSJLT05UQUtUSSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMjY3LjAwMDAwMCwgMzUuMDAwMDAwKSIgc2tldGNoOnR5cGU9Ik1TU2hhcGVHcm91cCI+DQoJCQkJPGcgaWQ9Ik92YWwtMS1feDJCXy1GaWxsLTEiPg0KCQkJCQk8cGF0aCBpZD0iRmlsbC0xIiBmaWxsPSIjOEJDMzRBIiBkPSJNOC43NjUsMTIuMzc1YzAuMDIsMC4xNjItMC4wMjgsMC4zMDMtMC4xNDMsMC40MjJMNy4yNDYsMTQuMTkNCgkJCQkJCWMtMC4wNjIsMC4wNy0wLjE0MywwLjEzMy0wLjI0MywwLjE4MmMtMC4xMDEsMC4wNDktMC4xOTcsMC4wOC0wLjI5NSwwLjA5NGMtMC4wMDcsMC0wLjAyOCwwLTAuMDYyLDAuMDA0DQoJCQkJCQljLTAuMDM0LDAuMDA1LTAuMDgsMC4wMDgtMC4xMzQsMC4wMDhjLTAuMTMxLDAtMC4zNDMtMC4wMjMtMC42MzUtMC4wNjhjLTAuMjkzLTAuMDQ1LTAuNjUxLTAuMTU4LTEuMDc2LTAuMzM2DQoJCQkJCQljLTAuNDI0LTAuMTgyLTAuOTA0LTAuNDUxLTEuNDQyLTAuODA5Yy0wLjUzNi0wLjM1Ny0xLjEwOS0wLjg1Mi0xLjcxNi0xLjQ3OWMtMC40ODEtMC40ODQtMC44OC0wLjk1LTEuMTk4LTEuMzkzDQoJCQkJCQlDMC4xMjgsOS45NS0wLjEyNSw5LjU0MS0wLjMxOSw5LjE2NGMtMC4xOTMtMC4zNzYtMC4zMzgtMC43MTctMC40MzQtMS4wMjNjLTAuMDk3LTAuMzA2LTAuMTYxLTAuNTctMC4xOTUtMC43OTINCgkJCQkJCWMtMC4wMzUtMC4yMjEtMC4wNS0wLjM5NC0wLjA0Mi0wLjUyMWMwLjAwNy0wLjEyNiwwLjAxLTAuMTk3LDAuMDEtMC4yMTFjMC4wMTQtMC4wOTksMC4wNDQtMC4xOTgsMC4wOTMtMC4zMDENCgkJCQkJCWMwLjA0OS0wLjEwMSwwLjEwOC0wLjE4NCwwLjE3Ni0wLjI0N2wxLjM3NS0xLjQwM2MwLjA5Ny0wLjA5OCwwLjIwNi0wLjE0NywwLjMzLTAuMTQ3YzAuMDksMCwwLjE2OSwwLjAyNiwwLjIzOCwwLjA3OQ0KCQkJCQkJQzEuMyw0LjY0OCwxLjM1OSw0LjcxNCwxLjQwNiw0Ljc5MWwxLjEwNiwyLjE0MWMwLjA2MiwwLjExNCwwLjA4LDAuMjM1LDAuMDUyLDAuMzdDMi41MzgsNy40MzYsMi40NzgsNy41NDgsMi4zODksNy42NA0KCQkJCQkJTDEuODgzLDguMTU3QzEuODY5LDguMTcxLDEuODU2LDguMTk0LDEuODQ2LDguMjI2QzEuODM1LDguMjU2LDEuODMsOC4yODMsMS44Myw4LjMwNGMwLjAyNywwLjE0NywwLjA5LDAuMzE3LDAuMTg3LDAuNTA3DQoJCQkJCQljMC4wODIsMC4xNjksMC4yMSwwLjM3NSwwLjM4MiwwLjYxOGMwLjE3MiwwLjI0MywwLjQxNywwLjUyMSwwLjczNCwwLjgzOWMwLjMxMSwwLjMyMiwwLjU4NSwwLjU3NCwwLjgyOCwwLjc1NQ0KCQkJCQkJYzAuMjQsMC4xNzgsMC40NDMsMC4zMDksMC42MDQsMC4zOTVjMC4xNjIsMC4wODUsMC4yODYsMC4xMzUsMC4zNzIsMC4xNTRsMC4xMjgsMC4wMjRjMC4wMTUsMCwwLjAzOC0wLjAwNiwwLjA2Ny0wLjAxNg0KCQkJCQkJYzAuMDMyLTAuMDEsMC4wNTQtMC4wMjEsMC4wNjctMC4wMzdsMC41ODgtMC42MTJjMC4xMjUtMC4xMTIsMC4yNy0wLjE2OCwwLjQzNi0wLjE2OGMwLjExNywwLDAuMjA3LDAuMDIxLDAuMjc3LDAuMDYxaDAuMDENCgkJCQkJCWwxLjk5NSwxLjIwM0M4LjY1MSwxMi4xMiw4LjczNywxMi4yMzQsOC43NjUsMTIuMzc1TDguNzY1LDEyLjM3NXoiLz4NCgkJCQk8L2c+DQoJCQk8L2c+DQoJCTwvZz4NCgk8L2c+DQo8L2c+DQo8L3N2Zz4NCg==" alt=""><span class="helper"></span></span>
                            <a href="tel:602-519-0450">(+84) 3987-30223</a>
                            <span class="helper"></span>
                        </div>
                        <div class="email right">
                            <span class="circle"><img src="data:image/svg+xml;charset=utf-8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxNS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zOnNrZXRjaD0iaHR0cDovL3d3dy5ib2hlbWlhbmNvZGluZy5jb20vc2tldGNoL25zIg0KCSB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCIgd2lkdGg9IjE0LjE3M3B4Ig0KCSBoZWlnaHQ9IjE0LjE3M3B4IiB2aWV3Qm94PSIwLjM1NCAtMi4yNzIgMTQuMTczIDE0LjE3MyIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwLjM1NCAtMi4yNzIgMTQuMTczIDE0LjE3MyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSINCgk+DQo8dGl0bGU+ZW1haWwxOTwvdGl0bGU+DQo8ZGVzYz5DcmVhdGVkIHdpdGggU2tldGNoLjwvZGVzYz4NCjxnIGlkPSJQYWdlLTEiIHNrZXRjaDp0eXBlPSJNU1BhZ2UiPg0KCTxnIGlkPSJJTlZPSUNFLTEiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC00MTcuMDAwMDAwLCAtNTUuMDAwMDAwKSIgc2tldGNoOnR5cGU9Ik1TQXJ0Ym9hcmRHcm91cCI+DQoJCTxnIGlkPSJaQUdMQVZMSkUiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDMwLjAwMDAwMCwgMTUuMDAwMDAwKSIgc2tldGNoOnR5cGU9Ik1TTGF5ZXJHcm91cCI+DQoJCQk8ZyBpZD0iS09OVEFLVEkiIHRyYW5zZm9ybT0idHJhbnNsYXRlKDI2Ny4wMDAwMDAsIDM1LjAwMDAwMCkiIHNrZXRjaDp0eXBlPSJNU1NoYXBlR3JvdXAiPg0KCQkJCTxnIGlkPSJPdmFsLTEtX3gyQl8tZW1haWwxOSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTE3LjAwMDAwMCwgMC4wMDAwMDApIj4NCgkJCQkJPHBhdGggaWQ9ImVtYWlsMTkiIGZpbGw9IiM4QkMzNEEiIGQ9Ik0zLjM1NCwxNC4yODFoMTQuMTczVjUuMzQ2SDMuMzU0VjE0LjI4MXogTTEwLjQ0LDEwLjg2M0w0LjYyNyw2LjAwOGgxMS42MjZMMTAuNDQsMTAuODYzDQoJCQkJCQl6IE04LjEyNSw5LjgxMkw0LjA1LDEzLjIxN1Y2LjQwOUw4LjEyNSw5LjgxMnogTTguNjUzLDEwLjI1M2wxLjc4OCwxLjQ5M2wxLjc4Ny0xLjQ5M2w0LjAyOSwzLjM2Nkg0LjYyNEw4LjY1MywxMC4yNTN6DQoJCQkJCQkgTTEyLjc1NSw5LjgxMmw0LjA3NS0zLjQwM3Y2LjgwOEwxMi43NTUsOS44MTJ6Ii8+DQoJCQkJPC9nPg0KCQkJPC9nPg0KCQk8L2c+DQoJPC9nPg0KPC9nPg0KPC9zdmc+DQo=" alt=""><span class="helper"></span></span>
                            <a href="mailto:company@example.com">huy@ou.edu.vn</a>
                            <span class="helper"></span>
                        </div>
                    </div>
                </div>
            </header>
        
            <section>
                <div class="container">
                    <div class="details clearfix">
                        <div class="client left">
                            <p>INVOICE TO:</p>
                            <p class="name">' . $orderDetails['name'] . '</p>
                            <p>' . $orderDetails['address'] . ',' . $orderDetails['state'] . ',' . $orderDetails['city'] . ',' . $orderDetails['country'] . '</p>
                            <a href="mailto:' . $orderDetails['email'] . '">' . $orderDetails['email'] . '</a>
                        </div>
                        <div class="data right">
                            <div class="title">ID: ' . $orderDetails['id'] . '</div>
                            <div class="date">
                                Order Date: ' . \Carbon\Carbon::parse($orderDetails['created_at'])->format('d/m/Y H:i:s') . '<br>
                                Order Amount: ' . number_format($orderDetails['grand_total'], 0, ',', '.') . ' VND<br>
                                Order Status: ' . $orderDetails['order_status'] . '<br>
                                Payment Method: ' . $orderDetails['payment_method'] . '<br>
                            </div>
                        </div>
                    </div>
        
                    <table border="0" cellspacing="0" cellpadding="0">
                        <thead>
                            <tr>
                                <th class="desc">Product Code</th>
                                <th class="qty">Size</th>
                                <th class="qty">Color</th>
                                <th class="qty">Quantity</th>
                                <th class="unit">Unit price</th>
                                <th class="total">Total</th>
                            </tr>
                        </thead>
                        <tbody>';
        $subTotal = 0;
        foreach ($orderDetails['orders_products'] as $product) {
            $invoiceHTML .= '<tr>
                                <td class="desc">' . $product['product_code'] . '</td>
                                <td class="qty">' . $product['product_size'] . '</td>
                                <td class="qty">' . $product['product_color'] . '</td>
                                <td class="qty">' . $product['product_qty'] . '</td>
                                <td class="unit">' . number_format($product['product_price'], 0, ',', '.') . ' VND</td>
                                <td class="total">' . number_format($product['product_price'] * $product['product_qty'], 0, ',', '.') . 'VND</td>
                            </tr>';
            $subTotal = $subTotal + ($product['product_price'] * $product['product_qty']);
        }
        $invoiceHTML .= '</tbody>
                    </table>
                    <div class="no-break">
                        <table class="grand-total">
                            <tbody>
                                <tr>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                    <td class="total" colspan=2>SUBTOTAL</td>
                                    <td class="total">' . number_format($subTotal, 0, ',', '.') . ' VND</td>
                                </tr>
                                <tr>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                  
                                    <td class="total" colspan=2>Shipping</td>
                                    <td class="total">0 VND</td>
                                </tr>
                                <tr>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                   
                                    <td class="total" colspan=2>Discount</td>';
        if ($orderDetails['coupon_amount'] > 0) {
            $invoiceHTML .= '<td class="total">' . number_format($orderDetails['coupon_amount'], 0, ',', '.') . 'VND</td>';
        } else {
            $invoiceHTML .= '<td class="total">0 VND</td>';
        }
        $invoiceHTML .= '</tr>
                                <tr>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                    <td class="desc"></td>
                                    <td class="total" colspan="2">TOTAL</td>
                                    <td class="total">' . number_format($orderDetails['grand_total'], 0, ',', '.') . 'VND</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        
            <footer>
                <div class="container">
                    <div class="thanks">Thank you!</div>
                    
                    <div class="end">Invoice was created on a computer and is valid without the signature and seal.</div>
                </div>
            </footer>
        
        </body>
        
        </html>
        ';
        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($invoiceHTML);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream();
    }
}
