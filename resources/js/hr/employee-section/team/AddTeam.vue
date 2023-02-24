<template>
    <div>
        <!--begin:: Add Modal-->
        <div aria-hidden="true" aria-labelledby="" class="modal fade" id="addModel" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add Team</h5>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data" method="POST" v-on:submit.prevent="store">
                        <div class="modal-body">

                            <div :class="{'has-danger': (errors.hasOwnProperty('dept_id'))}" class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12" for="dept_id">Department <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="dept_id" id="dept_id" style="width: 100%" v-model="form.dept_id">
                                        <option :value="value.id" v-for="(value,index) in dept_lists">{{value.name}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('dept_id'))">{{(errors.hasOwnProperty('dept_id')) ? errors.dept_id[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('title'))}" class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Team Name <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter team name" type="text" v-model="form.title"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('title'))">{{                                        (errors.hasOwnProperty('title')) ? errors.title[0] :'' }}</div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('head_person'))}" class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12" for="head_person">Head Person<span class="requiredField"></span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="head_person" id="head_person" style="width: 100%" v-model="form.head_person">
                                        <option :value="value.id" v-for="(value,index) in head_person_lists">{{value.name}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('head_person'))">{{(errors.hasOwnProperty('head_person')) ? errors.head_person[0] :'' }}</div>
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
                        <h5 class="modal-title">Edit Team</h5>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data" method="POST" v-on:submit.prevent="update(form.id)">
                        <div class="modal-body">

                            <div :class="{'has-danger': (errors.hasOwnProperty('dept_id'))}" class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12" for="dept_ids">Department <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="dept_id" id="dept_ids" style="width: 100%" v-model="form.dept_id">
                                        <option :value="value.id" v-for="(value,index) in dept_lists">{{value.name}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('dept_id'))">{{(errors.hasOwnProperty('dept_id')) ? errors.dept_id[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('title'))}" class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Team Name <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter Team name" type="text" v-model="form.title"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('title'))">{{(errors.hasOwnProperty('title')) ? errors.title[0] :'' }}</div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('head_person'))}" class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12" for="head_persons">Head Person<span class="requiredField"></span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="head_person" id="head_persons" style="width: 100%" v-model="form.head_person">
                                        <option :value="value.id" v-for="(value,index) in head_person_lists">{{value.name}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('head_person'))">{{(errors.hasOwnProperty('head_person')) ? errors.head_person[0] :'' }}
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
                        <div class="modal-footer">
                            <button class="btn btn-sm btn-secondary" data-dismiss="modal" type="reset">Close</button>
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

        props: ['permissions', 'head_person_lists','dept_lists'],

        data: function () {
            return {
                form: {
                    dept_id: '',
                    title: '',
                    head_person: '',
                    status: '1'
                },
                errors: {},
            };
        },

        methods: {
            store: function () {
                axios.post(base_url + 'team', this.form)
                    .then((response) => {
                        $('#addModel').modal('hide');
                        this.showMassage(response.data);
                        EventBus.$emit('new-data-created');
                    })
                    .catch(error => {
                        if (error.response.status == 422) {
                            this.errors = error.response.data.errors;
                        } else {
                            this.showMassage(error);
                        }
                    });
            },

            update: function (id) {
                axios.put(base_url + 'team/' + id, this.form)
                    .then((response) => {
                        $('#editModel').modal('hide');
                        this.showMassage(response.data);
                        EventBus.$emit('new-data-created');
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

            var Select2 = {init: function () {$(".select2").select2({placeholder: "Please select an option"})}};
            jQuery(document).ready(function () {Select2.init()});

            $('#addModel,#editModel').on('change', '.select2', function (e) {

                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if (selectField == 'head_person') {
                    _this.form.head_person = selectedItem.val();
                }else if(selectField == 'dept_id'){
                    _this.form.dept_id = selectedItem.val();
                }

            });

            $('#addModel,#editModel').on('hidden.bs.modal', function () {
                _this.errors = {};
                _this.form = {'dept_id': '', 'title': '', 'head_person': '', 'status': '1'};
            });

            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                axios.get(base_url + 'team/' + id + '/edit').then((response) => {
                    _this.form = response.data;
                    $('#editModel').modal("show");

                    var Select2 = {init: function () {$(".select2").select2({placeholder: "Please select an option"})}};
                    jQuery(document).ready(function () {Select2.init()});

                });
            });

        },

        created() {

        }

    }
</script>
