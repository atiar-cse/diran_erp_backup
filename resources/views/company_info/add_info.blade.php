@extends('master')
@section('title','Add Company Info')
@section('content')
    @push('custom_scripts')
    <script>
        $(function() {
            $.validator.addMethod("alphabetsnspace", function(value, element) {
                return this.optional(element) || /^[a-zA-Z-_. ]*$/.test(value);
            });

            $( "#company-form" ).validate({
                rules: {
                    name: {
                        required: true,
                        alphabetsnspace: true
                    },

                    address: {
                        required: true,
                    },
                },
                messages: {
                    name:{
                        required:"<span style='color:red'>The name  field  is required</span>",
                        alphabetsnspace: "<span style='color:red'>Please Enter Only Letters</span>"
                    },

                    address:{
                        required:"<span style='color:red'>The Address  field  is required</span>",

                    },
                }
            });

        });
    </script>
    @endpush
    <!-- BEGIN: Subheader -->
    <div class="m-subheader ">
        <div class="d-flex align-items-center">
            <div class="mr-auto">
                <ul class="m-subheader__breadcrumbs m-nav m-nav--inline">
                    <li class="m-nav__item m-nav__item--home">
                        <a href="{{url('dashboard')}}" class="m-nav__link m-nav__link--icon">
                            <i class="m-nav__link-icon la la-home"></i>
                        </a>
                    </li>
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
                    <section>
                        <div class="row">
                            <div class="tags col-lg-12">
                                @if(session()->has('successMsg'))
                                    <div class="alert alert-success alert-dismissible margin5 alert-message">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                        <strong>{{ session()->get('successMsg') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </section>

                    <!--begin::Form-->
                    @if(isset($editModeData))
                        {{ Form::model($editModeData, array('route' => array('Company-Info.update', 1),'enctype'=>'multipart/form-data', 'method' => 'PUT','files' => 'true','id' => 'company-form','class'=>'m-form m-form--fit m-form--label-align-right')) }}
                    @else
                        {{ Form::open(array('route' => 'Company-Info.store','enctype'=>'multipart/form-data','id'=>'company-form','class'=>'m-form m-form--fit m-form--label-align-right')) }}
                    @endif
                    <div class="m-portlet__body">
                        <div class="form-group m-form__group row">
                            <label for="user_name" class="col-lg-3 col-form-label">Company Name <span class="requiredField">*</span> </label>
                            <div class="col-lg-6">
                                {!! Form::text('name', Input::old('name'), $attributes = array('class'=>'form-control form-control-sm m-input','id'=>'name','placeholder'=>'Enter Company Name','autocomplete'=>'off')) !!}
                                @if($errors->has('name')) <span class="validation_msg"> <strong> {{ $errors->first('name') }} </strong> </span> @endif
                            </div>

                        </div>
                        <div class="form-group m-form__group row">
                            <label for="email" class="col-lg-3 col-form-label">Address <span class="requiredField">*</span> </label>
                            <div class="col-lg-6">
                                {!! Form::textarea('address', Input::old('address'), $attributes = array('class'=>'form-control form-control-sm m-input','id'=>'address','rows' => 4, 'cols' => 54,'placeholder'=>'Enter Address','autocomplete'=>'off')) !!}
                                @if($errors->has('address')) <span class="validation_msg"> <strong> {{ $errors->first('address') }} </strong> </span> @endif
                            </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label for="address2" class="col-lg-3 col-form-label">Address2<span class="requiredField"></span> </label>
                            <div class="col-lg-6">
                                {!! Form::textarea('address2', Input::old('address2'), $attributes = array('class'=>'form-control form-control-sm m-input','id'=>'address2','rows' => 4, 'cols' => 54,'placeholder'=>'Enter Address2','autocomplete'=>'off')) !!}
                                @if($errors->has('address2')) <span class="validation_msg"> <strong> {{ $errors->first('address2') }} </strong> </span> @endif
                            </div>
                        </div>

                        <div class="form-group m-form__group row">
                            <label for="phone" class="col-lg-3 col-form-label">Phone <span class="requiredField"></span> </label>
                            <div class="col-lg-6">
                                {!! Form::text('phone', Input::old('phone'), $attributes = array('class'=>'form-control form-control-sm m-input','id'=>'phone','placeholder'=>'Phone No','autocomplete'=>'off')) !!}
                                @if($errors->has('phone')) <span class="validation_msg"> <strong> {{ $errors->first('phone') }} </strong> </span> @endif
                            </div>
                        </div>

                        <div class="form-group m-form__group row">
                            <label for="mobile" class="col-lg-3 col-form-label">Mobile <span class="requiredField"></span> </label>
                            <div class="col-lg-6">
                                {!! Form::text('mobile', Input::old('mobile'), $attributes = array('class'=>'form-control form-control-sm m-input','id'=>'mobile','placeholder'=>'mobile No','autocomplete'=>'off')) !!}
                                @if($errors->has('mobile')) <span class="validation_msg"> <strong> {{ $errors->first('mobile') }} </strong> </span> @endif
                            </div>
                        </div>

                        <div class="form-group m-form__group row">
                            <label for="email" class="col-lg-3 col-form-label">Email <span class="requiredField"></span> </label>
                            <div class="col-lg-6">
                                {!! Form::text('email', Input::old('email'), $attributes = array('class'=>'form-control form-control-sm m-input','id'=>'email','placeholder'=>'Email','autocomplete'=>'off')) !!}
                                @if($errors->has('email')) <span class="validation_msg"> <strong> {{ $errors->first('email') }} </strong> </span> @endif
                            </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label for="fax" class="col-lg-3 col-form-label">Fax <span class="requiredField"></span> </label>
                            <div class="col-lg-6">
                                {!! Form::text('fax', Input::old('fax'), $attributes = array('class'=>'form-control form-control-sm m-input','id'=>'fax','placeholder'=>'Fax','autocomplete'=>'off')) !!}
                                @if($errors->has('fax')) <span class="validation_msg"> <strong> {{ $errors->first('fax') }} </strong> </span> @endif
                            </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label for="web" class="col-lg-3 col-form-label">Web <span class="requiredField"></span> </label>
                            <div class="col-lg-6">
                                {!! Form::text('web', Input::old('web'), $attributes = array('class'=>'form-control form-control-sm m-input','id'=>'web','placeholder'=>'www.example.com','autocomplete'=>'off')) !!}
                                @if($errors->has('web')) <span class="validation_msg"> <strong> {{ $errors->first('web') }} </strong> </span> @endif
                            </div>
                        </div>

                        <div class="form-group m-form__group row">
                            <label class="col-lg-3 col-form-label" for="user_photo">Logo <span class="requiredField">*</span> </label>
                            <div class="col-lg-6">
                                <?php if(isset($editModeData)){?>
                                <img width="100px" src="{{url('uploads/company_logo').'/'.$editModeData->logo}}">
                                    <?php } ?>
                                <br>
                                {!! Form::file('logo', Input::old('logo'), $attributes = array('class'=>'form-control form-control-sm m-input','id'=>'logo')) !!}
                            </div>
                        </div>
                    </div>
                    <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                        <div class="m-form__actions m-form__actions--solid">
                            <div class="row">
                                <div class="col-lg-9 m--align-right">
                                    {{--<button type="reset" class="btn btn-sm btn-secondary">Cancel</button>--}}
                                    <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Update</span></button>
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
