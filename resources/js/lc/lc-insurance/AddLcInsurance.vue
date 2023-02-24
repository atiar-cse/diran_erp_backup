<template>
    <div>
        <!--begin::Form-->
        <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data" id="addComponent"
              method="POST"
              v-on:submit.prevent="store">
            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="lc_insurance_no">Lc Insurance No. <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="lc_insurance_no"
                               placeholder="Lc Insurance No"
                               type="text" v-model="form.lc_insurance_no">
                    </div>

                    <label class="col-lg-2 col-form-label" for="lc_opening_info_id">LC No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-lc-no"
                                data-selectField="lc_opening_info_id"
                                id="lc_opening_info_id" v-model="form.lc_opening_info_id">
                            <option v-for="(svalue,sindex) in lc_lists" :value="svalue.id"
                                    :get_supplier="svalue.get_supplier ? svalue.get_supplier.purchase_supplier_name : ''"
                                    :lc_no="svalue.lc_no" :lc_total_value="svalue.lc_total_value"
                            > {{svalue.lc_no}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="errors['lc_opening_info_id']">{{
                            errors['lc_opening_info_id'][0] }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="supplier_info_display">Supplier </label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="supplier_info_display"
                               placeholder="Supplier"
                               readonly type="text" v-model="form.supplier_info_display">
                    </div>

                    <label class="col-lg-2 col-form-label" for="insurance_date">Insurance Date <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input class="form-control form-control-sm m-input datepicker"
                                   data-dateField="insurance_date"
                                   id="insurance_date" placeholder="Select date from picker" readonly
                                   type="text" v-model="form.insurance_date"/>
                            <div class="input-group-append"><span class="input-group-text"><i
                                class="la la-calendar-check-o"></i></span></div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('insurance_date'))">{{
                            (errors.hasOwnProperty('insurance_date')) ? errors.insurance_date[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="lc_value" class="col-lg-2 col-form-label">LC Value </label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="lc_value"
                               placeholder="Total LC Value (BDT)"
                               readonly type="text" v-model="form.lc_value">
                    </div>

                    <label class="col-lg-2 col-form-label" for="insurance_company">Insurance Company<span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="insurance_company"
                               placeholder="Insurance Company Name"
                               type="text" v-model="form.insurance_company">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('insurance_company'))">{{
                            (errors.hasOwnProperty('insurance_company')) ? errors.insurance_company[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="insurance_no">Insurance No.</label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="insurance_no" placeholder="Insurance No"
                               type="text" v-model="form.insurance_no">
                    </div>

                    <label class="col-lg-2 col-form-label" for="insurance_amount">Insurance Amount<span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="insurance_amount" min="0"
                               placeholder="Insurance Amount" step="any"
                               type="number" v-model="form.insurance_amount">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('insurance_amount'))">{{
                            (errors.hasOwnProperty('insurance_amount')) ? errors.insurance_amount[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="narration" class="col-lg-2 col-form-label">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control form-control-sm" id="narration" placeholder=""
                                  rows="2" v-model="form.narration"></textarea>
                    </div>
                </div>

                <!-- Start Account
                <hr>
                <div class="m-form__heading">
                    <h3 class="m-form__heading-title">Accounts <i data-toggle="m-tooltip" data-width="auto" class="m-form__heading-help-icon flaticon-info" title="" data-original-title="If different than the corresponding address"></i> <small><mark><i> Journal will hit once LC Insurance Amount approved. </i></mark></small> </h3>

                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="amount">Total Amount </label>
                    <div class="col-lg-4">
                        <input type="text" id="amount" v-model="form.amount" class="form-control form-control-sm m-input text-right" placeholder="Total Amount" readonly>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="debit_account_id">Debit Account <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2"  id="debit_account_id" v-model="form.debit_account_id" data-selectField="debit_account_id">
                            <option v-for="(value,index) in account_list" :value="value.id"> {{ value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('debit_account_id'))">{{ (errors.hasOwnProperty('debit_account_id')) ? errors.debit_account_id[0] :'' }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="credit_account_id">Credit Account <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2"  id="credit_account_id" v-model="form.credit_account_id" data-selectField="credit_account_id">
                            <option v-for="(value,index) in account_list" :value="value.id"> {{ value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('credit_account_id'))">{{ (errors.hasOwnProperty('credit_account_id')) ? errors.credit_account_id[0] :'' }}</div>
                    </div>
                </div>
                 //End Account -->
            </div>
            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button @click.prevent="store(0)" class="btn btn-sm btn-info" type="submit"><i
                                class="la la-save"></i> <span>Save</span></button>
                            <button @click.prevent="store(1)" class="btn btn-sm btn-success"
                                    type="submit" v-if="permissions.indexOf('lc-insurance.approve') !=-1"><i
                                class="la la-check"></i>
                                <span>Approve</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
</template>

<script>
    import {EventBus} from '../../vue-assets';

    export default {

        props: ['permissions', 'lc_lists'],

        data: function () {
            return {

                product_list: false,
                add_form: true,
                edit_form: false,

                form: {
                    lc_insurance_no: '',
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

                    /* amount: '',
                     debit_account_id: 47,
                     credit_account_id: 53,*/
                    // narration: '',
                },
                errors: {},
            };
        },

        methods: {

            store: function (approval) {
                var _this = this;
                _this.form.approve = approval;
                _this.$loading(true);
                axios.post(base_url + 'lc-insurance', _this.form)
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

            lcNoGenerate() {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'lc-insurance-no-generate')
                    .then((response) => {
                        _this.$loading(false);
                        _this.form.lc_insurance_no = response.data;
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
            var _this = this;

            var Select2 = {
                init: function () {
                    $(".select2").select2({placeholder: "Please select an option"})
                }
            };
            jQuery(document).ready(function () {
                Select2.init()
            });

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if (selectField == 'lc_opening_info_id') {
                    _this.form.lc_opening_info_id = selectedItem.val();

                    _this.form.lc_value = $('option:selected', this).attr('lc_total_value');
                    _this.form.supplier_info_display = $('option:selected', this).attr('get_supplier');
                    _this.form.lc_no = $('option:selected', this).attr('lc_no');
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
                        _this.form.insurance_date = '';
                    } else {
                        _this.form.insurance_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created() {
            var _this = this;
            _this.lcNoGenerate();
        }
    }
</script>
