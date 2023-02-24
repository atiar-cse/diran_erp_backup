<template>
    <div>
        <!--begin:: Add Modal-->
        <div class="modal fade" id="addModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add Purchase Cost Type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="store" class="m-form m-form--fit m-form--label-align-right">

                        <div class="modal-body">
                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('purchase_cost_types_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Cost Type Name <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.purchase_cost_types_name" placeholder="Enter purchase cost type name" />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('purchase_cost_types_name'))">{{ (errors.hasOwnProperty('purchase_cost_types_name')) ? errors.purchase_cost_types_name[0] :'' }}</div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('chart_ac_id'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Chart of Account Head <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="chart_ac_id" id="chart_ac_id" v-model="form.chart_ac_id" style="width: 100%"  >
                                        <option v-for="(value,index) in chat_lists" :value="value.id"> {{value.chart_of_accounts_title}}({{value.chart_of_account_code}})</option>
                                    </select>
                                    <div class="requiredField" v-if="errors['chart_ac_id']">{{ errors['chart_ac_id'][0] }}</div>
                                </div>
                                <!--<label class="col-form-label col-lg-4 col-sm-12">Cost Type Code <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.cost_types_code" placeholder="Enter purchase cost type code" />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('cost_types_code'))">{{ (errors.hasOwnProperty('cost_types_code')) ? errors.cost_types_code[0] :'' }}</div>
                                </div>-->
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

        <!--begin:: Edit Modal-->
        <div class="modal fade" id="editModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Purchase Cost Type</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">
                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('purchase_cost_types_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Cost Type Name <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.purchase_cost_types_name" placeholder="Enter purchase  cost type name" />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('purchase_cost_types_name'))">{{ (errors.hasOwnProperty('purchase_cost_types_name')) ? errors.purchase_cost_types_name[0] :'' }}</div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('chart_ac_id'))}">

                                <label class="col-form-label col-lg-4 col-sm-12">Chart of Account Head <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2" data-selectField="chart_ac_id" id="chart_ac_id2" v-model="form.chart_ac_id" style="width: 100%" >
                                        <option v-for="(value,index) in chat_lists" :value="value.id"> {{value.chart_of_accounts_title}}({{value.chart_of_account_code}})</option>
                                    </select>
                                    <div class="requiredField" v-if="errors['chart_ac_id']">{{ errors['chart_ac_id'][0] }}</div>
                                </div>

                                <!--<label class="col-form-label col-lg-4 col-sm-12">Cost Type Code <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.cost_types_code" placeholder="Enter purchase cost type code" />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('cost_types_code'))">{{ (errors.hasOwnProperty('cost_types_code')) ? errors.cost_types_code[0] :'' }}</div>
                                </div>-->
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

        props:['permissions','chat_lists'],

        data:function(){
            return{
                form:{
                    purchase_cost_types_name: '',
                    chart_ac_id: '',
                    status:'1'
                },
                errors: {},
            };
        },

        methods:{
            store:function(){
                axios.post(base_url+'cost-type/store', this.form).then( (response) => {
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
                axios.put(base_url+'cost-type/'+id+'/update', this.form).then( (response) => {
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

                if(selectField == 'chart_ac_id'){
                    _this.form.chart_ac_id = selectedItem.val();
                }
            });

            $('#addModel,#editModel').on('hidden.bs.modal', function(){
                _this.errors = {};
                _this.form = { 'purchase_cost_types_name': '','chart_ac_id': '','status':'1' };
            });

            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                axios.get(base_url+'cost-type/'+id+'/edit').then((response) => {
                    _this.form = response.data;
                    $('#editModel').modal("show");
                    setTimeout(function () {
                        var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                        jQuery(document).ready(function() {Select2.init()});

                    },100);
                });
            });

        },

        created(){

        }

    }
</script>
