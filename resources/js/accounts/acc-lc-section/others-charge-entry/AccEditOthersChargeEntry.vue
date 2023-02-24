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
                    <label for="others_charge_date" class="col-lg-2 col-form-label">Date </label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.others_charge_date" data-dateField="others_charge_date" readonly
                                   placeholder="Select date from picker" id="others_charge_date"/>
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

                    <label for="cost_category_id" class="col-lg-2 col-form-label">Cost Category <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" id="cost_category_id" v-model="form.cost_category_id"
                                data-selectField="cost_category_id" disabled>
                            <option v-for="(value,index) in lc_cost_name_category_list" :value="value.id">
                                {{value.cost_category_name}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="errors['cost_category_id']">{{ errors['cost_category_id'][0]
                            }}
                        </div>
                    </div>


                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.narration" id="narration" rows="2"
                                  disabled></textarea>
                    </div>
                    <!--<label for="debit_account_id" class="col-lg-2 col-form-label">Debit Account <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2"  id="debit_account_id" v-model="form.debit_account_id" data-selectField="debit_account_id">
                            <option v-for="(value,index) in account_list" :value="value.id"> {{ value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('debit_account_id'))">{{ (errors.hasOwnProperty('debit_account_id')) ? errors.debit_account_id[0] :'' }}</div>
                        <small><mark><i> Journal will hit once LC Other Charge approved. </i></mark></small>
                    </div>   -->
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
                                    <th style="width: 20%">Cost Name</th>
                                    <th style="width: 20%">Credit Accounts<span class="requiredField">*</span></th>
                                    <th style="width: 20%">Debit Accounts<span class="requiredField">*</span></th>
                                    <th style="width: 20%">Remarks</th>
                                    <th style="width: 20%">Cost Value</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <td>
                                        <!--  <select class="form-control m-select2 select2  select-cost-name" v-bind:data-index="index" v-model="details.lc_cost_name_id" disabled >
                                              <option v-for="(value,index) in lc_cost_name_lists" :value="value.id"> {{value.cost_name}}</option>
                                          </select>-->
                                        <span>{{ details.lc_cost_name }}</span>
                                        <input type="hidden" v-model="details.lc_cost_name_id"/>

                                    </td>
                                    <td>
                                        <select class="form-control select2 select-credit-account"
                                                v-model="details.credit_account_id" v-bind:data-index="index">
                                            <option v-for="(value,index) in account_list" :value="value.id"> {{
                                                value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}
                                            </option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.credit_account_id']">
                                            {{ errors['details.'+index+'.credit_account_id'][0] }}
                                        </div>
                                    </td>

                                    <td>
                                        <select class="form-control select2 select-debit-account"
                                                v-model="details.debit_account_id" v-bind:data-index="index">
                                            <option v-for="(value,index) in account_list" :value="value.id"> {{
                                                value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}
                                            </option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.debit_account_id']">
                                            {{ errors['details.'+index+'.debit_account_id'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.remarks"
                                               class="form-control form-control-sm m-input">
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="details.amount"
                                               class="form-control form-control-sm m-input text-right" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right">Total Bill Amount</td>
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
            </div>


            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="submit" class="btn btn-sm btn-info" @click.prevent="update(form.id,0)"><i
                                class="la la-save"></i> <span>Update</span></button>
                            <button v-if="permissions.indexOf('acc-others-charge-entry.approve') !=-1" type="submit"
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

        props: ['permissions', 'lc_lists', 'lc_cost_name_category_list', 'account_list', 'editId'],

        data: function () {
            return {

                acc_data_list: false,
                add_form: true,
                edit_form: false,

                ci_lists: {},
                lc_cost_name_lists: {},

                form: {
                    lc_opening_info_id: '',
                    lc_commercial_invoice_id: '',
                    cost_category_id: '',
                    others_charge_date: '',
                    total_cost: '',
                    narration: '',
                    status: 1,
                    details: [
                        {
                            lc_cost_name_id: '',
                            credit_account_id: '',
                            debit_account_id: '',
                            remarks: '',
                            amount: '',
                        }
                    ],


                },
                errors: {},
            };
        },

        methods: {

            edit(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'acc-others-charge-entry/' + id + '/edit')
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
                                _this.loadDetails();
                            });
                        }, 100);
                    });
            },

            update(id, approval) {
                var _this = this;
                _this.form.approve = approval;
                _this.$loading(true);
                axios.put(base_url + 'acc-others-charge-entry/' + id, _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        _this.showMassage(response.data);
                        EventBus.$emit('data-changed');
                    })
                    .catch(error => {
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
                var id = this.form.lc_opening_info_id;
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
                var id = this.form.cost_category_id;
                _this.$loading(true);
                axios.get(base_url + "lc-cost-name-entry/" + id + "/list-by-category")
                    .then((response) => {
                        _this.$loading(false);
                        if (response.data.length > 0) {
                            _this.lc_cost_name_lists = response.data;
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

            /*$('#editComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if( selectField == 'lc_opening_info_id' ){
                }else if(selectField == 'lc_commercial_invoice_id'){
                }else if(selectField == 'cost_category_id'){
                    /!*_this.form.cost_category_id= selectedItem.val();
                    _this.loadDetails();   *!/
                }else if(selectField == 'debit_account_id'){
                    _this.form.debit_account_id= selectedItem.val();
                }
            });*/

            $('#editComponent').on('change', '.select-credit-account', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.form.details[dataIndex].credit_account_id = selectedItem.val();

                var Select2 = {
                    init: function () {
                        $(".select2").select2({placeholder: "Please select an option"})
                    }
                };
                jQuery(document).ready(function () {
                    Select2.init()
                });
            });

            $('#editComponent').on('change', '.select-debit-account', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.form.details[dataIndex].debit_account_id = selectedItem.val();

                var Select2 = {
                    init: function () {
                        $(".select2").select2({placeholder: "Please select an option"})
                    }
                };
                jQuery(document).ready(function () {
                    Select2.init()
                });
            });
        },

        created() {
            var _this = this;
            _this.edit(_this.editId);
        }
    }
</script>
