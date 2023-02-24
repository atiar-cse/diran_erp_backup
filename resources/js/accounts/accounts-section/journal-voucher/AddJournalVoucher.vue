<template>
    <div>
        <!--begin::Form-->
        <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data" id="addComponent"
              method="POST"
              v-on:submit.prevent="store">
            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="transaction_reference_no">Transaction No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="transaction_reference_no"
                               placeholder="Enter Transaction No" readonly
                               type="text" v-model="form.transaction_reference_no">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('transaction_reference_no'))">{{
                            (errors.hasOwnProperty('transaction_reference_no')) ? errors.transaction_reference_no[0] :''
                            }}
                        </div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="transaction_date"> Date <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input class="form-control form-control-sm m-input datepicker"
                                   data-dateField="transaction_date"
                                   id="transaction_date" placeholder="Select date from picker"
                                   readonly type="text" v-model="form.transaction_date"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('transaction_date'))">{{
                            (errors.hasOwnProperty('transaction_date')) ? errors.transaction_date[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="type" class="col-lg-2 col-form-label">Type </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="type" id="type"
                                v-model="form.type">
                            <option value="customer">Customer</option>
                            <option value="supplier">Supplier</option>
                            <option value="employee">Employee</option>
                            <option value="lc">LC</option>
                        </select>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" id="narration" rows="2"
                                  v-model="form.narration"></textarea>
                    </div>
                </div>

                <br><br>
                <!--begin::Portlet-->

                <div class="form-group m-form__group row add-manage-section">
                    <div class="m-section__content col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse">
                                <tr>
                                    <th></th>
                                    <th width="20%">Account Name <span class="requiredField">*</span></th>
                                    <th width="20%">Remarks</th>
                                    <th width="20%">{{type_name_title}}</th>
                                    <th v-if="is_lc_type==true" width="15%">Voucher Type</th>
                                    <th v-if="is_lc_type==true" width="15%">Voucher Reference</th>
                                    <th width="15%">Debit</th>
                                    <th width="15%">Credit</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <td scope="row">
                                        <a @click="addNewItem"
                                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px"
                                           data-placement="top" data-toggle="m-tooltip" href="javascript:void(0)"
                                           title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <select style="width:100%" class="form-control m-select2 select2  select-account"
                                                v-bind:data-index="index"
                                                v-model="details.char_of_account_id">
                                            <option :value="value.id" v-for="(value,index) in account_list"> {{
                                                value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}
                                            </option>
                                        </select>
                                        <div class="requiredField"
                                             v-if="errors['details.'+index+'.char_of_account_id']">{{
                                            errors['details.'+index+'.char_of_account_id'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input class="form-control form-control-sm m-input" type="text"
                                               v-model="details.remarks">
                                    </td>
                                    <td>
                                        <select style="width:100%" class="form-control m-select2 select2  select-type_id"
                                                v-bind:data-index="index" v-model="details.type_id">
                                            <option :value="value.id" v-for="(value,index) in type_list">
                                                {{value.type_name}}
                                            </option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.type_id']">{{
                                            errors['details.'+index+'.type_id'][0] }}
                                        </div>
                                    </td>
                                    <td v-if="is_lc_type==true">
                                        <select style="width:100%" class="form-control m-select2 select2  select-voucher_type"
                                                v-bind:data-index="index" v-model="details.voucher_type_id">
                                            <option :value="value.id" v-for="(value,index) in voucher_type_list">
                                                {{value.voucher_type_name}}
                                            </option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.voucher_type']">{{
                                            errors['details.'+index+'.voucher_type'][0] }}
                                        </div>
                                    </td>
                                    <td v-if="is_lc_type==true">
                                        <select style="width:100%" class="form-control m-select2 select2  select-voucher_ref"
                                                v-bind:data-index="index" v-model="details.voucher_ref_id">
                                            <option :value="value.id" v-for="(value,index) in details.voucher_ref_list">
                                                {{value.transaction_no}}
                                            </option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.voucher_ref']">{{
                                            errors['details.'+index+'.voucher_ref'][0] }}
                                        </div>
                                    </td>

                                    <td>
                                        <input @input="calculation('dr',index)"
                                               class="form-control form-control-sm m-input text-right debit_amount width-100"
                                               step="any"
                                               type="number"
                                               v-model="details.debit_amount">
                                        <div class="requiredField" v-if="errors['details.'+index+'.debit_amount']">{{
                                            errors['details.'+index+'.debit_amount'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input @input="calculation('cr',index)"
                                               class="form-control form-control-sm m-input text-right credit_amount width-100"
                                               step="any"
                                               type="number"
                                               v-model="details.credit_amount">
                                        <div class="requiredField" v-if="errors['details.'+index+'.credit_amount']">{{
                                            errors['details.'+index+'.credit_amount'][0] }}
                                        </div>
                                    </td>

                                    <td>
                                        <a @click="removeItem(index)"
                                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-placement="top" data-toggle="m-tooltip"
                                           title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr v-if="is_lc_type==true">
                                    <td class="text-right" colspan="6">Total</td>
                                    <td class="">
                                        <input class="form-control form-control-sm m-input text-right" readonly
                                               type="text" v-model="form.total_debit">
                                    </td>
                                    <td class="">
                                        <input class="form-control form-control-sm m-input text-right" readonly
                                               type="text" v-model="form.total_credit">
                                    </td>
                                    <td></td>
                                </tr>
                                <tr v-if="is_lc_type==false">
                                    <td class="text-right" colspan="4">Total</td>
                                    <td class="">
                                        <input class="form-control form-control-sm m-input text-right" readonly
                                               type="text" v-model="form.total_debit">
                                    </td>
                                    <td class="">
                                        <input class="form-control form-control-sm m-input text-right" readonly
                                               type="text" v-model="form.total_credit">
                                    </td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button @click.prevent="store(1)" class="btn btn-sm btn-primary"
                                    id="approve"
                                    type="submit" v-if="permissions.indexOf('journal-vouchers.approve') != -1"><i
                                class="la la-check"></i>
                                <span>Approve</span></button>
                            <button @click.prevent="store(0)" class="btn btn-sm btn-success" id="submit" type="submit">
                                <i class="la la-check"></i> <span>Save and Go List</span></button>
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
        props: ['permissions', 'cost_center_list', 'account_list', 'branch_list'],

        data: function () {
            return {

                data_list: false,
                add_form: true,
                edit_form: false,

                is_lc_type: false,

                type_name_title: 'Type Name',
                type_list: [],
                voucher_type_list: [],

                form: {
                    transaction_reference_no: '',
                    transaction_date: '',
                    cost_center_id: '',
                    narration: '',
                    branch_id: '',
                    total_debit: 0,
                    total_credit: 0,
                    approve: '',
                    details: [
                        {
                            char_of_account_id: '',
                            remarks: '',
                            type_id: '',
                            type_desc: '', //hidden
                            debit_amount: '',
                            credit_amount: '',

                            voucher_type_id: '',
                            voucher_type: '',
                            voucher_ref_id: '',
                            voucher_ref: '',
                            voucher_ref_list: [],
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {

            addNewItem() {
                var _this = this;
                _this.form.details.push({
                    char_of_account_id: '',
                    remarks: '',
                    type_id: '',
                    type_desc: '', //hidden
                    debit_amount: '',
                    credit_amount: '',

                    voucher_type_id: '',
                    voucher_type: '',
                    voucher_ref_id: '',
                    voucher_ref: '',
                    voucher_ref_list: [],
                });
                _this.initSelect2();
            },

            removeItem(index) {
                var _this = this;
                if (_this.form.details.length > 1) {
                    _this.form.details.splice(index, 1);
                    _this.calculation();
                }
                _this.initSelect2();
            },

            transactionNoGenerate() {
                var _this = this;
                axios.get(base_url + 'journal-transaction-no')
                    .then((response) => {
                        _this.form.transaction_reference_no = response.data;
                    });
            },

            loadTypeDetails() {
                var _this = this;
                var type = _this.form.type;
                axios.get(base_url + "get_account_type_list/" + type)
                    .then((response) => {
                        _this.type_name_title = response.data.type_name_title;
                        _this.type_list = response.data.type_list;
                        if (type == 'lc') {
                            _this.is_lc_type = true;
                        } else {
                            _this.is_lc_type = false;
                        }
                        _this.initSelect2();
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
                var voucher_type_id = _this.form.details[index].voucher_type_id;
                axios.get(base_url + "get_voucher_ref_list/" + voucher_type_id)
                    .then((response) => {
                        _this.form.details[index].voucher_ref_list = response.data;
                        _this.initSelect2();
                    });
            },

            store(app) {

                var _this = this;
                _this.form.approve = app;
                axios.post(base_url + 'journal-voucher', this.form)
                    .then((response) => {

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

            showMassage(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, 'Success Alert');
                } else if (data.status == 'error') {
                    toastr.error(data.message, 'Error Alert');
                } else {
                    toastr.error(data.message, 'Error Alert');
                }
            },

            calculation(dr_cr, index) {
                var _this = this;

                if (dr_cr == 'dr') {
                    _this.form.details[index].credit_amount = 0;
                } else if (dr_cr == 'cr') {
                    _this.form.details[index].debit_amount = 0;
                }

                var totalDebit = 0;
                var totalCredit = 0;

                var details = this.form.details;
                var length = details.length;

                for (let i = 0; i < length; i++) {
                    totalDebit = Number(details[i].debit_amount) + totalDebit;
                    totalCredit = Number(details[i].credit_amount) + totalCredit;
                }

                totalDebit = totalDebit.toFixed(2);
                totalCredit = totalCredit.toFixed(2);

                _this.form.total_debit = totalDebit;
                _this.form.total_credit = totalCredit;

                if (totalDebit != totalCredit) {
                    $('body').find('#submit').attr('disabled', true);
                    $('body').find('#approve').attr('disabled', true);
                } else {
                    $('body').find('#submit').attr('disabled', false);
                    $('body').find('#approve').attr('disabled', false);
                }
            },

            duplicateCheck(selectedValue) {
                var no_index = this.form.details.length;
                for (let i = 0; i < no_index; i++) {
                    if (this.form.details[i].char_of_account_id == selectedValue) {
                        alert("Duplicate Found");
                        return true;
                    }
                }
                return false;
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
            _this.initSelect2();

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if (selectField == 'cost_center_id') {
                    _this.form.cost_center_id = selectedItem.val();
                } else if (selectField == 'branch_id') {
                    _this.form.branch_id = selectedItem.val();
                } else if (selectField == 'type') {
                    _this.form.type = selectedItem.val();
                    _this.loadTypeDetails();
                }
            });

            $('#addComponent').on('change', '.select-account', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.form.details[dataIndex].char_of_account_id = selectedItem.val();
                // var isDuplicated = _this.duplicateCheck(selectedItem.val());
                // if (isDuplicated) {
                //     _this.form.details[dataIndex].char_of_account_id = '';
                //     _this.initSelect2();
                // } else {
                //     _this.form.details[dataIndex].char_of_account_id = selectedItem.val();
                // }
            });

            $('#addComponent').on('change', '.select-type_id', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.details[dataIndex].type_id = selectedItem.val();
                _this.form.details[dataIndex].type_desc = $('option:selected', this).text();
            });

            $('#addComponent').on('change', '.select-voucher_type', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.details[dataIndex].voucher_type_id = selectedItem.val();
                _this.form.details[dataIndex].voucher_type = $('option:selected', this).text();
                _this.loadVoucherRefDetails(dataIndex);
            });

            $('#addComponent').on('change', '.select-voucher_ref', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.details[dataIndex].voucher_ref_id = selectedItem.val();
                _this.form.details[dataIndex].voucher_ref = $('option:selected', this).text();
            });

            $(document).on("focus", ".datepicker", function () {

                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.transaction_date = '';
                    } else {
                        _this.form.transaction_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created() {
            var _this = this;
            _this.calculation();
            _this.transactionNoGenerate();
            _this.loadVoucherTypeDetails();
        }
    }
</script>
