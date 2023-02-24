<template>
    <div>
        <!--begin:: Add Modal-->
        <div aria-hidden="true" aria-labelledby="" class="modal fade" id="addModel" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add Bonus</h5>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data"
                          method="POST"
                          v-on:submit.prevent="store">

                        <div class="modal-body">
                            <div :class="{'has-danger': (errors.hasOwnProperty('title'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Bonus Name <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter bonus name" type="text"
                                           v-model="form.title"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('title'))">{{
                                        (errors.hasOwnProperty('title')) ? errors.title[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('unit_id'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Unit Name <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="unit_id"
                                            id="unit_id" style="width: 100%" v-model="form.unit_id">
                                        <option :value="value.id" v-for="(value,index) in unit_list">
                                            {{value.unit_name}}
                                        </option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('unit_id'))">{{
                                        (errors.hasOwnProperty('unit_id')) ? errors.unit_id[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('employee_type'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Employee Type <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="employee_type"
                                            id="employee_type" style="width: 100%" v-model="form.employee_type">
                                        <option :value="value.id" v-for="(value,index) in employee_type_list">
                                            {{value.title}}
                                        </option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('employee_type'))">{{
                                        (errors.hasOwnProperty('employee_type')) ? errors.employee_type[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('start_month'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Start Month <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter start month" type="number"
                                           v-model="form.start_month"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('start_month'))">{{
                                        (errors.hasOwnProperty('start_month')) ? errors.start_month[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('end_month'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">End Month <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter start month" type="number"
                                           v-model="form.end_month"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('end_month'))">{{
                                        (errors.hasOwnProperty('end_month')) ? errors.end_month[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('salary_type'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Salary Type <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="salary_type"
                                            id="salary_type" style="width: 100%" v-model="form.salary_type">
                                        <option value="Basic">Basic</option>
                                        <option value="Gross">Gross</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('salary_type'))">{{
                                        (errors.hasOwnProperty('salary_type')) ? errors.salary_type[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('amount_percent'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Amount Percent <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter start month" type="number"
                                           v-model="form.amount_percent"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('amount_percent'))">{{
                                        (errors.hasOwnProperty('amount_percent')) ? errors.amount_percent[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('bonus_percent'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Bonus Percent <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter start month" type="number"
                                           v-model="form.bonus_percent"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('bonus_percent'))">{{
                                        (errors.hasOwnProperty('bonus_percent')) ? errors.bonus_percent[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('effective_date'))}"
                                 class="form-group m-form__group row">
                                <label class="col-form-label col-lg-4 col-sm-12">Effective Date <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <div class="input-group date">
                                        <input class="form-control datepicker" data-dateField="effective_date"
                                               id="effective_date" placeholder="Select date from picker" readonly
                                               type="text" v-model="form.effective_date"/>
                                        <div class="input-group-append"><span class="input-group-text"><i
                                            class="la la-calendar-check-o"></i></span></div>
                                    </div>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('effective_date'))">{{
                                        (errors.hasOwnProperty('effective_date')) ? errors.effective_date[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('status'))}"
                                 class="form-group m-form__group row">
                                <label class="col-form-label col-lg-4 col-sm-12">Status <span
                                    class="requiredField">*</span></label>
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

                        <div class="modal-footer">
                            <button class="btn btn-sm btn-secondary" data-dismiss="modal" type="reset">Close</button>
                            <button class="btn btn-sm btn-success" type="submit"><i class="la la-check"></i>
                                <span>Save</span></button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!--end::Modal-->

        <!--begin:: Add Modal-->
        <div aria-hidden="true" aria-labelledby="" class="modal fade" id="editModel" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Bonus</h5>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data"
                          method="POST"
                          v-on:submit.prevent="update(form.id)">

                        <div class="modal-body">
                            <div :class="{'has-danger': (errors.hasOwnProperty('title'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Bonus Name <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter bonus name" type="text"
                                           v-model="form.title"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('title'))">{{
                                        (errors.hasOwnProperty('title')) ? errors.title[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('unit_id'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Unit Name <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="unit_id"
                                            id="unit_id2" style="width: 100%" v-model="form.unit_id">
                                        <option :value="value.id" v-for="(value,index) in unit_list">
                                            {{value.unit_name}}
                                        </option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('unit_id'))">{{
                                        (errors.hasOwnProperty('unit_id')) ? errors.unit_id[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('employee_type'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Employee Type <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="employee_type"
                                            id="employee_type2" style="width: 100%" v-model="form.employee_type">
                                        <option :value="value.id" v-for="(value,index) in employee_type_list">
                                            {{value.title}}
                                        </option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('employee_type'))">{{
                                        (errors.hasOwnProperty('employee_type')) ? errors.employee_type[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('start_month'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Start Month <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter start month" type="number"
                                           v-model="form.start_month"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('start_month'))">{{
                                        (errors.hasOwnProperty('start_month')) ? errors.start_month[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('end_month'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">End Month <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter start month" type="number"
                                           v-model="form.end_month"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('end_month'))">{{
                                        (errors.hasOwnProperty('end_month')) ? errors.end_month[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('salary_type'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Salary Type <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="salary_type"
                                            id="salary_type2" style="width: 100%" v-model="form.salary_type">
                                        <option value="Basic">Basic</option>
                                        <option value="Gross">Gross</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('salary_type'))">{{
                                        (errors.hasOwnProperty('salary_type')) ? errors.salary_type[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('amount_percent'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Amount Percent <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter start month" type="number"
                                           v-model="form.amount_percent"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('amount_percent'))">{{
                                        (errors.hasOwnProperty('amount_percent')) ? errors.amount_percent[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('bonus_percent'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Bonus Percent <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter start month" type="number"
                                           v-model="form.bonus_percent"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('bonus_percent'))">{{
                                        (errors.hasOwnProperty('bonus_percent')) ? errors.bonus_percent[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('effective_date'))}"
                                 class="form-group m-form__group row">
                                <label class="col-form-label col-lg-4 col-sm-12">Effective Date <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <div class="input-group date">
                                        <input class="form-control datepicker" data-dateField="effective_date"
                                               id="effective_date2" placeholder="Select date from picker" readonly
                                               type="text" v-model="form.effective_date"/>
                                        <div class="input-group-append"><span class="input-group-text"><i
                                            class="la la-calendar-check-o"></i></span></div>
                                    </div>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('effective_date'))">{{
                                        (errors.hasOwnProperty('effective_date')) ? errors.effective_date[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('status'))}"
                                 class="form-group m-form__group row">
                                <label class="col-form-label col-lg-4 col-sm-12">Status <span
                                    class="requiredField">*</span></label>
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

                        <div class="modal-footer">
                            <button class="btn btn-sm btn-secondary" data-dismiss="modal" type="reset">Close</button>
                            <button class="btn btn-sm btn-success" type="submit"><i class="la la-check"></i>
                                <span>Save</span></button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        <!--end::Modal-->
    </div>
</template>

<script>
    import {EventBus} from '../../../vue-assets';

    export default {

        props: ['permissions', 'unit_list', 'employee_type_list'],

        data: function () {
            return {
                form: {
                    title: '',
                    unit_id: '',
                    employee_type: '',
                    start_month: '',
                    end_month: '',
                    salary_type: '',
                    amount_percent: '',
                    bonus_percent: '',
                    effective_date: '',
                    status: '1'
                },
                errors: {},
            };
        },

        methods: {
            store: function () {
                var _this = this;
                _this.$loading(true);
                axios.post(base_url + 'bonus', _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        $('#addModel').modal('hide');
                        _this.showMassage(response.data);
                        EventBus.$emit('new-data-created');
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

            update: function (id) {
                var _this = this;
                _this.$loading(true);
                axios.put(base_url + 'bonus/' + id, _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        $('#editModel').modal('hide');
                        _this.showMassage(response.data);
                        EventBus.$emit('new-data-created');
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
                    toastr.success(data.message, 'Success');
                } else if (data.status == 'error') {
                    toastr.error(data.message, 'Error');
                } else {
                    toastr.error(data.message, 'Error');
                }
            },
        },

        mounted() {
            var _this = this;

            var Select2 = {
                init: function () {
                    $(".select2").select2({placeholder: "Please select an option"})
                }
            };
            jQuery(document).ready(function () {
                    Select2.init()
                }
            );

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.effective_date = '';
                    } else {
                        _this.form.effective_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });

            $('#addModel,#editModel').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if (selectField == 'employee_type') {
                    _this.form.employee_type = selectedItem.val();
                }
            });

            $('#addModel,#editModel').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if (selectField == 'unit_id') {
                    _this.form.unit_id = selectedItem.val();
                }
            });

            $('#addModel,#editModel').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if (selectField == 'salary_type') {
                    _this.form.salary_type = selectedItem.val();
                }
            });

            $('#addModel,#editModel').on('hidden.bs.modal', function () {
                _this.errors = {};
                _this.form = {'title': '', 'unit_id': '', 'employee_type': '', 'salary_type': '', 'status': '1'};

                var Select2 = {
                    init: function () {
                        $(".select2").select2({placeholder: "Please select an option"})
                    }
                };
                jQuery(document).ready(function () {
                        Select2.init()
                    }
                );
            });

            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                _this.$loading(true);
                axios.get(base_url + 'bonus/' + id + '/edit').then((response) => {
                    _this.$loading(false);
                    _this.form = response.data;
                    $('#editModel').modal("show");

                    var Select2 = {
                        init: function () {
                            $(".select2").select2({placeholder: "Please select an option"})
                        }
                    };
                    jQuery(document).ready(function () {
                        Select2.init()
                    });
                });
            });
        },

        created() {

        }
    }
</script>
