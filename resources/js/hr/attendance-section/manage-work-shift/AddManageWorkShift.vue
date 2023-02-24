<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="shift_name">Shift Name <span
                        class="requiredField">*</span> </label>
                    <div class="col-lg-6">
                        <select class="form-control select2" id="shift_name" v-model="form.shift_name"
                                data-selectField="shift_name">
                            <option value="1">Day</option>
                            <option value="2">Evening</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('shift_name'))">{{
                            (errors.hasOwnProperty('shift_name')) ? errors.shift_name[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="start_time">Start Time <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <div class="input-group timepicker">
                            <input class="form-control m-input" v-model="form.start_time" id="start_time" readonly
                                   placeholder="Select time" type="text"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-clock-o"></i>
                                </span>
                            </div>
                        </div>

                        <div class="requiredField" v-if="(errors.hasOwnProperty('start_time'))">{{
                            (errors.hasOwnProperty('start_time')) ? errors.start_time[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="end_time">End Time <span class="requiredField">*</span>
                    </label>
                    <div class="col-lg-6">
                        <div class="input-group timepicker">
                            <input class="form-control m-input" v-model="form.end_time" id="end_time" readonly
                                   placeholder="Select time" type="text"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-clock-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('end_time'))">{{
                            (errors.hasOwnProperty('end_time')) ? errors.end_time[0] :'' }}
                        </div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="late_count_time">Late Count Time <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <div class="input-group timepicker">
                            <input class="form-control m-input" v-model="form.late_count_time" id="late_count_time"
                                   readonly placeholder="Select time" type="text"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-clock-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('late_count_time'))">{{
                            (errors.hasOwnProperty('late_count_time')) ? errors.late_count_time[0] :'' }}
                        </div>
                    </div>
                </div>


                <div class="form-group m-form__group row" :class="{'has-danger': (errors.hasOwnProperty('status'))}">
                    <label class="col-form-label col-lg-4 col-sm-12">Status <span class="requiredField">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12">
                        <span class="m-switch m-switch--icon">
                            <label>
                                <input type="checkbox" checked="checked" v-model="form.status">
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
                            <button type="reset" class="btn btn-sm btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i> <span>Save and Go List</span>
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

        data: function () {
            return {

                product_list: false,
                add_form: true,
                edit_form: false,

                form: {
                    shift_name: '',
                    start_time: '',
                    end_time: '',
                    late_count_time: '',
                    status: '1'
                },
                errors: {},
            };
        },

        methods: {
            store: function () {
                var _this = this;
                _this.$loading(true);
                axios.post(base_url + 'manage-work-shift', _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        _this.showMassage(response.data);
                        EventBus.$emit('data-changed');
                    })
                    .catch(error => {
                        _this.$loading(false);
                        if (error.response.status == 422) {
                            _this.errors = error.response.data.errors;
                        } else {
                            _this.showMassage(error);
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

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if (selectField == 'shift_name') {
                    _this.form.shift_name = selectedItem.val();
                }
            });

            $(document).on("focus", "#start_time", function () {
                $(this).timepicker({
                    format: 'LT',
                    scrollDefault: 'now',
                }).on('click change', function () {
                    var timeField = $('#start_time').val();
                    _this.form.start_time = timeField;
                })
            });

            $(document).on("focus", "#end_time", function () {
                $(this).timepicker({
                    format: 'LT',
                    scrollDefault: 'now',
                }).on('click change', function () {
                    var timeField = $('#end_time').val();
                    _this.form.end_time = timeField;
                })
            });

            $(document).on("focus", "#late_count_time", function () {
                $(this).timepicker({
                    format: 'LT',
                    scrollDefault: 'now',
                }).on('click change', function () {
                    var timeField = $('#late_count_time').val();
                    _this.form.late_count_time = timeField;
                })
            });
        },

        created() {

        }
    }
</script>
