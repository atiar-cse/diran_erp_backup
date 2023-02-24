<template>
    <div>
        <!--begin::Form-->
        <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data" id="editComponent"
              method="POST"
              v-on:submit.prevent="update(form.id)">
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="payment_transaction_no" class="col-lg-2 col-form-label">Payment No </label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="payment_transaction_no"
                               placeholder="Enter Payment Transaction No"
                               type="text" v-model="form.payment_transaction_no">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('payment_transaction_no'))">{{
                            (errors.hasOwnProperty('payment_transaction_no')) ? errors.payment_transaction_no[0] :'' }}
                        </div>
                    </div>
                    <label for="payment_date" class="col-lg-2 col-form-label">Payment Date </label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input class="form-control form-control-sm m-input datepicker" data-dateField="payment_date"
                                   id="payment_date" placeholder="Select date from picker" readonly
                                   type="text" v-model="form.payment_date"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('payment_date'))">{{
                            (errors.hasOwnProperty('payment_date')) ? errors.payment_date[0] :'' }}
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
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <label class="col-lg-2 col-form-label" for="project_id">Project </label>
                    <div class="col-lg-3">
                        <select class="form-control m-select2 select2" data-selectField="project_id" id="project_id"
                                v-model="form.project_id">
                            <option > No Selection </option>
                            <option :value="value.id" v-for="(value,oindex) in project_list"> {{value.project_name}}
                            </option>
                        </select>
                    </div>
                    <div class="col-lg-1">
                        <a class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-offset="-20px -20px"
                           data-placement="top" data-toggle="m-tooltip" href="project-info" title="Add Project">
                            <i class="la la-plus"></i>
                        </a>
                    </div>
                </div>

                <div class="form-group m-form__group row">
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
                        <div><strong>Debit Account</strong></div>
                        <div class="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse">
                                <tr>
                                    <th width="5%"></th>
                                    <th width="20%">Sub Code2 List</th>
                                    <th width="20%">Account List</th>
                                    <th width="20%">{{type_name_title}}</th>
                                    <th v-if="is_lc_type==true" width="15%">Voucher Type</th>
                                    <th v-if="is_lc_type==true" width="15%">Voucher Ref</th>
                                    <th class="text-right" width="30%">Remarks</th>
                                    <th class="text-right" width="30%">Amount</th>
                                    <th width="5%"></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(debit_details, index) in form.debit_details"
                                    :key="debit_details.id">
                                    <td scope="row">
                                        <a @click="addNewDebitAccount"
                                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px"
                                           data-placement="top" data-toggle="m-tooltip" href="javascript:void(0)"
                                           title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </td>
                                    <!--<td>
                                        <select class="form-control m-select2 select2  select-account" v-bind:data-index="index" v-model="debit_details.debit_account_id">
                                            <option v-for="(value,index) in account_list" :value="value.id" > {{ value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['debit_details.'+index+'.debit_account_id']">{{ errors['debit_details.'+index+'.debit_account_id'][0] }}</div>
                                    </td>-->
                                    <td>
                                        <select style="width:100%" class="form-control m-select2 select2  select-sbc2"
                                                v-bind:data-index="index" v-model="debit_details.sub_code2_id">
                                            <option :value="value.id" v-for="(value,index) in subcode2_list"> {{
                                                value.sub_code_title2 +'('+ value.sub_code2 +')'}}
                                            </option>
                                        </select>
                                        <div class="requiredField"
                                             v-if="errors['debit_details.'+index+'.sub_code2_id']">{{
                                            errors['debit_details.'+index+'.sub_code2_id'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <select style="width:100%" class="form-control m-select2 select2  select-account"
                                                v-bind:data-index="index" v-model="debit_details.debit_account_id">
                                            <option :value="value.id"
                                                    v-for="(value,index) in debit_details.sb2_wise_coa_lists"> {{
                                                value.chart_of_accounts_title +'('+
                                                value.chart_of_account_code +')'}}
                                            </option>
                                        </select>
                                        <div class="requiredField"
                                             v-if="errors['debit_details.'+index+'.debit_account_id']">{{
                                            errors['debit_details.'+index+'.debit_account_id'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <select style="width:100%" class="form-control m-select2 select2  select-type_id"
                                                v-bind:data-index="index" v-model="debit_details.type_id">
                                            <option :value="value.id" v-for="(value,index) in type_list">
                                                {{value.type_name}}
                                            </option>
                                        </select>
                                        <div class="requiredField" v-if="errors['debit_details.'+index+'.type_id']">{{
                                            errors['debit_details.'+index+'.type_id'][0] }}
                                        </div>
                                    </td>
                                    <td v-if="is_lc_type==true">
                                        <select style="width:100%" class="form-control m-select2 select2  select-voucher_type"
                                                v-bind:data-index="index"
                                                v-model="debit_details.voucher_type_id">
                                            <option :value="value.id" v-for="(value,index) in voucher_type_list">
                                                {{value.voucher_type_name}}
                                            </option>
                                        </select>
                                        <div class="requiredField"
                                             v-if="errors['debit_details.'+index+'.voucher_type_id']">{{
                                            errors['debit_details.'+index+'.voucher_type_id'][0] }}
                                        </div>
                                    </td>
                                    <td v-if="is_lc_type==true">
                                        <select style="width:100%" class="form-control m-select2 select2  select-voucher_ref"
                                                v-bind:data-index="index"
                                                v-model="debit_details.voucher_ref_id">
                                            <option :value="value.id"
                                                    v-for="(value,index) in debit_details.voucher_ref_list">
                                                {{value.transaction_no}}
                                            </option>
                                        </select>
                                        <div class="requiredField"
                                             v-if="errors['debit_details.'+index+'.voucher_ref_id']">{{
                                            errors['debit_details.'+index+'.voucher_ref_id'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input class="form-control form-control-sm m-input" style="min-width:120px;"
                                                   type="text" v-model="debit_details.remarks"/>
                                        </div>
                                        <div class="requiredField" v-if="errors['debit_details.'+index+'.remarks']">{{
                                            errors['debit_details.'+index+'.remarks'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input @input="calculation()"
                                                   class="form-control form-control-sm m-input text-right width-100"
                                                   step="any"
                                                   type="number" v-model="debit_details.debit_amount"/>
                                        </div>
                                        <div class="requiredField"
                                             v-if="errors['debit_details.'+index+'.debit_amount']">{{
                                            errors['debit_details.'+index+'.debit_amount'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <a @click="removeDebitAccount(index,debit_details.id)"
                                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-placement="top" data-toggle="m-tooltip"
                                           title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr v-if="is_lc_type==true">
                                    <td class="text-right" colspan="6">Total</td>
                                    <td class="">
                                        <input class="form-control form-control-sm m-input text-right" readonly
                                               step="any"
                                               type="number" v-model="form.total_debit_amount">
                                    </td>
                                    <td></td>
                                </tr>
                                <tr v-if="is_lc_type==false">
                                    <td class="text-right" colspan="5">Total</td>
                                    <td class="">
                                        <input class="form-control form-control-sm m-input text-right" readonly
                                               step="any"
                                               type="number" v-model="form.total_debit_amount">
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
                                    <th></th>
                                    <th>Account List</th>
                                    <th>Cheque Leaf</th>
                                    <th>Remarks</th>
                                    <th>Cheque Date</th>
                                    <th class="text-right">Amount</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(credit_details, index) in form.credit_details"
                                    :key="credit_details.id">
                                    <td scope="row">
                                        <a @click="addNewCreditAccount"
                                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px"
                                           data-placement="top" data-toggle="m-tooltip" href="javascript:void(0)"
                                           title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <select class="form-control m-select2 select2 select-credit-account"
                                                style="width: 100%" v-bind:data-index="index"
                                                v-model="credit_details.credit_account_id">
                                            <option :value="value.id" v-for="(value,index) in bank_wise_account_list">
                                                {{ value.chart_of_accounts_title +'('+ value.chart_of_account_code
                                                +')'}}
                                            </option>
                                        </select>
                                        <div class="requiredField"
                                             v-if="errors['credit_details.'+index+'.credit_account_id']">{{
                                            errors['credit_details.'+index+'.credit_account_id'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <select class="form-control m-select2 select2 select-check-leaf"
                                                    style="min-width:150px;" v-bind:data-index="index"
                                                    v-model="credit_details.check_leaf">
                                                <option :value="value.leaf_number"
                                                        v-for="(value,index) in credit_details.check_leaf_lists">
                                                    {{value.leaf_number}}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="requiredField" v-if="errors['credit_details.'+index+'.check_leaf']">
                                            {{ errors['credit_details.'+index+'.check_leaf'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input class="form-control form-control-sm m-input" style="min-width:120px;"
                                                   type="text" v-model="credit_details.remarks"/>
                                        </div>
                                        <div class="requiredField" v-if="errors['credit_details.'+index+'.remarks']">{{
                                            errors['credit_details.'+index+'.remarks'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input class="form-control form-control-sm m-input check_date  width-120"
                                                   placeholder="date"
                                                   readonly type="text"
                                                   v-bind:data-index="index" v-model="credit_details.check_date"/>
                                        </div>
                                        <div class="requiredField" v-if="errors['credit_details.'+index+'.check_date']">
                                            {{ errors['credit_details.'+index+'.check_date'][0] }}
                                        </div>
                                    </td>

                                    <td>
                                        <div class="input-group">
                                            <input @input="calculation()"
                                                   class="form-control form-control-sm m-input text-right"
                                                   step="any"
                                                   style="min-width:200px;" type="number"
                                                   v-model="credit_details.credit_amount"/>
                                        </div>
                                        <div class="requiredField"
                                             v-if="errors['credit_details.'+index+'.credit_amount']">{{
                                            errors['credit_details.'+index+'.credit_amount'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <a @click="removeCreditAccount(index,credit_details.id)"
                                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-placement="top" data-toggle="m-tooltip"
                                           title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right" colspan="5">Total</td>
                                    <td class="">
                                        <input class="form-control form-control-sm m-input text-right" readonly
                                               step="any"
                                               type="number" v-model="form.total_credit_amount">
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
                            <button id="approve" @click.prevent="update(form.id,1)" class="btn btn-sm btn-primary"
                                    type="submit" v-if="permissions.indexOf('bank-payment-vouchers.approve') != -1"><i
                                class="la la-check"></i> <span>Approve</span></button>
                            <button @click.prevent="update(form.id,0)" class="btn btn-sm btn-success" id="submit"
                                    type="submit"><i class="la la-check"></i> <span>Update and Go List</span>
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

        props: ['permissions', 'cost_center_list', 'editId', 'branch_list', 'bank_wise_account_list', 'subcode2_list'],

        data: function () {
            return {

                data_list: false,
                add_form: true,
                edit_form: false,

                is_lc_type: false,

                type_name_title: 'Type Name',
                type_list: [],
                voucher_type_list: [],
                project_list: [],

                form: {
                    payment_transaction_no: '',
                    payment_date: '',
                    type: '',
                    cost_center_id: '',
                    branch_id: '',
                    project_id: '',
                    narration: '',
                    total_debit_amount: '',
                    total_credit_amount: '',

                    approve: '',

                    debit_details: [
                        {
                            debit_account_id: '',
                            sub_code2_id: '',
                            type_id: '',
                            type_desc: '', //hidden
                            debit_amount: '',
                            sb2_wise_coa_lists: [],
                            remarks: '',
                            voucher_type_id: '',
                            voucher_type: '',
                            voucher_ref_id: '',
                            voucher_ref: '',
                            voucher_ref_list: [],
                        }
                    ],
                    credit_details: [
                        {
                            credit_account_id: '',
                            credit_amount: '',
                            check_leaf: '',
                            check_date: '',
                            remarks: '',
                            check_leaf_lists: [],
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {

            project_list_gen() {
                var _this = this;
                var token_project = 'token_project';
                axios.get(base_url + 'project-info?token_project=' + token_project)
                    .then((response) => {
                        _this.project_list = response.data;
                    });
            },

            getSbCode2WiseCoaLists(sub_code2_id, acctIndex2) {
                var _this = this;

                axios.get(base_url + "sbc2-wise-coa/" + sub_code2_id)
                    .then((response) => {
                        if (response.data.length > 0) {
                            _this.form.debit_details[acctIndex2].sb2_wise_coa_lists = response.data;
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

            getCheckLeafLists(credit_account_id, acctIndex) {
                var _this = this;

                axios.get(base_url + "coa-wise-check-leaf/" + credit_account_id).then((response) => {
                    if (response.data.length > 0) {
                        _this.form.credit_details[acctIndex].check_leaf_lists = response.data;

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
                        _this.form.credit_details[acctIndex].check_leaf_lists = response.data;
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

            addNewDebitAccount() {
                this.form.debit_details.push({
                    debit_account_id: '',
                    sub_code2_id: '',
                    type_id: '',
                    type_desc: '', //hidden
                    debit_amount: '',
                    sb2_wise_coa_lists: [],
                    remarks: '',
                    voucher_type_id: '',
                    voucher_type: '',
                    voucher_ref_id: '',
                    voucher_ref: '',
                    voucher_ref_list: [],
                });

                var Select2 = {
                    init: function () {
                        $(".select2").select2({placeholder: "Please select an option"})
                    }
                };
                jQuery(document).ready(function () {
                    Select2.init()
                });
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
                    credit_account_id: '',
                    check_leaf: '',
                    remarks: '',
                    check_date: '',
                    credit_amount: '',
                    check_leaf_lists: [],
                });

                var Select2 = {
                    init: function () {
                        $(".select2").select2({placeholder: "Please select an option"})
                    }
                };
                jQuery(document).ready(function () {
                    Select2.init()
                });
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
                axios.get(base_url + 'bank-payment-voucher/' + id + '/edit')
                    .then((response) => {
                        _this.type_name_title = response.data.data.type_data.type_name_title;
                        _this.type_list = response.data.data.type_data.type_list;
                        _this.form = response.data.data;
                        _this.is_lc_type = response.data.data.is_lc_type;

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

                this.form.approve = app;
                axios.put(base_url + 'bank-payment-voucher/' + id, this.form)
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

            loadTypeDetails() {
                var _this = this;
                var type = _this.form.type;

                axios.get(base_url + "get_account_type_list/" + type).then((response) => {
                    _this.type_name_title = response.data.type_name_title;
                    _this.type_list = response.data.type_list;

                    /*  _this.form.debit_details = [
                          {
                              debit_account_id: '',
                              type_id: '',
                              type_desc: '',
                              debit_amount: '',

                          }
                      ];*/

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
                var voucher_type_id = _this.form.debit_details[index].voucher_type_id;
                axios.get(base_url + "get_voucher_ref_list/" + voucher_type_id)
                    .then((response) => {
                        _this.form.debit_details[index].voucher_ref_list = response.data;
                        _this.initSelect2();
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
                }else if (selectField == 'project_id') {
                    _this.form.project_id = selectedItem.val();
                }
            });

            $('#editComponent').on('change', '.select-type_id', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.debit_details[dataIndex].type_id = selectedItem.val();
                _this.form.debit_details[dataIndex].type_desc = $('option:selected', this).text();
            });

            $('#editComponent').on('change', '.select-voucher_type', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.debit_details[dataIndex].voucher_type_id = selectedItem.val();
                _this.form.debit_details[dataIndex].voucher_type = $('option:selected', this).text();
                _this.loadVoucherRefDetails(dataIndex);
            });

            $('#editComponent').on('change', '.select-voucher_ref', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.debit_details[dataIndex].voucher_ref_id = selectedItem.val();
                _this.form.debit_details[dataIndex].voucher_ref = $('option:selected', this).text();
            });

            $('#editComponent').on('change', '.select-sbc2', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.debit_details[dataIndex].sub_code2_id = selectedItem.val();
                _this.getSbCode2WiseCoaLists(selectedItem.val(), dataIndex);
            });

            $('#editComponent').on('change', '.select-account', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.debit_details[dataIndex].debit_account_id = selectedItem.val();
            });

            $('#editComponent').on('change', '.select-check-leaf', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.form.credit_details[dataIndex].check_leaf = selectedItem.val();
            });

            $('#editComponent').on('change', '.select-credit-account', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.credit_details[dataIndex].credit_account_id = selectedItem.val();
                _this.getCheckLeafLists(selectedItem.val(), dataIndex);
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.payment_date = '';
                    } else {
                        _this.form.payment_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });

            $(document).on("focus", ".check_date", function (e) {
                var selectedItem = $(this);
                var dataIndex = $(e.currentTarget).attr('data-index');
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    //todayBtn: true,
                    clearBtn: true,
                }).on('click change', function () {
                    _this.form.credit_details[dataIndex].check_date = selectedItem.val();
                })
            });
        },

        created() {
            var _this = this;
            _this.loadVoucherTypeDetails();
            _this.edit(_this.editId);
            _this.calculation();
            _this.project_list_gen();
        }
    }
</script>
