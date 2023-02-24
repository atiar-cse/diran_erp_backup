<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="lc_opening_info_id" class="col-lg-2 col-form-label">LC No </label>
                    <div class="col-lg-4">
                        <!--<select class="form-control m-select2 select2  select-lc-no" id="lc_opening_info_id" data-selectField="lc_opening_info_id" v-model="form.lc_opening_info_id" disabled>
                            <option v-for="(svalue,sindex) in lc_lists" :value="svalue.id"> {{svalue.lc_no}}</option>
                        </select>-->
                        <input type="text" id="lc_opening_info_id" v-model="form.lc_opening_info_id"
                               class="form-control form-control-sm m-input" readonly>
                    </div>
                    <label for="lc_commercial_invoice_id" class="col-lg-2 col-form-label">CI No </label>
                    <div class="col-lg-4">
                        <!--<select class="form-control m-select2 select2  select-ci-no" id="lc_commercial_invoice_id" data-selectField="lc_commercial_invoice_id" v-model="form.lc_commercial_invoice_id" disabled >
                            <option v-for="(svalue,sindex) in ci_lists" :value="svalue.id" > {{svalue.ci_no}}</option>
                        </select>-->
                        <input type="text" id="lc_commercial_invoice_id" v-model="form.lc_commercial_invoice_id"
                               class="form-control form-control-sm m-input" readonly>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label for="custom_duty_date" class="col-lg-2 col-form-label">Date </label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.custom_duty_date" data-dateField="custom_duty_date" readonly
                                   placeholder="Select date from picker" id="custom_duty_date"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <label for="cnf_agent_id" class="col-lg-2 col-form-label">C&F Agent / Party </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-cnf-agent" id="cnf_agent_id"
                                data-selectField="cnf_agent_id" v-model="form.cnf_agent_id" disabled>
                            <option v-for="(svalue,sindex) in supplier_list" :value="svalue.id">
                                {{svalue.purchase_supplier_name}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="custom_duty_cost_id" class="col-lg-2 col-form-label">Custom Duty </label>
                    <div class="col-lg-4">
                        <select class="form-control select2" id="custom_duty_cost_id" v-model="form.custom_duty_cost_id"
                                data-selectField="custom_duty_cost_id" disabled>
                            <option v-for="(value,index) in lc_custom_duty_name_lists" :value="value.id">
                                {{value.duty_cost_name}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="errors['custom_duty_cost_id']">{{
                            errors['custom_duty_cost_id'][0] }}
                        </div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea style="font-size: 13px;" class="form-control m-input small" v-model="form.narration"
                                  id="narration" rows="2"></textarea>
                    </div>

                </div>
                <br><br>
                <!--begin::Portlet-->

                <div class="form-group m-form__group row">
                    <div class="m-section__content col-lg-12">
                        <div><strong>Products</strong></div>
                        <div class="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse">
                                <tr>
                                    <th width="50%">Product Name</th>
                                    <th width="50%">Cost Value</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <td>
                                        <select class="form-control m-select2 select2  select-product"
                                                v-bind:data-index="index" v-model="details.product_id"
                                                style="width:100%" disabled>
                                            <option v-for="(value,index) in product_lists" :value="value.id">
                                                {{value.product_name}}
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" v-model="details.cost_value"
                                               class="form-control form-control-sm m-input text-right" disabled>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">Total Bill Amount</td>
                                    <td class="">
                                        <input type="text" v-model="form.total_cost"
                                               class="form-control form-control-sm m-input text-right" readonly>
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
                            <mark><i> Journal will hit once Custom Duty Charge approved. </i></mark>
                        </small></h3>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="amount">Total Custom Duty Charge Amount </label>
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
            </div>


            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="submit" class="btn btn-sm btn-info" @click.prevent="update(form.id,0)"><i
                                class="la la-save"></i> <span>Update</span></button>
                            <button v-if="permissions.indexOf('acc-custom-duty-charge-entry.approve') !=-1"
                                    type="submit" class="btn btn-sm btn-success" @click.prevent="update(form.id,1)"><i
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

        props: ['permissions', 'lc_lists', 'lc_custom_duty_name_lists', 'product_lists', 'account_list', 'supplier_list', 'editId'],

        data: function () {
            return {

                acc_data_list: false,
                edit_form: true,
                view_form: false,

                ci_lists: {},

                form: {
                    lc_opening_info_id: '',
                    lc_commercial_invoice_id: '',
                    custom_duty_date: '',
                    custom_duty_cost_id: '',
                    total_cost: '',
                    narration: '',
                    cnf_agent_id: '',
                    status: 1,
                    details: [
                        {
                            product_id: '',
                            cost_value: '',
                        }
                    ],

                    amount: '',
                    credit_account_id: '',
                    debit_account_id: '',
                },
                errors: {},
            };
        },

        methods: {

            edit(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'acc-custom-duty-charge-entry/' + id + '/edit')
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
                                //_this.loadCommercialInvoicesByLcNo();
                            });
                        }, 100);
                    });
            },

            update(id, approval) {
                var _this = this;
                _this.form.approve = approval;
                _this.$loading(true);
                axios.put(base_url + 'acc-custom-duty-charge-entry/' + id, _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        _this.showMassage(response.data);
                        if (response.data.status == 'success') {
                            EventBus.$emit('data-changed');
                        }
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

            loadCommercialInvoicesByLcNo() {
                var _this = this;
                var id = this.form.lc_opening_info_id;

                _this.$loading(true);
                axios.get(base_url + "latr-entry/" + id + "/get-commercial-invoices-by-lcno")
                    .then((response) => {
                        _this.$loading(false);
                        if (response.data.length > 0) {
                            this.ci_lists = response.data;

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
