<template>
    <div>
        <!--begin:: Add Modal-->
        <div class="modal fade" id="addModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add Leave Type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="store" class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">

                            <div :class="{'has-danger': (errors.hasOwnProperty('employee_type_id'))}" class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12" for="employee_type_id_add">Employee Type <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="employee_type_id" id="employee_type_id_add" style="width: 100%" v-model="form.employee_type_id">
                                        <option :value="value.id" v-for="(value,index) in employee_type_lists">{{value.name}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('employee_type_id'))">{{(errors.hasOwnProperty('employee_type_id')) ? errors.employee_type_id[0] :'' }}</div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('leave_type_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Leave Type Name <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.leave_type_name" placeholder="Enter leave type name" />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('leave_type_name'))">{{ (errors.hasOwnProperty('leave_type_name')) ? errors.leave_type_name[0] :'' }}</div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('number_of_day'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Number Of Day <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.number_of_day" placeholder="Enter number of day" />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('number_of_day'))">{{ (errors.hasOwnProperty('number_of_day')) ? errors.number_of_day[0] :'' }}</div>
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
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save</span></button>
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
                        <h5 class="modal-title">Edit Leave Type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">
                            <div :class="{'has-danger': (errors.hasOwnProperty('employee_type_id'))}" class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12" for="employee_type_ids">Employee Type <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="employee_type_id" id="employee_type_ids" style="width: 100%" v-model="form.employee_type_id">
                                        <option :value="value.id" v-for="(value,index) in employee_type_lists">{{value.name}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('employee_type_id'))">{{(errors.hasOwnProperty('employee_type_id')) ? errors.employee_type_id[0] :'' }}</div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('leave_type_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Leave Type Name <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.leave_type_name" placeholder="Enter leave type name" />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('leave_type_name'))">{{ (errors.hasOwnProperty('leave_type_name')) ? errors.leave_type_name[0] :'' }}</div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('number_of_day'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Number Of Day  <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.number_of_day" placeholder="Enter number of day" />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('number_of_day'))">{{ (errors.hasOwnProperty('number_of_day')) ? errors.number_of_day[0] :'' }}</div>
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
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Update</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end::Modal-->
    </div>
</template>

<script>
    import { EventBus } from '../../../vue-assets';

    export default {
        props:['employee_type_lists'],

        data:function(){
            return{
                form:{
                    employee_type_id: '',
                    leave_type_name: '',
                    number_of_day: '',
                    status:'1'
                },
                errors: {},
            };
        },

        methods:{
            store:function(){
                axios.post(base_url+'leave-type', this.form).then( (response) => {
                    $('#addModel').modal('hide');
                    this.showMassage(response.data);
                    EventBus.$emit('new-data-created');
                })
                    .catch(error => {
                        if(error.response.status == 422){
                            this.errors = error.response.data.errors;
                        }else{
                            this.showMassage(error);
                        }
                    });
            },

            update:function (id) {
                axios.put(base_url+'leave-type/'+id, this.form).then( (response) => {
                    $('#editModel').modal('hide');
                    this.showMassage(response.data);
                    EventBus.$emit('new-data-created');
                })
                    .catch(error => {
                        if(error.response.status == 422){
                            this.errors = error.response.data.errors;
                        }else{
                            this.showMassage(error);
                        }
                    });
            },

            showMassage(data){
                if(data.status == 'success'){
                    toastr.success(data.message, 'Success');
                }else if(data.status == 'error'){
                    toastr.error(data.message, 'Error');
                }else{
                    toastr.error(data.message, 'Error');
                }
            },

            initSelect2(){
                setTimeout(function () {
                    var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                    jQuery(document).ready(function() {Select2.init();});
                },250);
            },
        },

        mounted(){
            var _this = this;
            _this.initSelect2();

            $('#addModel,#editModel').on('hidden.bs.modal', function(){
                _this.errors = {};
                _this.form = {
                    employee_type_id: '',
                    leave_type_name: '',
                    number_of_day: '',
                    status:'1'};

                _this.initSelect2();
            });

            $('#addModel,#editModel').on('change', '.select2', function (e) {

                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if (selectField == 'employee_type_id') {
                    _this.form.employee_type_id = selectedItem.val();
                }

            });

            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                axios.get(base_url+'leave-type/'+id+'/edit').then((response) => {
                    _this.form = response.data;
                    $('#editModel').modal("show");
                    _this.initSelect2();
                });
            });

        },

        created(){

        }

    }
</script>