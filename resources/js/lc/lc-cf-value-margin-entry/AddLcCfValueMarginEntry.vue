<template>
    <div>
        <!--begin::Form-->
        <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data" id="addComponent"
              method="POST"
              v-on:submit.prevent="store">

            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="lc_margin_no">Lc Margin No. <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="lc_margin_no"
                               placeholder="Lc Margin No"
                               type="text" v-model="form.lc_margin_no">
                    </div>

                    <label class="col-lg-2 col-form-label" for="lc_opening_info_id">LC No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-lc-no"
                                data-selectField="lc_opening_info_id"
                                id="lc_opening_info_id" v-model="form.lc_opening_info_id">
                            <option :bank_id="svalue.get_opening_bank ? svalue.get_opening_bank.id : ''"
                                    :get_opening_bank="svalue.get_opening_bank ? svalue.get_opening_bank.accounts_bank_names : ''"
                                    :get_supplier="svalue.get_supplier ? svalue.get_supplier.purchase_supplier_name : ''"
                                    :lc_total_value="svalue.lc_total_value"
                                    :supplier_id="svalue.get_supplier ? svalue.get_supplier.id : ''"
                                    :value="svalue.id"
                                    v-for="(svalue,sindex) in lc_lists"
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

                    <label class="col-lg-2 col-form-label" for="margin_entry_date">Margin Entry Date <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input class="form-control form-control-sm m-input datepicker"
                                   data-dateField="margin_entry_date"
                                   id="margin_entry_date" placeholder="Select date from picker" readonly
                                   type="text" v-model="form.margin_entry_date"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('margin_entry_date'))">{{
                            (errors.hasOwnProperty('margin_entry_date')) ? errors.margin_entry_date[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="bank_info_display" class="col-lg-2 col-form-label">Bank </label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="bank_info_display"
                               placeholder="Bank Name"
                               readonly type="text" v-model="form.bank_info_display">
                    </div>

                    <label class="col-lg-2 col-form-label" for="lc_value">LC Value </label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="lc_value"
                               placeholder="Total LC Value (BDT)"
                               readonly type="text" v-model="form.lc_value">
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="margin_percentage">Margin Percentage</label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input"
                               id="margin_percentage" min="0"
                               placeholder="Enter Margin Percentage" step="any"
                               type="number" v-model="form.margin_percentage">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('margin_percentage'))">{{
                            (errors.hasOwnProperty('margin_percentage')) ? errors.margin_percentage[0] :'' }}
                        </div>
                    </div>

                    <label class="col-lg-2 col-form-label" for="margin_value">Margin value<span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="margin_value" min="0"
                               placeholder="Margin value"
                               step="any" type="number" v-model="form.margin_value">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('margin_value'))">{{
                            (errors.hasOwnProperty('margin_value')) ? errors.margin_value[0] :'' }}
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

            </div>

            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">

                            <button @click.prevent="store(0)" class="btn btn-sm btn-info" type="submit"><i
                                class="la la-save"></i> <span>Save</span></button>
                            <button @click.prevent="store(1)" class="btn btn-sm btn-success"
                                    type="submit" v-if="permissions.indexOf('lc-cf-value-margin-entry.approve') !=-1"><i
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
                    lc_margin_no: '',
                    lc_opening_info_id: '',
                    supplier_id: '',
                    margin_entry_date: '',
                    bank_id: '',
                    lc_value: '',
                    margin_percentage: 0,
                    margin_value: '',
                    narration: '',
                    status: '1',
                    approve: 0,
                    supplier_info_display: '',
                    bank_info_display: '',
                },
                errors: {},
            };
        },

        methods: {

            store: function (approval) {
                var _this = this;
                _this.form.approve = approval;
                _this.$loading(true);
                axios.post(base_url + 'lc-cf-value-margin-entry', _this.form)
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

            calcMarginValPercentage() {
                var lc_value = this.form.lc_value;
                if (lc_value && lc_value > 0) {
                    var margin_value_percentage = Number(this.form.margin_percentage / 100) * lc_value;
                    this.form.margin_value = margin_value_percentage.toFixed(2);
                } else {
                    alert('Please select LC No first!');
                    $("#lc_opening_info_id").focus();
                    this.form.margin_percentage = '';
                }
            },

            lcNoGenerate() {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'lc-cf-value-margin-entry-no-generate')
                    .then((response) => {
                        _this.$loading(false);
                        _this.form.lc_margin_no = response.data;
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

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if (selectField == 'lc_opening_info_id') {
                    _this.form.lc_opening_info_id = selectedItem.val();

                    _this.form.supplier_id = $('option:selected', this).attr('supplier_id');
                    _this.form.bank_id = $('option:selected', this).attr('bank_id');
                    _this.form.lc_value = $('option:selected', this).attr('lc_total_value');

                    _this.form.supplier_info_display = $('option:selected', this).attr('get_supplier');
                    _this.form.bank_info_display = $('option:selected', this).attr('get_opening_bank');

                    //_this.calcMarginValPercentage();
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
        },

        created() {
            var _this = this;
            _this.lcNoGenerate();
        }
    }
</script>
