<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <label for="lc_opening_info_id" class="col-lg-2 col-form-label">LC No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-lc-no" id="lc_opening_info_id"
                                data-selectField="lc_opening_info_id" v-model="form.lc_opening_info_id" disabled>
                            <option v-for="(svalue,sindex) in lc_lists"
                                    :value="svalue.id"
                                    :get_cnf_margin="svalue.get_cnf_margin ? svalue.get_cnf_margin.margin_percentage : ''"
                            > {{svalue.lc_no}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="errors['lc_opening_info_id']">{{
                            errors['lc_opening_info_id'][0] }}
                        </div>
                    </div>
                    <label for="lc_commercial_invoice_id" class="col-lg-2 col-form-label">CI No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-ci-no" id="lc_commercial_invoice_id"
                                data-selectField="lc_commercial_invoice_id" v-model="form.lc_commercial_invoice_id"
                                disabled>
                            <option v-for="(svalue,sindex) in ci_lists" :value="svalue.id"> {{svalue.ci_no}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['lc_commercial_invoice_id']">{{
                            errors['lc_commercial_invoice_id'][0] }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="latr_date" class="col-lg-2 col-form-label">LATR Date <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.latr_date" data-dateField="latr_date" readonly
                                   placeholder="Select date from picker" id="latr_date"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('latr_date'))">{{
                            (errors.hasOwnProperty('latr_date')) ? errors.latr_date[0] :'' }}
                        </div>
                    </div>
                    <label for="lc_margin_percentage" class="col-lg-2 col-form-label">LC Margin Percentage </label>
                    <div class="col-lg-4">
                        <input type="text" id="lc_margin_percentage" v-model="form.lc_margin_percentage"
                               class="form-control form-control-sm m-input" placeholder="LC Margin Percentage" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('lc_margin_percentage'))">{{
                            (errors.hasOwnProperty('lc_margin_percentage')) ? errors.lc_margin_percentage[0] :'' }}
                        </div>
                    </div>

                </div>

                <div class="form-group m-form__group row">
                    <label for="bank_latr_no" class="col-lg-2 col-form-label">Bank LATR No. </label>
                    <div class="col-lg-4">
                        <input type="text" id="bank_latr_no" v-model="form.bank_latr_no"
                               class="form-control form-control-sm m-input" placeholder="Bank LATR No." readonly>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.narration" id="narration"
                                  rows="2"></textarea>
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
                                    <th class="width-210">Product <span class="requiredField">*</span></th>
                                    <th>H.S Code</th>
                                    <th>Unit</th>
                                    <th>LC Unit Cost <span class="requiredField">*</span></th>
                                    <th>Qty <span class="requiredField">*</span></th>

                                    <th class="width-160">Total Price</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <td class="width-210">
                                        <select class="form-control m-select2 select2  select-product width-210"
                                                v-bind:data-index="index" v-model="details.product_id"
                                                style="width:100%" disabled>
                                            <option v-for="(value,index) in product_lists" :value="value.id"
                                                    :get_measure_unit_id="value.measure_unit_id"
                                            > {{value.product_name}}
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.hs_code"
                                               class="form-control form-control-sm m-input" placeholder="HS Code"
                                               readonly>
                                    </td>
                                    <td>
                                        <select class="form-control m-select2 select2 select-measure-unit"
                                                v-bind:data-index="index" v-model="details.measure_unit_id" disabled>
                                            <option v-for="(value,index) in measure_unit_lists" :value="value.id">
                                                {{value.measure_unit}}
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" v-model="details.qty"
                                               class="form-control form-control-sm m-input" readonly>
                                    </td>

                                    <td style="width: 150px;">
                                        <input style="width: 150px;" type="number" v-model="details.unit_price"
                                               class="form-control form-control-sm m-input text-right"
                                               placeholder="Unit Price" readonly>
                                    </td>

                                    <td style="width: 150px;">
                                        <input style="width: 150px;" type="text" v-model="details.total_amount"
                                               class="form-control form-control-sm m-input text-right" readonly>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3" class="text-right">Total</td>
                                    <td class="">
                                        <input type="text" v-model="form.total_qty"
                                               class="form-control form-control-sm m-input" placeholder="Total Qty"
                                               readonly>
                                    </td>
                                    <td></td>
                                    <td class="">
                                        <input type="text" v-model="form.total_amount"
                                               class="form-control form-control-sm m-input text-right"
                                               placeholder="Total Amount" readonly>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3" class="text-right">Bank Interest (%)</td>
                                    <td class="">
                                        <input type="text" v-model="form.bank_percentage"
                                               class="form-control form-control-sm m-input" placeholder="Bank interest"
                                               readonly>
                                    </td>
                                    <td>Bank Interest Amount</td>
                                    <td class="">
                                        <input type="text" v-model="form.bank_percentage_amount"
                                               class="form-control form-control-sm m-input text-right"
                                               placeholder="Bank Interest Amount" readonly>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3" class="text-right">LATR percentage</td>
                                    <td class="">
                                        <input type="number" v-model="form.latr_percentage"
                                               class="form-control form-control-sm m-input" placeholder="90%" readonly>
                                    </td>
                                    <td class="text-right"> LATR Amount =</td>
                                    <td class="">
                                        <input type="number" v-model="form.latr_total_amount"
                                               class="form-control form-control-sm m-input text-right"
                                               placeholder="LATR Total Amount" readonly>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <br><br>
                    </div>
                </div>

                <!-- Start Account -->
                <hr>
                <div class="m-form__heading">
                    <h3 class="m-form__heading-title">Accounts <i data-toggle="m-tooltip" data-width="auto"
                                                                  class="m-form__heading-help-icon flaticon-info"
                                                                  title=""
                                                                  data-original-title="If different than the corresponding address"></i>
                        <small>
                            <mark><i> Journal will hit once LATR Payment approved. </i></mark>
                        </small></h3>

                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="amount">Total LATR Amount </label>
                    <div class="col-lg-4">
                        <input type="text" id="amount" v-model="form.amount"
                               class="form-control form-control-sm m-input text-right" placeholder="Total Amount"
                               readonly>
                    </div>
                    <label class="col-lg-2 col-form-label"> </label>
                    <div class="col-lg-4"></div>
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
                        <div class="requiredField" v-if="(errors.hasOwnProperty('debit_account_id'))">{{
                            (errors.hasOwnProperty('debit_account_id')) ? errors.debit_account_id[0] :'' }}
                        </div>
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
                        <div class="requiredField" v-if="(errors.hasOwnProperty('credit_account_id'))">{{
                            (errors.hasOwnProperty('credit_account_id')) ? errors.credit_account_id[0] :'' }}
                        </div>
                    </div>
                </div>
                <!-- //End Account -->
            </div>


            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="submit" class="btn btn-sm btn-info" @click.prevent="update(form.id,0)"><i
                                class="la la-save"></i> <span>Update</span></button>
                            <button v-if="permissions.indexOf('acc-latr-entry.approve') !=-1" type="submit"
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

        props: ['permissions', 'lc_lists', 'product_lists', 'measure_unit_lists', 'account_list', 'editId'],

        data: function () {
            return {

                acc_data_list: false,
                add_form: true,
                edit_form: false,

                ci_lists: {},

                form: {
                    lc_opening_info_id: '',
                    lc_commercial_invoice_id: '',
                    latr_date: '',
                    lc_margin_percentage: '',
                    bank_latr_no: '',
                    narration: '',
                    total_qty: '',
                    total_amount: '',
                    latr_percentage: '',

                    bank_percentage: '',
                    bank_percentage_amount: '',

                    latr_total_amount: '',
                    status: 1,
                    details: [
                        {
                            lc_latr_payment_entry_id: '',
                            product_id: '',
                            remarks: '',
                            hs_code: '',
                            unit_price: '',
                            qty: '',
                            measure_unit_id: '',
                            total_amount: '',
                        }
                    ],

                    amount: '',
                    debit_account_id: '',
                    credit_account_id: '',

                },
                errors: {},
            };
        },

        methods: {

            edit(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'acc-latr-entry/' + id + '/edit')
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
                                _this.loadCommercialInvoicesByLcNo();
                            });
                        }, 100);
                    });
            },

            update(id, approval) {
                var _this = this;
                _this.form.approve = approval;

                _this.$loading(true);
                axios.put(base_url + 'acc-latr-entry/' + id, _this.form)
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

            loadCommercialInvoicesByLcNo() {
                var _this = this;
                var id = _this.form.lc_opening_info_id;

                _this.$loading(true);
                axios.get(base_url + "acc-latr-entry/" + id + "/get-commercial-invoices-by-lcno")
                    .then((response) => {
                        _this.$loading(false);
                        if (response.data.length > 0) {
                            _this.ci_lists = response.data;
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
                            alert('Commercial Invoice is not found.')
                        }
                    });
            },

            totalQtyAndPrice() {
                var _this = this;

                var total_qty = 0;
                var total_amount = 0;
                var total_price = 0;
                var details = this.form.details;
                var length = details.length;

                for (let i = 0; i < length; i++) {
                    total_price = Number(details[i].qty) * Number(details[i].unit_price);
                    this.form.details[i].total_amount = total_price.toFixed(2);
                    total_qty = Number(details[i].qty) + total_qty;
                    total_amount = total_price + total_amount;
                }

                this.form.total_qty = total_qty;
                this.form.total_amount = total_amount.toFixed(2);

                _this.calcLatrPercentageTotal();
            },

            calcLatrPercentageTotal() {
                var grand_latr_total = (this.form.latr_percentage / 100) * this.form.total_amount;
                this.form.latr_total_amount = grand_latr_total.toFixed(2);
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
                }
            });
        },

        created() {
            var _this = this;
            _this.edit(_this.editId);
        }
    }
</script>
