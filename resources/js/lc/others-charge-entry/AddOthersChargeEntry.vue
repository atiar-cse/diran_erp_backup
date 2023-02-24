<template>
    <div>
        <!--begin::Form-->
        <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data" id="addComponent"
              method="POST"
              v-on:submit.prevent="store">

            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="lc_others_charge_no">Lc Insurance No. <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="lc_others_charge_no"
                               placeholder="Lc Insurance No"
                               type="text" v-model="form.lc_others_charge_no">
                    </div>

                    <label class="col-lg-2 col-form-label" for="lc_opening_info_id">LC No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-lc-no"
                                data-selectField="lc_opening_info_id"
                                id="lc_opening_info_id" v-model="form.lc_opening_info_id">
                            <option
                                :get_cnf_margin="svalue.get_cnf_margin ? svalue.get_cnf_margin.margin_percentage : ''"
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
                    <label class="col-lg-2 col-form-label" for="lc_commercial_invoice_id">CI No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-ci-no"
                                data-selectField="lc_commercial_invoice_id"
                                id="lc_commercial_invoice_id" v-model="form.lc_commercial_invoice_id">
                            <option :value="svalue.id" v-for="(svalue,sindex) in ci_lists"> {{svalue.ci_no}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['lc_commercial_invoice_id']">{{
                            errors['lc_commercial_invoice_id'][0] }}
                        </div>
                    </div>

                    <label for="others_charge_date" class="col-lg-2 col-form-label">Date </label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input class="form-control form-control-sm m-input datepicker"
                                   data-dateField="others_charge_date"
                                   id="others_charge_date" placeholder="Select date from picker" readonly
                                   type="text" v-model="form.others_charge_date"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('others_charge_date'))">{{
                            (errors.hasOwnProperty('others_charge_date')) ? errors.others_charge_date[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" id="narration" rows="2"
                                  v-model="form.narration"></textarea>
                    </div>

                    <label class="col-lg-2 col-form-label" for="total_amount">Total Amount</label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="total_amount"
                               type="text" v-model="form.total_amount" readonly>

                        <div class="requiredField" v-if="errors['total_amount']">{{
                            errors['total_amount'][0] }}
                        </div>
                    </div>
                </div>

                <br><br>

                <!--begin::Portlet-->

                <div class="form-group m-form__group row">
                    <div class="m-section__content col-lg-12">

                        <div v-for="category in form.categories" class="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless bg-secondary">
                                <thead class="thead-inverse">
                                <tr>
                                    <th> {{ category.cost_category_name }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="">
                                        <div class="table-responsive">
                                            <table class="table table-sm m-table table-bordered table-hover">
                                                <thead class="thead-inverse">

                                                </thead>
                                                <tbody>
                                                <tr v-for="(details, index) in category.get_cost_names">
                                                    <td width="50%">
                                                        {{ details.cost_name }}
                                                    </td>
                                                    <td width="50%">
                                                        <input @input="totalCostValue()"
                                                               class="form-control form-control-sm m-input text-right"
                                                               min="0"
                                                               placeholder=""
                                                               step="any"
                                                               type="number" v-model="details.cost_value">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right">Sub Total</td>
                                                    <td class="">
                                                        <input class="form-control form-control-sm m-input text-right"
                                                               readonly
                                                               type="text" v-model="category.total_cost">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <br>
                        </div>

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
                                    type="submit" v-if="permissions.indexOf('others-charge-entry.approve') !=-1"><i
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
        props: ['permissions', 'lc_lists', 'lc_cost_name_category_list'],

        data: function () {
            return {
                data_list: false,
                add_form: true,
                edit_form: false,

                ci_lists: {},

                form: {
                    lc_others_charge_no: '',
                    lc_opening_info_id: '',
                    lc_commercial_invoice_id: '',
                    others_charge_date: '',
                    total_amount: '',
                    narration: '',
                    status: 1,
                    approve: 0,

                    categories: [
                        {
                            cost_category_name: '',
                            get_cost_names: {
                                cost_name: '',
                                cost_value: '',
                            },
                            total_cost: '',
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {

            store: function (approval) {
                var _this = this;
                _this.form.approve = approval;
                _this.$loading(true);
                axios.post(base_url + 'others-charge-entry', _this.form)
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
                axios.get(base_url + "latr-entry/" + id + "/get-commercial-invoices-by-lcno")
                    .then((response) => {
                        _this.$loading(false);
                        if (response.data.length > 0) {
                            _this.ci_lists = response.data;
                            _this.initSelect2();
                        } else {
                            alert('Commercial Invoice is not found.')
                        }
                    });
            },

            totalCostValue() {
                var _this = this;

                var total_amount = 0;
                var categories = _this.form.categories;
                categories.forEach(function (item) {
                    let sub_total = 0;
                    var details = item.get_cost_names;
                    details.forEach(function (item2) {
                        sub_total += parseFloat(item2.cost_value);
                    });
                    item.total_cost = sub_total;
                    total_amount += sub_total;
                });

                _this.form.total_amount = total_amount.toFixed(2);
            },

            duplicateCheck(selectedValue) {
                var no_index = this.form.details.length;
                for (let i = 0; i < no_index; i++) {
                    if (this.form.details[i].lc_cost_name_id == selectedValue) {
                        alert("Duplicate Found");
                    }
                }
            },

            lcNoGenerate() {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'others-charge-entry-no-generate')
                    .then((response) => {
                        _this.$loading(false);
                        _this.form.lc_others_charge_no = response.data;
                    });
            },

            initSelect2() {
                setTimeout(function () {
                    var Select2 = {
                        init: function () {
                            $(".select2").select2({placeholder: "Please select an option"})
                        }
                    };
                    jQuery(document).ready(function () {
                        Select2.init()
                    });
                }, 250);
            },
        },

        mounted() {
            var _this = this;
            _this.initSelect2();

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if (selectField == 'lc_opening_info_id') {
                    _this.form.lc_opening_info_id = selectedItem.val();
                    _this.loadCommercialInvoicesByLcNo(); //for the dropdown
                } else if (selectField == 'lc_commercial_invoice_id') {
                    _this.form.lc_commercial_invoice_id = selectedItem.val();
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
                        _this.form.others_charge_date = '';
                    } else {
                        _this.form.others_charge_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created() {
            var _this = this;
            _this.lcNoGenerate();

            _this.form.others_charge_date = moment().format('DD/MM/YYYY');

            setTimeout(function () {
                _this.form.categories = _this.lc_cost_name_category_list;
            }, 250);
        }
    }
</script>
