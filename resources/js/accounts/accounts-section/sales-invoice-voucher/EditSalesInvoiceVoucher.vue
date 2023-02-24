<template>
    <div>
        <!--begin::Form-->
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <label for="vouchers_no" class="col-lg-2 col-form-label">Voucher No <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text"  id="vouchers_no"  v-model="form.vouchers_no" class="form-control form-control-sm m-input" readonly>
                    </div>
                    <label for="sales_challan_id" class="col-lg-2 col-form-label">Challan NO </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="sales_challan_id" id="sales_challan_id" v-model="form.sales_challan_id" disabled="disabled">
                            <option v-for="(slvalue,slindex) in challan_lists" :value="slvalue.id" >{{slvalue.sales_delivery_no}}</option>
                        </select>
                     </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="sales_date" class="col-lg-2 col-form-label">Sale Date </label>
                    <div class="col-lg-4">
                        <input type="text"  id="sales_date"  v-model="form.sales_date" class="form-control form-control-sm m-input disabled" disabled="disabled">
                     </div>
                    <label for="customer_id" class="col-lg-2 col-form-label">Customer</label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="customer_id" id="customer_id" v-model="form.customer_id" disabled="disabled">
                            <option v-for="(cvalue,cindex) in customer_list" :value="cvalue.id" > {{cvalue.sales_customer_name}} ({{cvalue.sales_customer_id}})</option>
                        </select>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="tender_no" class="col-lg-2 col-form-label">Cotract NO</label>
                    <div class="col-lg-4">
                        <input type="text"  id="tender_no"  v-model="form.tender_no" class="form-control form-control-sm m-input" disabled="disabled">
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="dr_chart_of_account_id" class="col-lg-2 col-form-label">Debit <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="dr_chart_of_account_id" id="dr_chart_of_account_id" v-model="form.dr_chart_of_account_id" >
                            <option v-for="(dvalue,dindex) in dr_ac_lists" :value="dvalue.id">{{dvalue.chart_of_accounts_title}} ({{dvalue.chart_of_account_code}})</option>
                        </select>
                        <div class="requiredField" v-if="errors['dr_chart_of_account_id']">{{ errors['dr_chart_of_account_id'][0] }}</div>
                    </div>
                    <label for="cost_center_id" class="col-lg-2 col-form-label">Credit <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="cr_chart_of_account_id" id="cr_chart_of_account_id" v-model="form.cr_chart_of_account_id" >
                            <option v-for="(cvalue,cindex) in cr_ac_lists" :value="cvalue.id">{{cvalue.chart_of_accounts_title}} ({{cvalue.chart_of_account_code}})</option>
                        </select>
                        <div class="requiredField" v-if="errors['cr_chart_of_account_id']">{{ errors['cr_chart_of_account_id'][0] }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="dr_chart_of_account_id" class="col-lg-2 col-form-label">Payment Type</label>
                    <div class="col-lg-4">
                        <select class="form-control m-bootstrap-select m_selectpicker" id="payment_type" v-model="form.payment_type" >
                            <option value="1" >Cash</option>
                            <option value="2">Cheque</option>
                            <option value="3">Bank Deposit</option>
                            <option value="4">Card</option>
                        </select>
                        <div class="requiredField" v-if="errors['payment_type']">{{ errors['payment_type'][0] }}</div>
                    </div>
                    <label for="narration" class="col-lg-2 col-form-label">Narration </label>
                    <div class="col-lg-4">
                        <textarea id="narration" class="form-control m-input" v-model="form.narration" rows="2"></textarea>
                     </div>
                </div>

                <div v-show="form.payment_type == 2 || form.payment_type == 3 || form.payment_type == 4">

                    <div class="m-form__heading">
                        <h3 class="m-form__heading-title">Cheque Info <i data-toggle="m-tooltip" data-width="auto" class="m-form__heading-help-icon flaticon-info" title="" data-original-title="If different than the corresponding address"></i></h3>
                    </div>

                    <div class="form-group m-form__group row">
                        <!-- <label class="col-lg-2 col-form-label" for="bank_id">Bank </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="bank_id" v-model="form.bank_id" @change="loadBankAcctList()" data-selectField="bank_id"  style="width: 100%">
                                <option v-for="(value,index) in bank_lists" :value="value.id"> {{value.accounts_bank_names}}</option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('bank_id'))">{{ (errors.hasOwnProperty('bank_id')) ? errors.bank_id[0] :'' }}</div>
                        </div> -->

                        <label class="col-lg-2 col-form-label" for="account_no">Account </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="account_no" data-selectField="account_no"  v-model="form.account_no" @change="loadCheckLeafList()"  style="width:100%">
                                <option v-for="(svalue,sindex) in bank_wise_account_list" :value="svalue.id"> 
                                    {{ svalue.chart_of_accounts_title +' ('+ svalue.chart_of_account_code +')'}}
                                </option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('account_no'))">{{ (errors.hasOwnProperty('account_no')) ? errors.account_no[0] :'' }}</div>
                        </div>

                        <label class="col-lg-2 col-form-label" for="cheque_leaf">Cheque Leaf </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="cheque_leaf" data-selectField="cheque_leaf"  v-model="form.cheque_leaf"  style="width:100%">
                                <option v-for="(svalue,sindex) in check_leaf_list" :value="svalue.id" > {{ svalue.leaf_number}}</option>
                            </select>

                            <div class="requiredField" v-if="(errors.hasOwnProperty('cheque_leaf'))">{{ (errors.hasOwnProperty('cheque_leaf')) ? errors.cheque_leaf[0] :'' }}</div>
                        </div>

                    </div>

                    <div class="form-group m-form__group row">
                        <!-- <label class="col-lg-2 col-form-label" for="account_name">Account Name </label>
                        <div class="col-lg-4">
                            <input type="text" v-model="form.account_name" id="account_name" class="form-control form-control-sm m-input" placeholder="Enter Account Name">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('account_name'))">{{ (errors.hasOwnProperty('account_name')) ? errors.account_name[0] :'' }}</div>
                        </div> -->


                    </div>

                    <div class="form-group m-form__group row">

                        <label class="col-lg-2 col-form-label" for="cheque_date">Cheque Date </label>
                        <div class="col-lg-4">
                            <div class="input-group date">
                                <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.cheque_date" data-dateField="cheque_date" readonly placeholder="Select date from picker" id="cheque_date" />
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="la la-calendar-check-o"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('cheque_date'))">{{ (errors.hasOwnProperty('cheque_date')) ? errors.cheque_date[0] :'' }}</div>
                        </div>

                        <label class="col-lg-2 col-form-label" for="cheque_remarks">Cheque Remarks </label>
                        <div class="col-lg-4">
                            <textarea class="form-control form-control-sm" id="cheque_remarks" v-model="form.cheque_remarks" placeholder="" rows="2"></textarea>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('cheque_remarks'))">{{ (errors.hasOwnProperty('cheque_remarks')) ? errors.cheque_remarks[0] :'' }}</div>
                        </div>
                    </div>
                </div>


                <br><br>
                <!--begin::Portlet-->

                <div class="form-group m-form__group row">
                    <div class="m-section__content col-lg-12">
                        <div class ="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse" >
                                <tr>
                                    <th>Sl </th>
                                    <th width="20%">Product </th>
                                    <th>Remarks</th>
                                    <th>M Unit</th>
                                    <th>Unit Price</th>
                                    <th>Discount Type</th>
                                    <th>Discount Amount</th>
                                    <th>Vat Type</th>
                                    <th>Qty</th>
                                    <th>Total Price</th>

                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <td scope="row">
                                        {{index+1}}
                                    </td>
                                    <td>
                                        <select class="form-control m-select2 select2 select-product"  v-bind:data-index="index" v-model="details.product_id"  disabled="disabled">
                                            <option v-for="(value,index) in pid_lists" :value="value.id"  > {{value.product_name}}</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.remarks" class="form-control form-control-sm m-input" disabled="disabled">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.m_unit" class="form-control form-control-sm m-input" disabled="disabled">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.unit_price"  class="form-control form-control-sm m-input number" disabled="disabled">
                                     </td>
                                    <td>
                                        <select class="" v-model="details.discount_type" disabled="disabled">
                                            <option>Pct(%)</option>
                                            <option>Fixed</option>
                                        </select>
                                    </td>
                                    <td><input type="text" v-model="details.details_discount" class="form-control form-control-sm m-input number" disabled="disabled" /></td>
                                    <td>
                                        <select class="" v-model="details.vat_sub" disabled="disabled" >
                                            <option>No Vat</option>
                                            <option>15%</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.qty" class="form-control form-control-sm m-input number" disabled="disabled">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.total_price" class="form-control form-control-sm m-input number" disabled="disabled">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8" class="text-right">Sub Total</td>
                                    <td >
                                        <input type="text" v-model="form.total_qty" class="form-control form-control-sm m-input" disabled="disabled">
                                    </td>
                                    <td >
                                        <input type="text" v-model="form.sub_total_price" class="form-control form-control-sm m-input" disabled="disabled">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="text-right">Commision</td>
                                    <td >
                                        <input type="text" v-model="form.commission" class="form-control form-control-sm m-input" disabled="disabled">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="text-right">Discount</td>
                                    <td >
                                        <input type="text" v-model="form.discount" class="form-control form-control-sm m-input" disabled="disabled">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="text-right">Vat</td>
                                    <td >
                                        <input type="text" v-model="form.vat" class="form-control form-control-sm m-input" disabled="disabled">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="8" class="text-right"> Total </td>
                                    <td class="">
                                        <select class="" v-model="form.vat_type" data-selectField="forming_type" id="vat_inc_exclude" disabled="disabled" >
                                            <option>Incl Vat</option>
                                            <option>Excl Vat</option>
                                        </select>
                                    </td>
                                    <td  class="" width="10%">
                                        <input type="text" v-model="form.total_price" class="form-control form-control-sm m-input number" disabled="disabled">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="text-right">Amount Paid</td>
                                    <td >
                                        <input type="text" v-model="form.price_paid" class="form-control form-control-sm m-input number" @input="totalAayment()" >
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="text-right">Amount Due</td>
                                    <td>
                                        <input type="text" v-model="form.price_due" class="form-control form-control-sm m-input number" @input="totalAayment2()" >
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                        <br><br>
                    </div>
                </div>
            </div>

            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="reset" class="btn btn-sm btn-secondary">Cancel</button>

                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="update(form.id,0)"  ><i class="la la-check"></i> <span>Update</span></button>
                            <button v-if="permission.indexOf('sales-invoice-voucher.approve') != -1" type="submit" class="btn btn-sm btn-success" @click.prevent="update(form.id,1)"  ><i class="la la-check"></i> <span>Approve</span></button>

                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import { EventBus } from '../../../vue-assets';

    export default {
        props:['permission','editId','pid_lists','inv_lists',
            'challan_lists','customer_list','bank_wise_account_list',
            'cr_ac_lists', 'dr_ac_lists','bank_lists'],

        data:function(){
            return{
                sales_voucher_list:false,
                add_form:false,
                edit_form:true,

                account_numb_list:[],
                check_leaf_list:[],

                form:{
                    vouchers_no: '',
                    sales_challan_id: '',
                    sales_date: '',
                    customer_id: '',
                    tender_no: '',
                    dr_chart_of_account_id: '',
                    cr_chart_of_account_id: '',
                    cost_center_id: '',
                    narration: '',


                    payment_type: '',
                    bank_id: '',
                    cheque_date: '',
                    account_name: '',
                    account_no: '',
                    cheque_book: '',
                    cheque_leaf: '',
                    cheque_remarks: '',



                    sub_total_price: '',
                    commission: '',
                    discount: '',
                    vat: '',
                    vat_type: '',

                    total_qty: '',
                    total_price: '',
                    price_paid: '',
                    price_due: '',

                    approve_status: '',

                    details: [
                        {
                            ac_sales_voucher_id: '',
                            product_id: '',
                            remarks: '',
                            m_unit: '',
                            unit_price: '',
                            discount_type: '',
                            discount: '',
                            vat_sub: '',
                            qty: '',
                            total_price: '',
                        }
                    ],
                },
                errors: {},
            };
        },

        methods:{

            edit(id) {
                var _this = this;
                axios.get(base_url+'sales-invoice-voucher/'+id+'/edit')
                    .then( (response) => {
                        _this.form  = response.data.data;
                        _this.account_numb_list  = response.data.account_no;
                        _this.check_leaf_list  = response.data.cheque_leaf;
                        _this.totalAayment();
                        setTimeout(function () {
                            var BootstrapSelect={
                                init:function(){
                                    $(".m_selectpicker").selectpicker('refresh')
                                }
                            };
                            jQuery(document).ready(function(){
                                BootstrapSelect.init()
                                _this. loadBankAcctList();
                            });


                            //selectpicker('refresh');
                            var Select2= {
                                init:function() {
                                    $(".select2").select2( {
                                        placeholder: "Please select an option"
                                    })
                                }
                            };
                            jQuery(document).ready(function() {
                                Select2.init()
                            });
                        },100);
                    });
            },

            update(id,app){

                var _this = this;
                _this.form.approve_status = app;

                axios.put(base_url+'sales-invoice-voucher/'+id,
                    this.form).then( (response) => {
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

            loadBankAcctList(){
                var _this = this;
                axios.get(base_url+"sales_inv_bank-wise-account-number?bank_id=" + this.form.bank_id).then((response) => {
                    if(response.data.length > 0){
                        _this.account_numb_list = response.data;
                    }
                });
            },

            loadCheckLeafList(){
                var _this = this;
                axios.get(base_url + 'bank-transfer-acct-num-wise-check-leaf-list?account_no=' + this.form.account_no)
                    .then((response) => {
                        if(response.data.length > 0){
                            _this.check_leaf_list = response.data;
                        }else{
                            _this.check_leaf_list = response.data;
                            alert('No Check Leaf! Please Generate New Check Book Leaf');
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

            totalAayment(){
                var total = this.form.total_price;
                var paid = this.form.price_paid;
                var due = total - paid;
                this.form.price_due = due;
            },

            totalAayment2(){
                var total = this.form.total_price;
                var due = this.form.price_due;
                var paid = total - due;
                this.form.price_paid = paid;
            },

        },

        mounted(){
            var _this = this;

            var BootstrapSelect={
                init:function(){
                    $(".m_selectpicker").selectpicker()
                }
            };
            jQuery(document).ready(function(){
                BootstrapSelect.init()
            });

            $('#editComponent').on('change', '.select2', function (e) {
                /*var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');*/
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if(selectField == 'cost_center_id'){
                    _this.form.cost_center_id = selectedItem.val();
                }else if(selectField == 'dr_chart_of_account_id'){
                    _this.form.dr_chart_of_account_id = selectedItem.val();
                }else if(selectField == 'cr_chart_of_account_id'){
                    _this.form.cr_chart_of_account_id = selectedItem.val();
                }else if(selectField == 'bank_id'){
                    _this.form.bank_id= selectedItem.val();
                    _this.loadBankAcctList();
                }else if(selectField == 'account_no'){
                    _this.form.account_no= selectedItem.val();
                    _this.loadCheckLeafList();
                }else if(selectField == 'cheque_leaf'){
                    _this.form.cheque_leaf= selectedItem.val();

                }
            });

            var Select2= {
                init:function() {
                    $(".select2").select2( {
                        placeholder: "Please select an option"
                    })
                }
            };

            jQuery(document).ready(function() {
                Select2.init()
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.cheque_date = '';

                    }else {
                        _this.form.cheque_date = moment(ev.date).format('DD/MM/YYYY');

                    }

                });
            });

            $('.number').keypress(function(event) {
                if (((event.which != 46 || (event.which == 46 && $(this).val() == '')) ||
                    $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            }).on('paste', function(event) {
                event.preventDefault();
            });

        },

        created(){
            var _this = this;
            _this.edit(_this.editId);
            _this.totalAayment();

        }

    }
</script>
