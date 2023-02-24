<template>
    <div>
        <!--begin::Form-->
        <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data" id="editComponent"
              method="POST"
              v-on:submit.prevent="update(form.id)">
            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="lc_custom_duty_no">Lc Custom Duty No. <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="lc_custom_duty_no"
                               placeholder="Lc Custom Duty No"
                               type="text" v-model="form.lc_custom_duty_no">
                    </div>

                    <label class="col-lg-2 col-form-label" for="lc_opening_info_id">LC No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-lc-no"
                                data-selectField="lc_opening_info_id"
                                id="lc_opening_info_id" v-model="form.lc_opening_info_id">
                            <option :value="svalue.id"
                                    v-for="(svalue,sindex) in lc_lists"> {{svalue.lc_no}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="errors['lc_opening_info_id']">{{errors['lc_opening_info_id'][0]
                            }}
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
                            <option v-for="(svalue,sindex) in ci_lists"
                                :get_cnf_agent_id="svalue.cnf_agent" 
                                :value="svalue.id"> 
                                {{svalue.ci_no}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="errors['lc_commercial_invoice_id']">
                            {{errors['lc_commercial_invoice_id'][0] }}
                        </div>
                    </div>

                    <label class="col-lg-2 col-form-label" for="custom_duty_date">Date <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input class="form-control form-control-sm m-input datepicker"
                                   data-dateField="custom_duty_date"
                                   id="custom_duty_date" placeholder="Select date from picker" readonly
                                   type="text" v-model="form.custom_duty_date"/>
                            <div class="input-group-append"><span class="input-group-text"><i
                                class="la la-calendar-check-o"></i></span></div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('custom_duty_date'))">
                            {{(errors.hasOwnProperty('custom_duty_date')) ? errors.custom_duty_date[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="cnf_agent_id" class="col-lg-2 col-form-label">C&F Agent</label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-cnf-agent" data-selectField="cnf_agent_id"
                                disabled id="cnf_agent_id" v-model="form.cnf_agent_id">
                            <option :value="svalue.id" v-for="(svalue,sindex) in cnf_agent_list">
                                {{svalue.cnf_agent_name}}
                            </option>
                        </select>
                    </div>

                    <label class="col-lg-2 col-form-label" for="custom_duty_cost_id">Custom Duty <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" data-selectField="custom_duty_cost_id"
                                id="custom_duty_cost_id"
                                v-model="form.custom_duty_cost_id">
                            <option :value="value.id" v-for="(value,index) in lc_custom_duty_name_lists">
                                {{value.duty_cost_name}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="errors['custom_duty_cost_id']">
                            {{errors['custom_duty_cost_id'][0] }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
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
                        <div><strong>Products</strong></div>
                        <div class="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse">
                                <tr>
                                    <th>Product Name <span class="requiredField">*</span></th>
                                    <th>Cost Value <span class="requiredField">*</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <td>
                                        <select class="form-control m-select2 select2  select-product"
                                                disabled style="width:100%"
                                                v-bind:data-index="index" v-model="details.product_id">
                                            <option :value="value.id" v-for="(value,index) in product_lists">
                                                {{value.product_name}}
                                            </option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{
                                            errors['details.'+index+'.product_id'][0] }}
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
                            <button @click.prevent="update(form.id,0)" class="btn btn-sm btn-info" type="submit"><i
                                class="la la-save"></i> <span>Update</span></button>
                            <button @click.prevent="update(form.id,1)" class="btn btn-sm btn-success"
                                    type="submit" v-if="permissions.indexOf('custom-duty-charge-entry.approve') !=-1"><i
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
    import {EventBus} from '../../vue-assets';

    export default {

        props: ['permissions', 'lc_lists', 'lc_custom_duty_name_lists', 'product_lists', 'cnf_agent_list', 'editId'],

        data: function () {
            return {

                data_list: false,
                add_form: false,
                edit_form: true,

                ci_lists: {},

                form: {
                    lc_custom_duty_no: '',
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
                },
                errors: {},
            };
        },

        methods: {

            edit(id) {
                var _this = this;
                axios.get(base_url + 'custom-duty-charge-entry/' + id + '/edit')
                    .then((response) => {
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

                axios.put(base_url + 'custom-duty-charge-entry/' + id, this.form)
                    .then((response) => {
                        this.showMassage(response.data);
                        if (response.data.status == 'success') {
                            EventBus.$emit('data-changed');
                        }
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

            loadCommercialInvoicesByLcNo() {
                var _this = this;
                var id = this.form.lc_opening_info_id;

                axios.get(base_url + "latr-entry/" + id + "/get-commercial-invoices-by-lcno").then((response) => {
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

            loadDetails() {
                var _this = this;
                var id = this.form.lc_commercial_invoice_id;

                axios.get(base_url + "custom-duty-charge-entry/" + id + "/ci-product-list").then((response) => {

                    if (response.data.length > 0) {
                        this.form.details = response.data;

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
                this.form.amount = total_cost.toFixed(2);
            },

            duplicateCheck(selectedValue) {
                var no_index = this.form.details.length;
                for (let i = 0; i < no_index; i++) {
                    if (this.form.details[i].product_id == selectedValue) {
                        alert("Duplicate Found");
                    }
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

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if (selectField == 'lc_opening_info_id') {
                } else if (selectField == 'lc_commercial_invoice_id') {
                    _this.form.cnf_agent_id = $('option:selected', $(e.target)).attr('get_cnf_agent_id');
                } else if (selectField == 'cnf_agent_id') {
                    
                } else if (selectField == 'custom_duty_cost_id') {
                    _this.form.custom_duty_cost_id = selectedItem.val();
                }
            });

            $('#editComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.duplicateCheck(selectedItem.val());
                _this.form.details[dataIndex].product_id = selectedItem.val();

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
                        _this.form.custom_duty_date = '';
                    } else {
                        _this.form.custom_duty_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created() {
            var _this = this;
            _this.edit(_this.editId);
        }
    }
</script>
