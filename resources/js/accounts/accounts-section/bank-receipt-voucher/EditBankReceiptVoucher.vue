<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="receipt_transaction_no" class="col-lg-2 col-form-label">Receipt No </label>
                    <div class="col-lg-4">
                        <input type="text" id="receipt_transaction_no" v-model="form.receipt_transaction_no"
                               class="form-control form-control-sm m-input" placeholder="Enter Receipt Transaction No">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('receipt_transaction_no'))">{{
                            (errors.hasOwnProperty('receipt_transaction_no')) ? errors.receipt_transaction_no[0] :'' }}
                        </div>
                    </div>
                    <label for="reciept_date" class="col-lg-2 col-form-label">Receipt Date </label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.reciept_date" data-dateField="reciept_date" readonly
                                   placeholder="Select date from picker" id="reciept_date"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('reciept_date'))">{{
                            (errors.hasOwnProperty('reciept_date')) ? errors.reciept_date[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="type" class="col-lg-2 col-form-label">Type </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" id="type" data-selectField="type"
                                v-model="form.type">
                            <option value="customer">Customer</option>
                            <option value="supplier">Supplier</option>
                            <option value="employee">Employee</option>
                            <option value="lc">LC</option>
                        </select>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.narration" id="narration"
                                  rows="2"></textarea>
                    </div>
                </div>
                <br><br>
                <!--begin::Portlet-->

                <div class="form-group m-form__group row">
                    <div class="m-section__content col-lg-12">
                        <div><strong>Debit Account</strong></div>
                        <div class="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse">
                                <tr>
                                    <th width="5%"></th>
                                    <th width="30%">Account List</th>
                                    <th width="15%">Cheque Leaf</th>
                                    <th width="10%">Cheque Date</th>
                                    <th width="15%">Remarks</th>
                                    <th class="text-right" width="20%">Amount</th>
                                    <th width="5%"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(debit_details, index) in form.debit_details"
                                    :key="debit_details.id">
                                    <td scope="row" width="5%">
                                        <a href="javascript:void(0)" @click="addNewDebitAccount"
                                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </td>
                                    <td width="30%">
                                        <select class="form-control m-select2 select2  select-account"
                                                v-bind:data-index="index" v-model="debit_details.debit_account_id"
                                                style="width:100%">
                                            <option v-for="(value,index) in bank_wise_account_list" :value="value.id">
                                                {{ value.chart_of_accounts_title +'('+ value.chart_of_account_code
                                                +')'}}
                                            </option>
                                        </select>
                                        <div class="requiredField"
                                             v-if="errors['debit_details.'+index+'.debit_account_id']">{{
                                            errors['debit_details.'+index+'.debit_account_id'][0] }}
                                        </div>
                                    </td>
                                    <td width="15%">
                                        <div class="input-group">
                                            <select class="form-control m-select2 select2 select-check-leaf"
                                                    v-bind:data-index="index" v-model="debit_details.cheque_no"
                                                    style="width:100%;">
                                                <option v-for="(value,index) in debit_details.check_leaf_lists"
                                                        :value="value.leaf_number"> {{value.leaf_number}}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="requiredField" v-if="errors['debit_details.'+index+'.cheque_no']">{{
                                            errors['debit_details.'+index+'.cheque_no'][0] }}
                                        </div>
                                    </td>
                                    <td width="10%">
                                        <div class="input-group">
                                            <input type="text"
                                                   class="form-control form-control-sm m-input cheque_date width-130"
                                                   v-bind:data-index="index" v-model="debit_details.cheque_date"
                                                   readonly placeholder="date"/>
                                        </div>
                                        <div class="requiredField" v-if="errors['debit_details.'+index+'.cheque_date']">
                                            {{ errors['debit_details.'+index+'.cheque_date'][0] }}
                                        </div>
                                    </td>
                                    <td width="15%">
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm m-input width-120"
                                                   v-model="debit_details.remarks"/>
                                        </div>
                                        <div class="requiredField" v-if="errors['debit_details.'+index+'.remarks']">{{
                                            errors['debit_details.'+index+'.remarks'][0] }}
                                        </div>
                                    </td>
                                    <td width="20%">
                                        <div class="input-group">
                                            <input type="number" step="any"
                                                   class="form-control form-control-sm m-input text-right"
                                                   style="min-width:130px;" v-model="debit_details.debit_amount"
                                                   @input="calculation()"/>
                                        </div>
                                        <div class="requiredField"
                                             v-if="errors['debit_details.'+index+'.debit_amount']">{{
                                            errors['debit_details.'+index+'.debit_amount'][0] }}
                                        </div>
                                    </td>
                                    <td width="5%">
                                        <a @click="removeDebitAccount(index)"
                                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Total</td>
                                    <td class="">
                                        <input type="number" step="any" v-model="form.total_debit_amount"
                                               class="form-control form-control-sm m-input text-right" readonly>
                                    </td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <br><br>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <div class="m-section__content col-lg-12">
                        <div><strong>Credit Account</strong></div>
                        <div class="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse">
                                <tr>
                                    <th width="5%"></th>
                                    <th width="20%">Sub Code2 List</th>
                                    <th width="20%">Account List</th>
                                    <th width="20%">{{type_name_title}}</th>
                                    <th width="20%">Remarks</th>
                                    <th class="text-right" width="30%">Amount</th>
                                    <th width="5%"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(credit_details, index) in form.credit_details"
                                    :key="credit_details.id">
                                    <td scope="row">
                                        <a href="javascript:void(0)" @click="addNewCreditAccount"
                                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <select class="form-control m-select2 select2  select-sbc2"
                                                v-bind:data-index="index" v-model="credit_details.sub_code2_id">
                                            <option v-for="(value,index) in subcode2_list" :value="value.id"> {{
                                                value.sub_code_title2 +'('+ value.sub_code2 +')'}}
                                            </option>
                                        </select>
                                        <div class="requiredField"
                                             v-if="errors['credit_details.'+index+'.sub_code2_id']">{{
                                            errors['credit_details.'+index+'.sub_code2_id'][0] }}
                                        </div>
                                    </td>

                                    <td>
                                        <select class="form-control m-select2 select2 select-credit-account"
                                                v-bind:data-index="index" v-model="credit_details.credit_account_id">
                                            <option v-for="(value,index) in credit_details.account_list"
                                                    :value="value.id"> {{ value.chart_of_accounts_title +'('+
                                                value.chart_of_account_code +')'}}
                                            </option>
                                        </select>
                                        <div class="requiredField"
                                             v-if="errors['credit_details.'+index+'.credit_account_id']">{{
                                            errors['credit_details.'+index+'.credit_account_id'][0] }}
                                        </div>
                                    </td>

                                    <td>
                                        <select class="form-control m-select2 select2  select-type_id "
                                                v-bind:data-index="index" v-model="credit_details.type_id">
                                            <option v-for="(value,index) in type_list" :value="value.id">
                                                {{value.type_name}}
                                            </option>
                                        </select>
                                        <div class="requiredField" v-if="errors['credit_details.'+index+'.type_id']">{{
                                            errors['credit_details.'+index+'.type_id'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="text" class="form-control form-control-sm m-input text-right" v-model="credit_details.credit_remarks"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input type="number" step="any"
                                                   class="form-control form-control-sm m-input text-right"
                                                   v-model="credit_details.credit_amount" @input="calculation()"/>
                                        </div>
                                        <div class="requiredField"
                                             v-if="errors['credit_details.'+index+'.credit_amount']">{{
                                            errors['credit_details.'+index+'.credit_amount'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <a @click="removeCreditAccount(index,credit_details.id)"
                                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Total</td>
                                    <td class="">
                                        <input type="number" step="any" v-model="form.total_credit_amount"
                                               class="form-control form-control-sm m-input text-right" readonly>
                                    </td>
                                    <td></td>
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
                            <button type="submit" v-if="permissions.indexOf('bank-receipt-vouchers.approve') != -1" id="approve"
                                    @click.prevent="update(form.id,1)" class="btn btn-sm btn-primary"><i
                                class="la la-check"></i> <span>Approve</span></button>
                            <button type="submit" class="btn btn-sm btn-success" id="submit"
                                    @click.prevent="update(form.id,0)"><i class="la la-check"></i><span>Update and Go List</span>
                            </button>
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

        props: ['permissions', 'cost_center_list', 'subcode2_list', 'editId', 'branch_list', 'bank_wise_account_list'],

        data: function () {
            return {

                data_list: false,
                add_form: true,
                edit_form: false,

                type_name_title: 'Type Name',
                type_list: [],

                form: {
                    receipt_transaction_no: '',
                    reciept_date: '',
                    type: '',
                    cost_center_id: '',
                    branch_id: '',
                    narration: '',
                    total_debit_amount: '',
                    total_credit_amount: '',

                    debit_details: [
                        {
                            debit_account_id: '',
                            remarks: '',
                            cheque_no: '',
                            cheque_date: '',
                            debit_amount: '',
                            check_leaf_lists: [],
                        }
                    ],
                    credit_details: [
                        {
                            sub_code2_id: '',
                            credit_account_id: '',
                            type_id: '',
                            type_desc: '', //hidden
                            credit_remarks: '',
                            credit_amount: '',
                            account_list: [],
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {

            addNewDebitAccount() {
                this.form.debit_details.push({
                    debit_account_id: '',
                    remarks: '',
                    cheque_no: '',
                    cheque_date: '',
                    debit_amount: '',
                    check_leaf_lists: [],
                });

                var Select2 = {
                    init: function () {
                        $(".select2").select2({
                                placeholder: "Please select an option"
                            }
                        )
                    }
                };
                jQuery(document).ready(function () {
                        Select2.init()
                    }
                );
            },

            removeDebitAccount(index, deletedId) {
                var _this = this;
                if (_this.form.debit_details.length > 1) {
                    _this.form.debit_details.splice(index, 1);
                }

                if (deletedId) {
                    _this.form.deleteID.push(deletedId);
                }
                _this.calculation();
            },


            addNewCreditAccount() {
                this.form.credit_details.push({
                    sub_code2_id: '',
                    credit_account_id: '',
                    type_id: '',
                    type_desc: '', //hidden
                    credit_remarks: '',
                    credit_amount: '',
                    account_list: [],
                });

                var Select2 = {
                    init: function () {
                        $(".select2").select2({
                                placeholder: "Please select an option"
                            }
                        )
                    }
                };
                jQuery(document).ready(function () {
                        Select2.init()
                    }
                );
            },

            removeCreditAccount(index, deletedId) {
                var _this = this;
                if (_this.form.credit_details.length > 1) {
                    _this.form.credit_details.splice(index, 1);
                }

                if (deletedId) {
                    _this.form.deleteID.push(deletedId);
                }
                _this.calculation();

            },


            edit(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'bank-receipt-voucher/' + id + '/edit')
                    .then((response) => {
                        _this.$loading(false);
                        _this.type_name_title = response.data.data.type_data.type_name_title;
                        _this.type_list = response.data.data.type_data.type_list;
                        _this.form = response.data.data;
                        setTimeout(function () {
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
                _this.$loading(true);
                axios.put(base_url + 'bank-receipt-voucher/' + id, _this.form)
                    .then((response) => {

                        document.getElementById("submit").disabled = true;
                        if ($('#approve').length > 0) {
                            document.getElementById("approve").disabled = true;
                        }

                        _this.$loading(false);
                        _this.showMassage(response.data);
                        EventBus.$emit('data-changed');
                    })
                    .catch(error => {
                        _this.$loading(false);
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

            getCheckLeafLists(credit_account_id, acctIndex) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + "coa-wise-check-leaf/" + credit_account_id)
                    .then((response) => {
                        _this.$loading(false);
                        if (response.data.length > 0) {
                            _this.form.debit_details[acctIndex].check_leaf_lists = response.data;

                            setTimeout(function () {
                                var Select2 = {
                                    init: function () {
                                        $(".select2").select2({placeholder: "Please select an option"})
                                    }
                                };
                                jQuery(document).ready(function () {
                                    Select2.init()
                                });

                            }, 100);
                        } else {
                            _this.form.debit_details[acctIndex].check_leaf_lists = response.data;
                            alert('No Check Leaf! Please Generate New Check Book Leaf');

                            setTimeout(function () {
                                var Select2 = {
                                    init: function () {
                                        $(".select2").select2({placeholder: "Please select an option"})
                                    }
                                };
                                jQuery(document).ready(function () {
                                    Select2.init()
                                });

                            }, 100);
                        }
                    });
            },

            getSbCode2WiseCoaLists(sub_code2_id, acctIndex2) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + "sbc2-wise-coa/" + sub_code2_id)
                    .then((response) => {
                        _this.$loading(false);
                        if (response.data.length > 0) {
                            _this.form.credit_details[acctIndex2].account_list = response.data;

                            setTimeout(function () {
                                var Select2 = {
                                    init: function () {
                                        $(".select2").select2({placeholder: "Please select an option"})
                                    }
                                };
                                jQuery(document).ready(function () {
                                    Select2.init()
                                });
                            }, 100);
                        }
                    });
            },

            loadTypeDetails() {
                var _this = this;
                var type = _this.form.type;
                _this.$loading(true);
                axios.get(base_url + "get_account_type_list/" + type)
                    .then((response) => {
                        _this.$loading(false);
                        _this.type_name_title = response.data.type_name_title;
                        _this.type_list = response.data.type_list;

                        setTimeout(function () {
                            var Select2 = {
                                init: function () {
                                    $(".select2").select2({
                                            placeholder: "Please select an option"
                                        }
                                    )
                                }
                            };
                            jQuery(document).ready(function () {
                                    Select2.init();
                                }
                            );
                        }, 100);
                    });
            },

            calculation() {
                var _this = this;
                var totalDebit = 0;
                var totalCredit = 0;

                var debit_details = this.form.debit_details;
                var debit_length = debit_details.length;

                var credit_details = this.form.credit_details;
                var credit_length = credit_details.length;

                for (let i = 0; i < debit_length; i++) {
                    totalDebit = Number(debit_details[i].debit_amount) + totalDebit;
                }

                for (let i = 0; i < credit_length; i++) {
                    totalCredit = Number(credit_details[i].credit_amount) + totalCredit;
                }

                totalDebit = totalDebit.toFixed(2);
                totalCredit = totalCredit.toFixed(2);

                _this.form.total_debit_amount = totalDebit;
                _this.form.total_credit_amount = totalCredit;

                if (totalDebit != totalCredit) {
                    $('body').find('#submit').attr('disabled', true);
                } else {
                    $('body').find('#submit').attr('disabled', false);
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
            var Select2 = {
                init: function () {
                    $(".select2").select2({
                            placeholder: "Please select an option"
                        }
                    )
                }
            };
            jQuery(document).ready(function () {
                    Select2.init()
                }
            );

            $('#editComponent').on('change', '.select2', function (e) {
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

            $('#editComponent').on('change', '.select-account', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.debit_details[dataIndex].debit_account_id = selectedItem.val();

                _this.getCheckLeafLists(selectedItem.val(), dataIndex);

            });

            $('#editComponent').on('change', '.select-type_id', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.credit_details[dataIndex].type_id = selectedItem.val();
                _this.form.credit_details[dataIndex].type_desc = $('option:selected', this).text();
            });

            $('#editComponent').on('change', '.select-check-leaf', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.form.debit_details[dataIndex].cheque_no = selectedItem.val();
            });

            $('#editComponent').on('change', '.select-sbc2', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.credit_details[dataIndex].sub_code2_id = selectedItem.val();
                _this.getSbCode2WiseCoaLists(selectedItem.val(), dataIndex);
            });

            $('#editComponent').on('change', '.select-credit-account', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.credit_details[dataIndex].credit_account_id = selectedItem.val()
            });


            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.reciept_date = '';

                    } else {
                        _this.form.reciept_date = moment(ev.date).format('DD/MM/YYYY');

                    }
                });
            });

            $(document).on("focus", ".cheque_date", function (e) {
                var selectedItem = $(this);
                var dataIndex = $(e.currentTarget).attr('data-index');
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                }).on('click change', function () {
                    _this.form.debit_details[dataIndex].cheque_date = selectedItem.val();
                })
            });
        },

        created() {
            var _this = this;
            _this.edit(_this.editId);
            _this.calculation();
        }
    }
</script>
