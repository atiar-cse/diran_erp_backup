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
                    <label class="col-lg-2 col-form-label" for="cost_category_id">Cost Category <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" data-selectField="cost_category_id" id="cost_category_id"
                                v-model="form.cost_category_id">
                            <option :value="value.id" v-for="(value,index) in lc_cost_name_category_list">
                                {{value.cost_category_name}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="errors['cost_category_id']">{{ errors['cost_category_id'][0]
                            }}
                        </div>
                    </div>

                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" id="narration" rows="2"
                                  v-model="form.narration"></textarea>
                    </div>
                </div>

                <br><br>

                <!--begin::Portlet-->
                <div class="form-group m-form__group row">
                    <div class="m-section__content col-lg-12">
                        <div><strong>Charges</strong></div>
                        <div class="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse">
                                <tr>
                                    <th>Cost Name <span class="requiredField">*</span></th>
                                    <th>Cost Value <span class="requiredField">*</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <td>
                                        <select class="form-control m-select2 select2  select-cost-name"
                                                disabled style="width:100%"
                                                v-bind:data-index="index" v-model="details.lc_cost_name_id">
                                            <option :value="value.id" v-for="(value,index) in lc_cost_name_lists">
                                                {{value.cost_name}}
                                            </option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.lc_cost_name_id']">{{
                                            errors['details.'+index+'.lc_cost_name_id'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input @input="totalCostValue()"
                                               class="form-control form-control-sm m-input text-right" min="0"
                                               placeholder=""
                                               step="any"
                                               type="number" v-model="details.cost_value">
                                        <div class="requiredField" v-if="errors['details.'+index+'.cost_value']">{{
                                            errors['details.'+index+'.cost_value'][0] }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">Total Bill Amount</td>
                                    <td class="">
                                        <input class="form-control form-control-sm m-input text-right" readonly
                                               type="text" v-model="form.total_cost">
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
                lc_cost_name_lists: {},

                form: {
                    lc_others_charge_no: '',
                    lc_opening_info_id: '',
                    lc_commercial_invoice_id: '',
                    cost_category_id: '',
                    others_charge_date: '',
                    total_cost: '',
                    narration: '',
                    status: 1,
                    approve: 0,
                    details: [
                        {
                            lc_cost_name_id: '',
                            cost_value: '',
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

            loadDetails() {
                var _this = this;
                var id = _this.form.cost_category_id;

                _this.$loading(true);
                axios.get(base_url + "lc-cost-name-entry/" + id + "/list-by-category")
                    .then((response) => {
                        _this.$loading(false);
                        _this.lc_cost_name_lists = response.data.lsits;
                        _this.form.details = response.data.detailsArray;
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

            totalCostValue() {
                var total_cost = 0;
                var details = this.form.details;
                var length = details.length;

                for (let i = 0; i < length; i++) {
                    var getCost = 0;
                    if (Number(details[i].cost_value) > 0) {
                        getCost = Number(details[i].cost_value);
                    }
                    total_cost = getCost + total_cost;
                }
                this.form.total_cost = total_cost.toFixed(2);
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
                    _this.loadCommercialInvoicesByLcNo(); //for the dropdown
                } else if (selectField == 'lc_commercial_invoice_id') {
                    _this.form.lc_commercial_invoice_id = selectedItem.val();
                } else if (selectField == 'cost_category_id') {
                    _this.form.cost_category_id = selectedItem.val();
                    _this.loadDetails();
                }
            });

            $('#addComponent').on('change', '.select-cost-name', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.duplicateCheck(selectedItem.val());
                _this.form.details[dataIndex].lc_cost_name_id = selectedItem.val();

                var Select2 = {
                    init: function () {
                        $(".select2").select2({placeholder: "Please select an option"})
                    }
                };
                jQuery(document).ready(function () {
                    Select2.init()
                });
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
            _this.totalCostValue();
            _this.form.others_charge_date = moment().format('DD/MM/YYYY');
        }
    }
</script>
