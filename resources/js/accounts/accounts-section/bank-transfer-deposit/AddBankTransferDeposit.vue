<template>
    <div>
        <!--begin::Form-->
        <form method="POST" v-on:submit.prevent="store" id="addComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <label for="bank_transfer_no" class="col-lg-2 col-form-label">Bank Transfer No <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="bank_transfer_no" v-model="form.bank_transfer_no" class="form-control form-control-sm m-input" placeholder="Enter Bank Transfer/Deposit No">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('bank_transfer_no'))">{{(errors.hasOwnProperty('bank_transfer_no')) ? errors.bank_transfer_no[0] :'' }}</div>
                    </div>
                    <label for="bank_transfer_date" class="col-lg-2 col-form-label">Bank Transfer Date <span
                            class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.bank_transfer_date" data-dateField="bank_transfer_date" readonly placeholder="Select date from picker" id="bank_transfer_date"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('bank_transfer_date'))">{{(errors.hasOwnProperty('bank_transfer_date')) ? errors.bank_transfer_date[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="type" class="col-lg-2 col-form-label">Type </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" id="type" data-selectField="type" v-model="form.type">
                            <option value="customer">Customer</option>
                            <option value="supplier">Supplier</option>
                            <option value="employee">Employee</option>
                            <option value="lc">LC</option>
                        </select>
                    </div>
                    <label class="col-lg-2 col-form-label">{{type_name_title}} </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-type_id" data-selectField="type_id"
                                v-model="form.type_id">
                            <option v-for="(value,index) in type_list" :value="value.id"> {{value.type_name}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="payment_method">Payment Method <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-bootstrap-select m_selectpicker" id="payment_method" v-model="form.payment_method">
                            <option value="1" selected>Cash</option>
                            <option value="2">Cheque</option>
                            <option value="3">Bank Deposit</option>
                            <option value="4">Card</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('payment_method'))">{{(errors.hasOwnProperty('payment_method')) ? errors.payment_method[0] :'' }}</div>
                    </div>

                    <label for="amount" class="col-lg-2 col-form-label">Voucher Amount <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="number" step="any" v-model="form.amount" id="amount" class="form-control form-control-sm m-input" placeholder="Enter Amount">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('amount'))">{{(errors.hasOwnProperty('amount')) ? errors.amount[0] :'' }}</div>
                    </div>

                </div>

                <div class="form-group m-form__group row">
                    <label for="narration" class="col-lg-2 col-form-label">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control form-control-sm" id="narration" v-model="form.narration" placeholder="" rows="2"></textarea>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('narration'))">{{(errors.hasOwnProperty('narration')) ? errors.narration[0] :'' }}</div>
                    </div>
                    <label></label>
                    <div class="col-lg-4"></div>
                </div>

                <div class="m-form__heading">
                    <h3 class="m-form__heading-title">Accounts <i data-toggle="m-tooltip" data-width="auto" class="m-form__heading-help-icon flaticon-info" title="" data-original-title="If different than the corresponding address"></i></h3>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="debit_account_id">Debit Account <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" id="debit_account_id" v-model="form.debit_account_id" data-selectField="debit_account_id">
                            <option v-for="(value,index) in account_list" :value="value.id"> {{
                                value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('debit_account_id'))">{{(errors.hasOwnProperty('debit_account_id')) ? errors.debit_account_id[0] :'' }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="credit_account_id">Credit Account <span
                            class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" id="credit_account_id" v-model="form.credit_account_id" data-selectField="credit_account_id">
                            <option v-for="(value,index) in account_list" :value="value.id"> {{
                                value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('credit_account_id'))">{{(errors.hasOwnProperty('credit_account_id')) ? errors.credit_account_id[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="debit_remarks">Debit Remarks </label>
                    <div class="col-lg-4">
                        <textarea class="form-control form-control-sm" id="debit_remarks" placeholder=""
                                  rows="2" v-model="form.debit_remarks"></textarea>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('debit_remarks'))">{{
                            (errors.hasOwnProperty('debit_remarks')) ? errors.debit_remarks[0] :'' }}
                        </div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="credit_remarks">Credit Remarks </label>
                    <div class="col-lg-4">
                        <textarea class="form-control form-control-sm" id="credit_remarks" placeholder=""
                                  rows="2" v-model="form.credit_remarks"></textarea>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('credit_remarks'))">{{
                            (errors.hasOwnProperty('credit_remarks')) ? errors.credit_remarks[0] :'' }}
                        </div>
                    </div>
                </div>

                <div v-show="form.payment_method == 2 || form.payment_method == 3 || form.payment_method == 4">
                    <div class="m-form__heading">
                        <h3 class="m-form__heading-title">Cheque Info <i data-toggle="m-tooltip" data-width="auto" class="m-form__heading-help-icon flaticon-info" title="" data-original-title="If different than the corresponding address"></i></h3>
                    </div>

                    <div class="form-group m-form__group row">
                        <!-- <label class="col-lg-2 col-form-label" for="bank_id">Bank </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="bank_id" v-model="form.bank_id" @change="loadBankAcctList()" data-selectField="bank_id" style="width: 100%">
                                <option v-for="(value,index) in bank_list" :value="value.id">
                                    {{value.accounts_bank_names}}
                                </option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('bank_id'))">{{(errors.hasOwnProperty('bank_id')) ? errors.bank_id[0] :'' }}</div>
                        </div> -->

                    </div>

                    <div class="form-group m-form__group row">
                        <!-- <label class="col-lg-2 col-form-label" for="account_name">Account Name </label>
                        <div class="col-lg-4">
                            <input type="text" v-model="form.account_name" id="account_name" class="form-control form-control-sm m-input" placeholder="Enter Account Name">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('account_name'))">{{(errors.hasOwnProperty('account_name')) ? errors.account_name[0] :'' }}</div>
                        </div> -->

                        <label class="col-lg-2 col-form-label" for="account_no">Account </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="account_no" data-selectField="account_no" v-model="form.account_no" @change="loadCheckLeafList()" style="width:100%">
                                <option v-for="(svalue,sindex) in bank_wise_account_list" :value="svalue.id">
                                    {{ svalue.chart_of_accounts_title +' ('+ svalue.chart_of_account_code +')'}}
                                </option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('account_no'))">{{(errors.hasOwnProperty('account_no')) ? errors.account_no[0] :'' }}</div>
                        </div>

                        <label class="col-lg-2 col-form-label" for="cheque_leaf">Cheque Leaf </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="cheque_leaf" data-selectField="cheque_leaf" v-model="form.cheque_leaf" style="width:100%">
                                <option v-for="(svalue,sindex) in check_leaf_list" :value="svalue.id"> {{
                                    svalue.leaf_number}}
                                </option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('cheque_leaf'))">{{(errors.hasOwnProperty('cheque_leaf')) ? errors.cheque_leaf[0] :'' }}</div>
                        </div>
                    </div>

                    <div class="form-group m-form__group row">



                        <label class="col-lg-2 col-form-label" for="cheque_remarks">Cheque Remarks </label>
                        <div class="col-lg-4">
                            <textarea class="form-control form-control-sm" id="cheque_remarks" v-model="form.cheque_remarks" placeholder="" rows="2"></textarea>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('cheque_remarks'))">{{(errors.hasOwnProperty('cheque_remarks')) ? errors.cheque_remarks[0] :'' }}</div>
                        </div>

                        <label class="col-lg-2 col-form-label" for="cheque_date">Cheque Date </label>
                        <div class="col-lg-4">
                            <div class="input-group date">
                                <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.cheque_date" data-dateField="cheque_date" readonly placeholder="Select date from picker" id="cheque_date"/>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="la la-calendar-check-o"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('cheque_date'))">{{(errors.hasOwnProperty('cheque_date')) ? errors.cheque_date[0] :'' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="submit" id="approve" v-if="permissions.indexOf('bank-transfer-deposits.approve') != -1" @click.prevent="store(1)" class="btn btn-sm btn-primary"><i class="la la-check"></i><span>Approve</span></button>
                            <button type="submit" class="btn btn-sm btn-success" id="submit" @click.prevent="store(0)"><i class="la la-check"></i> <span>Save and Go List</span></button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
        <!--end::Form-->
    </div>
</template>

<script>
    import {EventBus} from '../../../vue-assets';

    export default {

        props: ['bank_wise_account_list', 'account_list', 'bank_list', 'permissions', 'branch_list'],

        data: function () {
            return {

                data_list: false,
                add_form: true,
                edit_form: false,

                type_name_title: 'Type Name',
                type_list: [],

                account_numb_list: [],
                check_leaf_list: [],

                form: {
                    bank_transfer_no: '',
                    bank_transfer_date: '',

                    type: '',
                    type_id: '',
                    type_desc: '',

                    payment_method: '',
                    amount: '',
                    debit_account_id: '',
                    credit_account_id: '',
                    credit_remarks: '',
                    debit_remarks: '',
                    bank_id: '',
                    cheque_date: '',
                    account_name: '',
                    account_no: '',
                    cheque_book: '',
                    cheque_leaf: '',
                    cheque_remarks: '',
                    narration: '',

                    status: '1'
                },
                errors: {},
            };
        },

        methods: {
            store: function (app) {

                var _this = this;
                _this.form.approve_status = app;
                axios.post(base_url + 'bank-transfer-deposit', this.form).then((response) => {

                    document.getElementById("submit").disabled = true;
                    if ($('#approve').length > 0) {
                        document.getElementById("approve").disabled = true;
                    }

                    this.showMassage(response.data);
                    EventBus.$emit('data-changed');
                })
                .catch(error => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        this.showMassage(error);
                    }
                });
            },

            transactionNoGenerate() {
                var _this = this;
                axios.get(base_url + 'bank-transfer-no')
                    .then((response) => {
                        _this.form.bank_transfer_no = response.data;
                    });
            },

            loadTypeDetails(){
                var _this = this;
                var type= _this.form.type;
                axios.get(base_url+"get_account_type_list/"+type).then((response) => {
                    _this.type_name_title = response.data.type_name_title;
                    _this.type_list = response.data.type_list;
                    setTimeout(function () {
                        var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                        jQuery(document).ready(function() {Select2.init();});
                    },100);
                });
            },

            loadBankAcctList() {
                var _this = this;
                axios.get(base_url + "bank-transfer-bank-wise-account-no?bank_id=" + this.form.bank_id).then((response) => {
                    if (response.data.length > 0) {
                        _this.account_numb_list = response.data;
                    }
                });
            },

            loadCheckLeafList() {
                var _this = this;
                axios.get(base_url + 'bank-transfer-acct-num-wise-check-leaf-list?account_no=' + this.form.account_no)
                    .then((response) => {
                        if (response.data.length > 0) {
                            _this.check_leaf_list = response.data;
                        } else {
                            _this.check_leaf_list = response.data;
                            alert('No Check Leaf! Please Generate New Check Book Leaf');
                        }
                    });
            },

            showMassage(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, 'Success Alert');
                } else if (data.status == 'error') {
                    toastr.error(data.message, 'Error Alert');
                } else {
                    toastr.error(data.message, 'Error Alert');
                }
            },
        },

        mounted() {

            $('form').on('focus', 'input[type=number]', function (e) {
                $(this).on('mousewheel.disableScroll', function (e) {
                    e.preventDefault()
                })
            });
            $('form').on('blur', 'input[type=number]', function (e) {
                $(this).off('mousewheel.disableScroll')
            });

            var _this = this;

            var Select2 = {init: function () {$(".select2").select2({placeholder: "Please select an option"})}};
            jQuery(document).ready(function () {Select2.init()});

            var BootstrapSelect = {init: function () {$(".m_selectpicker").selectpicker()}};
            jQuery(document).ready(function () {BootstrapSelect.init()});

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if (selectField == 'debit_account_id') {
                    _this.form.debit_account_id = selectedItem.val();
                } else if (selectField == 'credit_account_id') {
                    _this.form.credit_account_id = selectedItem.val();
                } else if (selectField == 'bank_id') {
                    _this.form.bank_id = selectedItem.val();
                    _this.loadBankAcctList();
                } else if (selectField == 'account_no') {
                    _this.form.account_no = selectedItem.val();
                    _this.loadCheckLeafList();
                } else if (selectField == 'cheque_leaf') {
                    _this.form.cheque_leaf = selectedItem.val();
                }else if(selectField == 'type'){
                    _this.form.type= selectedItem.val();
                    _this.loadTypeDetails();
                }
            });

            $('#addComponent').on('change', '.select-type_id', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                _this.form.type_id = selectedItem.val();
                _this.form.type_desc = $('option:selected', this).text();
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    var dateField = $(this).attr('data-dateField');
                    if (ev.date == undefined) {
                        if (dateField == 'bank_transfer_date') {
                            _this.form.bank_transfer_date = '';
                        } else if (dateField == 'cheque_date') {
                            _this.form.cheque_date = '';
                        }
                    } else {
                        if (dateField == 'bank_transfer_date') {
                            _this.form.bank_transfer_date = moment(ev.date).format('DD/MM/YYYY');
                        } else if (dateField == 'cheque_date') {
                            _this.form.cheque_date = moment(ev.date).format('DD/MM/YYYY');
                        }
                    }
                });
            });
        },
        created() {
            var _this = this;
            _this.transactionNoGenerate();
        }

    }
</script>
