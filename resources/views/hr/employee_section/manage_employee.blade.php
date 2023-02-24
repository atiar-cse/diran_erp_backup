@extends('master')
@section('title','Manage Employee')
@section('content')
    <!-- BEGIN: Subheader -->
    <div class="diran-erp-main-content" id="erp-app">
        <div class="m-subheader ">
            <div class="d-flex align-items-center">
                <div class="mr-auto">
                    <ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
                        <li class="m-nav__item m-nav__item--home">
                            <a href="{{ url('dashboard') }}" class="m-nav__link m-nav__link--icon">
                                <i class="m-nav__link-icon la la-home"></i>
                            </a>
                        </li>
                        <li class="m-nav__separator">-</li>
                        <li class="m-nav__item">
                            <a href="" class="m-nav__link">
                                <span class="m-nav__link-text">HR</span>
                            </a>
                        </li>
                        <li class="m-nav__separator">-</li>
                        <li class="m-nav__item">
                            <a href="" class="m-nav__link">
                                <span class="m-nav__link-text">Section</span>
                            </a>
                        </li>
                        <li class="m-nav__separator">-</li>
                        <li class="m-nav__item">
                            <a href="" class="m-nav__link">
                                <span class="m-nav__link-text">@yield('title')</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="br-for-mobile-break m--visible-mobile-inline-block">
            <br>
        </div>

        <!-- END: Subheader -->
        <div class="m-content">
            <div class="row">
                <div class="col-lg-12">
                    <!--begin::Portlet-->
                    <div class="m-portlet">
                        <div class="m-portlet__head">
                            <div class="m-portlet__head-caption">
                                <div class="m-portlet__head-title">
                                    <span class="m-portlet__head-icon m--visible-desktop-inline-block">
                                        <i class="fa fa-plus-square"></i>
                                    </span>
                                    <h3 class="m-portlet__head-text">
                                        @yield('title')
                                    </h3>
                                </div>
                            </div>
                            <div class="m-portlet__head-tools">
                                {{--  @if (in_array('production-entry.store',permissionCheck()))--}}
                                <a href="javascript:void(0)"  class="btn btn-sm btn-brand m-btn--icon m-btn--wide m-btn--sm m--margin-right-10 m-btn--air" id="addButton">
                                    <span>
                                        <i class="fa fa-list"></i>
                                        <span>Add Employee</span>
                                    </span>
                                </a>
                                {{--  @endif--}}
                                <a href="javascript:void(0)" class="btn btn-sm btn-brand m-btn--icon m-btn--wide m-btn--sm m--margin-right-10 m-btn--air" style="display: none" id="listButton">
                                    <span>
                                        <i class="fa fa-list"></i>
                                        <span>Manage Termination</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                        <manage-employee-list
                                :role_list="{{json_encode($roleList)}}"
                                :department_list="{{json_encode($departmentList)}}"
                                :designation_list="{{json_encode($designationList)}}"
                                :branch_list="{{json_encode($branchList)}}"
                                :monthly_pay_grade_list="{{json_encode($monthlyPayGradeList)}}"
                                :hourly_pay_grade_list="{{json_encode($hourlyPayGradeList)}}"
                                :shift_list="{{json_encode($ShiftList)}}"
                                :type_list="{{json_encode($typeList)}}"
                                :team_list="{{json_encode($teamList)}}"
                                :religion_list="{{json_encode($religionList)}}"
                                :grade_list="{{json_encode($salaryGradeList)}}"
                                :salary_configuration_list="{{json_encode($salaryConfigurationList)}}"
                        ></manage-employee-list>
                        <!--begin::Form-->
                        <!--end::Form-->
                    </div>
                    <!--end::Portlet-->
                </div>
            </div>
        </div>
    </div>
@stop

@push('custom_scripts')
<script type="text/javascript" src="{!! asset('js/hr/manage-employee.js') !!}"></script>
@endpush
