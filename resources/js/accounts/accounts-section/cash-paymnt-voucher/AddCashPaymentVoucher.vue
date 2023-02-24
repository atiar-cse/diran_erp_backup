<template>
    <div>
        <div class="modal fade" id="addModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add Project Info</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data"
                          id="addModalComponent"
                          method="POST" v-on:submit.prevent="storeProject">
                        <div class="modal-body">
                            <input type="hidden" v-model="form2.id">
                            <div :class="{'has-danger': (errors.hasOwnProperty('project_name'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Project Name <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter Project Name" type="text"
                                           v-model="form2.project_name"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('project_name'))">{{
                                        (errors.hasOwnProperty('project_name')) ? errors.project_name[0] :'' }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('remarks'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Remarks <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter Remarks" type="text"
                                           v-model="form2.remarks"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('remarks'))">{{
                                        (errors.hasOwnProperty('remarks')) ? errors.remarks[0] :'' }}
                                    </div>
                                </div>
                            </div>
                            <div :class="{'has-danger': (errors.hasOwnProperty('status'))}"
                                 class="form-group m-form__group row">
                                <label class="col-form-label col-lg-4 col-sm-12">Status <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <span class="m-switch m-switch--icon">
                                        <label>
                                            <input type="checkbox" checked="checked" v-model="form2.status">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                            <button class="btn btn-sm btn-success" type="submit"><i class="la la-check"></i>
                                <span>Save</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!--begin::Form-->
        <form class="m-form m-form--fit m-form--label-align-right" id="addComponent" method="POST"
              v-on:submit.prevent="store">
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
                        <select class="form-control m-select2 select2" data-selectField="project_id" id="project_id" v-model="form.project_id">
                            <option > No Selection </option>
                            <option :value="value.id" v-for="(value,oindex) in project_list"> {{value.project_name}}</option>
                        </select>
                    </div>
                    <div class="col-lg-1">

                        <a class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-target="#addModel"
                           data-toggle="modal" href="javascript:void(0)"><i class="la la-plus"></i></a>

                        <!--<a href="project-info" class="btn btn-outline-success m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Add Project">
                            <i class="la la-plus"></i>
                        </a>-->
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" id="narration" rows="2"
                                  v-model="form.narration"></textarea>
                    </div>
                </div>
                <br>
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
                                    <th width="10%">Remarks</th>
                                    <th width="20%">{{type_name_title}}</th>
                                    <th v-if="is_lc_type==true" width="20%">Voucher Type</th>
                                    <th v-if="is_lc_type==true" width="20%">Voucher Ref</th>
                                    <th class="text-right" width="120px">Amount</th>
                                    <th width="5%"></th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr v-for="(debit_details, index) in form.debit_details" :key="debit_details.id">
                                    <td scope="row">
                                        <a @click="addNewDebitAccount"
                                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px"
                                           data-placement="top" data-toggle="m-tooltip" href="javascript:void(0)"
                                           title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <select class="form-control m-select2 select2  select-sbc2"
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
                                        <select class="form-control m-select2 select2  select-account"
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
                                        <div class="input-group">
                                            <input class="form-control form-control-sm m-input width-200" type="text"
                                                   v-model="debit_details.remarks"/>
                                        </div>
                                        <div class="requiredField" v-if="errors['debit_details.'+index+'.remarks']">{{
                                            errors['debit_details.'+index+'.remarks'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <select class="form-control m-select2 select2  select-type_id"
                                                style="min-width:200px;" v-bind:data-index="index"
                                                v-model="debit_details.type_id">
                                            <option :value="value.id" v-for="(value,index) in type_list">
                                                {{value.type_name}}
                                            </option>
                                        </select>
                                        <div class="requiredField" v-if="errors['debit_details.'+index+'.type_id']">{{
                                            errors['debit_details.'+index+'.type_id'][0] }}
                                        </div>
                                    </td>
                                    <td v-if="is_lc_type==true">
                                        <select class="form-control m-select2 select2  select-voucher_type"
                                                style="min-width:200px;" v-bind:data-index="index"
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
                                        <select class="form-control m-select2 select2  select-voucher_ref"
                                                style="min-width:200px;" v-bind:data-index="index"
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
                                    <td style="width: 120px;">
                                        <div class="input-group">
                                            <input @input="calculation()"
                                                   class="form-control form-control-sm m-input text-right" step="any"
                                                   style="width: 120px;"
                                                   type="number" v-model="debit_details.debit_amount"/>
                                        </div>
                                        <div class="requiredField"
                                             v-if="errors['debit_details.'+index+'.debit_amount']">{{
                                            errors['debit_details.'+index+'.debit_amount'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <a @click="removeDebitAccount(index)"
                                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-placement="top" data-toggle="m-tooltip"
                                           title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>

                                <tr v-if="is_lc_type==true">
                                    <td class="text-right" colspan="7">Total</td>
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
                                    <th class="width-50pr">Account List</th>
                                    <th class="width-50pr">Remarks</th>
                                    <th class="text-right width-50pr">Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(credit_details, index) in form.credit_details" :key="credit_details.id">
                                    <td>
                                        <select class="form-control m-select2 select2 select-credit-account"
                                                style="width: 100%" v-bind:data-index="index"
                                                v-model="credit_details.credit_account_id">
                                            <option :value="value.id" v-for="(value,index) in account_list"
                                                    v-if="value.chart_of_account_code == 320801 || value.chart_of_account_code == 320802">
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
                                        <input class="form-control form-control-sm m-input" id="remarks"
                                               placeholder="Enter Remarks"
                                               type="text" v-model="credit_details.remarks">
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('remarks'))">{{
                                            (errors.hasOwnProperty('remarks')) ? errors.remarks[0] :'' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="input-group">
                                            <input @input="calculation()" READONLY
                                                   class="form-control form-control-sm m-input text-right"
                                                   step="any"
                                                   type="number" v-model="credit_details.credit_amount"/>
                                        </div>
                                        <div class="requiredField"
                                             v-if="errors['credit_details.'+index+'.credit_amount']">{{
                                            errors['credit_details.'+index+'.credit_amount'][0] }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right" colspan="2">Total</td>
                                    <td class="">
                                        <input class="form-control form-control-sm m-input text-right" readonly
                                               step="any"
                                               type="number" v-model="form.total_credit_amount">
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
                            <button id="approve" @click.prevent="store(1)" class="btn btn-sm btn-primary"
                                    type="submit" v-if="permissions.indexOf('cash-payment-vouchers.approve') != -1"><i
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
        props: ['permissions', 'cost_center_list', 'account_list', 'subcode2_list', 'branch_list'],

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

                form2: {
                    project_name: '',
                    remarks: '',
                    status: '1'
                },

                form: {
                    payment_transaction_no: '',
                    payment_date: '',
                    type: '',
                    cost_center_id: '',
                    project_id: '',
                    branch_id: '',
                    narration: '',
                    total_debit_amount: '',
                    total_credit_amount: '',

                    approve: '',

                    debit_details: [
                        {
                            type_id: '',
                            type_desc: '', //hidden
                            sub_code2_id: '',
                            debit_account_id: '',
                            remarks: '',
                            debit_amount: '',
                            sb2_wise_coa_lists: [],

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
                            remarks: '',
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

            storeProject: function () {

                var _this = this;
                axios.post(base_url + 'store-project', this.form2).then((response) => {
                    /*this.showMassage(response.data);*/
                    $('#addModel').modal('hide');

                    console.log(response.data.project_id);
                    _this.form.project_id = response.data.project_id;
                    _this.project_list = response.data.project_list;

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

                    //EventBus.$emit('data-changed');
                }).catch(error => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        this.showMassage(error);
                    }
                });
            },

            addNewDebitAccount() {
                this.form.debit_details.push({
                    type_id: '',
                    type_desc: '', //hidden
                    sub_code2_id: '',
                    debit_account_id: '',
                    remarks: '',
                    debit_amount: '',
                    sb2_wise_coa_lists: [],

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

            removeDebitAccount(index) {
                var _this = this;
                if (_this.form.debit_details.length > 1) {
                    _this.form.debit_details.splice(index, 1);
                }
                _this.calculation();
            },

            transactionNoGenerate() {
                var _this = this;
                axios.get(base_url + 'cash-payment-no')
                    .then((response) => {
                        _this.form.payment_transaction_no = response.data;
                    });
            },

            store(app) {

                var _this = this;
                _this.form.approve = app;
                axios.post(base_url + 'cash-payment-voucher', this.form)
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

            getSbCode2WiseCoaLists(sub_code2_id, acctIndex2) {
                var _this = this;

                axios.get(base_url + "sbc2-wise-coa/" + sub_code2_id).then((response) => {
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

            loadTypeDetails() {
                var _this = this;
                var type = _this.form.type;

                axios.get(base_url + "get_account_type_list/" + type).then((response) => {
                    _this.type_name_title = response.data.type_name_title;
                    _this.type_list = response.data.type_list;

                    if (type == 'lc') {
                        _this.is_lc_type = true;
                    } else {
                        _this.is_lc_type = false;
                    }

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

                totalCredit = totalDebit;

                totalDebit = totalDebit.toFixed(2);
                totalCredit = totalCredit.toFixed(2);

                _this.form.total_debit_amount = totalDebit;
                _this.form.total_credit_amount = totalCredit;

                _this.form.credit_details[0].credit_amount = totalDebit;

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

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if (selectField == 'project_id') {
                    _this.form.project_id = selectedItem.val();
                } else if (selectField == 'type') {

                    _this.form.type = selectedItem.val();
                    _this.loadTypeDetails();
                }
            });

            $('#addComponent').on('change', '.select-sbc2', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.debit_details[dataIndex].sub_code2_id = selectedItem.val();
                _this.getSbCode2WiseCoaLists(selectedItem.val(), dataIndex);
            });


            $('#addComponent').on('change', '.select-account', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.debit_details[dataIndex].debit_account_id = selectedItem.val();
            });

            /* $('#addComponent').on('change', '.select-account', function (e) {
                 var selectedItem = $(this),
                     curerntVal = !!selectedItem.val(),
                     dataIndex = $(e.currentTarget).attr('data-index');
                     _this.form.debit_details[dataIndex].debit_account_id = selectedItem.val();
             });*/

            $('#addComponent').on('change', '.select-type_id', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.debit_details[dataIndex].type_id = selectedItem.val();
                _this.form.debit_details[dataIndex].type_desc = $('option:selected', this).text();
            });

            $('#addComponent').on('change', '.select-voucher_type', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.debit_details[dataIndex].voucher_type_id = selectedItem.val();
                _this.form.debit_details[dataIndex].voucher_type = $('option:selected', this).text();
                _this.loadVoucherRefDetails(dataIndex);
            });

            $('#addComponent').on('change', '.select-voucher_ref', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.debit_details[dataIndex].voucher_ref_id = selectedItem.val();
                _this.form.debit_details[dataIndex].voucher_ref = $('option:selected', this).text();
            });

            $('#addComponent').on('change', '.select-credit-account', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.credit_details[dataIndex].credit_account_id = selectedItem.val();
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
        },

        created() {
            var _this = this;
            _this.calculation();
            _this.transactionNoGenerate();
            _this.project_list_gen();
            _this.loadVoucherTypeDetails();
        }
    }
</script>
