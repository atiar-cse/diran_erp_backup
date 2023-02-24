@extends('master')
@section('title','Manage Closing')
@section('content')



    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>

    <script>
        $( function() {
            $( "#datepicker" ).datepicker();
        } );
    </script>

    <!-- BEGIN: Subheader -->
    <div class="diran-erp-main-content" id="erp-app" >
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
                                <span class="m-nav__link-text">Accounts</span>
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
                            {{--<div class="m-portlet__head-tools">
                                @if (in_array('production-entry.store',permissionCheck()))
                                    <a href="javascript:void(0)"  class="btn btn-sm btn-brand m-btn--icon m-btn--wide m-btn--sm m--margin-right-10 m-btn--air" id="addButton">
                                    <span>
                                        <i class="fa fa-list"></i>
                                        <span>Add Year Closing</span>
                                    </span>
                                    </a>
                                @endif
                                <a href="javascript:void(0)" class="btn btn-sm btn-brand m-btn--icon m-btn--wide m-btn--sm m--margin-right-10 m-btn--air" style="display: none" id="listButton">
                                    <span>
                                        <i class="fa fa-list"></i>
                                        <span>Manage Year Closing</span>
                                    </span>
                                </a>
                            </div>--}}
                        </div>
                        {{--<year-end-list></year-end-list>--}}
                        <!--begin::Form-->
                        <!--end::Form-->
                    </div>
                    <!--end::Portlet-->
                </div>
            </div>
        </div>

        <h1 align="center">

            <form action="{{url('year-closing')}}" method="post" enctype="multipart/form-data">
                @csrf
                <label style="font-family: 'Times New Roman';font-style: italic;font-size: 25px;font-weight: bold">Date: </label>
                <input type="text" required id="datepicker" name="date" style="width: 180px">
                <button class="btn btn-info" type="submit">Process Closing</button>

            </form>



        </h1>

        <section>
            <div class="row">
                <div class="tags col-lg-12">
                    <?php if (count($errors->all()) > 0) {?>
                    <div class="alert alert-dismissible margin5 alert-message" style="background: #7f8c8d;">
                        <table class="table table-bordered">
                            <thead>
                            <th>SL#</th>
                            <th>Issue</th>
                            </thead>
                            <tbody>
                            <?php $i = 1; ?>
                            @foreach ($errors->all() as $error)
                                <tr>
                                    <td><strong>No. {{ $i }}</strong></td>
                                    <td><strong>{{ $error }}</strong></td>
                                </tr>
                                <?php $i++; ?>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </section>

    </div>
@stop

@push('custom_scripts')
<script type="text/javascript" src="{!! asset('js/account/year-end.js') !!}"></script>
@endpush
