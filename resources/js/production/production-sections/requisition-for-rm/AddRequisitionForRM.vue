<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="requisition_no" class="col-lg-2 col-form-label">Requisition No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="requisition_no" v-model="form.rm_requisition_no"
                               class="form-control form-control-sm m-input" placeholder="Enter requisition no" readonly
                               autocomplete="off">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('rm_requisition_no'))">{{
                            (errors.hasOwnProperty('rm_requisition_no')) ? errors.rm_requisition_no[0] :'' }}
                        </div>
                    </div>
                    <label for="date" class="col-lg-2 col-form-label">Requisition Date <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" id="date" readonly
                                   class="form-control form-control-sm m-input datepicker"
                                   v-model="form.requisition_date" data-dateField="requisition_date"
                                   placeholder="Select date from picker" autocomplete="off"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('requisition_date'))">{{
                            (errors.hasOwnProperty('requisition_date')) ? errors.requisition_date[0] :'' }}
                        </div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.narration" id="narration"
                                  rows="2"></textarea>
                    </div>
                    <label for="warehouse_id" class="col-lg-2 col-form-label">Warehouse <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="warehouse_id" id="warehouse_id"
                                v-model="form.warehouse_id">
                            <option v-for="(value,index) in warehouse_lists" :value="value.id">
                                {{value.purchase_ware_houses_name}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="errors['warehouse_id']">{{ errors['warehouse_id'][0] }}</div>
                    </div>
                </div>
                <div class="massbody">
                    <div class="form-group m-form__group row">
                        <label class="col-lg-2 col-form-label" for="requisition_types">Requisition Types <span
                            class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <select class="form-control m-bootstrap-select" id="requisition_types"
                                    v-model="form.requisition_types" @change="getRmSetupData()">
                                <option value="">---Select---</option>
                                <option value="Normal Requisition (Consumable)">Normal Requisition (Consumable)</option>
                                <option value="Massbody Requisition">Massbody Requisition</option>
                                <option value="Glaze Materials">Glaze Materials</option>
                                <option value="Packing">Packing</option>
                                <option value="Assembling">Assembling</option>
                                <option value="Fitting">Fitting</option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('payment_method'))">{{
                                (errors.hasOwnProperty('payment_method')) ? errors.payment_method[0] :'' }}
                            </div>
                        </div>
                        <label v-show="form.requisition_types === 'Massbody Requisition' || form.requisition_types === 'Glaze Materials'"
                               class="col-lg-2 col-form-label" for="estimate_qty_for_production">Estimate KG for
                            production <span class="requiredField">*</span></label>
                        <div class="col-lg-4" v-show="form.requisition_types === 'Massbody Requisition'  || form.requisition_types === 'Glaze Materials'">
                            <input type="text" class="form-control form-control-sm m-input number"
                                   @input="massbodyCalculation()" v-model="form.estimate_qty_for_production"
                                   id="estimate_qty_for_production" placeholder="Enter estimate kg for production">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('estimate_qty_for_production'))">{{
                                (errors.hasOwnProperty('estimate_qty_for_production')) ?
                                errors.estimate_qty_for_production[0] :'' }}
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
                <!--begin::Portlet-->
                <div class="form-group m-form__group row add-manage-section">
                    <div class="m-section__content col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse">
                                <tr>
                                    <th></th>
                                    <th class="width-210">Product Name <span class="requiredField">*</span></th>
                                    <th>Current Stock</th>
                                    <th>Remarks</th>
                                    <th>Qty / Kg (Massbody) <span class="requiredField">*</span></th>
                                    <th>Unit Price (Inv. Avg)<span class="requiredField">*</span></th>
                                    <th class="width-160">Total Price <span class="requiredField">*</span></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr v-for="(details, index) in form.details"
                                    v-show="form.requisition_types === 'Normal Requisition (Consumable)' || form.requisition_types === 'Glaze Materials' || form.requisition_types === 'Packing' || form.requisition_types === 'Assembling' || form.requisition_types === 'Fitting'">
                                    <th scope="row">
                                        <a href="javascript:void(0)" @click="addNewItem"
                                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </th>
                                    <td class="width-210">
                                        <select class="form-control m-select2 select2 select-product width-210"
                                                v-bind:data-index="index" v-model="details.product_id"
                                                @input="normalCalculation()" style="width: 100%">
                                            <option v-for="(value,index) in product_lists" :value="value.id"
                                                    :buying-price="value.buying_price"> {{value.product_name}}
                                            </option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{
                                            errors['details.'+index+'.product_id'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input style="color: red;font-size: 12px;font-weight: bold" type="text" v-model="details.current_stock_qty" readonly
                                               class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.remarks"
                                               class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.qty"
                                               class="form-control form-control-sm m-input number" placeholder=""
                                               @input="normalCalculation()">
                                        <div class="requiredField" v-if="errors['details.'+index+'.qty']">{{
                                            errors['details.'+index+'.qty'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.unit_price"
                                               class="form-control form-control-sm m-input number"
                                               @input="normalCalculation()">
                                        <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{
                                            errors['details.'+index+'.unit_price'][0] }}
                                        </div>
                                    </td>
                                    <td class="width-160">
                                        <input type="text" v-model="details.total_price"
                                               class="form-control form-control-sm m-input number width-160"
                                               @input="normalCalculation()" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.total_price']">{{
                                            errors['details.'+index+'.total_price'][0] }}
                                        </div>
                                    </td>

                                    <td>
                                        <a @click="removeItem(index)"
                                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>

                                <tr v-for="(details, index) in form.details"
                                    v-show="form.requisition_types === 'Massbody Requisition'">
                                    <th scope="row">
                                        {{index+1}}
                                    </th>
                                    <td>
                                        <select class="form-control m-select2 select2 select-product"
                                                v-bind:data-index="index" v-model="details.product_id"
                                                style="width: 100%" disabled>
                                            <option v-for="(value,index) in product_lists" :value="value.id"
                                                    :measure-unit="value.measure_unit"> {{value.product_name}}
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
                                    <td>
                                        <input type="hidden" v-model="details.one_kg_weight"
                                               class="form-control form-control-sm m-input number" placeholder="">
                                        <input type="text" v-model="details.qty" @input="massbodyItemCalculation()"
                                               class="form-control form-control-sm m-input number" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.qty']">{{
                                            errors['details.'+index+'.qty'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.unit_price"
                                               @input="massbodyItemCalculation()"
                                               class="form-control form-control-sm m-input number">
                                        <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{
                                            errors['details.'+index+'.unit_price'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.total_price"
                                               class="form-control form-control-sm m-input number" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.total_price']">{{
                                            errors['details.'+index+'.total_price'][0] }}
                                        </div>
                                    </td>

                                    <td>
                                        <a @click="removeItem(index)"
                                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right">Total</td>
                                    <td class="">
                                        <input type="text" v-model="form.total_qty"
                                               class="form-control form-control-sm m-input" readonly>
                                    </td>
                                    <td class="text-right">Total Amount</td>
                                    <td class="">
                                        <input type="text" v-model="form.total_amount"
                                               class="form-control form-control-sm m-input" readonly>
                                    </td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <br><br>
                    </div>
                </div>

                <div class="alert alert-info">
                    <strong>Info!</strong> For "Glaze Materials" Unit Price Per Kg will be calculated by (Estimate KG / Total Price).
                </div>

            </div>

            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="submit" @click.prevent="store(0)" class="btn btn-sm btn-success"><i
                                class="la la-check"></i> <span>Save</span></button>
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
    import moment from 'moment';

    export default {

        props: ['permissions', 'product_lists', 'warehouse_lists', 'account_lists', 'cost_center_lists'],

        data: function () {
            return {

                data_list: false,
                add_form: true,
                edit_form: false,
                view_form: false,

                form: {
                    rm_requisition_no: '',
                    requisition_date: '',
                    warehouse_id: '',
                    narration: '',
                    estimate_qty_for_production: '',
                    total_qty: '',
                    total_amount: '',
                    requisition_types: '',
                    approve_status: '',
                    details: [
                        {
                            product_id: '',
                            remarks: '',
                            one_kg_weight: '',
                            current_stock_qty: '',
                            qty: '',
                            unit_price: 0,
                            total_price: '',
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {

            initFormDetails() {
                var _this = this;
                _this.form.details = [
                    {
                        product_id: '',
                        remarks: '',
                        current_stock_qty: '',
                        qty: '',
                        unit_price: 0,
                        total_price: '',
                    }
                ]
            },

            getRmSetupData() { //RM Ratio Setup
                var _this = this;

                if (_this.form.requisition_types === 'Massbody Requisition') {

                    if (_this.form.warehouse_id) {
                        _this.$loading(true);
                        axios.get(base_url + "requisition-for-raw-material/rm-ratio-setup-list")
                            .then((response) => {
                                _this.$loading(false);
                                if (response.data.length > 0) {
                                    this.form.details = response.data;
                                }
                            });
                    } else {
                        _this.form.requisition_types = '';
                        alert('Please Select Warehouse First!');

                        setTimeout(function () { //Refresh
                            var BootstrapSelect = {
                                init: function () {
                                    $(".m-bootstrap-select").selectpicker('refresh')
                                }
                            };
                            jQuery(document).ready(function () {
                                BootstrapSelect.init()
                            });
                        }, 250);

                        _this.initFormDetails();
                        _this.normalCalculation();
                    }
                } else {
                    _this.initFormDetails();
                    _this.normalCalculation();
                }
                _this.initSelect2();
            },

            normalCalculation() {
                var details = this.form.details;
                var length = details.length;
                var totalWeight = 0;
                var total_prices = 0;
                var totalAmount = 0;

                for (let i = 0; i < length; i++) {
                    total_prices = Number(details[i].qty) * details[i].unit_price;
                    details[i].total_price = total_prices.toFixed(2);
                    totalWeight = Number(details[i].qty) + totalWeight;
                    totalAmount = Number(total_prices) + totalAmount;
                }
                this.form.total_qty = totalWeight.toFixed(2);
                this.form.total_amount = totalAmount.toFixed(2);
            },

            massbodyCalculation() {
                var estimateQty = this.form.estimate_qty_for_production;
                var details = this.form.details;
                var length = details.length;
                var totalWeight = 0;
                var tQty = 0;
                var totalPrice = 0;
                var totalAmount = 0;

                for (let i = 0; i < length; i++) {
                    tQty = Number(details[i].one_kg_weight) * estimateQty;
                    details[i].qty = tQty.toFixed(2);
                    totalPrice = tQty * details[i].unit_price;

                    details[i].total_price = totalPrice.toFixed(2);

                    totalWeight = Number(tQty) + totalWeight;
                    totalAmount = Number(totalPrice) + totalAmount;
                }
                this.form.total_qty = totalWeight.toFixed(2);
                this.form.total_amount = totalAmount.toFixed(2);
            },

            massbodyItemCalculation() {
                var estimateQty = this.form.estimate_qty_for_production;
                var details = this.form.details;
                var length = details.length;
                var totalWeight = 0;
                var totalPrice = 0;
                var totalAmount = 0;

                for (let i = 0; i < length; i++) {

                    totalPrice = Number(details[i].qty) * Number(details[i].unit_price);
                    details[i].total_price = totalPrice.toFixed(2);

                    totalWeight = Number(details[i].qty) + totalWeight;
                    totalAmount = Number(totalPrice) + totalAmount;
                }
                this.form.total_qty = totalWeight.toFixed(2);
                this.form.total_amount = totalAmount.toFixed(2);
            },

            addNewItem() {
                var _this = this;
                this.form.details.push({
                    product_id: '',
                    remarks: '',
                    current_stock_qty: '',
                    qty: '',
                    unit_price: '',
                    total_price: '',
                });
                _this.initSelect2();
            },

            removeItem(index) {
                var _this = this;
                if (_this.form.details.length > 1) {
                    _this.form.details.splice(index, 1);
                }
                _this.normalCalculation();
                _this.massbodyCalculation();
            },

            store: function (app) {
                var _this = this;

                var details = _this.form.details;
                if (details[0].product_id) {
                    _this.form.approve_status = app;
                    axios.post(base_url + 'requisition-for-raw-material/store', _this.form).then((response) => {
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
                } else {
                    alert('Please select required field!')
                }
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

            duplicateCheck() {
                var _this = this;
                var no_index = this.form.details.length;
                var details = this.form.details;
                if (no_index > 1) {
                    for (let i = 0; i < no_index; i++) {
                        for (let j = i + 1; j < no_index; j++) {
                            if (details[i].product_id == details[j].product_id) {
                                alert("Duplicate Found");
                                details[j].product_id = '';

                                _this.initSelect2();
                            }
                        }
                    }
                }
            },

            rmNoGenerate() {
                var _this = this;
                axios.get(base_url + 'rm-requisition-no')
                    .then((response) => {
                        _this.form.rm_requisition_no = response.data;
                    });
            },

            getInventoryStockQtyAvgPrice(product_id, dataIndex) {
                var _this = this;
                var warehouse_id = _this.form.warehouse_id;

                var token_ics_qty_price = 'token_ics'; //ics=InventoryCurrentStock
                axios.get(base_url + 'stock-open?token_ics_qty_price=' + token_ics_qty_price + '&warehouse_id=' + warehouse_id + '&product_id=' + product_id)
                    .then((response) => {
                        //console.log(response.data.unit_price)
                        _this.form.details[dataIndex].current_stock_qty = response.data.stock_qty;
                        _this.form.details[dataIndex].unit_price = response.data.unit_price;
                        _this.normalCalculation();
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

                if (selectField == 'warehouse_id') {
                    _this.form.warehouse_id = selectedItem.val();
                } else if (selectField == 'debit_account_id') {
                    _this.form.debit_account_id = selectedItem.val();
                } else if (selectField == 'credit_account_id') {
                    _this.form.credit_account_id = selectedItem.val();
                } else if (selectField == 'cost_center_id') {
                    _this.form.cost_center_id = selectedItem.val();
                }
            });

            $('#addComponent').on('change', '.select-product', function (e) {

                if (_this.form.warehouse_id) {
                    var selectedItem = $(this),
                        curerntVal = !!selectedItem.val(),
                        dataIndex = $(e.currentTarget).attr('data-index'),
                        buyingPrice = $('option:selected', $(e.target)).attr('buying-price');

                    _this.getInventoryStockQtyAvgPrice(selectedItem.val(), dataIndex);

                    _this.form.details[dataIndex].product_id = selectedItem.val();
                    // _this.form.details[dataIndex].unit_price = buyingPrice;
                    _this.duplicateCheck();

                } else {
                    alert('Please Select Warehouse First!');
                }

                _this.initSelect2();
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                    //startDate: '-7d',
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.requisition_date = '';
                    } else {
                        _this.form.requisition_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });

            var BootstrapSelect = {
                init: function () {
                    $(".m-bootstrap-select").selectpicker()
                }
            };
            jQuery(document).ready(function () {
                BootstrapSelect.init()
            });

            $('.number').keypress(function (event) {
                if (((event.which != 46 || (event.which == 46 && $(this).val() == '')) ||
                    $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            }).on('paste', function (event) {
                event.preventDefault();
            });
        },

        created() {
            var _this = this;
            _this.rmNoGenerate();
            _this.massbodyCalculation();
        }
    }
</script>
