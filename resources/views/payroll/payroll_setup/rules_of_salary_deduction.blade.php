@extends('master')
@section('title','Rules Of Salary Deduction')
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
                            <span class="m-nav__link-text">Payroll</span>
                        </a>
                    </li>
                    <li class="m-nav__separator">-</li>
                    <li class="m-nav__item">
                        <a href="" class="m-nav__link">
                            <span class="m-nav__link-text">Setup</span>
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
        <div class="m-portlet m-portlet--mobile">
            <div class="m-portlet__head">
                <div class="m-portlet__head-caption">
                    <div class="m-portlet__head-title">
                        <h3 class="m-portlet__head-text">
                            @yield('title')
                        </h3>
                    </div>
                </div>
            </div>
            <div class="m-portlet__body">
                @if($data)
                <table class="table table-bordered table-hover" id="m_table_1">
                    <thead>
                    <tr>
                        <th class="w-20">For Days</th>
                        <th class="w-20">Day Of Salary Deduction</th>
                        <th class="w-20">Status</th>
                        <th class="w-20">Update</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" class="form-control form-control-sm m-input salary_deduction_for_late_attendance_id"  value="{{$data->id}}">
                            <input type="number" class="form-control form-control-sm m-input for_days"  value="{{$data->for_days}}" placeholder="For Days EX:(3)">
                        </td>
                        <td>
                            <input type="number"   class="form-control form-control-sm m-input day_of_salary_deduction"  value="{{$data->day_of_salary_deduction}}" placeholder="Salary Deduction For Day EX:(1)">
                        </td>
                        <td>
                            <select class="form-control select2 select-product status"  style="width:100%;">
                                <option value="Active" @if($data->status == "Active") {{"selected"}} @endif>Active</option>
                                <option value="Inactive"  @if($data->status == "Inactive") {{"selected"}} @endif>Inactive</option>
                            </select>
                        </td>
                        <td>
                            <input type="button"  class="btn btn-sm btn-success updateRule" value="Update">
                        </td>

                    </tr>
                    </tbody>
                </table >
               @else
                <table class="table table-bordered table-hover" id="m_table_1">
                    <thead>
                    <tr>
                        <th class="w-20">For Days</th>
                        <th class="w-20">Day Of Salary Deduction</th>
                        <th class="w-20">Status</th>
                        <th class="w-20">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="id" class="form-control form-control-sm m-input salary_deduction_for_late_attendance_id" >
                            <input type="number" name="for_days" class="form-control form-control-sm m-input for_days"  placeholder="For Days EX:(3)">
                        </td>
                        <td>
                            <input type="number"   class="form-control form-control-sm m-input day_of_salary_deduction"   placeholder="Salary Deduction For Day EX:(1)">
                        </td>
                        <td>
                            <select class="form-control select2 select-product status"  style="width:100%;">
                                <option value="Active" >Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </td>
                        <td>
                            <input type="button"  class="btn btn-sm btn-success storeRule" value="Save">
                        </td>

                    </tr>
                    </tbody>
                </table >

                    @endif

            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
    </div>
@stop

@push('custom_scripts')
<script>

    jQuery(function(){

    $("body").on("click",".storeRule",function(){
        var salary_deduction_for_late_attendance_id  = $('.salary_deduction_for_late_attendance_id').val();
        var for_days      							 = $('.for_days').val();
        var day_of_salary_deduction      			 = $('.day_of_salary_deduction').val();
        var status      							 = $('.status').val();

        var action = "{{ URL::to('rules-of-salary-deduction') }}";
        $.ajax({
            type: "post",
            url: action,
            data: {'id': salary_deduction_for_late_attendance_id, 'for_days': for_days,'day_of_salary_deduction':day_of_salary_deduction,'status':status,'_token': $('input[name=_token]').val()},
            success: function (data) {
                if(data){
                    toastr.success('Salary deduction rule Insert successfully !');
                }
                location.reload();
            }
         });

      })
    });



    jQuery(function(){
            $("body").on("click",".updateRule",function(){
            var salary_deduction_for_late_attendance_id  = $('.salary_deduction_for_late_attendance_id').val();
            var for_days      							 = $('.for_days').val();
            var day_of_salary_deduction      			 = $('.day_of_salary_deduction').val();
            var status      							 = $('.status').val();

            var action = "{{ URL::to('update-rules-of-salary') }}";
            $.ajax({
                type: "post",
                url: action,
                data: {'id': salary_deduction_for_late_attendance_id, 'for_days': for_days,'day_of_salary_deduction':day_of_salary_deduction,'status':status,'_token': $('input[name=_token]').val()},
                success: function (data) {
                    toastr.success('Salary deduction rule update successfully !');
                    //console.log(data);
                }
            });
        })
    });
</script>
@endpush


