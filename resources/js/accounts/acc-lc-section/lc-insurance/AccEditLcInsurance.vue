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
                                data-selectField="lc_opening_info_id" v-model="form.lc_opening_info_id">
                            <option v-for="(svalue,sindex) in lc_lists"
                                    :value="svalue.id"
                                    :get_supplier="svalue.get_supplier ? svalue.get_supplier.purchase_supplier_name : ''"
                                    :lc_total_value="svalue.lc_total_value"
                                    :lc_no="svalue.lc_no"
                            > {{svalue.lc_no}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="errors['lc_opening_info_id']">{{
                            errors['lc_opening_info_id'][0] }}
                        </div>
                    </div>
                    <label for="supplier_info_display" class="col-lg-2 col-form-label">Supplier </label>
                    <div class="col-lg-4">
                        <input type="text" id="supplier_info_display" v-model="form.supplier_info_display"
                               class="form-control form-control-sm m-input" placeholder="Supplier" readonly>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="insurance_date" class="col-lg-2 col-form-label">Insurance Date <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.insurance_date" data-dateField="insurance_date" readonly
                                   placeholder="Select date from picker" id="insurance_date"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('insurance_date'))">{{
                            (errors.hasOwnProperty('insurance_date')) ? errors.insurance_date[0] :'' }}
                        </div>
                    </div>
                    <label for="lc_value" class="col-lg-2 col-form-label">LC Value </label>
                    <div class="col-lg-4">
                        <input type="text" id="lc_value" v-model="form.lc_value"
                               class="form-control form-control-sm m-input" placeholder="Total LC Value (BDT)" readonly>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="insurance_company" class="col-lg-2 col-form-label">Insurance Company <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="insurance_company" v-model="form.insurance_company"
                               class="form-control form-control-sm m-input" placeholder="Insurance Company Name">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('insurance_company'))">{{
                            (errors.hasOwnProperty('insurance_company')) ? errors.insurance_company[0] :'' }}
                        </div>
                    </div>
                    <label for="insurance_no" class="col-lg-2 col-form-label">Insurance No.</label>
                    <div class="col-lg-4">
                        <input type="text" id="insurance_no" v-model="form.insurance_no"
                               class="form-control form-control-sm m-input" placeholder="Insurance No">
                    </div>
                    <label for="insurance_amount" class="col-lg-2 col-form-label">Insurance Amount<span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="number" step="any" min="0" id="insurance_amount" @input="calcInsuranceAmount()"
                               v-model="form.insurance_amount" class="form-control form-control-sm m-input"
                               placeholder="Insurance Amount">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('insurance_amount'))">{{
                            (errors.hasOwnProperty('insurance_amount')) ? errors.insurance_amount[0] :'' }}
                        </div>
                    </div>
                    <label for="narration" class="col-lg-2 col-form-label">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control form-control-sm" id="narration" v-model="form.narration"
                                  placeholder="" rows="2"></textarea>
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
                            <mark><i> Journal will hit once LC Insurance Amount approved. </i></mark>
                        </small></h3>

                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="amount">Total Amount </label>
                    <div class="col-lg-4">
                        <input type="text" id="amount" v-model="form.amount"
                               class="form-control form-control-sm m-input text-right" placeholder="Total Amount"
                               readonly>
                    </div>
                    <!-- <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control form-control-sm" id="narration" v-model="form.narration" placeholder="" rows="1"></textarea>
                    </div> -->
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
                            <button v-if="permissions.indexOf('acc-lc-insurance.approve') !=-1" type="submit"
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

        props: ['permissions', 'lc_lists', 'account_list', 'editId'],

        data: function () {
            return {

                acc_data_list: false,
                add_form: true,
                edit_form: false,

                form: {
                    lc_opening_info_id: '',
                    lc_no: '',
                    insurance_date: '',
                    insurance_company: '',
                    insurance_no: '',
                    insurance_amount: '',
                    narration: '',
                    approve: 0,

                    supplier_info_display: '',
                    lc_value: '',

                    amount: '',
                    debit_account_id: 47,
                    credit_account_id: 53,
                    // narration: '',
                },
                errors: {},
            };
        },

        methods: {

            edit(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'acc-lc-insurance/' + id + '/edit')
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
                                    //*Load some field data after page rendered completely
                                    _this.form.supplier_info_display = $('option:selected', this).attr('get_supplier');
                                    _this.form.lc_value = $('option:selected', this).attr('lc_total_value');
                                }
                            );
                        }, 100);
                    });
            },

            update(id, approval) {
                var _this = this;
                _this.form.approve = approval;
                _this.$loading(true);
                axios.put(base_url + 'acc-lc-insurance/' + id, _this.form)
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

            calcInsuranceAmount() {
                this.form.amount = this.form.insurance_amount;
            },
        },

        mounted() {

            var _this = this;

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if (selectField == 'lc_opening_info_id') {
                    _this.form.lc_opening_info_id = selectedItem.val();

                    _this.form.lc_value = $('option:selected', this).attr('lc_total_value');

                    _this.form.supplier_info_display = $('option:selected', this).attr('get_supplier');
                    _this.form.lc_no = $('option:selected', this).attr('lc_no');
                } else if (selectField == 'debit_account_id') {
                    _this.form.debit_account_id = selectedItem.val();
                } else if (selectField == 'credit_account_id') {
                    _this.form.credit_account_id = selectedItem.val();
                }
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.margin_entry_date = '';
                    } else {
                        _this.form.margin_entry_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
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

        created() {
            var _this = this;
            _this.edit(_this.editId);
        }
    }
</script>
