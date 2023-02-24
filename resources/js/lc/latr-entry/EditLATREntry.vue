<template>
    <div>
        <!--begin::Form-->
        <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data" id="editComponent"
              method="POST"
              v-on:submit.prevent="update(form.id)">
            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="lc_opening_info_id">LC No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-lc-no"
                                data-selectField="lc_opening_info_id"
                                disabled id="lc_opening_info_id" v-model="form.lc_opening_info_id">
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
                    <label class="col-lg-2 col-form-label" for="lc_commercial_invoice_id">CI No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-ci-no"
                                data-selectField="lc_commercial_invoice_id"
                                disabled id="lc_commercial_invoice_id"
                                v-model="form.lc_commercial_invoice_id">
                            <option :value="svalue.id" v-for="(svalue,sindex) in ci_lists"> {{svalue.ci_no}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['lc_commercial_invoice_id']">{{
                            errors['lc_commercial_invoice_id'][0] }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="latr_date">LATR Date <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input class="form-control form-control-sm m-input datepicker" data-dateField="latr_date"
                                   id="latr_date" placeholder="Select date from picker" readonly
                                   type="text" v-model="form.latr_date"/>
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
                    <!-- <label for="lc_margin_percentage" class="col-lg-2 col-form-label">LC Margin Percentage </label>
                    <div class="col-lg-4">
                        <input type="text"  id="lc_margin_percentage"  v-model="form.lc_margin_percentage" class="form-control form-control-sm m-input" placeholder="LC Margin Percentage" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('lc_margin_percentage'))">{{ (errors.hasOwnProperty('lc_margin_percentage')) ? errors.lc_margin_percentage[0] :'' }}</div>
                    </div> -->
                    <label class="col-lg-2 col-form-label" for="bank_latr_no">Bank LATR No. <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="bank_latr_no"
                               placeholder="Bank LATR No."
                               type="text" v-model="form.bank_latr_no">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('bank_latr_no'))">{{
                            (errors.hasOwnProperty('bank_latr_no')) ? errors.bank_latr_no[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="latr_total_amount">LATR Total Amount <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input class="form-control form-control-sm m-input" id="latr_total_amount"
                               placeholder="Total Amount"
                               type="text" v-model="form.latr_total_amount">
                        <div class="requiredField" v-if="errors['latr_total_amount']">{{ errors['latr_total_amount'][0]
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

                <!-- <div class="form-group m-form__group row add-manage-section">
                    <div class="m-section__content col-lg-12">
                        <div class ="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse" >
                                    <tr>
                                        <th></th>
                                        <th class="width-210">Product <span class="requiredField">*</span></th>
                                        <th>H.S Code</th>
                                        <th>Unit</th>
                                        <th>Qty <span class="requiredField">*</span></th>
                                        <th class="width-210">LC Unit Cost <span class="requiredField">*</span></th>
                                        <th style="width: 150px;">Total Price</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(details, index) in form.details">
                                        <td scope="row">
                                            <a href="javascript:void(0)"  @click="addNewItem" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Add More">
                                                <i class="la la-plus"></i>
                                            </a>
                                        </td>
                                        <td class="width-210">
                                            <select class="form-control m-select2 select2  select-product width-210" v-bind:data-index="index" v-model="details.product_id" style="width:100%" >
                                                <option v-for="(value,index) in product_lists"
                                                    :value="value.id"
                                                    :get_measure_unit_id="value.measure_unit_id"
                                                > {{value.product_name}}</option>
                                            </select>
                                            <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{ errors['details.'+index+'.product_id'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.hs_code" class="form-control form-control-sm m-input" placeholder="HS Code">
                                        </td>
                                        <td >
                                            <select class="form-control m-select2 select2 select-measure-unit" v-bind:data-index="index" v-model="details.measure_unit_id" style="width:100%" >
                                                <option v-for="(value,index) in measure_unit_lists" :value="value.id" > {{value.measure_unit}}</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="any" min="0" v-model="details.qty" @input="totalQtyAndPrice()" class="form-control form-control-sm m-input">
                                            <div class="requiredField" v-if="errors['details.'+index+'.qty']">{{ errors['details.'+index+'.qty'][0] }}</div>
                                        </td>

                                        <td style="width: 150px;">
                                            <input style="width: 150px;" type="number" step="any" min="0" v-model="details.unit_price" @input="totalQtyAndPrice()" class="form-control form-control-sm m-input text-right" placeholder="Unit Price">
                                            <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{ errors['details.'+index+'.unit_price'][0] }}</div>
                                        </td>

                                        <td style="width: 150px;">
                                            <input style="width: 150px;" type="text" v-model="details.total_amount" class="form-control form-control-sm m-input text-right" readonly>
                                        </td>
                                        <td>
                                            <a @click="removeItem(index,details.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colspan="4" class="text-right">Total </td>
                                        <td class="">
                                            <input type="text" v-model="form.total_qty" class="form-control form-control-sm m-input" placeholder="Total Qty" readonly>
                                        </td>
                                        <td></td>
                                        <td  class="">
                                            <input type="text" v-model="form.total_amount" class="form-control form-control-sm m-input text-right" placeholder="Total Amount" readonly>
                                        </td>
                                        <td></td>
                                    </tr>

                                    <tr>
                                        <td colspan="4" class="text-right">Bank Interest (%) </td>
                                        <td class="">
                                            <input type="text" v-model="form.bank_percentage" class="form-control form-control-sm m-input" placeholder="Bank interest"  >
                                        </td>
                                        <td>Bank Interest Amount  </td>
                                        <td  class="">
                                            <input type="text" v-model="form.bank_percentage_amount" class="form-control form-control-sm m-input text-right" placeholder="Bank Interest Amount"  >
                                            <div class="requiredField" v-if="errors['bank_percentage_amount']">{{ errors['bank_percentage_amount'][0] }}</div>
                                        </td>
                                        <td></td>
                                    </tr>

                                    <tr>
                                        <td colspan="4" class="text-right">LATR percentage </td>
                                        <td class="">
                                            <input type="number" step="any" min="0" v-model="form.latr_percentage"  class="form-control form-control-sm m-input" placeholder="90%">
                                        </td>
                                        <td class="text-right"> LATR Amount = </td>
                                        <td  class="">
                                            <input type="number" step="any" v-model="form.latr_total_amount" class="form-control form-control-sm m-input text-right" placeholder="LATR Total Amount">
                                            <div class="requiredField" v-if="errors['latr_total_amount']">{{ errors['latr_total_amount'][0] }}</div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <br><br>
                    </div>
                </div> -->


            </div>


            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button @click.prevent="update(form.id,0)" class="btn btn-sm btn-info" type="submit"><i
                                class="la la-save"></i> <span>Update</span></button>
                            <button @click.prevent="update(form.id,1)" class="btn btn-sm btn-success"
                                    type="submit" v-if="permissions.indexOf('latr-entry.approve') !=-1"><i
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

        props: ['permissions', 'lc_lists', 'product_lists', 'measure_unit_lists', 'editId'],

        data: function () {
            return {

                data_list: false,
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

                },
                errors: {},
            };
        },

        methods: {

            addNewItem() {
                this.form.details.push({
                    lc_latr_payment_entry_id: '',
                    product_id: '',
                    remarks: '',
                    hs_code: '',
                    unit_price: '',
                    qty: '',
                    measure_unit_id: '',
                    total_amount: '',
                });

                var Select2 = {
                    init: function () {
                        $(".select2").select2({placeholder: "Please select an option"})
                    }
                };
                jQuery(document).ready(function () {
                    Select2.init()
                });
            },

            removeItem(index, deletedId) {
                var _this = this;
                if (_this.form.details.length > 1) {
                    _this.form.details.splice(index, 1);
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

                    if (deletedId) {
                        _this.form.deleteID.push(deletedId);
                    }
                    _this.totalQtyAndPrice();
                }
            },

            edit(id) {
                var _this = this;
                axios.get(base_url + 'latr-entry/' + id + '/edit')
                    .then((response) => {
                        _this.form = response.data.data;
                        _this.loadCommercialInvoicesByLcNo();
                        console.log(response.data.data);
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

            update(id, approval) {
                var _this = this;
                _this.form.approve = approval;

                axios.put(base_url + 'latr-entry/' + id, this.form).then((response) => {
                    this.showMassage(response.data);
                    EventBus.$emit('data-changed');
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

                axios.get(base_url + "latr-entry/" + id + "/ci-product-list").then((response) => {

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
                var _this = this;
                var bankInt = 0;
                var bankIntAmout = 0;
                var grand_latr_total = (_this.form.latr_percentage / 100) * _this.form.total_amount;

                // bank_percentage: '5',
                //bank_percentage_amount: '',
                bankInt = _this.form.bank_percentage;
                bankIntAmout = _this.form.bank_percentage_amount;

                if (Number(bankInt) != 0) {
                    bankIntAmout = ((grand_latr_total / 100) * _this.form.bank_percentage).toFixed(2);
                } else {
                    bankIntAmout = bankIntAmout;
                }

                // _this.form.bank_percentage_amount = bankIntAmout ;
                // _this.form.latr_total_amount = (Number(grand_latr_total) + Number(bankIntAmout)).toFixed(2);
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
                    // _this.form.lc_opening_info_id= selectedItem.val();

                    // var get_cnf_margin = $('option:selected', this).attr('get_cnf_margin');
                    // _this.form.lc_margin_percentage= get_cnf_margin;

                    // _this.loadCommercialInvoicesByLcNo(); //for the dropdown

                } else if (selectField == 'lc_commercial_invoice_id') {
                    // _this.form.lc_commercial_invoice_id= selectedItem.val();

                    // _this.loadDetails();
                    // _this.form.latr_percentage = 100 - _this.form.lc_margin_percentage;
                }
            });

            $('#editComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.duplicateCheck(selectedItem.val());
                _this.form.details[dataIndex].product_id = selectedItem.val();

                _this.form.details[dataIndex].measure_unit_id = $('option:selected', this).attr('get_measure_unit_id');

                var Select2 = {
                    init: function () {
                        $(".select2").select2({placeholder: "Please select an option"})
                    }
                };
                jQuery(document).ready(function () {
                    Select2.init()
                });
            });
            $('#editComponent').on('change', '.select-measure-unit', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.form.details[dataIndex].measure_unit_id = selectedItem.val();
            });

            $(document).on("focus", ".datepicker", function () {

                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.latr_date = '';
                    } else {
                        _this.form.latr_date = moment(ev.date).format('DD/MM/YYYY');
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
