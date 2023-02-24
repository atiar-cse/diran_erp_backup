
@extends('master')
@section('title','LC Basic Setup')
@section('content')
    <!-- BEGIN: Subheader -->
    <div class="m-subheader ">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
                    <li class="m-nav__item m-nav__item--home">
                        <a href="#" class="m-nav__link m-nav__link--icon">
                            <i class="m-nav__link-icon la la-home"></i>
                        </a>
                    </li>
                    <li class="m-nav__separator">-</li>
                    <li class="m-nav__item">
                        <a href="" class="m-nav__link">
                            <span class="m-nav__link-text">Administration</span>
                        </a>
                    </li>
                    <li class="m-nav__separator">-</li>
                    <li class="m-nav__item">
                        <a href="" class="m-nav__link">
                            <span class="m-nav__link-text">Module Setup</span>
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
                    </div>
                    <!--begin::Form-->
                        {{ Form::open(array('route' => 'config.update','id'=>'sys_config','class'=>'m-form m-form--fit m-form--label-align-right')) }}
                        <div class="m-portlet__body">
                            <input type="hidden" name="module_id" value="7">
                            <div class="form-group m-form__group row">
                                <div class="col-md-6">
                                    <label for="lc_opening_charge_chart_accid" class="col-form-label">Chart Of Accounts For 'LC Opening Charges' </label>
                                    <select class="form-control select-mini m-select2 select2" id="lc_opening_charge_chart_accid" name="config_value[]">
                                        <option>---Please Select---</option>
                                        @foreach($chartofAccountList as $row)
                                            <option value="{{$row->id}}">{{$row->chart_of_accounts_title}} ({{$row->chart_of_account_code}})</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('lc_opening_charge_chart_accid')) <span class="validation_msg"> <strong> {{ $errors->first('lc_opening_charge_chart_accid') }} </strong> </span> @endif
                                    <input type="hidden" name="config_name[]" value="lc_opening_charge_chart_accid">
                                </div>
                                <div class="col-md-6">
                                    <label for="lc_open_bank_charge_chart_accid" class="col-form-label">Chart Of Accounts For 'LC Opening Bank Expenses' <span class="requiredField">*</span></label>
                                    <select class="form-control select-mini m-select2 select2" id="lc_open_bank_charge_chart_accid" name="config_value[]">
                                        <option>---Please Select---</option>
                                        @foreach($chartofAccountList as $row)
                                            <option value="{{$row->id}}">{{$row->chart_of_accounts_title}} ({{$row->chart_of_account_code}})</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('lc_open_bank_charge_chart_accid')) <span class="validation_msg"> <strong> {{ $errors->first('lc_open_bank_charge_chart_accid') }} </strong> </span> @endif
                                    <input type="hidden" name="config_name[]" value="lc_open_bank_charge_chart_accid">
                                </div>
                                <div class="col-md-6">
                                    <label for="lc_open_insurance_amt_chart_accid" class="col-form-label">Chart Of Accounts For 'LC Opening Insurance Amount' <span class="requiredField">*</span></label>
                                    <select class="form-control select-mini m-select2 select2" id="lc_open_insurance_amt_chart_accid" name="config_value[]">
                                        <option>---Please Select---</option>
                                        @foreach($chartofAccountList as $row)
                                            <option value="{{$row->id}}">{{$row->chart_of_accounts_title}} ({{$row->chart_of_account_code}})</option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('lc_open_insurance_amt_chart_accid')) <span class="validation_msg"> <strong> {{ $errors->first('lc_open_insurance_amt_chart_accid') }} </strong> </span> @endif
                                    <input type="hidden" name="config_name[]" value="lc_open_insurance_amt_chart_accid">
                                </div>
                            </div>

                        </div>
                        <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                            <div class="m-form__actions m-form__actions--solid">
                                <div class="row">
                                    <div class="col-lg-9 m--align-right">
                                        <button type="reset" class="btn btn-sm btn-secondary">Cancel</button>
                                        <button type="submit" class="btn btn-sm btn-success" ><i class="la la-save"></i> <span>Save</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                       {{ Form::close() }}
                    <!--end::Form-->
                </div>
                <!--end::Portlet-->
            </div>
        </div>
    </div>
@stop


@push('custom_scripts')
<script type="text/javascript">

    $(document).on('change','[data-menu]',function(event){
        if(this.checked==false){
            var getMenuId = $(this).attr('data-menu');
            $('[data-formenu="'+getMenuId+'"]').prop('checked',false);
        }else{
            var getMenuId = $(this).attr('data-menu');
            $('[data-formenu="'+getMenuId+'"]').prop('checked',true);
        }
    });

    $(document).on('change','[data-formenu]',function(event){
        if(this.checked==true){
            var getMenuId = $(this).attr('data-formenu');
            $('[data-menu="'+getMenuId+'"]').prop('checked',true);
        }
    });

    var Select2= {
        init:function() {
            $(".select2").select2( {
                    placeholder: "Please select an option"
                }
            )
        }
    };
    jQuery(document).ready(function() {
            Select2.init()
        }
    );
</script>
@endpush
