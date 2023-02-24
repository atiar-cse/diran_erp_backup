<template>
    <div>
        <!--begin::Form-->

        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">
                <div class="col-lg-12">
                    <div class="form-group m-form__group row">
                        <div class="m-section__content col-lg-6">

                            <div class="form-group m-form__group row">
                                <label class="col-lg-4 col-form-label" for="lc_no">LC No <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-7">
                                    <input type="text" id="lc_no" v-model="form.lc_no"
                                           class="form-control form-control-sm m-input" placeholder="Enter LC No">
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('lc_no'))">{{
                                        (errors.hasOwnProperty('lc_no')) ? errors.lc_no[0] :'' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label class="col-lg-4 col-form-label" for="lc_opening_date">LC Open Date <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-7">
                                    <div class="input-group date">
                                        <input type="text" class="form-control form-control-sm m-input datepicker"
                                               v-model="form.lc_opening_date" data-dateField="lc_opening_date" readonly
                                               placeholder="Select date from picker" id="lc_opening_date"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                            <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('lc_opening_date'))">{{
                                        (errors.hasOwnProperty('lc_opening_date')) ? errors.lc_opening_date[0] :'' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label class="col-lg-4 col-form-label" for="proforma_invoice_id">PI No <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-7">
                                    <select class="form-control m-select2 select2  select-pi-no"
                                            id="proforma_invoice_id" data-selectField="proforma_invoice_id"
                                            v-model="form.proforma_invoice_id">
                                        <option v-for="(svalue,sindex) in pi_invoice_lists"
                                                :value="svalue.id"
                                                :total_amount_taka="svalue.total_amount_taka"
                                                :supplier_id="svalue.supplier_id"
                                                :get_supplier="svalue.get_supplier ? svalue.get_supplier.purchase_supplier_name : ''"
                                        > {{svalue.pi_no}} ({{svalue.get_work_order ?
                                            svalue.get_work_order.work_order_no : ''}})
                                        </option>
                                    </select>
                                    <div class="requiredField" v-if="errors['proforma_invoice_id']">{{
                                        errors['proforma_invoice_id'][0] }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label for="supplier_info_display" class="col-lg-4 col-form-label">Supplier
                                    Party</label>
                                <div class="col-lg-7">
                                    <input type="text" id="supplier_info_display" v-model="form.supplier_info_display"
                                           class="form-control form-control-sm m-input" placeholder="Supplier" readonly>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label class="col-lg-4 col-form-label" for="lc_category">LC Category <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-7">
                                    <select class="form-control select2" id="lc_category" v-model="form.lc_category"
                                            data-selectField="lc_category">
                                        <option value="1"> Export</option>
                                        <option value="2"> Import</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('lc_category'))">{{
                                        (errors.hasOwnProperty('lc_category')) ? errors.lc_category[0] :'' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label class="col-lg-4 col-form-label" for="lc_type">LC Type <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-7">
                                    <select class="form-control select2" id="lc_type" v-model="form.lc_type"
                                            data-selectField="lc_type">
                                        <option value="1"> Master LC</option>
                                        <option value="2"> Deffard LC</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('lc_type'))">{{
                                        (errors.hasOwnProperty('lc_type')) ? errors.lc_type[0] :'' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--col-md-6-->
                        <div class="col-md-6">

                            <div class="form-group m-form__group row">
                                <label class="col-lg-4 col-form-label" for="lc_opening_bank_id">LC Opening Bank <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-7">
                                    <select class="form-control select2" id="lc_opening_bank_id"
                                            v-model="form.lc_opening_bank_id" data-selectField="lc_opening_bank_id">
                                        <option>---Select Bank---</option>
                                        <option v-for="(value,index) in bank_lists" :value="value.id">
                                            {{value.accounts_bank_names}}
                                        </option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('lc_opening_bank_id'))">{{
                                        (errors.hasOwnProperty('lc_opening_bank_id')) ? errors.lc_opening_bank_id[0] :''
                                        }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row">
                                <label class="col-lg-4 col-form-label" for="baneficiary_bank">LC Beneficiary
                                    Bank </label>
                                <div class="col-lg-7">
                                    <select class="form-control select2" id="baneficiary_bank"
                                            v-model="form.baneficiary_bank" data-selectField="baneficiary_bank">
                                        <option>---Select Beneficiary Bank---</option>
                                        <option v-for="(value,index) in bank_lists" :value="value.id">
                                            {{value.accounts_bank_names}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label class="col-lg-4 col-form-label" for="lc_latest_shipment_date">LC Latest Shipment
                                    Date </label>
                                <div class="col-lg-7">
                                    <div class="input-group date">
                                        <input type="text" class="form-control form-control-sm m-input datepicker"
                                               v-model="form.lc_latest_shipment_date"
                                               data-dateField="lc_latest_shipment_date" readonly
                                               placeholder="Select date from picker" id="lc_latest_shipment_date"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                            <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="requiredField"
                                         v-if="(errors.hasOwnProperty('lc_latest_shipment_date'))">{{
                                        (errors.hasOwnProperty('lc_latest_shipment_date')) ?
                                        errors.lc_latest_shipment_date[0] :'' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label class="col-lg-4 col-form-label" for="lc_expire_date">LC Expire Date </label>
                                <div class="col-lg-7">
                                    <div class="input-group date">
                                        <input type="text" class="form-control form-control-sm m-input datepicker"
                                               v-model="form.lc_expire_date" data-dateField="lc_expire_date" readonly
                                               placeholder="Select date from picker" id="lc_expire_date"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                            <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('lc_expire_date'))">{{
                                        (errors.hasOwnProperty('lc_expire_date')) ? errors.lc_expire_date[0] :'' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label class="col-lg-4 col-form-label" for="lc_total_value">LC Total Value
                                    (BDT) </label>
                                <div class="col-lg-7">
                                    <input type="text" id="lc_total_value" v-model="form.lc_total_value"
                                           class="form-control form-control-sm m-input"
                                           placeholder="Enter LC Total Value">
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('lc_total_value'))">{{
                                        (errors.hasOwnProperty('lc_total_value')) ? errors.lc_total_value[0] :'' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- //col-md-6-->
                    </div>

                    <div class="m-form__heading">
                        <h3 class="m-form__heading-title">Accounts <i data-toggle="m-tooltip" data-width="auto"
                                                                      class="m-form__heading-help-icon flaticon-info"
                                                                      title=""
                                                                      data-original-title="If different than the corresponding address"></i>
                            <small>
                                <mark><i> Journal will hit once LC approved. </i></mark>
                            </small></h3>

                    </div>

                    <div class="form-group m-form__group row">
                        <label class="col-lg-2 col-form-label" for="amount">Opening Charge </label>
                        <div class="col-lg-4">
                            <input type="hidden" id="amount" v-model="form.amount"
                                   class="form-control form-control-sm m-input text-right" placeholder="Total Amount"
                                   readonly>
                            <input type="number" step="any" min="0" id="lc_opening_charges"
                                   v-model="form.lc_opening_charges" @input="totalLcOpeningCharge()"
                                   class="form-control form-control-sm m-input" placeholder="Enter Opening Charge">
                        </div>
                        <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                        <div class="col-lg-4">
                            <textarea class="form-control form-control-sm" id="narration" v-model="form.narration"
                                      placeholder="" rows="1"></textarea>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-lg-2 col-form-label" for="debit_account_id">Debit Account <span
                            class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="debit_account_id" v-model="form.debit_account_id"
                                    data-selectField="debit_account_id">
                                <option v-for="(value,index) in account_list" :value="value.id"> {{
                                    value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}
                                </option>
                            </select>

                        </div>
                        <label class="col-lg-2 col-form-label" for="credit_account_id">Credit Account <span
                            class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="credit_account_id" v-model="form.credit_account_id"
                                    data-selectField="credit_account_id">
                                <option v-for="(value,index) in account_list" :value="value.id"> {{
                                    value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}
                                </option>
                            </select>

                        </div>
                    </div>
                    <hr>
                    <div class="form-group m-form__group row">
                        <label class="col-lg-2 col-form-label" for="lc_bank_expenses">LC Bank Expense </label>
                        <div class="col-lg-4">
                            <input type="number" step="any" min="0" id="lc_bank_expenses"
                                   v-model="form.lc_bank_expenses" @input="totalLcOpeningCharge()"
                                   class="form-control form-control-sm m-input" placeholder="Enter Bank Expenses">
                        </div>
                        <label class="col-lg-2 col-form-label" for="narrationB">Narration </label>
                        <div class="col-lg-4">
                            <textarea class="form-control form-control-sm" id="narrationB" v-model="form.narrationB"
                                      placeholder="" rows="1"></textarea>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-lg-2 col-form-label" for="debit_account_id">Debit Account <span
                            class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="debit_account_idB" v-model="form.debit_account_idB"
                                    data-selectField="debit_account_idB">
                                <option v-for="(value,index) in account_list" :value="value.id"> {{
                                    value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}
                                </option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('debit_account_idB'))">{{
                                (errors.hasOwnProperty('debit_account_idB')) ? errors.debit_account_idB[0] :'' }}
                            </div>
                        </div>
                        <label class="col-lg-2 col-form-label" for="credit_account_idB">Credit Account <span
                            class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="credit_account_idB"
                                    v-model="form.credit_account_idB" data-selectField="credit_account_idB">
                                <option v-for="(value,index) in account_list" :value="value.id"> {{
                                    value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}
                                </option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('credit_account_idB'))">{{
                                (errors.hasOwnProperty('credit_account_idB')) ? errors.credit_account_idB[0] :'' }}
                            </div>
                        </div>
                    </div>

                    <!--col-lg-12-->
                </div>

            </div>
            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="submit" class="btn btn-sm btn-info" @click.prevent="update(form.id,0)"><i
                                class="la la-save"></i> <span>Update</span></button>
                            <button v-if="permissions.indexOf('acc-lc-opening-section.approve') !=-1" type="submit"
                                    class="btn btn-sm btn-success" @click.prevent="update(form.id,1)"><i
                                class="la la-check"></i> <span>Approve</span></button>
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

        props: ['permissions', 'pi_invoice_lists', 'bank_lists', 'account_list', 'editId'],

        data: function () {
            return {

                data_list: false,
                add_form: true,
                edit_form: false,

                form: {
                    lc_no: '',
                    supplier_id: '',
                    supplier_info_display: '',
                    proforma_invoice_id: '',
                    lc_category: '',
                    lc_type: '',
                    lc_opening_date: '',
                    lc_opening_charges: '',
                    lc_opening_bank_id: '',
                    lc_bank_expenses: '',
                    baneficiary_bank: '',
                    lc_latest_shipment_date: '',
                    lc_expire_date: '',
                    lc_total_value: '',

                    lc_status: 1,
                    status: '1',
                    approve: '',

                    amount: '',

                    debit_account_id: '',
                    credit_account_id: '',
                    debit_account_idB: '',
                    credit_account_idB: '',

                    narration: '',
                    narrationB: '',
                },
                errors: {},
            };
        },

        methods: {

            edit(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'acc-lc-opening-section/' + id + '/edit')
                    .then((response) => {
                        _this.$loading(false);
                        _this.form = response.data.data;
                        setTimeout(function () {
                            var Select2 = {
                                init: function () {
                                    $(".select2").select2({placeholder: "Please select an option"})
                                }
                            };
                            jQuery(document).ready(function () {
                                Select2.init();
                            });
                        }, 500);
                    });
            },

            update(id, approval) {
                var _this = this;
                _this.form.approve = approval;
                _this.$loading(true);
                axios.put(base_url + 'acc-lc-opening-section/' + id, _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        _this.showMassage(response.data);
                        EventBus.$emit('data-changed');
                    })
                    .catch(error => {
                        _this.$loading(false);
                        if (error.response.status == 422) {
                            _this.errors = error.response.data.errors;
                        } else {
                            _this.showMassage(error);
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

            totalLcOpeningCharge() {
                var total_amount = 0;
                total_amount = Number(this.form.lc_opening_charges) + Number(this.form.lc_bank_expenses);

                this.form.amount = Number(total_amount).toFixed(2);
            },
        },

        mounted() {
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

                if (selectField == 'debit_account_id') {
                    _this.form.debit_account_id = selectedItem.val();
                } else if (selectField == 'credit_account_id') {
                    _this.form.credit_account_id = selectedItem.val();
                } else if (selectField == 'debit_account_idB') {
                    _this.form.debit_account_idB = selectedItem.val();
                } else if (selectField == 'credit_account_idB') {
                    _this.form.credit_account_idB = selectedItem.val();
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

                    if (ev.date == undefined) {
                        if (selectField == 'lc_opening_date') {
                            _this.form.lc_opening_date = '';
                        } else if (selectField == 'lc_latest_shipment_date') {
                            _this.form.lc_latest_shipment_date = '';
                        } else if (selectField == 'lc_expire_date') {
                            _this.form.lc_expire_date = '';
                        }
                    } else if (selectField == 'lc_opening_date') {
                        _this.form.lc_opening_date = moment(ev.date).format('DD/MM/YYYY');
                    } else if (selectField == 'lc_latest_shipment_date') {
                        _this.form.lc_latest_shipment_date = moment(ev.date).format('DD/MM/YYYY');
                    } else if (selectField == 'lc_expire_date') {
                        _this.form.lc_expire_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created() {
            var _this = this;
            _this.edit(_this.editId);
            _this.totalLcOpeningCharge();
        }
    }
</script>
