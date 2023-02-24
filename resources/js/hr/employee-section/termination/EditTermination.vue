<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="employee_terminated_employee_id" class="col-lg-3 col-form-label">Employee Terminated
                        <span class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <select class="form-control select2" id="employee_terminated_employee_id"
                                v-model="form.employee_terminated_employee_id"
                                data-selectField="employee_terminated_employee_id">
                            <option>----Select----</option>
                            <option v-for="(etvalue,index) in employee_list" :value="etvalue.id"> {{etvalue.first_name}}
                                {{etvalue.last_name}}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="termination_types">Termination Type </label>
                    <div class="col-lg-6">
                        <input type="text" id="termination_types" v-model="form.termination_types"
                               class="form-control form-control-sm m-input" placeholder="Enter termination types">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('termination_types'))">{{
                            (errors.hasOwnProperty('termination_types')) ? errors.termination_types[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="subject">Subject </label>
                    <div class="col-lg-6">
                        <input type="text" id="subject" v-model="form.subject"
                               class="form-control form-control-sm m-input" placeholder="Enter subject">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('subject'))">{{
                            (errors.hasOwnProperty('subject')) ? errors.subject[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="terminated_by_employee_id">Termination By </label>
                    <div class="col-lg-6">
                        <select class="form-control select2" id="terminated_by_employee_id"
                                v-model="form.terminated_by_employee_id" data-selectField="terminated_by_employee_id">
                            <option>----Select Category----</option>
                            <option v-for="(tvalue,index) in employee_list" :value="tvalue.id"> {{tvalue.first_name}}
                                {{tvalue.last_name}}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="notice_date">Notice Date </label>
                    <div class="col-lg-6">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.notice_date" data-dateField="notice_date" readonly
                                   placeholder="Select date from picker" id="notice_date"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('notice_date'))">{{
                            (errors.hasOwnProperty('notice_date')) ? errors.notice_date[0] :'' }}
                        </div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="termination_date">Termination Date </label>
                    <div class="col-lg-6">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.termination_date" data-dateField="termination_date" readonly
                                   placeholder="Select date from picker" id="termination_date"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('termination_date'))">{{
                            (errors.hasOwnProperty('termination_date')) ? errors.termination_date[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="narration" class="col-lg-3 col-form-label">Narration <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <textarea class="form-control form-control-sm" id="narration" v-model="form.narration"
                                  placeholder="" rows="2"></textarea>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('narration'))">{{
                            (errors.hasOwnProperty('narration')) ? errors.narration[0] :'' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i> <span>Update and Go List</span>
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

        props: ['employee_list', 'permissions', 'editId'],

        data: function () {
            return {

                data_list: false,
                add_form: true,
                edit_form: true,

                form: {
                    employee_terminated_employee_id: '',
                    termination_types: '',
                    subject: '',
                    terminated_by_employee_id: '',
                    notice_date: '',
                    termination_date: '',
                    narration: '',
                    status: '1'
                },
                errors: {},
            };
        },

        methods: {

            edit(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'termination/' + id + '/edit')
                    .then((response) => {
                        _this.$loading(false);
                        _this.form = response.data.data;
                        setTimeout(function () {
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
                        }, 100);
                    });
            },

            update(id) {
                var _this = this;
                _this.$loading(true);
                axios.put(base_url + 'termination/' + id, _this.form)
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

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if (selectField == 'employee_terminated_employee_id') {
                    _this.form.employee_terminated_employee_id = selectedItem.val();
                } else if (selectField == 'terminated_by_employee_id') {
                    _this.form.terminated_by_employee_id = selectedItem.val();
                }
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    var selectField = $(ev.currentTarget).attr('data-dateField');

                    if (ev.date == undefined) {
                        if (selectField == 'notice_date') {
                            _this.form.notice_date = '';
                        } else if (selectField == 'termination_date') {
                            _this.form.termination_date = '';
                        }
                    } else if (selectField == 'notice_date') {
                        _this.form.notice_date = moment(ev.date).format('DD-MM-YYYY');
                    } else if (selectField == 'termination_date') {
                        _this.form.termination_date = moment(ev.date).format('DD-MM-YYYY');
                    }
                });
            });

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
        },

        created() {
            var _this = this;
            _this.edit(_this.editId);
        }
    }
</script>
