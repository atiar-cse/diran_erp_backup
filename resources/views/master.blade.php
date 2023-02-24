<!DOCTYPE html>
<html lang="en">
	<!-- begin::Head -->
	<head>
		<meta charset="utf-8" />
		<title>IVL ERP | @yield('title')</title>
		<meta name="description" content="Multi column form examples">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
		<!--begin::Web font -->
		<script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
		<!-- <script src="https://unpkg.com/vuejs-datepicker"></script> -->
		<script>
			WebFont.load({
            google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
            active: function() {
                sessionStorage.fonts = true;
            }
          });
        </script>

		<?php
		$company_info= DB::table('company_infos')->first();
		if(isset($company_info->logo))
		{
		?>
			<link rel="shortcut icon" href="{{url('uploads/company_logo').'/'. $company_info->logo}}" onerror="this.src='img/1200px-Channel-i.png';" />
		<?php
		}else{
		?>
			<link rel="shortcut icon" href="{!! asset('assets/demo/default/media/img/logo/favicon.ico') !!}" />
		<?php } ?>
		<!--end::Web font -->
		<!--begin::Global Theme Styles -->
		<link href="{!! asset('assets/vendors/base/vendors.bundle.css') !!}" rel="stylesheet" type="text/css" />

		{{--Modified CSS--}}
		<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/app.css') }}">

		<!--RTL version:<link href="../../../assets/vendors/base/vendors.bundle.rtl.css') !!}" rel="stylesheet" type="text/css" />-->
		<link href="{!! asset('assets/demo/default/base/style.bundle.css') !!}" rel="stylesheet" type="text/css" />
		<!--Begin::Custom Style CSS -->
		<link href="{!! asset('assets/vendors/custom/datatables/datatables.bundle.css') !!}" rel="stylesheet" type="text/css" />
        <!--End::Custom Style CSS -->
		<!-- sweetalert-->
		<link rel="stylesheet" href="{!! asset('assets/sweetalert/sweetalert.css') !!}">
		<link href="{!! asset('assets/custom_style.css')!!}" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" type="text/css" href="{!! asset('assets/media_print.css')!!}" media="print">



		<script type="text/javascript">
            var base_url = "{{ url('/').'/' }}";
            var current_url = "{{ url()->current() }}";
		</script>
	</head>
	<!-- end::Head -->

	<!-- begin::Body -->
	<body onLoad="activeMenu()" class="m-page--fluid m--skin- m-content--skin-light2 m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">

		<!-- begin:: Page -->
		<div class="m-grid m-grid--hor m-grid--root m-page">

			<!-- BEGIN: Header -->
			<header id="m_header" class="m-grid__item m-header header-section-hide-print" m-minimize-offset="200" m-minimize-mobile-offset="200">
				<div class="m-container m-container--fluid m-container--full-height">
					<div class="m-stack m-stack--ver m-stack--desktop">

						<!-- BEGIN: Brand -->
						<div class="m-stack__item m-brand  m-brand--skin-light header-custom-top ">
							<div class="m-stack m-stack--ver m-stack--general">
								<div class="m-stack__item m-stack__item--middle m-brand__logo">
									<a href="{{ url('dashboard') }}" class="m-brand__logo-wrapper">
										<?php
										if(isset($company_info->logo))
										{
										?>
										<img width="80px" src="{{url('uploads/company_logo').'/'. $company_info->logo}}" class="img responsive" onerror="this.src='img/1200px-Channel-i.png';">
										<?php
										}else{
										?>
										<img alt="" src="{!! asset('assets/demo/default/media/img/logo/iventure_erp_logo.png') !!}" />
										<?php } ?>
									</a>
								</div>

                                <?php $configuration_notice = ConfigurationNotice();?>
								<div class="dashboard-notification">
									<marquee direction="left" behavior="scroll">{{ $configuration_notice ? $configuration_notice->title : ''}}  &nbsp;&nbsp;</marquee>
								</div>
								<div class="m-stack__item m-stack__item--middle m-brand__tools">

									<!-- BEGIN: Left Aside Minimize Toggle -->
									<a href="javascript:;" id="m_aside_left_minimize_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-desktop-inline-block">
										<span></span>
									</a>

									<!-- END -->
									<!-- BEGIN: Responsive Aside Left Menu Toggler -->
									<a href="javascript:;" id="m_aside_left_offcanvas_toggle" class="m-brand__icon m-brand__toggler m-brand__toggler--left m--visible-tablet-and-mobile-inline-block">
										<span></span>
									</a>
									<!-- END -->
									<!-- BEGIN: Responsive Header Menu Toggler -->
									<a id="m_aside_header_menu_mobile_toggle" href="javascript:;" class="m-brand__icon m-brand__toggler m--visible-tablet-and-mobile-inline-block">
										<span></span>
									</a>
									<!-- END -->
									<!-- BEGIN: Topbar Toggler -->
									<a id="m_aside_header_topbar_mobile_toggle" href="javascript:;" class="m-brand__icon m--visible-tablet-and-mobile-inline-block">
										<i class="flaticon-more"></i>
									</a>
									<!-- BEGIN: Topbar Toggler -->
								</div>
							</div>
						</div>

						<!-- END: Brand -->
						<div class="m-stack__item m-stack__item--fluid m-header-head header-custom" id="m_header_nav">
							<div class="dashboard-notification">
								<marquee direction="left">{{ $configuration_notice ? $configuration_notice->title : '' }}  &nbsp;&nbsp;</marquee>
							</div>
							<!-- BEGIN: Horizontal Menu -->
							<div class="profile-options">
								<button class="m-aside-header-menu-mobile-close  m-aside-header-menu-mobile-close--skin-dark " id="m_aside_header_menu_mobile_close_btn"><i class="la la-close"></i></button>
								<div id="m_header_menu" class="m-header-menu m-aside-header-menu-mobile m-aside-header-menu-mobile--offcanvas  m-header-menu--skin-light m-header-menu--submenu-skin-light m-aside-header-menu-mobile--skin-dark m-aside-header-menu-mobile--submenu-skin-dark ">
								</div>
							<!-- END: Horizontal Menu -->

							<?php
							$total_count = 0;
							$permissionCheck =  permissionCheckForNotification();
							$accSalesInvDt =  get_acc_sales_pending_voucher();
							$SalesPendingChalan =  get_sales_pending_challan();
							$SalesOrder =  get_sales_pending_order();

							$accPoInvDt =  get_acc_po_pending_voucher();
							$PoPending =  get_po_pending();
							$PoReq =  get_po_req_pending();

							$accBankPay =  get_acc_bank_payment_pending_voucher();
							$accCashPay =  get_acc_cash_payment_pending_voucher();

							$accBankRev =  get_acc_bank_rev_pending_voucher();
							$accCashRev =  get_acc_cash_rev_pending_voucher();
							$hrLeave    =  get_hr_leave_pending();


							if (in_array('sales-invoice.approve',$permissionCheck))
							{
								$total_count += $SalesOrder->count();
							}
							if (in_array('sales-delivery-challan.approve',$permissionCheck))
							{
								$total_count += $SalesPendingChalan->count();
							}
							if (in_array('sales-invoice-voucher.approve',$permissionCheck))
							{
								$total_count += $accSalesInvDt->count();
							}

							/* Purchase */
							if (in_array('pr-requisition.approve',$permissionCheck))
							{
								$total_count += $PoReq->count();
							}
							if (in_array('po-receive.approve',$permissionCheck))
							{
								$total_count += $PoPending->count();
							}
							if (in_array('purchase-invoice-voucher.approve',$permissionCheck))
							{
								$total_count += $accPoInvDt->count();
							}

							/*Payment*/
							if (in_array('bank-payment-vouchers.approve',$permissionCheck))
							{
								$total_count += $accBankPay->count();
							}
							if (in_array('cash-payment-vouchers.approve',$permissionCheck))
							{
								$total_count += $accCashPay->count();
							}

							/*Recevive*/
							if (in_array('bank-receipt-vouchers.approve',$permissionCheck))
							{
								$total_count += $accBankRev->count();
							}
							if (in_array('cash-receipt-vouchers.approve',$permissionCheck))
							{
								$total_count += $accCashRev->count();

							}

							/*HR*/
							if (in_array('apply-for-leave.leave-approved',$permissionCheck))
							{
								$total_count += $hrLeave->count();
							}

							?>

							<!-- BEGIN: Topbar -->
								<div id="m_header_topbar" class="m-topbar  m-stack m-stack--ver m-stack--general m-stack--fluid">
									<div class="m-stack__item m-topbar__nav-wrapper">
										<ul class="m-topbar__nav m-nav m-nav--inline">

											<li class="m-nav__item m-topbar__notifications m-topbar__notifications--img m-dropdown m-dropdown--large m-dropdown--header-bg-fill m-dropdown--arrow m-dropdown--align-center m-dropdown--mobile-full-width" m-dropdown-toggle="click" m-dropdown-persistent="1" aria-expanded="true">
												<a href="#" class="m-nav__link m-dropdown__toggle" id="m_topbar_notification_icon">
													<?php
													if($total_count > 0){?>
													<span class="m-nav__link-badge m-badge m-badge--danger"><?=$total_count;?></span> {{--m-badge--dotcc m-badge--dot-smallcc--}}
													<?php } ?>
													<span class="m-nav__link-icon"><i class="flaticon-alarm"></i></span>
												</a>
												<div class="m-dropdown__wrapper" style="z-index: 101;">
													<span class="m-dropdown__arrow m-dropdown__arrow--center"></span>
													<div class="m-dropdown__inner">
														<div class="m-dropdown__header m--align-center" style="background: url(assets/app/media/img/misc/notification_bg.jpg); background-size: cover;">
															<span class="m-dropdown__header-title"><?=$total_count;?> New</span>
															<span class="m-dropdown__header-subtitle">
																Notifications
																</span>
														</div>
														<div class="m-dropdown__body">
															<div class="m-dropdown__content">
																<ul class="nav nav-tabs m-tabs m-tabs-line m-tabs-line--brand" role="tablist">

																	{{--@if (in_array('sales-invoice-voucher.approve',$permissionCheck))
																		<li class="nav-item m-tabs__item">
																			<a class="nav-link m-tabs__link active" data-toggle="tab" href="#topbar_notifications_notifications" role="tab">
																				Acc(Sl) @if($accSalesInvDt->count() != 0 ) ({{ $accSalesInvDt->count() }}) @endif
																			</a>
																		</li>
																	@endif



																	@if (in_array('purchase-invoice-voucher.approve',$permissionCheck))
																		<li class="nav-item m-tabs__item">
																			<a class="nav-link m-tabs__link" data-toggle="tab" href="#topbar_notifications_logs_ac_po" role="tab">
																				Acc (PO) @if($accPoInvDt->count() != 0 ) ({{ $accPoInvDt->count() }}) @endif
																			</a>
																		</li>
																	@endif

																	@if (in_array('po-receive.approve',$permissionCheck))
																		<li class="nav-item m-tabs__item">
																			<a class="nav-link m-tabs__link" data-toggle="tab" href="#topbar_notifications_logs_po" role="tab">
																				PO Revcive @if($PoPending->count() != 0 ) ({{ $PoPending->count() }}) @endif
																			</a>
																		</li>
																	@endif


																	@if (in_array('pr-requisition.approve',$permissionCheck))
																		<li class="nav-item m-tabs__item">
																			<a class="nav-link m-tabs__link" data-toggle="tab" href="#topbar_notifications_logs_po_req" role="tab">
																				PO Req @if($PoReq->count() != 0 ) ({{ $PoReq->count() }}) @endif
																			</a>
																		</li>
																	@endif



																	@if (in_array('bank-payment-vouchers.approve',$permissionCheck))
																		<li class="nav-item m-tabs__item">
																			<a class="nav-link m-tabs__link" data-toggle="tab" href="#topbar_notifications_logs_bp" role="tab">
																				BPaymnet @if($accBankPay->count() != 0 ) ({{ $accBankPay->count() }}) @endif
																			</a>
																		</li>
																	@endif

																	@if (in_array('cash-payment-vouchers.approve',$permissionCheck))
																		<li class="nav-item m-tabs__item">
																			<a class="nav-link m-tabs__link" data-toggle="tab" href="#topbar_notifications_logs_cp" role="tab">
																				CPaymnet @if($accCashPay->count() != 0 ) ({{ $accCashPay->count() }}) @endif
																			</a>
																		</li>
																	@endif

																	@if (in_array('bank-receipt-vouchers.approve',$permissionCheck))
																		<li class="nav-item m-tabs__item">
																			<a class="nav-link m-tabs__link" data-toggle="tab" href="#topbar_notifications_logs_br" role="tab">
																				BReceipt @if($accBankRev->count() != 0 ) ({{ $accBankRev->count() }}) @endif
																			</a>
																		</li>
																	@endif

																	@if (in_array('cash-receipt-vouchers.approve',$permissionCheck))
																		<li class="nav-item m-tabs__item">
																			<a class="nav-link m-tabs__link" data-toggle="tab" href="#topbar_notifications_logs_cr" role="tab">
																				CReceipt @if($accCashRev->count() != 0 ) ({{ $accCashRev->count() }}) @endif
																			</a>
																		</li>
																	@endif
																	--}}
																	<li class="nav-item m-tabs__item">
																		<a class="nav-link m-tabs__link active" data-toggle="tab" href="#account" role="tab">
																			Account
																		</a>
																	</li>
																	<li class="nav-item m-tabs__item">
																		<a class="nav-link m-tabs__link" data-toggle="tab" href="#purchase" role="tab">
																			Purchase
																		</a>
																	</li>
																	<li class="nav-item m-tabs__item">
																		<a class="nav-link m-tabs__link" data-toggle="tab" href="#production" role="tab">
																			Production
																		</a>
																	</li>
																	<li class="nav-item m-tabs__item">
																		<a class="nav-link m-tabs__link" data-toggle="tab" href="#inventory" role="tab">
																			Inventory
																		</a>
																	</li>
																	<li class="nav-item m-tabs__item">
																		<a class="nav-link m-tabs__link" data-toggle="tab" href="#sales" role="tab">
																			Sales
																		</a>
																	</li>
																	<li class="nav-item m-tabs__item">
																		<a class="nav-link m-tabs__link" data-toggle="tab" href="#hr" role="tab">
																			HR
																		</a>
																	</li>
																</ul>
																<div class="tab-content">
																	<div class="tab-pane" id="topbar_notifications_notifications" role="tabpanel">
																		<div class="m-scrollable m-scroller ps ps--active-y" data-scrollable="true" data-height="250" data-mobile-height="200" style="height: 250px; overflow: hidden;">
																			<div class="m-list-timeline m-list-timeline--skin-light">
																				<div class="m-list-timeline__items">
																					<?php
																					if($accSalesInvDt->count() > 0)
																					{
																					foreach ($accSalesInvDt as $value){
																					?>
																					<div class="m-list-timeline__item">
																						<span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
																						<span class="m-list-timeline__text"><a href="{{url('sales-invoice-voucher')}}"><?=$value->vouchers_no;?> </a><span class="m-badge m-badge--danger m-badge--wide">pending</span></span>
																						<span class="m-list-timeline__time"><?=date('d-M',strtotime($value->sales_date));?></span>
																					</div>
																					<?php } }else{ echo "No Data"; } ?>
																				</div>
																			</div>
																			<div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 4px; height: 250px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 223px;"></div></div></div>
																	</div>
																	<div class="tab-pane" id="topbar_notifications_events" role="tabpanel">
																		<div class="m-scrollable m-scroller ps" data-scrollable="true" data-height="250" data-mobile-height="200" style="height: 250px; overflow: hidden;">

																			<div class="m-list-timeline m-list-timeline--skin-light">
																				<div class="m-list-timeline__items">
																					<?php
																					if($SalesPendingChalan->count() > 0)
																					{
																					foreach ($SalesPendingChalan as $value){
																					?>
																					<div class="m-list-timeline__item">
																						<span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
																						<span class="m-list-timeline__text"><a href="{{url('sales-delivery-challan')}}"><?=$value->challan_no;?> </a><span class="m-badge m-badge--danger m-badge--wide">pending</span></span>
																						<span class="m-list-timeline__time"><?=date('d-M',strtotime($value->challan_date));?></span>

																					</div>
																					<?php } }else{ echo "No Data"; } ?>
																				</div>
																			</div>
																			<div class="ps__rail-x" style="left: 0px; bottom: 0px;">
																				<div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;">
																				</div>
																			</div>
																			<div class="ps__rail-y" style="top: 0px; right: 4px;">
																				<div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;">
																				</div>
																			</div>
																		</div>
																	</div>
																	<div class="tab-pane" id="topbar_notifications_logs" role="tabpanel">
																		<div class="m-scrollable m-scroller ps" data-scrollable="true" data-height="250" data-mobile-height="200" style="height: 250px; overflow: hidden;">
																			<div class="m-list-timeline m-list-timeline--skin-light">
																				<div class="m-list-timeline__items">
																					<?php
																					if($SalesOrder->count() > 0)
																					{
																					foreach ($SalesOrder as $value){
																					?>
																					<div class="m-list-timeline__item">
																						<span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
																						<span class="m-list-timeline__text"><a href="{{url('sales-invoice')}}"><?=$value->sales_invoices_no;?> </a><span class="m-badge m-badge--danger m-badge--wide">pending</span></span>
																						<span class="m-list-timeline__time"><?=date('d-M',strtotime($value->sales_invoices_date));?></span>
																					</div>
																					<?php } }else{ echo "No Data"; }?>
																				</div>
																			</div>
																			<div class="ps__rail-x" style="left: 0px; bottom: 0px;">
																				<div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;">
																				</div>
																			</div>
																			<div class="ps__rail-y" style="top: 0px; right: 4px;">
																				<div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;">
																				</div>
																			</div>
																		</div>
																	</div>

																	{{--Accounts--}}
																	<div class="tab-pane active" id="account">
																		<div class="m-scrollable m-scroller ps ps--active-y" data-scrollable="true" data-height="250" data-mobile-height="200" style="height: 250px; overflow: hidden;">
																			<div class="m-list-timeline m-list-timeline--skin-light">
																				<div class="m-list-timeline__items">

																					<div class="m-list-timeline__item">
																						<ul>
																							@if (in_array('cash-receipt-vouchers.approve',$permissionCheck))
																								<li class="nav-item m-tabs__item">
																									<a class="nav-link m-tabs__link" data-toggle="collapse" href="#cashreceipt">
																										CReceipt @if($accCashRev->count() != 0 ) ({{ $accCashRev->count() }}) @endif
																									</a>
																									<div class="collapse" id="cashreceipt">
																											<?php
																											if($accCashRev->count() > 0)
																											{
																											foreach ($accCashRev as $value){
																											?>
																											<div class="m-list-timeline__item">
																												<span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
																												<span class="m-list-timeline__text"><a href="{{url('cash-receipt-voucher')}}"><?=$value->receipt_transaction_no;?> </a><span class="m-badge m-badge--danger m-badge--wide">pending</span></span>
																												<span class="m-list-timeline__time"><?=date('d-M',strtotime($value->receipt_date));?></span>
																											</div>
																											<?php } }else{ echo "No Data"; }?>
																									</div>
																								</li>
																							@endif
																							@if (in_array('bank-payment-vouchers.approve',$permissionCheck))
																								<li class="nav-item m-tabs__item">
																									<a class="nav-link m-tabs__link" data-toggle="collapse" href="#bpayment">
																										BPaymnet @if($accBankPay->count() != 0 ) ({{ $accBankPay->count() }}) @endif
																									</a>
																									<div class="collapse" id="bpayment">
                                                                                                        <?php
                                                                                                        if($accBankPay->count() > 0)
                                                                                                        {
                                                                                                        foreach ($accBankPay as $value){
                                                                                                        ?>
																										<div class="m-list-timeline__item">
																											<span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
																											<span class="m-list-timeline__text"><a href="{{url('bank-payment-voucher')}}"><?=$value->payment_transaction_no;?> </a><span class="m-badge m-badge--danger m-badge--wide">pending</span></span>
																											<span class="m-list-timeline__time"><?=date('d-M',strtotime($value->payment_date));?></span>
																										</div>
                                                                                                        <?php } }else{ echo "No Data"; }?>
																									</div>
																								</li>
																							@endif
																							@if (in_array('cash-payment-vouchers.approve',$permissionCheck))
																								<li class="nav-item m-tabs__item">
																									<a class="nav-link m-tabs__link" data-toggle="collapse" href="#cpayment">
																										CPaymnet @if($accCashPay->count() != 0 ) ({{ $accCashPay->count() }}) @endif
																									</a>
																									<div class="collapse" id="cpayment">
                                                                                                        <?php
                                                                                                        if($accCashPay->count() > 0)
                                                                                                        {
                                                                                                        foreach ($accCashPay as $value){
                                                                                                        ?>
																										<div class="m-list-timeline__item">
																											<span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
																											<span class="m-list-timeline__text"><a href="{{url('cash-payment-voucher')}}"><?=$value->payment_transaction_no;?> </a><span class="m-badge m-badge--danger m-badge--wide">pending</span></span>
																											<span class="m-list-timeline__time"><?=date('d-M',strtotime($value->payment_date));?></span>
																										</div>
                                                                                                        <?php } }else{ echo "No Data"; }?>
																									</div>
																								</li>
																							@endif
																							@if (in_array('bank-receipt-vouchers.approve',$permissionCheck))
																								<li class="nav-item m-tabs__item">
																									<a class="nav-link m-tabs__link" data-toggle="collapse" href="#breceipt">
																										BReceipt @if($accBankRev->count() != 0 ) ({{ $accBankRev->count() }}) @endif
																									</a>
																									<div class="collapse" id="breceipt">
                                                                                                        <?php
                                                                                                        if($accBankRev->count() > 0)
                                                                                                        {
                                                                                                        foreach ($accBankRev as $value){
                                                                                                        ?>
																										<div class="m-list-timeline__item">
																											<span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
																											<span class="m-list-timeline__text"><a href="{{url('bank-payment-voucher')}}"><?=$value->receipt_transaction_no;?> </a><span class="m-badge m-badge--danger m-badge--wide">pending</span></span>
																											<span class="m-list-timeline__time"><?=date('d-M',strtotime($value->reciept_date));?></span>
																										</div>
                                                                                                        <?php } }else{ echo "No Data"; }?>
																									</div>
																								</li>
																							@endif
																							@if (in_array('cash-receipt-vouchers.approve',$permissionCheck))
																								<li class="nav-item m-tabs__item">
																									<a class="nav-link m-tabs__link" data-toggle="collapse" href="#creceipt">
																										CReceipt @if($accCashRev->count() != 0 ) ({{ $accCashRev->count() }}) @endif
																									</a>
																									<div class="collapse" id="creceipt">
                                                                                                        <?php
                                                                                                        if($accCashRev->count() > 0)
                                                                                                        {
                                                                                                        foreach ($accCashRev as $value){
                                                                                                        ?>
																										<div class="m-list-timeline__item">
																											<span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
																											<span class="m-list-timeline__text"><a href="{{url('cash-receipt-voucher')}}"><?=$value->receipt_transaction_no;?> </a><span class="m-badge m-badge--danger m-badge--wide">pending</span></span>
																											<span class="m-list-timeline__time"><?=date('d-M',strtotime($value->receipt_date));?></span>
																										</div>
                                                                                                        <?php } }else{ echo "No Data"; }?>
																									</div>
																								</li>
																							@endif
																						</ul>
																					</div>
																				</div>
																			</div>
																			<div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 4px; height: 250px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 223px;"></div></div></div>
																	</div>

																	{{--Purchase--}}
																	<div class="tab-pane" id="purchase">
																		<div class="m-scrollable m-scroller ps ps--active-y" data-scrollable="true" data-height="250" data-mobile-height="200" style="height: 250px; overflow: hidden;">
																			<div class="m-list-timeline m-list-timeline--skin-light">
																				<div class="m-list-timeline__items">

																					<div class="m-list-timeline__item">
																						<ul>
																							@if (in_array('purchase-invoice-voucher.approve',$permissionCheck))
																								<li class="nav-item m-tabs__item">
																									<a class="nav-link m-tabs__link" data-toggle="collapse" href="#accpo">
																										Acc (PO) @if($accPoInvDt->count() != 0 ) ({{ $accPoInvDt->count() }}) @endif
																									</a>
																									<div class="collapse" id="accpo">
                                                                                                        <?php
                                                                                                        if($accPoInvDt->count() > 0)
                                                                                                        {
                                                                                                        foreach ($accPoInvDt as $value){
                                                                                                        ?>
																										<div class="m-list-timeline__item">
																											<span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
																											<span class="m-list-timeline__text"><a href="{{url('purchase-invoice-voucher')}}"><?=$value->vouchers_no;?> </a><span class="m-badge m-badge--danger m-badge--wide">pending</span></span>
																											<span class="m-list-timeline__time"><?=date('d-M',strtotime($value->purchase_date));?></span>
																										</div>
                                                                                                        <?php } }else{ echo "No Data"; }?>
																									</div>
																								</li>
																							@endif

																							@if (in_array('pr-requisition.approve',$permissionCheck))
																								<li class="nav-item m-tabs__item">
																									<a class="nav-link m-tabs__link" data-toggle="collapse" href="#poreq">
																										PO Req @if($PoReq->count() != 0 ) ({{ $PoReq->count() }}) @endif
																									</a>
																									<div class="collapse" id="poreq">
                                                                                                        <?php
                                                                                                        if($PoReq->count() > 0)
                                                                                                        {
                                                                                                        foreach ($PoReq as $value){
                                                                                                        ?>
																										<div class="m-list-timeline__item">
																											<span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
																											<span class="m-list-timeline__text"><a href="{{url('pr-requisition')}}"><?=$value->purchase_requisition_no;?> </a><span class="m-badge m-badge--danger m-badge--wide">pending</span></span>
																											<span class="m-list-timeline__time"><?=date('d-M',strtotime($value->purchase_requisition_date));?></span>
																										</div>
                                                                                                        <?php } }else{ echo "No Data"; }?>
																									</div>
																								</li>
																							@endif
																						</ul>
																					</div>
																				</div>
																			</div>
																			<div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 4px; height: 250px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 223px;"></div></div></div>
																	</div>

																	{{--Production--}}
																	<div class="tab-pane" id="production">
																		<div class="m-scrollable m-scroller ps ps--active-y" data-scrollable="true" data-height="250" data-mobile-height="200" style="height: 250px; overflow: hidden;">
																			<div class="m-list-timeline m-list-timeline--skin-light">
																				<div class="m-list-timeline__items">

																					<div class="m-list-timeline__item">
																						<ul>
																							No Data
																						</ul>
																					</div>
																				</div>
																			</div>
																			<div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 4px; height: 250px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 223px;"></div></div></div>
																	</div>

																	{{--Inventory--}}
																	<div class="tab-pane" id="inventory">
																		<div class="m-scrollable m-scroller ps ps--active-y" data-scrollable="true" data-height="250" data-mobile-height="200" style="height: 250px; overflow: hidden;">
																			<div class="m-list-timeline m-list-timeline--skin-light">
																				<div class="m-list-timeline__items">

																					<div class="m-list-timeline__item">
																						<ul>
																							No Data
																						</ul>
																					</div>
																				</div>
																			</div>
																			<div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 4px; height: 250px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 223px;"></div></div></div>
																	</div>

																	{{--Sales--}}
																	<div class="tab-pane" id="sales">
																		<div class="m-scrollable m-scroller ps ps--active-y" data-scrollable="true" data-height="250" data-mobile-height="200" style="height: 250px; overflow: hidden;">
																			<div class="m-list-timeline m-list-timeline--skin-light">
																				<div class="m-list-timeline__items">

																					<div class="m-list-timeline__item">
																						<ul>
																							@if (in_array('sales-delivery-challan.approve',$permissionCheck))
																								<li class="nav-item m-tabs__item">
																									<a class="nav-link m-tabs__link" data-toggle="collapse" href="#collsales1" role="tab" >
																										Sales Delivery @if($SalesPendingChalan->count() != 0 ) ({{ $SalesPendingChalan->count() }}) @endif
																									</a>
																									<div class="collapse" id="collsales1">
                                                                                                        <?php
                                                                                                        if($SalesOrder->count() > 0)
                                                                                                        {
                                                                                                        foreach ($SalesOrder as $value){
                                                                                                        ?>
																										<div class="m-list-timeline__item">
																											<span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
																											<span class="m-list-timeline__text"><a href="{{url('sales-invoice')}}"><?=$value->sales_invoices_no;?> </a><span class="m-badge m-badge--danger m-badge--wide">pending</span></span>
																											<span class="m-list-timeline__time"><?=date('d-M',strtotime($value->sales_invoices_date));?></span>
																										</div>
                                                                                                        <?php } }else{ echo "No Data"; }?>
																									</div>
																								</li>
																							@endif
																						</ul>


																					</div>
																				</div>
																			</div>
																			<div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 4px; height: 250px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 223px;"></div></div></div>
																	</div>

																	{{--HR--}}
																	<div class="tab-pane" id="hr">
																		<div class="m-scrollable m-scroller ps ps--active-y" data-scrollable="true" data-height="250" data-mobile-height="200" style="height: 250px; overflow: hidden;">
																			<div class="m-list-timeline m-list-timeline--skin-light">
																				<div class="m-list-timeline__items">

																					<div class="m-list-timeline__item">
																						<ul>
																							@if (in_array('apply-for-leave.leave-approved',$permissionCheck))
																								<li class="nav-item m-tabs__item">
																									<a class="nav-link m-tabs__link" data-toggle="collapse" href="#hrLeave" >
																										Pending Leave @if($hrLeave->count() != 0 ) ({{ $hrLeave->count() }}) @endif
																									</a>
																									<div class="collapse" id="hrLeave">
                                                                                                        <?php
                                                                                                        if($hrLeave->count() > 0)
                                                                                                        {
                                                                                                        foreach ($hrLeave as $value){
                                                                                                        ?>
																										<div class="m-list-timeline__item">
																											<span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
																											<span class="m-list-timeline__text"><a href="{{url('apply-for-leave')}}"><?=$value->employee->first_name.' '.$value->employee->last_name;?> </a></span>
																											<span class="m-list-timeline__time"><?=date('d-M',strtotime($value->application_from_date));?></span>
																										</div>
                                                                                                        <?php } }else{ echo "No Data"; }?>
																									</div>
																								</li>
																							@endif
																						</ul>
																					</div>
																				</div>
																			</div>
																			<div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 4px; height: 250px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 223px;"></div></div></div>
																	</div>

																</div>
															</div>
														</div>
													</div>
												</div>
											</li>

											<li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"></li>
											<li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"></li>
											<li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"></li>
											<li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"></li>
											<li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"></li>
											<li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"></li>
											<li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"></li>
											<li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"></li>
											<li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"></li>
											<li class="m-nav__item m-topbar__user-profile m-topbar__user-profile--img  m-dropdown m-dropdown--medium m-dropdown--arrow m-dropdown--header-bg-fill m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light"
												m-dropdown-toggle="click">
												<a href="#" class="m-nav__link m-dropdown__toggle">
													<span class="m-topbar__username capitalize text-dark">Hi, {!!  Auth::user()->user_name !!}</span>
													<span class="m-topbar__userpic">
														<img src="@if(Auth::user()->user_photo !=''){!! url('uploads/user_photo/'.Auth::user()->user_photo) !!} @else {{ url('assets/app/media/img/users/user2.png')}} @endif" class="m--img-rounded m--marginless" alt="User" style="width: 30px;height: 30px"/>
													</span>													
												</a>
												<div class="m-dropdown__wrapper">
													<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
													<div class="m-dropdown__inner">
														<div class="m-dropdown__header m--align-center" style="background: url(../../../assets/app/media/img/misc/user_profile_bg.jpg); background-size: cover;">

															<div class="m-card-user m-card-user--skin-dark">
																<div class="m-card-user__pic">
																	<img  src="@if(Auth::user()->user_photo !=''){!! url('uploads/user_photo/'.Auth::user()->user_photo) !!} @else {{ url('assets/app/media/img/users/user2.png')}} @endif" class="m--img-rounded m--marginless" alt="User"  style="width: 80px;height: 80px"/>

																	<!--
							<span class="m-type m-type--lg m--bg-danger"><span class="m--font-light">S<span><span>-->
																</div>
																<div class="m-card-user__details">
																	<span class="m-card-user__name m--font-weight-500">{!!  Auth::user()->user_name !!}</span>
																	<a href="" class="m-card-user__email m--font-weight-300 m-link">{!!  Auth::user()->email !!}</a>
																</div>
															</div>
														</div>
														<div class="m-dropdown__body">
															<div class="m-dropdown__content">
																<ul class="m-nav m-nav--skin-light">
																	<li class="m-nav__section m--hide">
																		<span class="m-nav__section-text">Section</span>
																	</li>
																	<li class="m-nav__item">
																		<a href="{{url('user/'. Auth::user()->id  .'/edit')}}" class="m-nav__link">
																			<i class="m-nav__link-icon flaticon-profile-1"></i>
																			<span class="m-nav__link-title">
																				<span class="m-nav__link-wrap">
																					<span class="m-nav__link-text">My Profile</span>
																					<span class="m-nav__link-badge"><span class="m-badge m-badge--success">2</span></span>
																				</span>
																			</span>
																		</a>
																	</li>
																	<li class="m-nav__item">
																		<a href="" class="m-nav__link">
																			<i class="m-nav__link-icon flaticon-lifebuoy"></i>
																			<span class="m-nav__link-text">Support</span>
																		</a>
																	</li>
																	<li class="m-nav__separator m-nav__separator--fit">
																	</li>
																	<li class="m-nav__item">
																		<a href="{{URL::to('logout')}}" class="btn m-btn--pill    btn-secondary m-btn m-btn--custom m-btn--label-brand m-btn--bolder">Logout</a>
																	</li>
																</ul>
															</div>
														</div>
													</div>
												</div>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<!-- END: Topbar -->
						</div>
					</div>
				</div>
			</header>

			<!-- END: Header -->




			<!-- begin::Body -->
			<div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">

				<!-- BEGIN: Left Aside -->
				<button class="m-aside-left-close  m-aside-left-close--skin-light " id="m_aside_left_close_btn"><i class="la la-close"></i></button>
				<div id="m_aside_left" class="m-grid__item	m-aside-left  m-aside-left--skin-light">

					<!-- BEGIN: Aside Menu -->
					<div id="m_ver_menu" class="m-aside-menu  m-aside-menu--skin-light m-aside-menu--submenu-skin-light " m-menu-vertical="1" m-menu-scrollable="1" m-menu-dropdown-timeout="500" style="position: relative;">
						<ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow ">
							<li class="m-menu__item " aria-haspopup="true">
								<a href="{{ route('dashboard') }}" class="m-menu__link ">
									<i class="m-menu__link-icon flaticon-line-graph"></i>
									<span class="m-menu__link-title">
										<span class="m-menu__link-wrap">
											<span class="m-menu__link-text">Dashboard</span>
										</span>
									</span>
								</a>
							</li>

                            <?php
                            $sideMenu = showMenu();

                            $menuItem = '';
                            if($sideMenu){
                                foreach ($sideMenu as $key => $value) {
                                    $menuItem .= '<li class="m-menu__item  m-menu__item--submenu" aria-haspopup="true" m-menu-submenu-toggle="hover">
                             						   <a href="javascript:;" class="m-menu__link m-menu__toggle">
															<i class="m-menu__link-icon '.$value['icon_class'].'"></i>
															<span class="m-menu__link-text">'.$value['module_name'].'</span>
															<i class="m-menu__ver-arrow la la-angle-right"></i>
														</a>';

                                    if($value['sub_menu']){
                                        $menuItem .= '<div class="m-menu__submenu "><span class="m-menu__arrow"></span>';
                                        $menuItem .= '<ul class="m-menu__subnav">';

                                        foreach($value['sub_menu'] as $menu){
                                            if($menu['menu_url'] != '' || $menu['sub_menu']){
                                                $menuItem .= '<li class="m-menu__item  m-menu__item--submenu" aria-haspopup="true" m-menu-submenu-toggle="hover">
																	<a href="'.($menu['menu_url'] ? route($menu['menu_url']) : 'javascript:void(0)').'" class="m-menu__link m-menu__toggle">
																		<i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i>
																			<span class="m-menu__link-text">'.$menu['menu_name'].'</span>' .($menu['menu_url'] ? '' : '<i class="m-menu__ver-arrow la la-angle-right"></i>'). '</a>';

															if($menu['sub_menu']){
																$menuItem .= '<div class="m-menu__submenu ">
																				<span class="m-menu__arrow"></span>
																					<ul class="m-menu__subnav">';
																foreach($menu['sub_menu'] as $subMenu){
																	$menuItem .= '<li class="m-menu__item " aria-haspopup="true">';
																	$menuItem .= '<a href="'.($subMenu['menu_url'] ? route($subMenu['menu_url']) : 'javascript:void(0)').'" class="m-menu__link">
																					<i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i>
																					<span class="m-menu__link-text">'.$subMenu['menu_name'].'</span></a>';
																	$menuItem .= '</li>';
																}
																$menuItem .= '</ul></div>';
															}
															$menuItem .= '</li>';
                                            			}
                                        			}
                                        $menuItem .= '</ul>';
                                        $menuItem .= '</div>';
                                    }
                                    $menuItem .= '</li>';
                                }
                            }
                            echo $menuItem;
                            ?>
						</ul>
					</div>

					<!-- END: Aside Menu -->
				</div>

				<!-- END: Left Aside -->
				<div class="m-grid__item m-grid__item--fluid m-wrapper">
					<!-- BEGIN: Middle Content -->

					@yield('content')


					<!-- END: Middle Content -->
				</div>
			</div>
			<!-- end:: Body -->

			<!-- begin::Footer -->
			<footer class="m-grid__item		m-footer ">
				<div class="m-container m-container--fluid m-container--full-height m-page__container">
					<div class="m-stack m-stack--flex-tablet-and-mobile m-stack--ver m-stack--desktop">
						<div class="m-stack__item m-stack__item--left m-stack__item--middle m-stack__item--last">
							<span class="m-footer__copyright">
								2018 &copy; I-Venture ERP by <a href="http://iventurebd.com/" class="m-link"><img src="{{ asset('assets/images/logo/i-venture.png') }}" title="I-Venture"></a>
							</span>
						</div>
						<div class="m-stack__item m-stack__item--right m-stack__item--middle m-stack__item--first">
							<ul class="m-footer__nav m-nav m-nav--inline m--pull-right">
								<li class="m-nav__item">
									<a href="#" class="m-nav__link">
										<span class="m-nav__link-text">About</span>
									</a>
								</li>
								<li class="m-nav__item">
									<a href="#" class="m-nav__link">
										<span class="m-nav__link-text">Privacy</span>
									</a>
								</li>
								<li class="m-nav__item">
									<a href="#" class="m-nav__link">
										<span class="m-nav__link-text">FAQ</span>
									</a>
								</li>
								<li class="m-nav__item m-nav__item">
									<a href="#" class="m-nav__link" data-toggle="m-tooltip" title="Support Center" data-placement="left">
										<i class="m-nav__link-icon flaticon-info m--icon-font-size-lg3"></i>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</footer>

			<!-- end::Footer -->
		</div>

		<!-- end:: Page -->
		<!-- begin::Scroll Top -->
		<div id="m_scroll_top" class="m-scroll-top">
			<i class="la la-arrow-up"></i>
		</div>

		<!-- end::Scroll Top -->

		<!--begin::Global Theme Bundle -->
		<script src="{!! asset('assets/vendors/base/vendors.bundle.js') !!}" type="text/javascript"></script>
		<script src="{!! asset('assets/demo/default/base/scripts.bundle.js') !!}" type="text/javascript"></script>

		<!--end::Global Theme Bundle -->
		<!--begin::Page Scripts -->
		<script src="{!! asset('assets/demo/default/custom/crud/forms/widgets/select2.js') !!}" type="text/javascript"></script>
		<script src="{!! asset('assets/demo/default/custom/crud/forms/widgets/bootstrap-datepicker.js') !!}" type="text/javascript"></script>
		<script src="{!! asset('assets/demo/default/custom/crud/forms/widgets/bootstrap-timepicker.js') !!}" type="text/javascript"></script>
		<script src="{!! asset('assets/demo/default/custom/crud/forms/widgets/form-repeater.js') !!}" type="text/javascript"></script>
		<script src="{!! asset('assets/demo/default/custom/crud/forms/widgets/bootstrap-select.js') !!}" type="text/javascript"></script>
		{{--<script src="{!! asset('assets/demo/default/custom/crud/metronic-datatable/scrolling/horizontal.js') !!}" type="text/javascript"></script>--}}
		<!--end::Global Theme Bundle -->
		<!--begin::Page Vendors -->
		<script src="{!! asset('assets/vendors/custom/datatables/datatables.bundle.js')!!}" type="text/javascript"></script>
		<!--end::Page Vendors -->
		<!--begin::Page Scripts -->
		{{--<script src="{!! asset('assets/demo/default/custom/crud/datatables/basic/paginations.js') !!}" type="text/javascript"></script>--}}
		<!--end::Page Scripts -->
		<!--begin::Page Scripts -->
		<script src="{!! asset('assets/app/js/dashboard.js') !!}" type="text/javascript"></script>
		<script src="{!! asset('assets/sweetalert/sweetalert-dev.js') !!}"></script>
		<!--end::Page Vendors -->
		<script src="{!! asset('assets/demo/default/custom/components/base/toastr.js') !!}" type="text/javascript"></script>
		<script src="{!! asset('assets/demo/default/custom/components/base/sweetalert2.js') !!}" type="text/javascript"></script>
		<!--end::Page Scripts -->
		<!--begin::wizard -->
		<script src="{!! asset('assets/demo/default/custom/crud/wizard/wizard.js') !!}" type="text/javascript"></script>
		<!--end::wizard-->

		<script type="text/javascript">
            function activeMenu(){
                $('a[href="' + current_url + '"]').parents('.m-menu__item').addClass('m-menu__item--open');
                $('a[href="' + current_url + '"]').parent('.m-menu__item').addClass('m-menu__item--active');
            }
		</script>

		<script>

            $(document).on('click', '.delete', function () {
                var actionTo = $(this).attr('href');
                var token = $(this).attr('data-token');
                var id = $(this).attr('data-id');
                swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this imaginary file!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                url: actionTo,
                                type: 'post',
                                data: {_method: 'delete', _token: token},
                                success: function (data) {
                                    if (data == 'hasForeignKey') {
                                        swal({
                                            title: "Oops!",
                                            text: "This data is used anywhere",
                                            type: "error"
                                        });
                                    } else if (data == 'success') {
                                        swal({
                                                title: "Deleted!",
                                                text: "Your information delete successfully.",
                                                type: "success"
                                            },
                                            function (isConfirm) {
                                                if (isConfirm) {
                                                    $('.' + id).fadeOut();
                                                }
                                            });
                                    } else {
                                        swal({
                                            title: "Deleted!",
                                            text: "Something Error Found !, Please try again.",
                                            type: "error"
                                        });
                                    }
                                }

                            });
                        } else {
                            swal("Cancelled", "Your data is safe .", "error");
                        }
                    });
                return false;
            });


			//////////////////Employee Permanent Alert/////////////////////////

            $(document).on('click', '.update_permanent', function () {
                var actionTo = $(this).attr('href');
                var token = $(this).attr('data-token');
                var id = $(this).attr('data-id');
                //alert('ase');
                swal({
                        title: "Are you sure want to approve?",
                        text: "You will not be able to recover this imaginary file!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, approve it!",
                        closeOnConfirm: false
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                url: actionTo,
                                type: 'post',
                                data: {_method: 'get', _token: token},
                                //alert(actionTo);
                                success: function (data) {
                                    console.log(data.data);
                                    if (data == 'hasForeignKey') {
                                        swal({
                                            title: "Oops!",
                                            text: "This data is used anywhere",
                                            type: "error"
                                        });
                                    } else if (data == 'success') {
                                        swal({
                                                title: "Approved!",
                                                text: "Your information approved successfully.",
                                                type: "success"
                                            },
                                            function (isConfirm) {
                                                if (isConfirm) {
                                                    $('.' + id).fadeOut();
                                                }
                                            });
                                    } else {
                                        swal({
                                            title: "Error",
                                            text: "Some Error Found !, Please try again.",
                                            type: "error"
                                        });
                                    }
                                }

                            });
                        } else {
                            swal("Cancelled", "Your data is safe .", "error");
                        }
                    });
                return false;
            });



            //////////////////Employee Permanent Alert/////////////////////////


            /////////////Leave Approved/////////

            $(document).on('click', '.approvedEmpLeave', function () {
                var actionTo = $(this).attr('href');
                var token = $(this).attr('data-token');
                var id = $(this).attr('data-id');
                //alert('ase');
                swal({
                        title: "Are you sure want to approve?",
                        text: "You will not be able to recover this imaginary file!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, approve it!",
                        closeOnConfirm: false
                    },
                    function (isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                url: actionTo,
                                type: 'post',
                                data: {_method: 'get', _token: token},
                                //alert(actionTo);
                                success: function (data) {
                                    console.log(data.data);
                                    if (data == 'hasForeignKey') {
                                        swal({
                                            title: "Oops!",
                                            text: "This data is used anywhere",
                                            type: "error"
                                        });
                                    } else if (data == 'success') {
                                        swal({
                                                title: "Approved!",
                                                text: "Your information approved successfully.",
                                                type: "success"
                                            },
                                            function (isConfirm) {
                                                if (isConfirm) {
                                                    $('.' + id).fadeOut();
                                                }
                                            });
                                    } else {
                                        swal({
                                            title: "Error",
                                            text: "Some Error Found !, Please try again.",
                                            type: "error"
                                        });
                                    }
                                }

                            });
                        } else {
                            swal("Cancelled", "Your data is safe .", "error");
                        }
                    });
                return false;
            });

		</script>
		@stack('custom_scripts')
	</body>
	<!-- end::Body -->
</html>
