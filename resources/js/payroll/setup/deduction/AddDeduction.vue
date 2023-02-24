<template>
    <div>
        <!--begin:: Add Modal-->
        <div class="modal fade" id="addModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Deduction</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store"
                          class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">
                            <div class="form-group m-form__group row m--margin-top-20"
                                 :class="{'has-danger': (errors.hasOwnProperty('deduction_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Deduction Name<span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.deduction_name"
                                           placeholder="Enter deduction name "/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('deduction_name'))">{{
                                        (errors.hasOwnProperty('deduction_name')) ? errors.deduction_name[0] :'' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row m--margin-top-20"
                                 :class="{'has-danger': (errors.hasOwnProperty('deduction_type'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Deduction Type<span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control select2" id="deduction_type"
                                            v-model="form.deduction_type" data-selectField="deduction_type">
                                        <option value="Percentage">Percentage</option>
                                        <option value="Fixed">Fixed</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('deduction_type'))">{{
                                        (errors.hasOwnProperty('deduction_type')) ? errors.deduction_type[0] :'' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row m--margin-top-20"
                                 :class="{'has-danger': (errors.hasOwnProperty('percentage_of_basic'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Percentage Of Basic<span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.percentage_of_basic"
                                           placeholder="Enter percentage of basic"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('percentage_of_basic'))">{{
                                        (errors.hasOwnProperty('percentage_of_basic')) ? errors.percentage_of_basic[0]
                                        :'' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row m--margin-top-20"
                                 :class="{'has-danger': (errors.hasOwnProperty('limit_per_month'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Max Limit Per Month<span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.limit_per_month"
                                           placeholder="Enter limit per month"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('limit_per_month'))">{{
                                        (errors.hasOwnProperty('limit_per_month')) ? errors.limit_per_month[0] :'' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row"
                                 :class="{'has-danger': (errors.hasOwnProperty('status'))}">
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
                            <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i>
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
                        <h5 class="modal-title">Edit Deduction</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)"
                          class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">
                            <div class="form-group m-form__group row m--margin-top-20"
                                 :class="{'has-danger': (errors.hasOwnProperty('deduction_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Deduction Name<span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.deduction_name"
                                           placeholder="Enter deduction name"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('deduction_name'))">{{
                                        (errors.hasOwnProperty('deduction_name')) ? errors.deduction_name[0] :'' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row m--margin-top-20"
                                 :class="{'has-danger': (errors.hasOwnProperty('deduction_type'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Deduction Type<span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control select2" id="allowance_types"
                                            v-model="form.deduction_type" data-selectField="deduction_type">
                                        <option value="Percentage">Percentage</option>
                                        <option value="Fixed">Fixed</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('deduction_type'))">{{
                                        (errors.hasOwnProperty('deduction_type')) ? errors.deduction_type[0] :'' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row m--margin-top-20"
                                 :class="{'has-danger': (errors.hasOwnProperty('percentage_of_basic'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Percentage Of Basic<span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.percentage_of_basic"
                                           placeholder="Enter percentage of basic"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('percentage_of_basic'))">{{
                                        (errors.hasOwnProperty('percentage_of_basic')) ? errors.percentage_of_basic[0]
                                        :'' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row m--margin-top-20"
                                 :class="{'has-danger': (errors.hasOwnProperty('limit_per_month'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Max Limit Per Month<span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.limit_per_month"
                                           placeholder="Enter limit per month"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('limit_per_month'))">{{
                                        (errors.hasOwnProperty('limit_per_month')) ? errors.limit_per_month[0] :'' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row"
                                 :class="{'has-danger': (errors.hasOwnProperty('status'))}">
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
                            <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i> <span>Update</span>
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

        data: function () {
            return {
                form: {
                    deduction_name: '',
                    deduction_type: '',
                    percentage_of_basic: '',
                    limit_per_month: '',
                    status: '1'
                },
                errors: {},
            };
        },

        methods: {

            store: function () {
                var _this = this;

                _this.$loading(true);
                axios.post(base_url + 'deduction', _this.form)
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
                axios.put(base_url + 'deduction/' + id, _this.form)
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
            $('#addModel,#editModel').on('hidden.bs.modal', function () {
                _this.errors = {};
                _this.form = {
                    'deduction_name': '',
                    'deduction_type': '',
                    'percentage_of_basic': '',
                    'limit_per_month': '',
                    'status': '1'
                };
            });

            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                _this.$loading(true);
                axios.get(base_url + 'deduction/' + id + '/edit')
                    .then((response) => {
                        _this.$loading(false);
                        _this.form = response.data;
                        $('#editModel').modal("show");
                    });
            });
        },

        created() {

        }
    }
</script>
