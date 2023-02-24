<template>
    <div>
        <!--begin:: Add Modal-->
        <div class="modal fade" id="addModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add Department</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="store" class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">

                            <div :class="{'has-danger': (errors.hasOwnProperty('div_id'))}" class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12" for="div_id_add">Division <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="div_id" id="div_id_add" style="width: 100%" v-model="form.div_id">
                                        <option :value="value.id" v-for="(value,index) in div_lists">{{value.name}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('div_id'))">{{(errors.hasOwnProperty('div_id')) ? errors.div_id[0] :'' }}</div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('department_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Department Name <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.department_name" placeholder="Enter department name" />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('department_name'))">{{ (errors.hasOwnProperty('department_name')) ? errors.department_name[0] :'' }}</div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('head_person'))}">
                                <label class="col-form-label col-lg-4 col-sm-12" for="head_person">Head Person<span class="requiredField"></span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="head_person" id="head_person" v-model="form.head_person" style="width: 100%">
                                        <option v-for="(value,index) in head_person_lists" :value="value.id" > {{value.name}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('head_person'))">{{ (errors.hasOwnProperty('head_person')) ? errors.head_person[0] :'' }}</div>
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
                        <h5 class="modal-title">Edit Department</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">

                            <div :class="{'has-danger': (errors.hasOwnProperty('div_id'))}" class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12" for="div_id">Division <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="div_id" id="div_id" style="width: 100%" v-model="form.div_id">
                                        <option :value="value.id" v-for="(value,index) in div_lists">{{value.name}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('div_id'))">{{(errors.hasOwnProperty('div_id')) ? errors.div_id[0] :'' }}</div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('department_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Department Name <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.department_name" placeholder="Enter department name" />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('department_name'))">{{ (errors.hasOwnProperty('department_name')) ? errors.department_name[0] :'' }}</div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('head_person'))}">
                                <label class="col-form-label col-lg-4 col-sm-12" for="head_persons">Head Person<span class="requiredField"></span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="head_person" id="head_persons" v-model="form.head_person" style="width: 100%">
                                        <option v-for="(value,index) in head_person_lists" :value="value.id" > {{value.name}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('head_person'))">{{ (errors.hasOwnProperty('head_person')) ? errors.head_person[0] :'' }}</div>
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

        props:['permissions','head_person_lists','div_lists'],

        data:function(){
            return{
                form:{
                    div_id: '',
                    department_name: '',
                    head_person: '',
                    status:'1'
                },
                errors: {},
            };
        },

        methods:{
            store:function(){
                axios.post(base_url+'department', this.form).then( (response) => {
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
                axios.put(base_url+'department/'+id, this.form).then( (response) => {
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
        },

        mounted(){
            var _this = this;

            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
            jQuery(document).ready(function() {Select2.init()});

            $('#addModel,#editModel').on('change', '.select2', function (e) {

                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if( selectField == 'head_person' ){
                    _this.form.head_person= selectedItem.val();
                }else if( selectField == 'div_id' ){
                    _this.form.div_id= selectedItem.val();
                }

            });

            $('#addModel,#editModel').on('hidden.bs.modal', function(){
                _this.errors = {};
                _this.form = { 'div_id': '','department_name': '','head_person': '','status':'1' };
            });

            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                axios.get(base_url+'department/'+id+'/edit').then((response) => {
                    _this.form = response.data;
                    $('#editModel').modal("show");

                    var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                    jQuery(document).ready(function() {Select2.init()});

                });
            });

        },

        created(){

        }

    }
</script>