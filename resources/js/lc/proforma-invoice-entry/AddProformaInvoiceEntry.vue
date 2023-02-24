<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <div class="col-md-4">
                        <label for="pi_no">PI No <span class="requiredField">*</span></label>
                        <input type="text" id="pi_no" v-model="form.pi_no" class="form-control form-control-sm m-input"
                               placeholder="Enter Proforma Invoice No">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('pi_no'))">{{
                            (errors.hasOwnProperty('pi_no')) ? errors.pi_no[0] :'' }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="work_order_id">Work Order No <span class="requiredField">*</span></label>
                        <select class="form-control m-select2 select2  select-work-order" id="work_order_id"
                                data-selectField="work_order_id" v-model="form.work_order_id">
                            <option v-for="(svalue,sindex) in work_order_lists"
                                    :value="svalue.id"
                                    :supplier_id="svalue.supplier_id"
                                    :get_supplier="svalue.get_supplier ? svalue.get_supplier.purchase_supplier_name : ''"
                            > {{svalue.work_order_no}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="errors['work_order_id']">{{ errors['work_order_id'][0] }}</div>
                    </div>
                    <div class="col-md-4">
                        <label for="supplier_info_display">Supplier/Exporter </label>
                        <input type="text" id="supplier_info_display" v-model="form.supplier_info_display"
                               class="form-control form-control-sm m-input" placeholder="Supplier" readonly>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <div class="col-md-4">
                        <label for="pi_date">PI Date </label>
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.pi_date" data-dateField="pi_date" readonly
                                   placeholder="Select date from picker" id="pi_date"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="usd_account_no">USD A/C No</label>
                        <input type="text" id="usd_account_no" v-model="form.usd_account_no"
                               class="form-control form-control-sm m-input" placeholder="Enter USD A/C  No">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('usd_account_no'))">{{
                            (errors.hasOwnProperty('usd_account_no')) ? errors.usd_account_no[0] :'' }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="consignee">Consignee </label>
                        <input type="text" id="consignee" v-model="form.consignee"
                               class="form-control form-control-sm m-input" placeholder="Enter Consignee Name">
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <div class="col-md-4">
                        <label for="swift_code">Swift Code </label>
                        <input type="text" id="swift_code" v-model="form.swift_code"
                               class="form-control form-control-sm m-input" placeholder="Enter Swift Code">
                    </div>
                    <div class="col-md-4">
                        <label for="terms_of_loading">Terms Of Loading <span class="requiredField">*</span></label>
                        <div class="input-group date">
                            <input type="text" id="terms_of_loading" v-model="form.terms_of_loading"
                                   class="form-control form-control-sm m-input" placeholder="Enter Terms Of Loading">
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('terms_of_loading'))">{{
                            (errors.hasOwnProperty('terms_of_loading')) ? errors.terms_of_loading[0] :'' }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="port_of_discharger">Port of Discharge <span class="requiredField">*</span></label>
                        <input type="text" id="port_of_discharger" v-model="form.port_of_discharger"
                               class="form-control form-control-sm m-input" placeholder="Enter Port of Discharge">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('port_of_discharger'))">{{
                            (errors.hasOwnProperty('port_of_discharger')) ? errors.port_of_discharger[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <div class="col-md-4">
                        <label for="delivery_time">Delivery Time </label>
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.delivery_time" data-dateField="delivery_time" readonly
                                   placeholder="Select date from picker" id="delivery_time"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="terms_of_delivery">Terms Of Delivery </label>
                        <div class="input-group date">
                            <input type="text" id="terms_of_delivery" v-model="form.terms_of_delivery"
                                   class="form-control form-control-sm m-input" placeholder="Enter Terms Of Delivery">
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('terms_of_delivery'))">{{
                            (errors.hasOwnProperty('terms_of_delivery')) ? errors.terms_of_delivery[0] :'' }}
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="terms_of_payment">Terms of Payment </label>
                        <input type="text" id="terms_of_payment" v-model="form.terms_of_payment"
                               class="form-control form-control-sm m-input" placeholder="Enter Terms of Payment">
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <div class="col-md-4">
                        <label for="origin_of_goods">Origin Of Goods </label>
                        <select class="form-control m-select2 select2  select-country" id="origin_of_goods"
                                data-selectField="origin_of_goods" v-model="form.origin_of_goods">
                            <option v-for="(svalue,sindex) in cuountry_lists" :value="svalue.id">
                                {{svalue.country_name}}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="final_destination">Final Destination </label>
                        <input type="text" id="final_destination" v-model="form.final_destination"
                               class="form-control form-control-sm m-input" placeholder="Enter Final Destination">
                    </div>
                    <div class="col-md-4">
                        <label for="narration">Narration </label>
                        <textarea class="form-control m-input" v-model="form.narration" id="narration"
                                  rows="2"></textarea>
                    </div>
                </div>
                <br>
                <!--begin::Portlet-->

                <div class="form-group m-form__group row add-manage-section">
                    <div class="m-section__content col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse">
                                <tr>
                                    <th></th>
                                    <th class="width-210">Product <span class="requiredField">*</span></th>
                                    <th>Remarks</th>
                                    <th class="width-210">Currency <span class="requiredField">*</span></th>
                                    <th>Unit Price <span class="requiredField">*</span></th>
                                    <th width="10%">Qty <span class="requiredField">*</span></th>
                                    <th width="10%">Total Price</th>
                                    <th>BDT Rate <span class="requiredField">*</span></th>
                                    <th>Unit Price (BDT)</th>
                                    <th class="width-160">Total Taka (BDT)</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <td scope="row">
                                        <a href="javascript:void(0)" @click="addNewItem"
                                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </td>
                                    <td class="width-210">
                                        <select class="form-control m-select2 select2  select-product width-210"
                                                v-bind:data-index="index" v-model="details.product_id"
                                                style="width:100%">
                                            <option v-for="(value,index) in product_lists" :value="value.id">
                                                {{value.product_name}} - {{value.product_code}}
                                            </option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{
                                            errors['details.'+index+'.product_id'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.remarks"
                                               class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td class="width-210">
                                        <select class="form-control m-select2 select2  select-currency width-210"
                                                v-bind:data-index="index" v-model="details.currency_id"
                                                style="width:100%">
                                            <option v-for="(value,index) in currency_lists" :value="value.id">
                                                {{value.symbol}} ({{value.name}})
                                            </option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.currency_id']">{{
                                            errors['details.'+index+'.currency_id'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="details.unit_price"
                                               @input="totalQtyAndPriceAndRate()"
                                               class="form-control form-control-sm m-input width-100" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{
                                            errors['details.'+index+'.unit_price'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="details.qty"
                                               @input="totalQtyAndPriceAndRate()"
                                               class="form-control form-control-sm m-input width-100">
                                        <div class="requiredField" v-if="errors['details.'+index+'.qty']">{{
                                            errors['details.'+index+'.qty'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.total_price"
                                               class="form-control form-control-sm m-input text-right width-100"
                                               placeholder="" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="details.bdt_convert_rate"
                                               @input="totalQtyAndPriceAndRate()"
                                               class="form-control form-control-sm m-input width-100">
                                        <div class="requiredField" v-if="errors['details.'+index+'.bdt_convert_rate']">
                                            {{ errors['details.'+index+'.bdt_convert_rate'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.unit_price_bdt"
                                               class="form-control form-control-sm m-input text-right width-100"
                                               readonly>
                                    </td>

                                    <td class="width-160">
                                        <input type="text" v-model="details.total_amount_taka"
                                               class="form-control form-control-sm m-input text-right width-160"
                                               readonly>
                                    </td>

                                    <td>
                                        <a @click="removeItem(index)"
                                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Total</td>
                                    <td class="">
                                        <input type="text" v-model="form.total_qty"
                                               class="form-control form-control-sm m-input" placeholder="Qty" readonly>
                                    </td>
                                    <td class="">
                                        <input type="text" v-model="form.total_amount"
                                               class="form-control form-control-sm m-input text-right"
                                               placeholder="Total Price" readonly>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td class="">
                                        <input type="text" v-model="form.total_amount_taka"
                                               class="form-control form-control-sm m-input text-right"
                                               placeholder="Amount in Taka" readonly>
                                    </td>
                                    <td></td>
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
                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="store(0)"><i
                                class="la la-check"></i> <span>Save</span></button>
                            <button v-if="permissions.indexOf('proforma-invoice-entry.approve') !=-1" type="submit"
                                    class="btn btn-sm btn-success" @click.prevent="store(1)"><i class="la la-check"></i>
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
        props: ['permissions', 'work_order_lists', 'product_lists', 'currency_lists', 'cuountry_lists'],

        data: function () {
            return {
                product_list: false,
                add_form: true,
                edit_form: false,

                form: {
                    pi_no: '',
                    work_order_id: '',
                    supplier_id: '',
                    supplier_info_display: '',
                    usd_account_no: '',
                    pi_date: '',
                    consignee: '',
                    swift_code: '',
                    terms_of_loading: '',
                    port_of_discharger: '',
                    delivery_time: '',
                    terms_of_delivery: '',
                    terms_of_payment: '',
                    origin_of_goods: '',
                    final_destination: '',
                    narration: '',
                    total_qty: 0,
                    total_amount: 0,
                    total_amount_taka: 0,
                    approve: '',
                    details: [
                        {
                            product_id: '',
                            remarks: '',
                            currency_id: '1',
                            unit_price: '',
                            qty: '',
                            total_price: 0,
                            bdt_convert_rate: '',
                            total_amount_taka: 0,
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {

            piNoGenerate() {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'pi-no-generate')
                    .then((response) => {
                        _this.$loading(false);
                        _this.form.pi_no = response.data;
                    });
            },

            addNewItem() {
                this.form.details.push({
                    product_id: '',
                    remarks: '',
                    currency_id: '1',
                    unit_price: '',
                    qty: '',
                    total_price: 0,
                    bdt_convert_rate: '',
                    total_amount_taka: 0,
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

            removeItem(index) {
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

                }
            },

            store(app) {
                var _this = this;
                _this.form.approve = app;

                _this.$loading(true);
                axios.post(base_url + 'proforma-invoice-entry', _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        if (response.data.status == 'success') {
                            _this.showMassage(response.data);
                            EventBus.$emit('data-changed');
                        } else {
                            _this.showMassage(response.data);
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

            loadDetails() {
                var _this = this;
                var id = _this.form.work_order_id;

                _this.$loading(true);
                axios.get(base_url + "work-order-import/" + id + "/lc-product-list")
                    .then((response) => {
                        _this.$loading(false);
                        if (response.data.length > 0) {
                            _this.form.details = response.data;
                            _this.totalQtyAndPriceAndRate();
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

            totalQtyAndPriceAndRate() {
                var total_qty = 0;
                var total_amount = 0;
                var total_price = 0;
                var total_amount_taka = 0;
                var grand_total_amount_taka = 0;
                var details = this.form.details;
                var length = details.length;

                for (let i = 0; i < length; i++) {
                    total_price = Number(details[i].unit_price) * Number(details[i].qty);
                    this.form.details[i].total_price = total_price;
                    total_qty = Number(details[i].qty) + total_qty;
                    total_amount = total_price + total_amount;

                    total_amount_taka = total_price * Number(details[i].bdt_convert_rate);
                    this.form.details[i].total_amount_taka = total_amount_taka.toFixed(2);
                    var get_unit_price_bdt = 0;
                    get_unit_price_bdt = total_amount_taka / Number(details[i].qty);
                    if (get_unit_price_bdt > 0) {
                        this.form.details[i].unit_price_bdt = (total_amount_taka / Number(details[i].qty)).toFixed(2);
                    }
                    grand_total_amount_taka = grand_total_amount_taka + total_amount_taka;
                }
                this.form.total_price = total_price;
                this.form.total_qty = total_qty;
                this.form.total_amount = total_amount;
                this.form.total_amount_taka = grand_total_amount_taka.toFixed(2);
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

            $('#addComponent').on('change', '.select-work-order', function (e) {
                var selectedItem = $(this);
                _this.form.work_order_id = selectedItem.val();
                _this.loadDetails();


                _this.form.supplier_id = $('option:selected', this).attr('supplier_id');
                _this.form.supplier_info_display = $('option:selected', this).attr('get_supplier');
            });
            $('#addComponent').on('change', '.select-country', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.form.origin_of_goods = selectedItem.val();
            });

            $('#addComponent').on('change', '.select-currency', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.form.details[dataIndex].currency_id = selectedItem.val();
            });

            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.duplicateCheck(selectedItem.val());
                _this.form.details[dataIndex].product_id = selectedItem.val();
            });

            $(document).on("focus", "#pi_date", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.pi_date = '';
                    } else {
                        _this.form.pi_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
            $(document).on("focus", "#delivery_time", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.delivery_time = '';
                    } else {
                        _this.form.delivery_time = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created() {
            var _this = this;
            _this.piNoGenerate();
            _this.form.pi_date = moment().format('DD/MM/YYYY');
        }
    }
</script>
