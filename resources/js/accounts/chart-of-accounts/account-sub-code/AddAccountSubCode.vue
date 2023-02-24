<template>
    <div>
        <!--begin:: Add Modal-->
        <div class="modal fade" id="addModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add Accounts Sub Code</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="store" class="m-form m-form--fit m-form--label-align-right">

                        <div class="modal-body">
                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('product_category_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12" for="main_code_id">Accounts Main Code<span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="main_code_id" id="main_code_id" v-model="form.main_code_id" style="width: 100%;">
                                        <option v-for="(value,index) in accounts_main_code_list" :value="value.id" > {{value.main_code_title +'('+ value.main_code +')'}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('main_code_id'))">{{ (errors.hasOwnProperty('main_code_id')) ? errors.main_code_id[0] :'' }}</div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('sub_code_title'))}">
                                <label class="col-form-label col-lg-4 col-sm-12" for="sub_code_title">Sub Code Title<span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.sub_code_title" id="sub_code_title" placeholder="Enter Subcode Title" />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('sub_code_title'))">{{ (errors.hasOwnProperty('sub_code_title')) ? errors.sub_code_title[0] :'' }}</div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('sub_code'))}">
                                <label class="col-form-label col-lg-4 col-sm-12" for="sub_code">Sub Code<span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.sub_code" id="sub_code" placeholder="Enter Sub Code"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('sub_code'))">{{ (errors.hasOwnProperty('sub_code')) ? errors.sub_code[0] :'' }}</div>
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
                        <h5 class="modal-title">Edit Accounts Sub Code</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">
                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('product_category_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12" for="main_code_ids">Accounts Main Code<span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="main_code_id" id="main_code_ids" v-model="form.main_code_id" style="width: 100%;">
                                        <option v-for="(value,index) in accounts_main_code_list" :value="value.id" > {{value.main_code_title+'('+ value.main_code +')'}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('main_code_id'))">{{ (errors.hasOwnProperty('main_code_id')) ? errors.main_code_id[0] :'' }}</div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('sub_code_title'))}">
                                <label class="col-form-label col-lg-4 col-sm-12" for="sub_code_titles">Sub Code Title<span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.sub_code_title" id="sub_code_titles" placeholder="Enter Subcode Title" />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('sub_code_title'))">{{ (errors.hasOwnProperty('sub_code_title')) ? errors.sub_code_title[0] :'' }}</div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('sub_code'))}">
                                <label class="col-form-label col-lg-4 col-sm-12" for="sub_codes">Sub Code<span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.sub_code" id="sub_codes" placeholder="Enter Sub Code"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('sub_code'))">{{ (errors.hasOwnProperty('sub_code')) ? errors.sub_code[0] :'' }}</div>
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
    </div>
</template>

<script>
    import { EventBus } from '../../../vue-assets';

    export default {
        props:['permissions','accounts_main_code_list'],
        data:function(){
            return{
                form:{
                    main_code_id: '',
                    sub_code_title: '',
                    sub_code: '',
                    status:'1'
                },
                errors: {},
            };
        },

        methods:{
            store:function(){

                axios.post(base_url+'account-sub-code', this.form).then( (response) => {
                    console.log(this.form);
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
                axios.put(base_url+'account-sub-code/'+id, this.form).then( (response) => {
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

            var Select2= {
                init:function() {
                    $(".select2").select2( {
                            placeholder: "Please select an option"
                        }
                    )
                }
            };
            jQuery(document).ready(function() {
                    Select2.init()
                }
            );

            $('#addModel,#editModel').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if( selectField == 'main_code_id' ){
                    _this.form.main_code_id= selectedItem.val();
                }

            });

            $('#addModel,#editModel').on('hidden.bs.modal', function(){
                _this.errors = {};
                _this.form = { 'main_code_id': '','sub_code_title': '','sub_code': '','status':'1' };
            });

            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                axios.get(base_url+'account-sub-code/'+id+'/edit').then((response) => {
                    _this.form = response.data;
                    $('#editModel').modal("show");

                    var Select2= {
                        init:function() {
                            $(".select2").select2( {
                                    placeholder: "Please select an option"
                                }
                            )
                        }
                    };
                    jQuery(document).ready(function() {
                            Select2.init()
                        }
                    );

                });
            });

        },

        created(){

        }

    }
</script>