<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right"  >
            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <div class="col-md-4">
                        <label for="lc_opening_info_id" class="col-form-label">LC No <span class="requiredField">*</span></label>
                        <select class="form-control m-select2 select2 select-ci-no" id="lc_opening_info_id" data-selectField="lc_opening_info_id" v-model="form.lc_opening_info_id" >
                            <option v-for="(svalue,sindex) in lc_lists" 
                                :value="svalue.id" 
                                :lc_opening_info_id="svalue.lc_opening_info_id"
                                :supplier_id="svalue.supplier_id"
                                :lc_opening_charges="svalue.lc_opening_charges"
                                :lc_bank_expenses="svalue.lc_bank_expenses"
                                :insurance_amount="svalue.insurance_amount"
                                :lc_total_value="svalue.lc_total_value"
                            > {{ svalue.lc_no }} ({{svalue.get_supplier ? svalue.get_supplier.purchase_supplier_name : ''}})</option>
                        </select>
                        <div class="requiredField" v-if="errors['lc_opening_info_id']">{{ errors['lc_opening_info_id'][0] }}</div>
                    </div>                   
                    <div class="col-md-4">
                        <label for="closing_date" class="col-form-label">Date <span class="requiredField">*</span></label>
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.closing_date" data-dateField="closing_date" readonly placeholder="Select date from picker" id="closing_date" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('closing_date'))">{{ (errors.hasOwnProperty('closing_date')) ? errors.entry_date[0] :'' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label" for="narration">Narration </label>
                        <textarea class="form-control m-input" v-model="form.narration"  id="narration" rows="1"></textarea>
                    </div> 
                    <!-- <div class="col-md-4">
                        <label class="col-form-label" for="total_cost">Total Cost  <span class="requiredField">*</span> </label>
                        <input type="text" id="total_cost" v-model="form.total_cost" class="form-control form-control-sm m-input" placeholder="LC Total Cost">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('total_cost'))">{{ (errors.hasOwnProperty('total_cost')) ? errors.total_cost[0] :'' }}</div>                        
                    </div> -->                    
                </div>
                <br>


                <!--begin::Portlet-->

                <div class="form-group m-form__group row add-manage-section">
                    <div class="m-section__content col-lg-12">

                        <h4><strong>LC Total Value : </strong> {{lc_total_value}}</h4>
                        <p><strong>LC Opening Charges : {{lc_opening_charges}}</strong></p>
                        <p><strong>Bank Expenses : {{lc_bank_expenses}}</strong></p>
                        <p><strong>Insurance : {{insurance_amount}}</strong></p>
                        <p><strong>Total Margin : {{lc_margin.total_margin}}</strong></p>

                        <div class ="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse" >
                                <tr>
                                    <th>SL.</th>
                                    <th>CI No</th>
                                    <th>Qty</th>
                                    <th>CI Amount (BDT)</th>
                                    <th>Approve Stauts</th>
                                    <th>Stock Entry Status</th>
                                    <th>Charges</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(ci_row, index) in ci_lists"> <!-- //for-loop -->
                                    <td scope="row">
                                        {{++index}}
                                    </td>
                                    <td>{{ci_row.ci_no}}</td>
                                    <td>{{ci_row.total_qty}}</td>
                                    <td class="text-right">{{ci_row.total_amount}}</td>
                                    <td>
                                        <span v-if="ci_row.approve_status == 0" class="m--font-danger" style="font-weight: 600"> Awaiting </span>
                                        <span v-if="ci_row.approve_status == 1" class="m--font-success" style="font-weight: 600"> Approved </span>
                                    </td>
                                    <td>
                                        <span v-if="ci_row.stock_enrty_status == 0" class="m--font-danger" style="font-weight: 600"> Awaiting </span>
                                        <span v-if="ci_row.stock_enrty_status == 1" class="m--font-success" style="font-weight: 600"> Approved </span>
                                    </td>

                                    <td>
                                        <!-- Charges Table -->
                                        <table class="table table-bordered">
                                            <thead>
                                                <th>Cost Name</th>
                                                <th>Amount</th>
                                                <th>Approve Stauts</th>
                                            </thead>
                                            <tbody>
                                                <!-- <tr v-for="(margin, index) in ci_row.get_margins">
                                                    <td>Margin {{margin.margin_percentage}} %</td>
                                                    <td class="text-right">{{margin.margin_value}}</td>
                                                    <td>
                                                        <span v-if="margin.approve_status == 0" class="m--font-danger" style="font-weight: 600"> Awaiting </span>
                                                        <span v-if="margin.approve_status == 1" class="m--font-success" style="font-weight: 600"> Approved </span>
                                                    </td>
                                                </tr> -->
                                                <tr v-for="(latr, index) in ci_row.get_latr_rows"> <!-- //for-loop -->
                                                    <td>LATR <span v-if="ci_row.ci_delivery_type==1">{{latr.latr_percentage}} %</span></td>
                                                    <td class="text-right">{{latr.latr_total_amount}}</td>
                                                    <td>
                                                        <span v-if="latr.approve_status == 0" class="m--font-danger" style="font-weight: 600"> Awaiting </span>
                                                        <span v-if="latr.approve_status == 1" class="m--font-success" style="font-weight: 600"> Approved </span>
                                                    </td>
                                                </tr>

                                                <tr v-for="(cd_charge, index) in ci_row.get_custom_duty_charges"> <!-- //for-loop -->
                                                    <td>{{cd_charge.get_custom_duty ? cd_charge.get_custom_duty.duty_cost_name : ''}}</td>
                                                    <td class="text-right">{{cd_charge.total_cost}}</td>
                                                    <td>
                                                        <span v-if="cd_charge.approve_status == 0" class="m--font-danger" style="font-weight: 600"> Awaiting </span>
                                                        <span v-if="cd_charge.approve_status == 1" class="m--font-success" style="font-weight: 600"> Approved </span>
                                                    </td>
                                                <tr v-for="(other_charge, index) in ci_row.get_other_charges"> <!-- //for-loop -->
                                                    <td>{{other_charge.get_cost_cat ? other_charge.get_cost_cat.cost_category_name : ''}}</td>
                                                    <td class="text-right">{{other_charge.total_cost}}</td>
                                                    <td>
                                                        <span v-if="other_charge.approve_status == 0" class="m--font-danger" style="font-weight: 600"> Awaiting </span>
                                                        <span v-if="other_charge.approve_status == 1" class="m--font-success" style="font-weight: 600"> Approved </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <!-- //Charges Table -->
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right"><strong>Total</strong> </td>
                                    <td class="">
                                        {{grand_total_qty}}
                                    </td>
                                    <td></td>
                                    <td  class="">
                                       
                                    </td>
                                    <td  class="">
                                       
                                    </td>
                                    <td>
                                        <span class="m--font-info" style="font-weight: 600"> {{grand_total_amount}} </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6"  class="text-right"> <strong>Total Landed Cost</strong> </td>
                                    <td>
                                        <span class="m--font-info" style="font-weight: 600"> {{grand_landed_cost}} </span>
                                    </td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                        <br><br>
                    </div>
                </div>

                <!-- Start Account -->
                <!-- <hr>
                <div class="m-form__heading">
                    <h3 class="m-form__heading-title">Accounts <i data-toggle="m-tooltip" data-width="auto" class="m-form__heading-help-icon flaticon-info" title="" data-original-title="If different than the corresponding address"></i> <small><mark><i> Journal will hit once LC Close Entry approved. </i></mark></small> </h3>

                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="amount">Total Charge Amount (Remaining) </label>
                    <div class="col-lg-4">
                        <input type="text" id="amount" v-model="form.amount" class="form-control form-control-sm m-input text-right" placeholder="Total Amount" readonly>
                    </div>
                    <label class="col-lg-2 col-form-label" for="landed_cost">Total Landed Cost (Taka) </label>
                    <div class="col-lg-4">
                        <input type="text" id="landed_cost" v-model="form.landed_cost" class="form-control form-control-sm m-input text-right" placeholder="Landed Cost" readonly>
                    </div>
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
                </div> -->
                <!-- //End Account -->     
            </div>

            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">

                            <button disabled v-if="permissions.indexOf('lc-closing.approve') !=-1" type="submit" class="btn btn-sm btn-success can_submit" @click.prevent="store(1)"><i class="la la-check-circle"></i> <span>Confirm & Approve</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
</template>

<script>
    import { EventBus } from '../../vue-assets';
    export default {

        props:['permissions','lc_lists','account_list'],

        data:function(){
            return{
                product_list:false,
                add_form:true,
                edit_form:false,
                edit_form:false,

                insurance_amount: '',
                lc_bank_expenses: '',
                lc_opening_charges: '',
                lc_total_value: '',
                lc_margin: '',

                grand_total_qty: 0,
                grand_total_amount: 0,

                grand_landed_cost: 0,

                ci_lists: [],

                form:{
                    lc_opening_info_id: '',
                    supplier_id: '',
                    closing_date: '',
                    narration: '',
                    total_cost: '',
                    landed_cost: '',
                    status: 1,
                    approve: 0,

                    amount: '',
                    debit_account_id: '',
                    credit_account_id: '',
                    // narration: '',                    
                },
                errors: {},
            };
        },

        methods:{

            calcGrandTotal(){
                var _this = this;
                var ci_lists = _this.ci_lists;

                let sum_qty = 0;
                let sum_ci_amount = 0;
                let grand_total_amount = 0;
                let get_total_amount = 0;
                let grand_landed_cost = 0;
                for(let i = 0; i < ci_lists.length; i++){
                    sum_qty += parseFloat(ci_lists[i].total_qty);
                    sum_ci_amount += parseFloat(ci_lists[i].total_amount);

                        // ci_lists[i].get_margins.forEach(function(item) {
                             
                        //     grand_total_amount += parseFloat(item.margin_value);
                        // });
                        ci_lists[i].get_latr_rows.forEach(function(item) {
                             
                            grand_total_amount += parseFloat(item.latr_total_amount);
                        });
                        ci_lists[i].get_custom_duty_charges.forEach(function(item) {
                             
                            grand_total_amount += parseFloat(item.total_cost);
                        });
                        ci_lists[i].get_other_charges.forEach(function(item) {
                             
                            grand_total_amount += parseFloat(item.total_cost);
                        });
                }

                _this.grand_total_qty = sum_qty;
                _this.grand_total_amount = grand_total_amount.toFixed(2);

                get_total_amount = parseFloat(_this.insurance_amount) + parseFloat(_this.lc_margin.total_margin) +
                    parseFloat(_this.lc_bank_expenses) +
                    parseFloat(_this.lc_opening_charges); //  parseFloat(_this.grand_total_amount);

                _this.form.amount = get_total_amount.toFixed(2);
                
                _this.grand_landed_cost = Number(get_total_amount+grand_total_amount).toFixed(2);

                _this.form.total_cost = _this.lc_total_value;
                _this.form.landed_cost = _this.grand_landed_cost;
            },

            store:function(approval){
                var _this = this;
                _this.form.approve = approval;

                var result = confirm("Are you sure?");
                if (result) {
                    axios.post(base_url+'lc-closing', this.form).then( (response) => {
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
                }
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

            loadCommercialInvoices(){
                var _this = this;
                var id= this.form.lc_opening_info_id;

                axios.get(base_url+"lc-closing/"+id+"/commercial-invoices").then((response) => {
                    if(response.data.data.ci_lists.length > 0){
                        _this.ci_lists = response.data.data.ci_lists;
                        _this.lc_margin = response.data.data.lc_margin;
                        $('body').find('.can_submit').attr('disabled', false);
                    } else {
                        alert('Sorry - No Commercial Invoice Found!');
                        $('body').find('.can_submit').attr('disabled', true);
                    }
                });
            },
        },

        mounted(){
            var _this = this;

            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
            jQuery(document).ready(function() {Select2.init()});

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if( selectField == 'lc_opening_info_id' ){
                    _this.form.lc_opening_info_id= selectedItem.val();

                    var supplier_id = $('option:selected', this).attr('supplier_id'); 
                    _this.form.supplier_id= supplier_id; 

                    _this.lc_opening_charges= $('option:selected', this).attr('lc_opening_charges');
                    _this.lc_bank_expenses= $('option:selected', this).attr('lc_bank_expenses');
                    _this.insurance_amount= $('option:selected', this).attr('insurance_amount');
                    _this.lc_total_value= $('option:selected', this).attr('lc_total_value');

                    _this.loadCommercialInvoices(); 

                        setTimeout(function () {
                            jQuery(document).ready(function() {
                                _this.calcGrandTotal();
                            });
                        },200);                                       

                } else if(selectField == 'debit_account_id'){
                    _this.form.debit_account_id= selectedItem.val();
                }else if(selectField == 'credit_account_id'){
                    _this.form.credit_account_id= selectedItem.val();
                }
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    var selectField = $(ev.currentTarget).attr('data-dateField');
                    if(ev.date == undefined){
                        _this.form.closing_date = '';
                    }else if(selectField == 'closing_date'){
                        _this.form.closing_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created() {
            var _this = this;
            _this.form.closing_date = moment().format('DD/MM/YYYY');
        }       
    }
</script>