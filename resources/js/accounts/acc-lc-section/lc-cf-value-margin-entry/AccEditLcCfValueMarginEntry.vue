<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right"  >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="lc_opening_info_id" class="col-lg-2 col-form-label">LC No <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-lc-no" id="lc_opening_info_id" data-selectField="lc_opening_info_id" v-model="form.lc_opening_info_id" >
                            <option v-for="(svalue,sindex) in lc_lists" 
                                :value="svalue.id" 
                                :get_supplier="svalue.get_supplier ? svalue.get_supplier.purchase_supplier_name : ''" 
                                :supplier_id="svalue.get_supplier ? svalue.get_supplier.id : ''" 
                                :get_opening_bank="svalue.get_opening_bank ? svalue.get_opening_bank.accounts_bank_names : ''" 
                                :bank_id="svalue.get_opening_bank ? svalue.get_opening_bank.id : ''" 
                                :lc_total_value="svalue.lc_total_value" 
                            > {{svalue.lc_no}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['lc_opening_info_id']">{{ errors['lc_opening_info_id'][0] }}</div>
                    </div>
                    <label for="supplier_info_display" class="col-lg-2 col-form-label">Supplier </label>
                    <div class="col-lg-4">
                        <input type="text" id="supplier_info_display" v-model="form.supplier_info_display" class="form-control form-control-sm m-input" placeholder="Supplier" readonly>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="margin_entry_date" class="col-lg-2 col-form-label">Margin Entry Date <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.margin_entry_date" data-dateField="margin_entry_date" readonly placeholder="Select date from picker" id="margin_entry_date" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('margin_entry_date'))">{{ (errors.hasOwnProperty('margin_entry_date')) ? errors.margin_entry_date[0] :'' }}</div>
                    </div>
                    <label for="bank_info_display" class="col-lg-2 col-form-label">Bank </label>
                    <div class="col-lg-4">
                        <input type="text" id="bank_info_display" v-model="form.bank_info_display" class="form-control form-control-sm m-input" placeholder="Bank Name" readonly>
                    </div>
                </div>


                <div class="form-group m-form__group row">
                    <label for="lc_value" class="col-lg-2 col-form-label">LC Value </label>
                    <div class="col-lg-4">
                        <input type="text" id="lc_value" v-model="form.lc_value" class="form-control form-control-sm m-input" placeholder="Total LC Value (BDT)" readonly>
                    </div>
                    <label for="margin_percentage" class="col-lg-2 col-form-label">Margin Percentage <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="number" step="any" min="0" id="margin_percentage" @input="calcMarginValPercentage()" v-model="form.margin_percentage" class="form-control form-control-sm m-input" placeholder="Enter Margin Percentage">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('margin_percentage'))">{{ (errors.hasOwnProperty('margin_percentage')) ? errors.margin_percentage[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label for="margin_value" class="col-lg-2 col-form-label">Margin value<span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="number" step="any" min="0" id="margin_value" v-model="form.margin_value" class="form-control form-control-sm m-input" placeholder="Margin value" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('margin_value'))">{{ (errors.hasOwnProperty('margin_value')) ? errors.margin_value[0] :'' }}</div>
                    </div>
                    <label for="narration" class="col-lg-2 col-form-label">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control form-control-sm" id="narration" v-model="form.narration" placeholder="" rows="2"></textarea>
                    </div>
                </div>


                <hr>
                <div class="m-form__heading">
                    <h3 class="m-form__heading-title">Accounts <i data-toggle="m-tooltip" data-width="auto" class="m-form__heading-help-icon flaticon-info" title="" data-original-title="If different than the corresponding address"></i> <small><mark><i> Journal will hit once LC Margin approved. </i></mark></small> </h3>

                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="amount">Total Margin </label>
                    <div class="col-lg-4">
                        <input type="text" id="amount" v-model="form.amount" class="form-control form-control-sm m-input text-right" placeholder="Total Amount" readonly>
                    </div>
                    <!-- <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control form-control-sm" id="narration" v-model="form.narration" placeholder="" rows="1"></textarea>
                    </div> -->
                </div> 
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="debit_account_id">Debit Account <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2"  id="debit_account_id" v-model="form.debit_account_id" data-selectField="debit_account_id">
                            <option v-for="(value,index) in account_list" :value="value.id"> {{ value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('debit_account_id'))">{{ (errors.hasOwnProperty('debit_account_id')) ? errors.debit_account_id[0] :'' }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="credit_account_id">Credit Account <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2"  id="credit_account_id" v-model="form.credit_account_id" data-selectField="credit_account_id">
                            <option v-for="(value,index) in account_list" :value="value.id"> {{ value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('credit_account_id'))">{{ (errors.hasOwnProperty('credit_account_id')) ? errors.credit_account_id[0] :'' }}</div>
                    </div>
                </div>                 
            </div>

            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="submit" class="btn btn-sm btn-info" @click.prevent="update(form.id,0)"><i class="la la-save"></i> <span>Update</span></button>
                            <button v-if="permissions.indexOf('acc-lc-cf-value-margin-entry.approve') !=-1" type="submit" class="btn btn-sm btn-success" @click.prevent="update(form.id,1)"><i class="la la-check"></i> <span>Approve</span></button>
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

        props:['permissions','lc_lists','account_list','editId'],

        data:function(){
            return{

                acc_data_list:false,
                add_form:true,
                edit_form:false,

                form:{
                    lc_opening_info_id: '',
                    supplier_id: '',
                    margin_entry_date: '',
                    bank_id: '',
                    lc_value: '',
                    margin_percentage: '',
                    margin_value: '',
                    narration: '',
                    status:'1',
                    approve:'',
                    supplier_info_display: '',
                    bank_info_display: '',

                    amount: '',
                    debit_account_id: '',
                    credit_account_id: '',

                },
                errors: {},
            };
        },

        methods:{

            edit(id) {
                var _this = this;
                axios.get(base_url+'acc-lc-cf-value-margin-entry/'+id+'/edit')
                    .then( (response) => {
                        _this.form  = response.data.data;                        
                        setTimeout(function () {

                            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                            jQuery(document).ready(function() {
                                    Select2.init();
                                    
                                    //*Load some field data after page rendered completely
                                    _this.form.supplier_info_display  = $('option:selected', this).attr('get_supplier');
                                    _this.form.bank_info_display = $('option:selected', this).attr('get_opening_bank');                                    
                                }
                            );
                        },100);

                    });
            },
            update(id,approval){
                var _this = this;
                _this.form.approve = approval;

                axios.put(base_url+'acc-lc-cf-value-margin-entry/'+id, this.form).then( (response) => {
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

            calcMarginValPercentage(){
                var lc_value = this.form.lc_value;
                if (lc_value && lc_value>0) {

                    var margin_value_percentage =  Number(this.form.margin_percentage/100) * lc_value;
                    this.form.margin_value =  margin_value_percentage.toFixed(2);

                    this.form.amount =  margin_value_percentage.toFixed(2);
                } else {
                    alert('Please select LC No first!');
                    $("#lc_opening_info_id").focus();
                    this.form.margin_percentage = '';
                }
            },         
        },

        mounted(){

            var _this = this;
            
            $('#editComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if( selectField == 'lc_opening_info_id' ){
                    _this.form.lc_opening_info_id= selectedItem.val();

                    _this.form.supplier_id = $('option:selected', this).attr('supplier_id'); 
                    _this.form.bank_id = $('option:selected', this).attr('bank_id'); 
                    _this.form.lc_value = $('option:selected', this).attr('lc_total_value'); 
                    
                    _this.form.supplier_info_display  = $('option:selected', this).attr('get_supplier');
                    _this.form.bank_info_display = $('option:selected', this).attr('get_opening_bank');

                    _this.calcMarginValPercentage();
                }else if(selectField == 'debit_account_id'){
                    _this.form.debit_account_id = selectedItem.val();
                }else if(selectField == 'credit_account_id'){
                    _this.form.credit_account_id = selectedItem.val();
                }
            });       

            $(document).on("focus", ".datepicker", function () {

                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.margin_entry_date = '';

                    }else {
                        _this.form.margin_entry_date = moment(ev.date).format('DD/MM/YYYY');

                    }

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
            var _this = this;
            _this.edit(_this.editId);            
        }

    }
</script>