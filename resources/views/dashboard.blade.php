@extends('master')
@section('title','Home')
@section('content')

    <!-- BEGIN: Subheader -->
    @php
    $permissionCheck =  permissionCheckForNotification();
    @endphp


        <!-- END: Subheader -->
        <div class="m-content">
            <!--Begin::Section-->
            <div class="m-portlet dashboard-brand">
                <div class="m-portlet__body  m-portlet__body--no-padding">
                    <div class="row m-row--no-padding m-row--col-separator-xl">
                        <div class="col-xl-4 dashboard-top-block">
                            <div class="dashboard-top-block__bg">
                                <img src="{{ asset('uploads/background/dashboard-block1.png') }}">
                            </div>
                            <!--begin:: Widgets/Stats2-1 -->
                            <div class="align-block">
                                <div class="m-widget1">
                                    <div class="m-widget1__item">
                                        <div class="row m-row--no-padding align-items-center">
                                            <div class="col">
                                            <span class="m-widget1__number m--font-success">
                                                @if(isset($company_info->logo))
                                                    <img width="150px" src="{{url('uploads/company_logo').'/'. $company_info->logo}}" class="img responsive" onerror="this.src='img/1200px-Channel-i.png';" alt="Missing Image">
                                                @else if
                                                <img width="150px" src="{{url('img/1200px-Channel-i.png')}}" class="img responsive">
                                                @endif
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item">
                                        <div class="row m-row--no-padding align-items-center">
                                            <div class="col">
                                                <span class="m-widget1__number m--font-brand">@if(isset($company_info->name)) {{$company_info->name }} @endif </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item">
                                        <div class="row m-row--no-padding align-items-center">
                                            <div class="col">
                                                <span class="m-widget1__number m--font-danger">@if(isset($company_info->address)) {{$company_info->address}} @endif</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--end:: Widgets/Stats2-1 -->
                        </div>
                        {{--<div class="col-xl-4 dashboard-top-block">
                            <div class="dashboard-top-block__bg">
                                <img src="{{ asset('uploads/background/dashboard-block2.png') }}">
                            </div>
                            <div class="align-block">
                                <div class="m-widget1">
                                    <div class="m-widget1__item">
                                        <div class="row m-row--no-padding align-items-center">
                                            <div class="col">
                                            <span class="m-widget1__number m--font-success">
                                                Last Four Days Sales
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item">
                                        <div class="row m-row--no-padding align-items-center">
                                            <div class="col">
                                                <span class="m-widget1__number m--font-brand">100000</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>--}}
                        <div class="col-xl-4 dashboard-top-block">
                            <div class="dashboard-top-block__bg">
                                <img src="{{ asset('uploads/background/dashboard-block2.png') }}">
                            </div>
                            <!--begin:: Widgets/Daily Sales-->
                            <div class="align-block">
                                <div class="m-widget1">
                                    <div class="m-widget1__item">
                                        <div class="row m-row--no-padding align-items-center">
                                            <div class="col">
                                            <span class="m-widget1__number m--font-success">
                                                Last Four Days Sales
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-widget14">
                                        <div class="m-widget14__header m--margin-bottom-30">
                                            <h3 class="m-widget14__title">
                                                Last Four Days Sales
                                            </h3>
                                            <span class="m-widget14__desc">
                                        Check out each collumn for more details
                                    </span>
                                        </div>
                                        {{--<div class="m-widget14__chart" style="height:160px;">--}}
                                        <div class="row  align-items-center">
                                            <div id="chart_div" style="width: 350px; height: 160px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end:: Widgets/Daily Sales-->
                        </div>
                        <div class="col-xl-4 dashboard-top-block">
                            <div class="dashboard-top-block__bg">
                                <img src="{{ asset('uploads/background/dashboard-block1.png') }}">
                            </div>
                            {{--<div class="align-block">
                                <div class="m-widget1">
                                    <div class="m-widget1__item">
                                        <div class="row m-row--no-padding align-items-center">
                                            <div class="col">
                                            <span class="m-widget1__number m--font-success">
                                                Monthly Balance
                                            </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-widget1__item">
                                        <div class="row m-row--no-padding align-items-center">
                                            <div class="col">
                                                <span class="m-widget1__number m--font-brand">567,12,000</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>--}}
                            <!--end:: Widgets/Stats2-1 -->
                        </div>

                        {{--<div class="col-xl-4 dashboard-top-block">
                            <div class="m-widget14">
                                <div class="m-widget14__header">
                                    <h3 class="m-widget14__title">
                                        Monthly Balance
                                    </h3>
                                    <span class="m-widget14__desc">
                                        Production, Purchase and Sales
                                    </span>
                                </div>
                                <div class="row  align-items-center">
                                    --}}{{--<div id="donutchart" style="width: 900px; height: 200px;"></div>--}}{{--
                                    test
                                </div>
                            </div>
                            <!--end:: Widgets/Profit Share-->
                        </div>--}}
                    </div>
                </div>
            </div>
            <br><br>
            <!--Begin::Section-->

            <!-- ---------Inventory Info Start---------------- -->
            <div class="m-portlet dashboard-single-wrapper">
                <div style="margin-left:15px; margin-top: 5px;">
                    <br>
                    <h5 class="m-portlet__head-text">
                        INVENTORY
                    </h5>
                    <hr>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" >
                                <div class="rotate">
                                    <i class="fa fa-money"></i>
                                </div>
                                <div class="details">
                                    <span class="title">{{--Total Product Qty--}}</span>
                                    <span class="sub">@php //echo $prd_inv;  @endphp</span>

                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Alert Qty Of Products</span>
                                    <span class="sub">@php echo $prd_alert_qty;  @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Product</span>
                                    <span class="sub">@php echo $total_product->total @endphp </span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total LC</span>
                                    <span class="sub">@php echo $total_lc ; @endphp</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <br/>
            </div>
            <!-- ---------Inventory Info End---------------- -->


            {{--Payment Start--}}
            {{-- Bank Payment--}}
            @if (in_array('bank-payment-vouchers.approve',$permissionCheck))
            <!-- ---------Bank Payment Info Start---------------- -->
            <div class="m-portlet dashboard-single-wrapper">
                <div style="margin-left:15px; margin-top: 10px;">
                    <h5 class="m-portlet__head-text">
                        <br>
                        BANK PAYMENT
                    </h5>
                    <hr>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #252a61;">
                                <div class="rotate">
                                    <i class="fa fa-money"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Requisition</span>
                                    <span class="sub">@php echo $total_bn_pay_acc_po; @endphp</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #252a61;">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Pending</span>
                                    <span class="sub">@php echo $total_bn_pay_pending;  @endphp</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #252a61;">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Approve</span>
                                    <span class="sub">@php echo $total_bn_pay_apprv; @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            {{--<span style="margin-left: 20px; font-weight: 500;">Pending Requisition </span>--}}
                            <div class="m-portlet__body display-block-list">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="m_widget2_tab1_content">
                                        <!--Begin::Timeline 3 -->
                                        @if($total_bn_pay_pending == 0 )
                                            <div class="no-data-block">
                                                No data available
                                            </div>
                                        @endif

                                        @if($total_bn_pay_pending !== 0 )
                                            <div class="m-timeline-3">
                                                <div class="m-timeline-3__items">
                                                    <div class="m-timeline-3__item">
                                                        <div class="adjust-pending-table">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Pending Requisition</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($bn_pay_lists as $value)
                                                                    <tr>
                                                                        <td rowspan="2">{{ date('d-M',strtotime($value->payment_date)) }}</td>
                                                                        <td><a href="{{ url('/bank-payment-voucher') }}">{{ $value->payment_transaction_no }}</a></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>{{ $value->total_debit_amount }}</td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        {{--<span class="m-timeline-3__item-time">{{ date('d-M',strtotime($value->payment_date)) }}</span>
                                                        <div class="m-timeline-3__item-desc">
                                                                <span class="m-timeline-3__item-text">
                                                            <a href="{{ url('/bank-payment-voucher') }}"  class="text-dark"> <span style="font-weight: 700">{{ $value->payment_transaction_no }} </span></a>
                                                                </span>
                                                            <br>
                                                            <span class="m-timeline-3__item-user-name">
                                                                <span style="font-weight: bold">Amount</span> :{{ $value->total_debit_amount }}
                                                            </span>
                                                        </div>--}}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    <!--End::Timeline 3 -->
                                    </div>
                                </div>
                            </div>
                            <!--End::Portlet-->
                        </div>
                    </div>
                </div>
                <br/>
            </div>
            @endif
            {{--Cash Payment--}}
            @if (in_array('cash-payment-vouchers.approve',$permissionCheck))
            <!--Cash Payment -->
            <div class="m-portlet dashboard-single-wrapper">
                <div style="margin-left:15px; margin-top: 10px;">
                    <h5 class="m-portlet__head-text">
                        <br>
                        CASH PAYMENT
                    </h5>
                    <hr>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #252a61;">
                                <div class="rotate">
                                    <i class="fa fa-money"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Requisition</span>
                                    <span class="sub"> @php echo $total_cash_pay_acc_po;  @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #252a61;">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Pending</span>
                                    <span class="sub">@php echo $total_cash_pay_pending;  @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #252a61;">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Approve</span>
                                    <span class="sub">@php echo $total_cash_pay_apprv; @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            {{--<span style="margin-left: 20px; font-weight: 500;">Pending Order </span>--}}

                            <div class="m-portlet__body display-block-list">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="m_widget2_tab1_content">
                                        <!--Begin::Timeline 3 -->
                                        @if($total_cash_pay_pending !== 0 )
                                            <div class="no-data-block">
                                                No data available
                                            </div>
                                        @endif

                                        @if($total_cash_pay_pending == 0 )
                                            <div class="m-timeline-3">
                                                <div class="m-timeline-3__items">
                                                    <div class="m-timeline-3__item">
                                                        <div class="adjust-pending-table">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Pending Order</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($cash_pay_lists as $value)
                                                                    <tr>
                                                                        <td rowspan="2">{{ date('d-M',strtotime($value->payment_date)) }}</td>
                                                                        <td><a href="{{ url('/cash-payment-voucher') }}">{{ $value->payment_transaction_no }}</a></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>{{ $value->total_credit_amount }}</td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        {{--<span class="m-timeline-3__item-time">{{ date('d-M',strtotime($value->payment_date)) }}</span>
                                                        <div class="m-timeline-3__item-desc">
                                                            <span class="m-timeline-3__item-text">
                                                                <a href="{{ url('/cash-payment-voucher') }}" class="text-dark" > <span style="font-weight: 700">{{ $value->payment_transaction_no }} </span></a>
                                                            </span>
                                                            <br>
                                                            <span class="m-timeline-3__item-user-name">
                                                                <span style="font-weight: bold">Amount</span> :{{ $value->total_credit_amount }}
                                                            </span>
                                                        </div>--}}
                                                    </div>

                                                </div>
                                            </div>
                                        @endif

                                    <!--End::Timeline 3 -->
                                    </div>
                                </div>
                            </div>
                            <!--End::Portlet-->
                        </div>
                    </div>
                </div>
                <br/>
            </div>
            @endif
            <!--End-->
            {{--Payment End--}}

            {{--Receive Start--}}
            {{-- Bank Receive--}}
            @if (in_array('bank-receipt-vouchers.approve',$permissionCheck))
            <!-- ---------Bank Receive Info Start---------------- -->
            <div class="m-portlet dashboard-single-wrapper">
                <div style="margin-left:15px; margin-top: 10px;">
                    <h5 class="m-portlet__head-text">
                        <br>
                        BANK RECEVIE
                    </h5>
                    <hr>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #252a61;">
                                <div class="rotate">
                                    <i class="fa fa-money"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Requisition</span>
                                    <span class="sub">@php echo $total_bn_re_acc; @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #252a61;">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Pending</span>
                                    <span class="sub">@php echo $total_bn_re_pending;  @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #252a61;">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Approve</span>
                                    <span class="sub">@php echo $total_bn_re_apprv; @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            {{--<span style="margin-left: 20px; font-weight: 500;">Pending Requisition </span>--}}
                            <div class="m-portlet__body display-block-list">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="m_widget2_tab1_content">
                                        <!--Begin::Timeline 3 -->
                                        @if($total_bn_re_pending == 0 )
                                            <div class="no-data-block">
                                                No data available
                                            </div>
                                        @endif

                                        @if($total_bn_re_pending !== 0 )
                                            <div class="m-timeline-3">
                                                <div class="m-timeline-3__items">
                                                    <div class="m-timeline-3__item">
                                                        <div class="adjust-pending-table">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Pending Requisition</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($bn_re_lists as $value)

                                                                    <tr>
                                                                        <td rowspan="2">{{ date('d-M',strtotime($value->reciept_date)) }}</td>
                                                                        <td><a href="{{ url('/bank-receipt-voucher') }}">{{ $value->receipt_transaction_no }}</a></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>{{ $value->total_debit_amount }}</td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        {{--<span class="m-timeline-3__item-time">{{ date('d-M',strtotime($value->reciept_date)) }}</span>
                                                        <div class="m-timeline-3__item-desc">
                                                            <span class="m-timeline-3__item-text">
                                                                <a href="{{ url('/bank-receipt-voucher') }}"  class="text-dark"> <span style="font-weight: 700">{{ $value->receipt_transaction_no }} </span></a>
                                                            </span>
                                                            <br>
                                                            <span class="m-timeline-3__item-user-name">
                                                                <span style="font-weight: bold">Amount</span> :{{ $value->total_debit_amount }}
                                                            </span>
                                                        </div>--}}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    <!--End::Timeline 3 -->
                                    </div>
                                </div>
                            </div>
                            <!--End::Portlet-->
                        </div>
                    </div>
                </div>
                <br/>
            </div>
            @endif
            {{--Cash Receive--}}
            @if (in_array('cash-receipt-vouchers.approve',$permissionCheck))
            <!--Cash Receive -->
            <div class="m-portlet dashboard-single-wrapper">
                <div style="margin-left:15px; margin-top: 10px;">
                    <h5 class="m-portlet__head-text">
                        <br>
                        CASH RECIVE
                    </h5>
                    <hr>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #252a61;">
                                <div class="rotate">
                                    <i class="fa fa-money"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Requisition</span>
                                    <span class="sub"> @php echo $total_cash_re_acc;  @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #252a61;">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Pending</span>
                                    <span class="sub">@php echo $total_cash_re_pending;  @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #252a61;">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Approve</span>
                                    <span class="sub">@php echo $total_cash_re_apprv; @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            {{--<span style="margin-left: 20px; font-weight: 500;">Pending Order </span>--}}

                            <div class="m-portlet__body display-block-list">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="m_widget2_tab1_content">
                                        <!--Begin::Timeline 3 -->
                                        @if($total_cash_re_pending == 0 )
                                            <div class="no-data-block">
                                                No data available
                                            </div>
                                        @endif

                                        @if($total_cash_re_pending !== 0 )
                                            <div class="m-timeline-3">
                                                <div class="m-timeline-3__items">
                                                    <div class="m-timeline-3__item">
                                                        <div class="adjust-pending-table">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Panding Order</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($cash_re_lists as $value)
                                                                    <tr>
                                                                        <td rowspan="2">{{ date('d-M',strtotime($value->receipt_date)) }}</td>
                                                                        <td><a href="{{ url('/cash-receipt-voucher') }}">{{ $value->receipt_transaction_no }}</a></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>{{ $value->total_credit_amount }}</td>
                                                                    </tr>
                                                                @endforeach

                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        {{--<span class="m-timeline-3__item-time">{{ date('d-M',strtotime($value->receipt_date)) }}</span>
                                                        <div class="m-timeline-3__item-desc">
                                                            <span class="m-timeline-3__item-text">
                                                                <a href="{{ url('/cash-receipt-voucher') }}" class="text-dark" > <span style="font-weight: 700">{{ $value->receipt_transaction_no }} </span></a>
                                                            </span>
                                                            <br>
                                                            <span class="m-timeline-3__item-user-name">
                                                                <span style="font-weight: bold">Amount</span> :{{ $value->total_credit_amount }}
                                                            </span>
                                                        </div>--}}
                                                    </div>

                                                </div>
                                            </div>
                                        @endif

                                    <!--End::Timeline 3 -->
                                    </div>
                                </div>
                            </div>
                            <!--End::Portlet-->
                        </div>
                    </div>
                </div>
                <br/>
            </div>
            @endif
            <!--End-->
            {{--Payment End--}}

            {{--Purchasae Start--}}
            {{-- REq--}}
            @if (in_array('pr-requisition.approve',$permissionCheck))
            <!-- ---------Purcahse REq Info Start---------------- -->
            <div class="m-portlet dashboard-single-wrapper">
                <div style="margin-left:15px; margin-top: 10px;">
                    <h5 class="m-portlet__head-text">
                        <br>
                        PURCHASE REQUISTION
                    </h5>
                    <hr>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #1f5e6d;">
                                <div class="rotate">
                                    <i class="fa fa-money"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Requisition</span>
                                    <span class="sub">@php echo $total_purchase_req; @endphp</span>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #1f5e6d;">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Pending</span>
                                    <span class="sub">@php echo $total_purchase_req_pending;  @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #1f5e6d;">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Approve</span>
                                    <span class="sub">@php echo $total_purchase_req_apprv; @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            {{--<span style="margin-left: 20px; font-weight: 500;">Pending Requisition </span>--}}
                            <div class="m-portlet__body display-block-list">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="m_widget2_tab1_content">
                                        <!--Begin::Timeline 3 -->
                                        @if($total_purchase_req_pending == 0 )
                                            <div class="no-data-block">
                                                No data available
                                            </div>
                                        @endif

                                        @if($total_purchase_req_pending !== 0 )

                                            <div class="m-timeline-3">
                                                <div class="m-timeline-3__items">
                                                    <div class="m-timeline-3__item">
                                                        <div class="adjust-pending-table">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Requisition info</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($purchase_order_lists as $value)
                                                                    <tr>
                                                                        <td rowspan="2">{{ date('d-M',strtotime($value->req_date)) }}</td>
                                                                        <td><a href="{{ url('/pr-requisition') }}">{{ $value->req_total_qty }}</a></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>{{ $value->req_total_qty }}</td>
                                                                    </tr>
                                                                @endforeach

                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        {{--<span class="m-timeline-3__item-time">{{ date('d-M',strtotime($value->req_date)) }}</span>
                                                        <div class="m-timeline-3__item-desc">
                                                                <span class="m-timeline-3__item-text">
                                                            <a href="{{ url('/pr-requisition') }}"  class="text-dark"> <span style="font-weight: 700">{{ $value->req_no }} </span></a>
                                                                </span>
                                                            <br>
                                                            <span class="m-timeline-3__item-user-name">
                                                                <span style="font-weight: bold">Qty</span> :{{ $value->req_total_qty }}
                                                            </span>
                                                        </div>--}}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    <!--End::Timeline 3 -->
                                    </div>
                                </div>
                            </div>
                            <!--End::Portlet-->
                        </div>
                    </div>
                </div>
                <br/>
            </div>
            @endif
            {{--PO OREDER--}}
            @if (in_array('po-receive.approve',$permissionCheck))
            <!--PO ORder -->
            <div class="m-portlet dashboard-single-wrapper">
                <div style="margin-left:15px; margin-top: 10px;">
                    <h5 class="m-portlet__head-text">
                        <br>
                        PO RECEIVE
                    </h5>
                    <hr>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #1f5e6d;">
                                <div class="rotate">
                                    <i class="fa fa-money"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Requisition</span>
                                    <span class="sub"> @php echo $total_po_order;  @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #1f5e6d;">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Pending</span>
                                    <span class="sub">@php echo $total_po_order_pending;  @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #1f5e6d;">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Approve</span>
                                    <span class="sub">@php echo $total_po_order_apprv; @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            {{--<span style="margin-left: 20px; font-weight: 500;">Pending Order </span>--}}

                            <div class="m-portlet__body display-block-list">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="m_widget2_tab1_content">
                                        <!--Begin::Timeline 3 -->
                                        @if($total_po_order_pending == 0 )
                                            <div class="no-data-block">
                                                No data available
                                            </div>
                                        @endif

                                        @if($total_po_order_pending !== 0 )
                                            <div class="m-timeline-3">
                                                <div class="m-timeline-3__items">
                                                    <div class="m-timeline-3__item">
                                                        <div class="adjust-pending-table">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Pending Order</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($po_order_lists as $value)
                                                                    <tr>
                                                                        <td rowspan="2">{{ date('d-M',strtotime($value->po_receive_date)) }}</td>
                                                                        <td><a href="{{ url('/po-receive') }}">{{ $value->po_order_no }}</a></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>{{ $value->po_total_qty }}</td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        {{--<span class="m-timeline-3__item-time">{{ date('d-M',strtotime($value->po_receive_date)) }}</span>
                                                        <div class="m-timeline-3__item-desc">
                                                            <span class="m-timeline-3__item-text">
                                                                <a href="{{ url('/po-receive') }}" class="text-dark" > <span style="font-weight: 700">{{ $value->po_order_no }} </span>({{ $value->get_supplier->purchase_supplier_name }})</a>
                                                            </span>
                                                            <br>
                                                            <span class="m-timeline-3__item-user-name">
                                                                <span style="font-weight: bold">Qty</span> :{{ $value->po_total_qty }}
                                                            </span>
                                                        </div>--}}
                                                    </div>

                                                </div>
                                            </div>
                                        @endif

                                        <!--End::Timeline 3 -->
                                    </div>
                                </div>
                            </div>
                            <!--End::Portlet-->
                        </div>
                    </div>
                </div>
                <br/>
            </div>
            @endif
            <!--End-->
            <!--Account-->
            @if (in_array('purchase-invoice-voucher.approve',$permissionCheck))
            <div class="m-portlet dashboard-single-wrapper">
                <div style="margin-left:15px; margin-top: 10px;">
                    <h5 class="m-portlet__head-text">
                        <br>
                        Account Purchase Invoice Voucher
                    </h5>
                    <hr>
                </div>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #1f5e6d;">
                                <div class="rotate">
                                    <i class="fa fa-money"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Requisition</span>
                                    <span class="sub">@php echo $total_acc_po; @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #1f5e6d;">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Pending</span>
                                    <span class="sub">@php echo $total_acc_po_pending;  @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            <a href="javascript:;" class="dashboard-block" style="background-color: #1f5e6d;">
                                <div class="rotate">
                                    <i class="fa fa-tachometer"></i>
                                </div>
                                <div class="details">
                                    <span class="title">Total Approve</span>
                                    <span class="sub">@php echo $total_acc_po_apprv; @endphp</span>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 col-sm-6">
                            {{--<span style="margin-left: 20px; font-weight: 500;">Pending Vouchers </span>--}}

                            <div class="m-portlet__body display-block-list">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="m_widget2_tab1_content">
                                        <!--Begin::Timeline 3 -->
                                        @if($total_acc_po_pending == 0 )
                                            <div class="no-data-block">
                                                No data available
                                            </div>
                                        @endif

                                        @if($total_acc_po_pending !== 0 )
                                            <div class="m-timeline-3">
                                                <div class="m-timeline-3__items">
                                                    <div class="m-timeline-3__item">
                                                        <div class="adjust-pending-table">
                                                            <table class="table table-bordered">
                                                                <thead>
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Pending Order</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @foreach($po_acc_lists as $value)
                                                                    <tr>
                                                                        <td rowspan="2">{{ date('d-M',strtotime($value->purchase_date)) }}</td>
                                                                        <td><a href="{{ url('/purchase-invoice-voucher') }}">{{ $value->vouchers_no }}</a></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>{{ $value->total_qty }}</td>
                                                                    </tr>
                                                                @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        {{--<span class="m-timeline-3__item-time">{{ date('d-M',strtotime($value->purchase_date)) }}</span>
                                                        <div class="m-timeline-3__item-desc">
                                                            <span class="m-timeline-3__item-text">
                                                                <a href="{{ url('/purchase-invoice-voucher') }}" class="text-dark"> <span style="font-weight: 700">{{ $value->vouchers_no }} </span></a>
                                                            </span>
                                                            <br>
                                                            <span class="m-timeline-3__item-user-name">
                                                                <span style="font-weight: bold">Qty</span> :{{ $value->total_qty }}
                                                            </span>
                                                        </div>--}}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!--End::Timeline 3 -->
                                    </div>
                                </div>
                            </div>
                            <!--End::Portlet-->
                        </div>

                    </div>
                </div>
                <br/>
            </div>
            @endif
            <!--End-->
            {{--Purchase End--}}

            @if (in_array('sales-invoice.approve',$permissionCheck))
                <!-- ---------Sales Info Start---------------- -->
                <div class="m-portlet dashboard-single-wrapper">
                        <div style="margin-left:15px; margin-top: 10px;">
                            <h5 class="m-portlet__head-text">
                                <br>
                                SALES ORDER
                            </h5>
                            <hr>
                        </div>
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <a href="javascript:;" class="dashboard-block">
                                        <div class="rotate">
                                            <i class="fa fa-money"></i>
                                        </div>
                                        <div class="details">
                                            <span class="title">Total Requisition</span>
                                            <span class="sub">@php echo $total_sales_req ;@endphp</span>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <a href="javascript:;" class="dashboard-block">
                                        <div class="rotate">
                                            <i class="fa fa-tachometer"></i>
                                        </div>
                                        <div class="details">
                                            <span class="title">Total Pending</span>
                                            <span class="sub">@php echo $total_sales_req_pending;  @endphp</span>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <a href="javascript:;" class="dashboard-block">
                                        <div class="rotate">
                                            <i class="fa fa-tachometer"></i>
                                        </div>
                                        <div class="details">
                                            <span class="title">Total Approve</span>
                                            <span class="sub">@php echo $total_sales_req_apprv; @endphp</span>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    {{--<span style="margin-left: 20px; font-weight: 500;">Pending Requisition </span>--}}

                                    <div class="m-portlet__body display-block-list">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="m_widget2_tab1_content">
                                                <!--Begin::Timeline 3 -->
                                                @if($total_sales_req_pending == 0 )
                                                    <div class="no-data-block">
                                                        No data available
                                                    </div>
                                                @endif


                                                @if($total_sales_req_pending !== 0 )
                                                    <div class="m-timeline-3">
                                                        <div class="m-timeline-3__items">
                                                            <div class="m-timeline-3__item">
                                                                <div class="adjust-pending-table">
                                                                    <table class="table table-bordered">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th>Pending Requisition</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @foreach($sales_order_lists as $value)
                                                                            <tr>
                                                                                <td rowspan="2">{{ date('d-M',strtotime($value->sales_invoices_date)) }}</td>
                                                                                <td><a href="{{ url('/sales-invoice') }}">{{ $value->sales_invoices_no }}</a></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td>{{ $value->total_qty }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                {{--<span class="m-timeline-3__item-time">{{ date('d-M',strtotime($value->sales_invoices_date)) }}</span>
                                                                <div class="m-timeline-3__item-desc">
                                                                    <span class="m-timeline-3__item-text">
                                                                        <a href="{{ url('/sales-invoice') }}"  class="text-dark"> <span style="font-weight: 700">{{ $value->sales_invoices_no }} </span>({{ $value->get_customer->customer_name }})</a>
                                                                    </span>
                                                                    <br>
                                                                    <span class="m-timeline-3__item-user-name">
                                                                        <span style="font-weight: bold">Qty</span> :{{ $value->total_qty }}
                                                                    </span>
                                                                </div>--}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!--End::Timeline 3 -->
                                            </div>
                                        </div>
                                    </div>
                                    <!--End::Portlet-->
                                </div>
                            </div>
                        </div>
                        <br/>
                    </div>
            @endif

            @if (in_array('sales-delivery-challan.approve',$permissionCheck))
                <!--Sales Delivary-->
                <div class="m-portlet dashboard-single-wrapper">
                    <div style="margin-left:15px; margin-top: 10px;">
                        <h5 class="m-portlet__head-text">
                            <br>
                            SALES DELIVERY
                        </h5>
                        <hr>
                    </div>

                    <div class="container-fluid">
                        <div class="row">

                            <div class="col-md-3 col-sm-6">
                                <a href="javascript:;" class="dashboard-block">
                                    <div class="rotate">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <div class="details">
                                        <span class="title">Total Requisition</span>
                                        <span class="sub">@php echo $total_sales_delivery ;@endphp</span>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <a href="javascript:;" class="dashboard-block">
                                    <div class="rotate">
                                        <i class="fa fa-tachometer"></i>
                                    </div>
                                    <div class="details">
                                        <span class="title">Total Pending</span>
                                        <span class="sub">@php echo $total_sales_delivery_pending;  @endphp</span>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <a href="javascript:;" class="dashboard-block">
                                    <div class="rotate">
                                        <i class="fa fa-tachometer"></i>
                                    </div>
                                    <div class="details">
                                        <span class="title">Total Approve</span>
                                        <span class="sub">@php echo $total_sales_delivery_apprv; @endphp</span>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                {{--<span style="margin-left: 20px; font-weight: 500;">Pending Challan </span>--}}

                                <div class="m-portlet__body display-block-list">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="m_widget2_tab1_content">
                                            <!--Begin::Timeline 3 -->
                                            @if($total_sales_delivery_pending == 0 )
                                                <div class="no-data-block">
                                                    No data available
                                                </div>
                                            @endif

                                            @if($total_sales_delivery_pending !== 0 )
                                                <div class="m-timeline-3">
                                                    <div class="m-timeline-3__items">
                                                        <div class="m-timeline-3__item">
                                                            <div class="adjust-pending-table">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Date</th>
                                                                        <th>Pending Delivery Chalan</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @foreach($sales_delivery_lists as $value)

                                                                        <tr>
                                                                            <td rowspan="2">{{ date('d-M',strtotime($value->sales_delivery_date)) }}</td>
                                                                            <td><a href="{{ url('/sales-delivery-challan') }}">{{ $value->sales_delivery_no }}</a></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>{{ $value->total_qty }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            {{--<span class="m-timeline-3__item-time">{{ date('d-M',strtotime($value->sales_delivery_date)) }}</span>
                                                            <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
															        <a href="{{ url('/sales-delivery-challan') }}" class="text-dark" > <span style="font-weight: 700">{{ $value->sales_delivery_no }} </span>({{ $value->get_customer->customer_name }})</a>
                                                                </span>
                                                                <br>
                                                                <span class="m-timeline-3__item-user-name">
                                                                    <span style="font-weight: bold">Qty</span> :{{ $value->total_qty }}
																</span>
                                                            </div>--}}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!--End::Timeline 3 -->
                                        </div>
                                    </div>
                                </div>
                                <!--End::Portlet-->
                            </div>
                        </div>
                    </div>
                    <br/>
                </div>
            @endif
            <!--Account-->
            @if (in_array('sales-invoice-voucher.approve',$permissionCheck))
                <div class="m-portlet dashboard-single-wrapper">
                    <div style="margin-left:15px; margin-top: 10px;">
                        <h5 class="m-portlet__head-text">
                            <br>
                            Account Sales Invoice Voucher
                        </h5>
                        <hr>
                    </div>
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <a href="javascript:;" class="dashboard-block">
                                    <div class="rotate">
                                        <i class="fa fa-money"></i>
                                    </div>
                                    <div class="details">
                                        <span class="title">Total Requisition</span>
                                        <span class="sub">@php echo $total_acc_sales ;@endphp</span>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <a href="javascript:;" class="dashboard-block">
                                    <div class="rotate">
                                        <i class="fa fa-tachometer"></i>
                                    </div>
                                    <div class="details">
                                        <span class="title">Total Pending</span>
                                        <span class="sub">@php echo $total_acc_sales_pending;  @endphp</span>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <a href="javascript:;" class="dashboard-block">
                                    <div class="rotate">
                                        <i class="fa fa-tachometer"></i>
                                    </div>
                                    <div class="details">
                                        <span class="title">Total Approve</span>
                                        <span class="sub">@php echo $total_acc_sales_apprv; @endphp</span>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <span style="margin-left: 20px; font-weight: 500;">Pending Vouchers </span>
                                <div class="m-portlet__body display-block-list">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="m_widget2_tab1_content">
                                            <!--Begin::Timeline 3 -->
                                            @if($total_acc_sales_pending == 0 )
                                                <div class="no-data-block">
                                                    No data available
                                                </div>
                                            @endif

                                            @if($total_acc_sales_pending !== 0 )
                                                <div class="m-timeline-3">
                                                    <div class="m-timeline-3__items">
                                                        <div class="m-timeline-3__item">
                                                            <div class="adjust-pending-table">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Date</th>
                                                                        <th>Pending Delivery Chalan</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @foreach($sales_acc_lists as $value)
                                                                        <tr>
                                                                            <td rowspan="2">{{ date('d-M',strtotime($value->sales_date)) }}</td>
                                                                            <td><a href="{{ url('/sales-invoice-voucher') }}">{{ $value->vouchers_no }} </a></td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td>{{ $value->total_qty }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                            {{--<span class="m-timeline-3__item-time">{{ date('d-M',strtotime($value->sales_date)) }}</span>
                                                            <div class="m-timeline-3__item-desc">
																<span class="m-timeline-3__item-text">
															        <a href="{{ url('/sales-invoice-voucher') }}" class="text-dark"> <span style="font-weight: 700">{{ $value->vouchers_no }} </span></a>
                                                                </span>
                                                                <br>
                                                                <span class="m-timeline-3__item-user-name">
                                                                    <span style="font-weight: bold">Qty</span> :{{ $value->total_qty }}
																</span>
                                                            </div>--}}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                        <!--End::Timeline 3 -->
                                        </div>
                                    </div>
                                </div>
                                <!--End::Portlet-->
                            </div>

                        </div>
                    </div>
                    <br/>
                </div>
            @endif
            <!--End-->

        </div>


    <style>

        .m-tabs-line .m-tabs__link{
            font-size: 12px !important;
        }

        .m-portlet__body .m-timeline-3__item-time
        {
            font-size: 12px !important;
        }

        .m-timeline-3__item-text
        {
            font-size: 12px !important;
        }
        .dashboard-block, .dashboard-block:hover, .dashboard-block:active, .dashboard-block:focus {
            color: #eee;
            text-decoration: none;
            text-shadow: 1px 1px 1px rgba(255, 255, 255, 0.5);
            outline: none;
        }

        .dashboard-block {
            position: relative;
            display: block;
            padding: 30px 20px;
            background-color: #244e44;
            opacity:0.9;
            overflow: hidden;
            margin-bottom:5px;
            color: #fff;
        }
        .dashboard-block .rotate {
            z-index: 8;
            float: right;
            height: 100%;
        }
        .dashboard-block .rotate i {
            position: absolute;
            left: 0;
            left: auto;
            right: 0;
            bottom: 0;
            display: block;
            height: 65px;
            font-size: 75px;
            -webkit-transform: rotate(-40deg);
            -moz-transform: rotate(-40deg);
            -o-transform: rotate(-40deg);
            -ms-transform: rotate(-40deg);
            transform: rotate(-40deg);
            text-shadow:1px 0 #ddd;
        }
        .dashboard-block i,.dashboard-block:hover i,.dashboard-block:active i,.dashboard-block:focus i {
            color: #e2e2e2;
        }
        .dashboard-block .more {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 16px;
            color: rgba(0, 0, 0, 0.25) !important;
            text-shadow: none;
        }
        .dashboard-block .details {
            position: relative;
            z-index: 10;
            float: left;
            margin-top: -10px;
            text-align: left;
        }
        .dashboard-block .title {
            display: block;
            margin-bottom: 1em;
            font-size: 12px;
            text-transform: uppercase;
        }
        .dashboard-block .sub {
            display: block;
            font-size: 16px;
        }
    </style>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Task', 'Hours per Day'],
                ['Production',     {{appPro()}}],
                ['Purchase',      {{ appPo() }}],
                ['Sales',   {{ appSl() }}   ]
            ]);

            var options = {
                title: 'Monthly Data',
                pieHole: 0.3,
            };

            var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
            chart.draw(data, options);
        }

        google.charts.load("current", {packages:["corechart"]});
        google.charts.setOnLoadCallback(drawChart2);
        function drawChart2() {
            var data = google.visualization.arrayToDataTable([
                ['Date', 'QTY'],
                    @foreach(sale_data() as $data)
                ['{{date('d-M',strtotime($data->sales_delivery_date))}}', {{$data->total}}],
                @endforeach
            ]);

            var options = {
                title: 'Last Four Sale',
                legend: { position: 'none' },
            };

            var chart = new google.visualization.LineChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>
@stop
