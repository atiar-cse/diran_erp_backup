<template>
    <div>
        <!--begin:: Add Modal-->
        <div class="modal fade" id="addModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add LC Custom Duty Cost Name</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="store" class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">
                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('duty_cost_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Custom Duty Cost Name <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.duty_cost_name" placeholder="e.g. VAT" />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('duty_cost_name'))">{{ (errors.hasOwnProperty('duty_cost_name')) ? errors.duty_cost_name[0] :'' }}</div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('chart_ac_id'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Chart Of Accounts <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2  select-chart_ac_id" id="chart_ac_id" data-selectField="chart_ac_id" v-model="form.chart_ac_id" style="width:100%">
                                        <option v-for="(svalue,sindex) in account_list" :value="svalue.id" > {{ svalue.chart_of_accounts_title +'('+ svalue.chart_of_account_code +')'}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('chart_ac_id'))">{{ (errors.hasOwnProperty('chart_ac_id')) ? errors.chart_ac_id[0] :'' }}</div>
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
        <!--end::Add Modal-->

        <!--begin:: Edit Modal-->
        <div class="modal fade" id="editModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit LC Custom Duty Cost Name Entry</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">
                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('duty_cost_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Cost Name <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.duty_cost_name" placeholder="e.g. VAT" />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('duty_cost_name'))">{{ (errors.hasOwnProperty('duty_cost_name')) ? errors.duty_cost_name[0] :'' }}</div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('chart_ac_id'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Chart Of Accounts <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <select class="form-control m-select2 select2  select-chart_ac_id" id="chart_ac_id_2" data-selectField="chart_ac_id" v-model="form.chart_ac_id" style="width:100%">
                                        <option v-for="(svalue,sindex) in account_list" :value="svalue.id" > {{ svalue.chart_of_accounts_title +'('+ svalue.chart_of_account_code +')'}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('chart_ac_id'))">{{ (errors.hasOwnProperty('chart_ac_id')) ? errors.chart_ac_id[0] :'' }}</div>
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
                            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Update</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end::Edit Modal-->
    </div>
</template>

<script>
    import { EventBus } from '../../vue-assets';

    export default {

        props:['account_list'],

        data:function(){
            return{
                form:{
                    duty_cost_name: '',
                    chart_ac_id: '',
                    status:'1'
                },
                errors: {},
            };
        },

        methods:{
            store:function(){
                axios.post(base_url+'lc-custom-duty-cost-name-entry', this.form).then( (response) => {
                    $('#addModel').modal('hide');
                    this.showMassage(response.data);
                    EventBus.$emit('new-data-created');
                })
                    .catch(error => {
                        if(error.response.status == 422){
                            this.errors = error.response.data.errors;
                        }else{
                            console.log(456);
                            this.showMassage(error);
                        }
                    });
            },

            update:function (id) {
                axios.put(base_url+'lc-custom-duty-cost-name-entry/'+id+'/', this.form).then( (response) => {
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
            $('#addModel,#editModel').on('hidden.bs.modal', function(){
                _this.errors = {};
                _this.form = {'duty_cost_name':'','status':'1'};
            });

            $('#addModel,#editModel').on('change', '.select-chart_ac_id', function (e) {
                var selectedItem = $(this);

                _this.form.chart_ac_id = selectedItem.val();
            });

            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                axios.get(base_url+'lc-custom-duty-cost-name-entry/'+id+'/edit').then((response) => {
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
        },

        created(){

        }

    }
</script>
