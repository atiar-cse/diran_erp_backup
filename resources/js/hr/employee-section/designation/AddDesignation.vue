<template>
    <div>
        <!--begin:: Add Modal-->
        <div class="modal fade" id="addModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add Designation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data"
                          method="POST"
                          v-on:submit.prevent="store">
                        <div class="modal-body">
                            <div :class="{'has-danger': (errors.hasOwnProperty('designation_name'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Designation Name <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter designation name" type="text"
                                           v-model="form.designation_name"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('designation_name'))">{{
                                        (errors.hasOwnProperty('designation_name')) ? errors.designation_name[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('employee_level_id'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12" for="employee_level_id">Employee
                                    Level<span class="requiredField"></span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="employee_level_id"
                                            id="employee_level_id" style="width: 100%" v-model="form.employee_level_id">
                                        <option :value="value.id" v-for="(value,index) in employee_level_list">
                                            {{value.employee_level_name}}
                                        </option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('employee_level_id'))">{{
                                        (errors.hasOwnProperty('employee_level_id')) ? errors.head_person[0] :'' }}
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
                                            <input type="checkbox" checked="checked" v-model="form.status">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                            <button class="btn btn-sm btn-success" type="submit"><i class="la la-check"></i>
                                <span>Save</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end::Modal-->

        <!--begin:: Add Modal-->
        <div class="modal fade" id="editModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Designation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data"
                          method="POST"
                          v-on:submit.prevent="update(form.id)">
                        <div class="modal-body">
                            <div :class="{'has-danger': (errors.hasOwnProperty('designation_name'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Designation Name <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter product category name" type="text"
                                           v-model="form.designation_name"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('designation_name'))">{{
                                        (errors.hasOwnProperty('designation_name')) ? errors.designation_name[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('employee_level_id'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12" for="employee_level_ids">Employee Level<span
                                    class="requiredField"></span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="employee_level_id"
                                            id="employee_level_ids" style="width: 100%"
                                            v-model="form.employee_level_id">
                                        <option :value="value.id" v-for="(value,index) in employee_level_list">
                                            {{value.employee_level_name}}
                                        </option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('employee_level_id'))">{{
                                        (errors.hasOwnProperty('employee_level_id')) ? errors.head_person[0] :'' }}
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
                                            <input type="checkbox" checked="checked" v-model="form.status">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                            <button class="btn btn-sm btn-success" type="submit"><i class="la la-check"></i> <span>Update</span>
                            </button>
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

        props: ['permissions', 'employee_level_list'],

        data: function () {
            return {
                form: {
                    designation_name: '',
                    employee_level_id: '',
                    status: '1'
                },
                errors: {},
            };
        },

        methods: {
            store: function () {
                var _this = this;
                _this.$loading(true);
                axios.post(base_url + 'designation', _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        $('#addModel').modal('hide');
                        _this.showMassage(response.data);
                        EventBus.$emit('new-data-created');
                    })
                    .catch(error => {
                        _this.$loading(false);
                        if (error.response.status == 422) {
                            this.errors = error.response.data.errors;
                        } else {
                            this.showMassage(error);
                        }
                    });
            },

            update: function (id) {
                var _this = this;
                _this.$loading(true);
                axios.put(base_url + 'designation/' + id, _this.form)
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

            $('#addModel,#editModel').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if (selectField == 'employee_level_id') {
                    _this.form.employee_level_id = selectedItem.val();
                }
            });

            $('#addModel,#editModel').on('hidden.bs.modal', function () {
                _this.errors = {};
                _this.form = {'designation_name': '', 'status': '1'};
            });

            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                _this.$loading(true);
                axios.get(base_url + 'designation/' + id + '/edit')
                    .then((response) => {
                        _this.$loading(false);
                        _this.form = response.data;
                        $('#editModel').modal("show");
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
                    });
            });
        },

        created() {

        }
    }
</script>
