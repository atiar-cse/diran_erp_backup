<template>
    <div>
        <!--begin::Form-->
        <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data" id="addComponent"
              method="POST"
              v-on:submit.prevent="update(form.id)">
            <div class="m-portlet__body">
                <div :class="{'has-danger': (errors.hasOwnProperty('title'))}"
                     class="form-group m-form__group row m--margin-top-20">
                    <label class="col-form-label col-lg-3 col-sm-12">Title<span class="requiredField">*</span></label>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <input class="form-control" placeholder="Enter shift schedule name" type="text"
                               v-model="form.title"/>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('title'))">{{
                            (errors.hasOwnProperty('title')) ? errors.title[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="in_start">In Start <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <div class="input-group timepicker">
                            <input class="form-control m-input" id="in_start" placeholder="Select time" readonly
                                   type="text" v-model="form.in_start"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-clock-o"></i>
                                </span>
                            </div>
                        </div>

                        <div class="requiredField" v-if="(errors.hasOwnProperty('in_start'))">{{
                            (errors.hasOwnProperty('in_start')) ? errors.in_start[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="in_time">In Time <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <div class="input-group timepicker">
                            <input class="form-control m-input" id="in_time" placeholder="Select time" readonly
                                   type="text" v-model="form.in_time"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-clock-o"></i>
                                </span>
                            </div>
                        </div>

                        <div class="requiredField" v-if="(errors.hasOwnProperty('in_time'))">{{
                            (errors.hasOwnProperty('in_time')) ? errors.in_time[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="late_start">Late Start <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <div class="input-group timepicker">
                            <input class="form-control m-input" id="late_start" placeholder="Select time" readonly
                                   type="text" v-model="form.late_start"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-clock-o"></i>
                                </span>
                            </div>
                        </div>

                        <div class="requiredField" v-if="(errors.hasOwnProperty('late_start'))">{{
                            (errors.hasOwnProperty('late_start')) ? errors.late_start[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="in_end">In End <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <div class="input-group timepicker">
                            <input class="form-control m-input" id="in_end" placeholder="Select time" readonly
                                   type="text" v-model="form.in_end"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-clock-o"></i>
                                </span>
                            </div>
                        </div>

                        <div class="requiredField" v-if="(errors.hasOwnProperty('in_end'))">{{
                            (errors.hasOwnProperty('in_end')) ? errors.in_end[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="out_start">Out Start <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <div class="input-group timepicker">
                            <input class="form-control m-input" id="out_start" placeholder="Select time" readonly
                                   type="text" v-model="form.out_start"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-clock-o"></i>
                                </span>
                            </div>
                        </div>

                        <div class="requiredField" v-if="(errors.hasOwnProperty('out_start'))">{{
                            (errors.hasOwnProperty('out_start')) ? errors.out_start[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="out_end">Out End <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <div class="input-group timepicker">
                            <input class="form-control m-input" id="out_end" placeholder="Select time" readonly
                                   type="text" v-model="form.out_end"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-clock-o"></i>
                                </span>
                            </div>
                        </div>

                        <div class="requiredField" v-if="(errors.hasOwnProperty('out_end'))">{{
                            (errors.hasOwnProperty('out_end')) ? errors.out_end[0] :'' }}
                        </div>
                    </div>
                </div>


                <div :class="{'has-danger': (errors.hasOwnProperty('status'))}" class="form-group m-form__group row">
                    <label class="col-form-label col-lg-4 col-sm-12">Status <span class="requiredField">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12">
                        <span class="m-switch m-switch--icon">
                            <label>
                                <input checked="checked" type="checkbox" v-model="form.status">
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
            </div>
            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button class="btn btn-sm btn-secondary" type="reset">Cancel</button>
                            <button class="btn btn-sm btn-success" type="submit"><i class="la la-check"></i> <span>Save and Go List</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
</template>

<script>
    import {EventBus} from '../../../vue-assets';

    export default {
        props: ['permissions', 'editId'],

        data: function () {
            return {

                product_list: false,
                add_form: true,
                edit_form: false,

                form: {
                    title: '',
                    in_start: '',
                    in_time: '',
                    late_start: '',
                    in_end: '',
                    out_start: '',
                    out_end: '',
                    status: '1'
                },
                errors: {},
            };
        },

        methods: {
            edit(id) {
                var _this = this;
                axios.get(base_url + 'shift-schedule/' + id + '/edit')
                    .then((response) => {
                        _this.form = response.data.data;
                        setTimeout(function () {
                            var Select2 = {
                                init: function () {
                                    $(".select2").select2({placeholder: "Please select an option"})
                                }
                            };
                            jQuery(document).ready(function () {
                                Select2.init()
                            });
                        }, 100);
                    });
            },

            update(id) {
                axios.put(base_url + 'shift-schedule/' + id, this.form)
                    .then((response) => {
                        this.showMassage(response.data);
                        EventBus.$emit('data-changed');
                    })
                    .catch(error => {
                        if (error.response.status == 422) {
                            this.errors = error.response.data.errors;
                        } else {
                            this.showMassage(error);
                        }
                    });
            },

            showMassage(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, 'Success Alert');
                } else if (data.status == 'error') {
                    toastr.error(data.message, 'Error Alert');
                } else {
                    toastr.error(data.message, 'Error Alert');
                }
            },
        },

        mounted() {
            var _this = this;

            var Select2 = {
                init: function () {
                    $(".select2").select2({
                            placeholder: "Please select an option"
                        }
                    )
                }
            };

            jQuery(document).ready(function () {
                    Select2.init()
                }
            );

            $(document).on("focus", "#in_start", function () {
                $(this).timepicker({
                    format: 'LT',
                    scrollDefault: 'now',
                }).on('click change', function () {
                    var timeField = $('#in_start').val();
                    _this.form.in_start = timeField;
                })
            });

            $(document).on("focus", "#in_time", function () {
                $(this).timepicker({
                    format: 'LT',
                    scrollDefault: 'now',
                }).on('click change', function () {
                    var timeField = $('#in_time').val();
                    _this.form.in_time = timeField;
                })
            });

            $(document).on("focus", "#late_start", function () {
                $(this).timepicker({
                    format: 'LT',
                    scrollDefault: 'now',
                }).on('click change', function () {
                    var timeField = $('#late_start').val();
                    _this.form.late_start = timeField;
                })
            });

            $(document).on("focus", "#in_end", function () {
                $(this).timepicker({
                    format: 'LT',
                    scrollDefault: 'now',
                }).on('click change', function () {
                    var timeField = $('#in_end').val();
                    _this.form.in_end = timeField;
                })
            });

            $(document).on("focus", "#out_start", function () {
                $(this).timepicker({
                    format: 'LT',
                    scrollDefault: 'now',
                }).on('click change', function () {
                    var timeField = $('#out_start').val();
                    _this.form.out_start = timeField;
                })
            });

            $(document).on("focus", "#out_end", function () {
                $(this).timepicker({
                    format: 'LT',
                    scrollDefault: 'now',
                }).on('click change', function () {
                    var timeField = $('#out_end').val();
                    _this.form.out_end = timeField;
                })
            });
        },

        created() {
            var _this = this;
            _this.edit(_this.editId);
        }
    }
</script>
