<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent"
              class="m-form m-form--fit m-form--label-align-right">

            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="forming_no" class="col-lg-2 col-form-label">Assembling No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="forming_no" v-model="form.assembling_no"
                               class="form-control form-control-sm m-input" placeholder="Enter Assembling No" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('assembling_no'))">{{
                            (errors.hasOwnProperty('assembling_no')) ? errors.assembling_no[0] :'' }}
                        </div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Assembling Date <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.assembling_date" data-dateField="assembling_date" readonly
                                   placeholder="Select date from picker" id="m_datepicker_2" autocomplete="off"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('assembling_date'))">{{
                            (errors.hasOwnProperty('assembling_date')) ? errors.assembling_date[0] :'' }}
                        </div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label for="warehouse_id" class="col-lg-2 col-form-label">Store Warehouse <span
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
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.narration" id="narration"
                                  rows="2"></textarea>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="assembling_types">Assembling Types <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" v-model="form.assembling_types"
                                data-selectField="assembling_types" id="assembling_types">
                            <option value="Production"> Production</option>
                            <!-- <option value="Reject"> Reject</option> -->
                        </select>
                    </div>
                </div>
                <div v-show="form.assembling_types === 'Production'">
                    <div class="form-group m-form__group row">
                        <label class="col-lg-2 col-form-label" for="finishingProduct">Finishing Product <span
                            class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <select class="form-control select2" v-model="form.finishing_product_id"
                                    data-selectField="finishing_product_id" id="finishingProduct" style="width: 100%">
                                <option v-for="(value,index) in finish_product_lists" :value="value.id"
                                        :finish-product-buying-price="value.buying_price"> {{value.product_name}}
                                </option>
                            </select>
                        </div>
                        <label class="col-lg-2 col-form-label" for="trans_to_packing_qty">Trans to inventory store qty
                            <span class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="trans_to_packing_qty"
                                   v-model="form.trans_to_packing_qty" @input="totalQtyPriceCalc()"
                                   class="form-control form-control-sm m-input"
                                   placeholder="Enter Trans to Packing Qty">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('trans_to_packing_qty'))">{{
                                (errors.hasOwnProperty('trans_to_packing_qty')) ? errors.trans_to_packing_qty[0] :'' }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="unit_price" class="col-lg-2 col-form-label">Unit Price <span
                            class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="unit_price" v-model="form.unit_price"
                                   @change="totalQtyPriceCalc()" class="form-control form-control-sm m-input"
                                   placeholder="Enter Unit Price" readonly>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('unit_price'))">{{
                                (errors.hasOwnProperty('unit_price')) ? errors.unit_price[0] :'' }}
                            </div>
                        </div>
                        <label class="col-lg-2 col-form-label" for="total_price">Total Price <span
                            class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="total_price" v-model="form.total_price"
                                   @change="totalQtyPriceCalc()" class="form-control form-control-sm m-input"
                                   placeholder="Enter Total Price" readonly>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('total_price'))">{{
                                (errors.hasOwnProperty('total_price')) ? errors.total_price[0] :'' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="pre_mth_ovr_price">Previous month overhead price
                        (Kg)</label>
                    <div class="col-lg-4">
                        <input type="text" id="overhead_kg_price" v-model="overhead_kg_price" class="form-control form-control-sm m-input" placeholder="ex: 1 kg" readonly>
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
                                    <th class="width-210">Product <span class="requiredField">*</span></th>
                                    <th>Remarks</th>
                                    <th>Semi.F Stock Qty <span class="requiredField">*</span></th>
                                    <th>Inventory Stock Qty <span class="requiredField">*</span></th>
                                    <th>Asse. Setup Per Qty <span class="requiredField">*</span></th>

                                    <th>Semi.F Use Qty <span class="requiredField">*</span></th>
                                    <th>Inventory Use Qty <span class="requiredField">*</span></th>
                                    <th>Total used Qty <span class="requiredField">*</span></th>
                                    <th>Unit Price <span class="requiredField">*</span></th>
                                    <th>Overhead Price <span class="requiredField">*</span></th>
                                    <th class="width-160">Total Price <span class="requiredField">*</span></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <th scope="row">
                                        <a href="javascript:void(0)" @click="addNewItem"
                                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </th>
                                    <td class="width-210">
                                        <select class="form-control select2 select-product width-210"
                                                v-bind:data-index="index" v-model="details.product_id"
                                                style="width:100%">
                                            <option v-for="(value,index) in product_lists"
                                                    :value="value.id"
                                                    :unit_price="value.unit_price"
                                                    :semi_finished_stock_qty="value.semi_finished_stock_qty"
                                                    :inventory_stock_qty="value.inventory_stock_qty"
                                            > {{value.product_name}}
                                            </option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{
                                            errors['details.'+index+'.product_id'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm m-input"
                                               v-model="details.remarks" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.remarks']">{{
                                            errors['details.'+index+'.remarks'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-100"
                                               v-model="details.semi_finished_stock_qty" placeholder="" readonly>
                                        <div class="requiredField"
                                             v-if="errors['details.'+index+'.semi_finished_stock_qty']">{{
                                            errors['details.'+index+'.semi_finished_stock_qty'][0] }}
                                        </div>
                                    </td>

                                    <td>
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-100"
                                               v-model="details.inventory_stock_qty" placeholder="" readonly>
                                        <div class="requiredField"
                                             v-if="errors['details.'+index+'.inventory_stock_qty']">{{
                                            errors['details.'+index+'.inventory_stock_qty'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" step="any"
                                               class="form-control form-control-sm m-input width-50"
                                               v-model="details.count_qty" @input="totalQtyPriceCalc()">
                                    </td>

                                    <td>
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-80"
                                               v-model="details.semi_finished_use_qty"
                                               v-if="details.semi_finished_stock_qty > 0"
                                               @input="rowSemiFinishUseQtyCalc()" placeholder="">
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-80"
                                               v-model="details.semi_finished_use_qty" v-else placeholder="" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-80"
                                               v-model="details.inventory_use_qty"
                                               v-if="details.inventory_stock_qty > 0" @input="rowInventoryUseQtyCalc()"
                                               placeholder="">
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-80"
                                               v-model="details.inventory_use_qty" v-else placeholder="" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-80"
                                               v-model="details.total_used_qty" placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.total_used_qty']">{{
                                            errors['details.'+index+'.total_used_qty'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-80"
                                               v-model="details.unit_price" @input="rowItemQtyPriceCalcWithOverhead()"
                                               placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{
                                            errors['details.'+index+'.unit_price'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.overhead_price"
                                               class="form-control form-control-sm m-input number" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.overhead_price']">{{
                                            errors['details.'+index+'.overhead_price'][0] }}
                                        </div>
                                    </td>
                                    <td class="width-160">
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-160"
                                               v-model="details.total_price" placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.total_price']">{{
                                            errors['details.'+index+'.total_price'][0] }}
                                        </div>
                                    </td>
                                    <td scope="row" class="width-100 text-center">
                                        <a @click="removeItem(index)"
                                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="text-right">Total</td>
                                    <td colspan="2">{{ form.entered_total_use_qty }}</td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_rm_qty"
                                               class="form-control form-control-sm m-input" placeholder="Used Qty"
                                               readonly>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_rm_price"
                                               class="form-control form-control-sm m-input" placeholder="Total Price"
                                               readonly>
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
                            <button type="submit" v-if="permissions.indexOf('assembling-section.approve') != -1"
                                    class="btn btn-sm btn-primary" @click.prevent="store(1)"><i class="la la-check"></i>
                                <span>Approve</span></button>
                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="store(0)"><i
                                class="la la-check"></i> <span>Save and Go List</span></button>
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

        props: ['permissions', 'finish_product_lists', 'warehouse_lists'],

        data: function () {
            return {

                data_list: false,
                add_form: true,
                edit_form: false,
                view_form: false,

                overhead_kg_price: 0,

                product_lists: [],

                form: {
                    assembling_no: '',
                    assembling_date: '',
                    warehouse_id: '',
                    assembling_types: 'Production',
                    finishing_product_id: '',
                    trans_to_packing_qty: 0,
                    unit_price: '',
                    total_price: '',
                    narration: '',
                    total_rm_qty: '',
                    total_rm_price: '',
                    approve_status: '',
                    status: '',
                    details: [
                        {
                            product_id: '',
                            remarks: '',
                            semi_finished_stock_qty: '',
                            inventory_stock_qty: '',
                            count_qty: 1,

                            semi_finished_use_qty: '',
                            inventory_use_qty: '',
                            total_used_qty: '',
                            unit_price: '',
                            overhead_price: '',
                            total_price: '',
                        }
                    ],
                    entered_total_use_qty: '',

                    last_month_overhead_amt: '',
                    last_month_production_kg: '',
                    overhead_per_kg: '',
                },
                errors: {},
            };
        },

        methods: {

            getOverheadCosting(){
                var _this = this;
                _this.$loading(true);
                axios.get(base_url+"raw-material-ratio-setup").then((response) => {
                    _this.$loading(false);
                    if (response.data.recyle_chip) {
                        _this.overhead_kg_price = response.data.recyle_chip.overhead_per_kg;

                        _this.form.last_month_overhead_amt = response.data.recyle_chip.last_month_overhead_amt;
                        _this.form.last_month_production_kg = response.data.recyle_chip.last_month_production_kg;
                        _this.form.overhead_per_kg = response.data.recyle_chip.overhead_per_kg;
                    }
                });
            },

            addNewItem() {
                var _this = this;
                _this.form.details.push({
                    product_id: '',
                    remarks: '',
                    semi_finished_stock_qty: '',
                    inventory_stock_qty: '',
                    count_qty: 1,

                    semi_finished_use_qty: 0,
                    inventory_use_qty: 0,
                    total_used_qty: '',
                    unit_price: '',
                    overhead_price: '',
                    total_price: '',
                });

                _this.totalQtyPriceCalc();
                _this.initSelect2();
            },

            removeItem(index) {
                var _this = this;
                if (_this.form.details.length > 1) {
                    _this.form.details.splice(index, 1);
                }
                _this.totalQtyPriceCalc();
            },

            store: function (app) {
                var _this = this;
                _this.form.approve_status = app;

                var form_status = _this.formValidationOnSubmit();
                if (form_status) {
                    _this.$loading(true);
                    axios.post(base_url + 'assembling-section/store', _this.form)
                        .then((response) => {
                            _this.$loading(false);
                            if (response.data.status == 'success') {
                                _this.showMassage(response.data);
                                EventBus.$emit('data-changed');
                            } else {
                                _this.showMassage(response.data.message);
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

            totalQtyPriceCalc() {
                var _this = this;

                var totalPackedPrice = 0;
                totalPackedPrice = Number(_this.form.unit_price) * Number(_this.form.trans_to_packing_qty);
                _this.form.total_price = totalPackedPrice.toFixed(2);

                var totalRmPrice = 0;
                var totalRmAmount = 0;
                var totalUsedQty = 0;
                var usedQty = 0;


                var details = this.form.details;
                var length = details.length;

                for (let i = 0; i < length; i++) {
                    usedQty = Number(_this.form.trans_to_packing_qty) * Number(details[i].count_qty);
                    totalRmPrice = Number(details[i].unit_price) * usedQty;

                    _this.form.details[i].total_used_qty = usedQty.toFixed(2);
                    _this.form.details[i].total_price = totalRmPrice.toFixed(2);

                    totalUsedQty = Number(details[i].total_used_qty) + totalUsedQty;
                    totalRmAmount = totalRmPrice + totalRmAmount;
                }
                _this.form.total_rm_qty = totalUsedQty.toFixed(2);
                _this.form.total_rm_price = totalRmAmount.toFixed(2);

                _this.rowItemQtyPriceCalcWithOverhead();
            },

            rowSemiFinishUseQtyCalc() {
                var _this = this;
                if (Number(_this.form.trans_to_packing_qty) > 0) {
                    var grandTotalQty = 0;
                    var details = this.form.details;
                    var length = details.length;
                    for (let i = 0; i < length; i++) {
                        if (Number(details[i].semi_finished_use_qty) > Number(details[i].semi_finished_stock_qty)) {
                            _this.form.details[i].semi_finished_use_qty = Number(details[i].semi_finished_stock_qty).toFixed(2);
                        }
                        if (Number(details[i].semi_finished_use_qty) > Number(details[i].total_used_qty)) {
                            _this.form.details[i].semi_finished_use_qty = Number(details[i].total_used_qty).toFixed(2);
                        }

                        var remain_qty = Number(details[i].total_used_qty) - Number(details[i].semi_finished_use_qty);
                        if (Number(details[i].inventory_stock_qty) > 0) {
                            _this.form.details[i].inventory_use_qty = remain_qty.toFixed(2);
                        }
                        grandTotalQty = grandTotalQty + Number(details[i].semi_finished_use_qty) + Number(details[i].inventory_use_qty);
                    }
                    _this.form.entered_total_use_qty = grandTotalQty.toFixed(2);
                } else {
                    alert('Please fill up this - Trans to inventory store qty!');
                    $("#trans_to_packing_qty").focus();
                }
            },

            rowInventoryUseQtyCalc() {
                var _this = this;
                if (Number(_this.form.trans_to_packing_qty) > 0) {
                    var grandTotalQty = 0;
                    var details = this.form.details;
                    var length = details.length;
                    for (let i = 0; i < length; i++) {
                        if (Number(details[i].inventory_use_qty) > Number(details[i].inventory_stock_qty)) {
                            _this.form.details[i].inventory_use_qty = Number(details[i].inventory_stock_qty).toFixed(2);
                        }
                        if (Number(details[i].inventory_use_qty) > Number(details[i].total_used_qty)) {
                            _this.form.details[i].inventory_use_qty = Number(details[i].total_used_qty).toFixed(2);
                        }

                        var remain_qty = Number(details[i].total_used_qty) - Number(details[i].inventory_use_qty);
                        if (Number(details[i].semi_finished_stock_qty) > 0) {
                            _this.form.details[i].semi_finished_use_qty = remain_qty.toFixed(2);
                        }
                        grandTotalQty = grandTotalQty + Number(details[i].semi_finished_use_qty) + Number(details[i].inventory_use_qty);
                    }
                    _this.form.entered_total_use_qty = grandTotalQty.toFixed(2);
                } else {
                    alert('Please fill up this - Trans to inventory store qty!');
                    $("#trans_to_packing_qty").focus();
                }
            },

            rowItemQtyPriceCalcWithOverhead() {
                var _this = this;
                var grandTotalPrice = 0;
                var overhead_price = 0;
                var details = this.form.details;

                var length = details.length;
                for (let i = 0; i < length; i++) {

                    // Over head calculation
                    overhead_price = Number(details[i].total_used_qty) * _this.overhead_kg_price;

                    _this.form.details[i].overhead_price = overhead_price.toFixed(2);

                    var total_price = Number(details[i].unit_price) * Number(details[i].total_used_qty) + overhead_price;
                    _this.form.details[i].total_price = total_price.toFixed(2);
                    grandTotalPrice = grandTotalPrice + total_price;
                }
                _this.form.total_rm_price = grandTotalPrice.toFixed(2);

                _this.calcFinishedProductUnitPrice();
            },

            calcFinishedProductUnitPrice() {

                var _this = this;

                _this.form.total_price = _this.form.total_rm_price;
                _this.form.unit_price = Number(_this.form.total_rm_price / _this.form.trans_to_packing_qty).toFixed(2);
            },

            formValidationOnSubmit() {
                var _this = this;

                if (_this.form.total_rm_qty === _this.form.entered_total_use_qty) {
                    // return true;
                } else {
                    alert('Sorry - Total Required Qty and Total Use Qty are not same!');
                    return false;
                }

                var totalStockQty = 0;
                var details = this.form.details;
                var length = details.length;
                for (let i = 0; i < length; i++) {
                    totalStockQty = totalStockQty + Number(details[i].semi_finished_stock_qty) + Number(details[i].inventory_stock_qty);
                }

                if (_this.form.entered_total_use_qty > totalStockQty) {
                    alert('Sorry - Insufficient Stock Qty!');
                    return false;
                } else {
                    return true;
                }

                alert('Sorry - something went worng!');
                return false;
            },

            loaProductsWithStock() {
                var _this = this;
                var warehouse_id = _this.form.warehouse_id;
                _this.$loading(true);
                axios.get(base_url + 'get-assembling-setup-data/finishing_product_id?warehouse_id=' + warehouse_id)
                    .then((response) => {
                        _this.$loading(false);
                        _this.product_lists = response.data;
                        _this.initSelect2();
                    });
            },

            duplicateCheck(selectedValue) {
                var no_index = this.form.details.length;
                for (let i = 0; i < no_index; i++) {
                    if (this.form.details[i].product_id == selectedValue) {
                        alert("Duplicate Found");
                        return true;
                    }
                }
                return false;
            },

            assemblingNoGenerate() {
                var _this = this;
                axios.get(base_url + 'assembling-no')
                    .then((response) => {
                        _this.form.assembling_no = response.data;
                    });
            },

            getInventoryOrSemiFinishedLastPrice(product_id, dataIndex) {
                var _this = this;
                axios.get(base_url + 'finished-stock-section?token_last_price=yes&product_id=' + product_id)
                    .then((response) => {

                        _this.form.details[dataIndex].unit_price = response.data.unit_price;
                        _this.totalQtyPriceCalc();
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

            $('form').on('focus', 'input[type=number]', function (e) {
                $(this).on('mousewheel.disableScroll', function (e) {
                    e.preventDefault()
                })
            })
            $('form').on('blur', 'input[type=number]', function (e) {
                $(this).off('mousewheel.disableScroll')
            })

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if (selectField == 'assembling_types') {
                    _this.form.assembling_types = selectedItem.val();

                } else if (selectField == 'warehouse_id') {
                    _this.form.warehouse_id = selectedItem.val();

                    _this.form.details = [
                        {
                            product_id: '',
                            remarks: '',
                            semi_finished_stock_qty: '',
                            inventory_stock_qty: '',
                            count_qty: 1,

                            semi_finished_use_qty: 0,
                            inventory_use_qty: 0,
                            total_used_qty: '',
                            unit_price: '',
                            overhead_price: '',
                            total_price: '',
                        }
                    ];

                    _this.loaProductsWithStock();
                } else if (selectField == 'finishing_product_id') {

                    var finishProductBuyingPrice = $('option:selected', $(e.target)).attr('finish-product-buying-price');
                    _this.form.finishing_product_id = selectedItem.val();
                    _this.form.unit_price = finishProductBuyingPrice;
                }
            });

            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');

                var unit_price = $('option:selected', $(e.target)).attr('unit_price');
                var semi_finished_stock_qty = $('option:selected', $(e.target)).attr('semi_finished_stock_qty');
                var inventory_stock_qty = $('option:selected', $(e.target)).attr('inventory_stock_qty');

                _this.form.details[dataIndex].unit_price = unit_price ? unit_price : 0;
                _this.form.details[dataIndex].semi_finished_stock_qty = semi_finished_stock_qty ? semi_finished_stock_qty : 0;
                _this.form.details[dataIndex].inventory_stock_qty = inventory_stock_qty ? inventory_stock_qty : 0;

                var isDuplicated = _this.duplicateCheck(selectedItem.val());
                if (isDuplicated) {
                    _this.form.details[dataIndex].product_id = '';
                    _this.initSelect2();
                } else {
                    _this.form.details[dataIndex].product_id = selectedItem.val();

                    _this.getInventoryOrSemiFinishedLastPrice(selectedItem.val(), dataIndex);
                }
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.assembling_date = '';
                    } else {
                        _this.form.assembling_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created() {
            var _this = this;
            _this.totalQtyPriceCalc();
            _this.assemblingNoGenerate();
            setTimeout(function () {
                _this.getOverheadCosting();
            }, 150);
        }
    }
</script>
