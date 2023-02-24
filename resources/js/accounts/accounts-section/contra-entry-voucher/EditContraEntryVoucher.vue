<template>
    <div>
        <!--begin::Form-->
        <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data" id="editComponent"
              method="POST"
              v-on:submit.prevent="update(form.id)">
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="contra_entry_no">Contra Entry No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="contra_entry_no"
                               placeholder="Enter Contra Entry No"
                               type="text" v-model="form.contra_entry_no">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('contra_entry_no'))">{{
                            (errors.hasOwnProperty('contra_entry_no')) ? errors.contra_entry_no[0] :'' }}
                        </div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="contra_date">Contra Entry Date <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input class="form-control form-control-sm m-input datepicker" data-dateField="contra_date"
                                   id="contra_date" placeholder="Select date from picker" readonly
                                   type="text" v-model="form.contra_date"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('contra_date'))">{{
                            (errors.hasOwnProperty('contra_date')) ? errors.contra_date[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="type" class="col-lg-2 col-form-label">Type </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="type" id="type"
                                v-model="form.type">
                            <option value="No Selection"> No Selection </option>
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

                <div class="form-group m-form__group row" v-if="form.is_lc_type==true">
                    <label class="col-lg-2 col-form-label" for="type">Voucher Type </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-voucher_type"
                                data-selectField="voucher_type_id"
                                v-model="form.voucher_type_id">
                            <option :value="value.id" v-for="(value,index) in voucher_type_list">
                                {{value.voucher_type_name}}
                            </option>
                        </select>
                    </div>

                    <label class="col-lg-2 col-form-label">Voucher Ref </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-voucher_ref"
                                data-selectField="voucher_ref_id"
                                v-model="form.voucher_ref_id">
                            <option :value="value.id" v-for="(value,index) in voucher_ref_list">
                                {{value.transaction_no}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="payment_method">Payment Method <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-bootstrap-select m_selectpicker" id="payment_method"
                                v-model="form.payment_method">
                            <option value="1" selected>Cash</option>
                            <option value="2">Cheque</option>
                            <option value="3">Bank Deposit</option>
                            <option value="4">Card</option>
                            <option value="5">Account Transfer</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('payment_method'))">{{
                            (errors.hasOwnProperty('payment_method')) ? errors.payment_method[0] :'' }}
                        </div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="amount">Voucher Amount <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="amount" placeholder="Enter Amount"
                               step="any"
                               type="number" v-model="form.amount">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('amount'))">{{
                            (errors.hasOwnProperty('amount')) ? errors.amount[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="narration">Narration <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <textarea class="form-control form-control-sm" id="narration" placeholder=""
                                  rows="2" v-model="form.narration"></textarea>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('narration'))">{{
                            (errors.hasOwnProperty('narration')) ? errors.narration[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="m-form__heading">
                    <h3 class="m-form__heading-title">Accounts <i class="m-form__heading-help-icon flaticon-info"
                                                                  data-original-title="If different than the corresponding address"
                                                                  data-toggle="m-tooltip"
                                                                  data-width="auto"
                                                                  title=""></i>
                    </h3>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="debit_account_id">Debit Account <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" data-selectField="debit_account_id" id="debit_account_id"
                                v-model="form.debit_account_id">
                            <option :value="value.id" v-for="(value,index) in account_list"> {{
                                value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('debit_account_id'))">{{
                            (errors.hasOwnProperty('debit_account_id')) ? errors.debit_account_id[0] :'' }}
                        </div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="credit_account_id">Credit Account <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" data-selectField="credit_account_id" id="credit_account_id"
                                v-model="form.credit_account_id">
                            <option :value="value.id" v-for="(value,index) in account_list"> {{
                                value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('credit_account_id'))">{{
                            (errors.hasOwnProperty('credit_account_id')) ? errors.credit_account_id[0] :'' }}
                        </div>
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
                        <h3 class="m-form__heading-title">Cheque Info <i class="m-form__heading-help-icon flaticon-info"
                                                                         data-original-title="If different than the corresponding address"
                                                                         data-toggle="m-tooltip"
                                                                         data-width="auto"
                                                                         title=""></i>
                        </h3>
                    </div>

                    <div class="form-group m-form__group row">

                        <!-- <label class="col-lg-2 col-form-label" for="bank_id">Bank </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="bank_id" v-model="form.bank_id" @change="loadBankAcctList()" data-selectField="bank_id"  style="width: 100%">
                                <option v-for="(value,index) in bank_list" :value="value.id"> {{value.accounts_bank_names}}</option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('bank_id'))">{{ (errors.hasOwnProperty('bank_id')) ? errors.bank_id[0] :'' }}</div>
                        </div> -->

                        <label class="col-lg-2 col-form-label" for="account_no">Account </label>
                        <div class="col-lg-4">
                            <select @change="loadCheckLeafList()" class="form-control select2"
                                    data-selectField="account_no"
                                    id="account_no" style="width:100%" v-model="form.account_no">
                                <option :value="svalue.id" v-for="(svalue,sindex) in bank_wise_account_list">
                                    {{ svalue.chart_of_accounts_title +' ('+ svalue.chart_of_account_code +')'}}
                                </option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('account_no'))">{{
                                (errors.hasOwnProperty('account_no')) ? errors.account_no[0] :'' }}
                            </div>
                        </div>

                        <label class="col-lg-2 col-form-label" for="cheque_leaf">Cheque Leaf </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" data-selectField="cheque_leaf" id="cheque_leaf"
                                    style="width:100%" v-model="form.cheque_leaf">
                                <option :value="svalue.id" v-for="(svalue,sindex) in check_leaf_list"> {{
                                    svalue.leaf_number}}
                                </option>
                            </select>

                            <div class="requiredField" v-if="(errors.hasOwnProperty('cheque_leaf'))">{{
                                (errors.hasOwnProperty('cheque_leaf')) ? errors.cheque_leaf[0] :'' }}
                            </div>
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
                                <input class="form-control form-control-sm m-input datepicker"
                                       data-dateField="cheque_date"
                                       id="cheque_date" placeholder="Select date from picker" readonly
                                       type="text" v-model="form.cheque_date"/>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="la la-calendar-check-o"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('cheque_date'))">{{
                                (errors.hasOwnProperty('cheque_date')) ? errors.cheque_date[0] :'' }}
                            </div>
                        </div>

                        <label class="col-lg-2 col-form-label" for="cheque_remarks">Cheque Remarks </label>
                        <div class="col-lg-4">
                            <textarea class="form-control form-control-sm" id="cheque_remarks"
                                      placeholder="" rows="2" v-model="form.cheque_remarks"></textarea>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('cheque_remarks'))">{{
                                (errors.hasOwnProperty('cheque_remarks')) ? errors.cheque_remarks[0] :'' }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button id="approve" @click.prevent="update(form.id,1)" class="btn btn-sm btn-primary"
                                    type="submit" v-if="permissions.indexOf('contra-entry-vouchers.approve') != -1"><i
                                class="la la-check"></i> <span>Approve</span></button>
                            <button @click.prevent="update(form.id,0)" class="btn btn-sm btn-success" type="submit" id="submit"><i
                                class="la la-check"></i> <span>Update and Go List</span></button>
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

        props: ['permissions', 'bank_wise_account_list', 'account_list', 'bank_list', 'branch_list', 'editId'],

        data: function () {
            return {
                data_list: false,
                add_form: true,
                edit_form: true,

                type_name_title: 'Type Name',
                type_list: [],
                voucher_type_list: [],
                voucher_ref_list: [],

                account_numb_list: [],
                check_leaf_list: [],

                form: {
                    contra_entry_no: '',
                    contra_date: '',

                    type: '',
                    type_id: '',
                    type_desc: '',

                    payment_method: '',
                    party_id: '',
                    cost_center_id: '',
                    branch_id: '',
                    amount: '',
                    debit_account_id: '',
                    credit_account_id: '',
                    debit_remarks: '',
                    credit_remarks: '',
                    bank_id: '',
                    cheque_date: '',
                    account_name: '',
                    account_no: '',
                    cheque_book: '',
                    cheque_leaf: '',
                    cheque_remarks: '',
                    narration: '',

                    status: '1',

                    is_lc_type: false,

                    voucher_type: '',
                    voucher_ref: '',
                    voucher_type_id: '',
                    voucher_ref_id: '',

                },
                errors: {},
            };
        },

        methods: {
            edit(id) {
                var _this = this;

                axios.get(base_url + 'contra-entry-voucher/' + id + '/edit')
                    .then((response) => {
                        //console.log(response)
                        _this.form = response.data.data;

                        _this.type_name_title = response.data.data.type_data.type_name_title;
                        _this.type_list = response.data.data.type_data.type_list;
                        _this.voucher_ref_list = response.data.data.voucher_ref_list;

                        _this.account_numb_list = response.data.account_no;
                        _this.check_leaf_list = response.data.cheque_leaf;

                        setTimeout(function () {
                            var BootstrapSelect = {
                                init: function () {
                                    $(".m_selectpicker").selectpicker('refresh')
                                }
                            };
                            jQuery(document).ready(function () {
                                BootstrapSelect.init();
                                //_this.loadCheckLeafList();
                                _this.loadBankAcctList();
                            });
                            var Select2 = {
                                init: function () {
                                    $(".select2").select2({placeholder: "Please select an option"})
                                }
                            };
                            jQuery(document).ready(function () {
                                Select2.init()
                            });
                        }, 100);
                    });
            },

            update(id, app) {
                var _this = this;
                _this.form.approve_status = app;
                axios.put(base_url + 'contra-entry-voucher/' + id, this.form)
                    .then((response) => {

                        document.getElementById("submit").disabled = true;
                        if ($('#approve').length > 0) {
                            document.getElementById("approve").disabled = true;
                        }

                        this.showMassage(response.data);
                        EventBus.$emit('data-changed');
                    })
                    .catch(error => {
                        if (error.response) {
                            this.errors = error.response.data.errors;
                        } else {
                            this.showMassage(error);
                        }
                    });
            },

            loadTypeDetails() {
                var _this = this;
                var type = _this.form.type;

                axios.get(base_url + "get_account_type_list/" + type)
                    .then((response) => {
                        _this.type_name_title = response.data.type_name_title;
                        _this.type_list = response.data.type_list;
                        setTimeout(function () {
                            var Select2 = {
                                init: function () {
                                    $(".select2").select2({placeholder: "Please select an option"})
                                }
                            };
                            jQuery(document).ready(function () {
                                Select2.init();
                            });
                        }, 100);
                    });
            },

            loadVoucherTypeDetails() {
                var _this = this;
                axios.get(base_url + "get_voucher_type_list/")
                    .then((response) => {
                        _this.voucher_type_list = response.data;
                        _this.initSelect2();
                    });
            },

            loadVoucherRefDetails(index) {
                var _this = this;
                var voucher_type_id = _this.form.voucher_type_id;
                axios.get(base_url + "get_voucher_ref_list/" + voucher_type_id)
                    .then((response) => {
                        _this.voucher_ref_list = response.data;
                        _this.initSelect2();
                    });
            },

            loadBankAcctList() {
                var _this = this;
                axios.get(base_url + "bank-wise-account-no?bank_id=" + this.form.bank_id)
                    .then((response) => {
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
            initSelect2() {
                setTimeout(function () {
                    var Select2 = {
                        init: function () {
                            $(".select2").select2({placeholder: "Please select an option"})
                        }
                    };
                    jQuery(document).ready(function () {
                        Select2.init();
                    });
                }, 250);
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

            var Select2 = {
                init: function () {
                    $(".select2").select2({placeholder: "Please select an option"})
                }
            };
            jQuery(document).ready(function () {
                Select2.init()
            });

            var BootstrapSelect = {
                init: function () {
                    $(".m_selectpicker").selectpicker()
                }
            };
            jQuery(document).ready(function () {
                BootstrapSelect.init()
            });

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if (selectField == 'cost_center_id') {
                    _this.form.cost_center_id = selectedItem.val();
                } else if (selectField == 'branch_id') {
                    _this.form.branch_id = selectedItem.val();
                } else if (selectField == 'debit_account_id') {
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
                } else if (selectField == 'type') {
                    _this.form.type = selectedItem.val();
                    _this.loadTypeDetails();
                }
            });

            $('#editComponent').on('change', '.select-type_id', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                _this.form.type_id = selectedItem.val();
                _this.form.type_desc = $('option:selected', this).text();
            });

            $('#editComponent').on('change', '.select-voucher_type', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                _this.form.voucher_type_id = selectedItem.val();
                _this.form.voucher_type = $('option:selected', this).text();
                _this.loadVoucherRefDetails(dataIndex);
            });

            $('#editComponent').on('change', '.select-voucher_ref', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                _this.form.voucher_ref_id = selectedItem.val();
                _this.form.voucher_ref = $('option:selected', this).text();
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.contra_date = '';
                        _this.form.cheque_date = '';
                    } else {
                        var dateField = $(this).attr('data-dateField');
                        if (dateField == 'contra_date') {
                            _this.form.contra_date = moment(ev.date).format('DD/MM/YYYY');
                        } else if (dateField == 'cheque_date') {
                            _this.form.cheque_date = moment(ev.date).format('DD/MM/YYYY');
                        }
                    }
                });
            });
        },

        created() {
            var _this = this;
            _this.loadVoucherTypeDetails();
            _this.edit(_this.editId);
        }
    }
</script>
