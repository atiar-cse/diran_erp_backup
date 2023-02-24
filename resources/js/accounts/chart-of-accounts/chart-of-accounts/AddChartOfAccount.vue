<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">

                <div class="form-group m-form__group row" :class="{'has-danger': (errors.hasOwnProperty('sub_code2_id'))}">
                    <label class="col-form-label col-lg-3" for="sub_code2_id">Accounts Sub2 Code<span class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <select class="form-control m-select2 select2" data-selectField="sub_code2_id" id="sub_code2_id" v-model="form.sub_code2_id" >
                            <option v-for="(value,index) in sub_code2_list" :value="value.id" > {{value.sub_code_title2+'('+ value.sub_code2 +')'}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('sub_code2_id'))">{{ (errors.hasOwnProperty('sub_code2_id')) ? errors.sub_code2_id[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="chart_of_accounts_title">Chart Of Accounts Title <span class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <input type="text" id="chart_of_accounts_title" v-model="form.chart_of_accounts_title" class="form-control form-control-sm m-input" placeholder="Enter Chart of Accounts Title">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('chart_of_accounts_title'))">{{ (errors.hasOwnProperty('chart_of_accounts_title')) ? errors.chart_of_accounts_title[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="chart_of_account_code">Chart Of Account Code <span class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <input type="text" id="chart_of_account_code" v-model="form.chart_of_account_code" class="form-control form-control-sm m-input" placeholder="Enter Chart of Accounts Code" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('chart_of_account_code'))">{{ (errors.hasOwnProperty('chart_of_account_code')) ? errors.chart_of_account_code[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="opening_date">Opening Date </label>
                    <div class="col-lg-6">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.opening_date" data-dateField="opening_date" id="opening_date" placeholder="Select date from picker"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="opening_balance">Opening Balance</label>
                    <div class="col-lg-6">
                        <input type="text" id="opening_balance" v-model="form.opening_balance" class="form-control form-control-sm m-input" placeholder="Enter Opening Balance">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('opening_balance'))">{{ (errors.hasOwnProperty('opening_balance')) ? errors.opening_balance[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="narration">Narration</label>
                    <div class="col-lg-6">
                        <textarea type="text" id="narration" v-model="form.narration" class="form-control form-control-sm m-input" placeholder="Enter Narration"></textarea>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('narration'))">{{ (errors.hasOwnProperty('narration')) ? errors.narration[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-3 col-form-label">Status ?</label>
                    <div class="col-lg-6">
                        <span class="m-switch m-switch--icon">
                            <label>
                                <input type="checkbox" checked="checked" v-model="form.status">
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
            </div>
            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="reset" class="btn btn-sm btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save and Go List</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
</template>

<script>
    import { EventBus } from '../../../vue-assets';

    export default {
        props:['permissions','sub_code2_list'],

        data:function(){
            return{

                data_list:false,
                add_form:true,
                edit_form:false,

                form:{
                    main_code_id: '',
                    sub_code_id: '',
                    sub_code2_id: '',

                    chart_of_accounts_title: '',
                    chart_of_account_code: '',
                    accounts_status: '',
                    current_balance: '',
                    opening_date: '',
                    opening_balance: '',
                    closing_date: '',
                    closing_balance: '',
                    status:'1'
                },
                errors: {},
            };
        },

        methods:{

            ReqGenerate(){
                var _this = this;
                axios.get(base_url + 'acc-chart-code?sub_code2_id=' + this.form.sub_code2_id)
                    .then( (response) => {
                        _this.form.chart_of_account_code = response.data;
                    });
            },

            store:function(){
                var _this = this;
                axios.post(base_url+'chart-of-accounts', this.form).then( (response) => {
                    this.showMassage(response.data);
                    EventBus.$emit('data-changed');
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
                    toastr.success(data.message, 'Success Alert');
                }else if(data.status == 'error'){
                    toastr.error(data.message, 'Error Alert');
                }else{
                    toastr.error(data.message, 'Error Alert');
                }
            },
        },

        mounted(){
            var _this = this;

            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
            jQuery(document).ready(function() {Select2.init()});

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if( selectField == 'sub_code2_id' ){
                    _this.form.sub_code2_id= selectedItem.val();
                    _this.ReqGenerate();
                }
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.opening_date = '';
                    }else {
                        _this.form.opening_date = moment(ev.date).format('DD/MM/YYYY');
                    }

                });
            });

        },

        created(){

        }

    }
</script>
