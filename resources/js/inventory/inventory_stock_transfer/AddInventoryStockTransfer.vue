<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="transfers_no" class="col-lg-2 col-form-label">Stock Transfer No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="transfers_no" v-model="form.transfers_no"
                               class="form-control form-control-sm m-input" placeholder="Enter Stock Transfer No "
                               readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('transfers_no'))">{{
                            (errors.hasOwnProperty('transfers_no')) ? errors.transfers_no[0] :'' }}
                        </div>
                    </div>

                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Return Date </label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.transfers_date" data-dateField="transfers_date" readonly
                                   placeholder="Select date from picker" id="m_datepicker_2"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('transfers_date'))">{{
                            (errors.hasOwnProperty('transfers_date')) ? errors.transfers_date[0] :'' }}
                        </div>
                    </div>

                </div>

                <div class="form-group m-form__group row">
                    <label for="from_warehouse_id" class="col-lg-2 col-form-label">From Warehouse</label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="from_warehouse_id"
                                id="from_warehouse_id" v-model="form.from_warehouse_id">
                            <option v-for="(wvalue,windex) in warehouse_lists" :value="wvalue.id">
                                {{wvalue.purchase_ware_houses_name}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('from_warehouse_id'))">{{
                            (errors.hasOwnProperty('from_warehouse_id')) ? errors.from_warehouse_id[0] :'' }}
                        </div>
                    </div>
                    <label for="to_warehouse_id" class="col-lg-2 col-form-label">To Warehouse</label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="to_warehouse_id"
                                id="to_warehouse_id" v-model="form.to_warehouse_id">
                            <option v-for="(wvalue,windex) in warehouse_lists" :value="wvalue.id">
                                {{wvalue.purchase_ware_houses_name}}
                            </option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('to_warehouse_id'))">{{
                            (errors.hasOwnProperty('to_warehouse_id')) ? errors.to_warehouse_id[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.transfers_narration" id="narration"
                                  rows="2"></textarea>
                    </div>
                    <label> </label>
                    <div class="col-lg-4"></div>
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
                                    <th width="210">Product Name</th>
                                    <th>Remarks</th>
                                    <th>Unit</th>
                                    <th>Current stock</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Total Price</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <th scope="row" style="min-width: 40px !important;">
                                        <a href="javascript:void(0)" @click="addNewItem"
                                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </th>
                                    <td width="210">
                                        <select class="form-control m-select2 select2 select-product width-210"
                                                v-bind:data-index="index" v-model="details.product_id">
                                            <option v-for="(value,index) in product_lists" :value="value.id"
                                                    :measure-unit="value.measure_unit" :unit-price="value.selling_price"
                                                    :current-stock="value.c_qty"> {{value.product_name}}
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
                                        <input type="text" v-model="details.unit"
                                               class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.current_qty"
                                               class="form-control form-control-sm m-input" placeholder="" disabled
                                               style="color: red;font-size: 11px;">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.qty" @input="totalQtyAndPrice()"
                                               class="form-control form-control-sm m-input" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.qty']">{{
                                            errors['details.'+index+'.qty'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.unit_price" @input="totalQtyAndPrice()"
                                               class="form-control form-control-sm m-input">
                                        <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{
                                            errors['details.'+index+'.unit_price'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.total_price"
                                               class="form-control form-control-sm m-input">
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
                                    <td colspan="5" class="text-right">Total Qty</td>
                                    <td class="">
                                        <input type="text" v-model="form.transfers_total_qty"
                                               class="form-control form-control-sm m-input">
                                    </td>
                                    <td class="text-right">Total Amount</td>
                                    <td class="">
                                        <input type="text" v-model="form.transfers_total_price"
                                               class="form-control form-control-sm m-input">
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
                            <button v-if="permissions.indexOf('stock-transfer.approve') !=-1" type="submit"
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

        props: ['permissions', 'warehouse_lists'],

        data: function () {
            return {
                transfer_list: false,
                add_form: true,
                edit_form: false,

                product_lists: [],

                form: {
                    transfers_no: '',
                    transfers_date: '',
                    from_warehouse_id: '',
                    to_warehouse_id: '',
                    transfers_narration: '',
                    transfers_total_qty: '',
                    transfers_total_price: '',

                    approve: '',

                    details: [
                        {
                            product_id: '',
                            remarks: '',
                            unit: '',
                            current_qty: 0,
                            unit_price: '',
                            qty: '',
                            total_price: 0,
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {

            transactionNoGenerate() {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'transfer-no')
                    .then((response) => {
                        _this.$loading(false);
                        console.log(response.data);
                        _this.form.transfers_no = response.data;
                    });
            },

            loadProduct() {
                var _this = this;
                var wid = _this.form.from_warehouse_id;
                if (wid == '') {
                    wid = '__';
                }

                _this.$loading(true);
                axios.get(base_url + wid + '/' + 'load-transfer-product')
                    .then((response) => {
                        _this.$loading(false);
                        _this.product_lists = response.data;
                    });
            },

            addNewItem() {
                this.form.details.push({
                    product_id: '',
                    remarks: '',
                    unit: '',
                    current_qty: 0,
                    unit_price: '',
                    qty: '',
                    total_price: 0,
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
                axios.post(base_url + 'stock-transfer/store', _this.form)
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

            totalQtyAndPrice() {
                var total_qty = 0;
                var total_amount = 0;
                var total_price = 0;
                var details = this.form.details;
                var length = details.length;

                for (let i = 0; i < length; i++) {
                    total_price = Number(details[i].qty) * Number(details[i].unit_price);
                    this.form.details[i].total_price = total_price;
                    total_qty = Number(details[i].qty) + total_qty;
                    total_amount = total_price + total_amount;
                }
                this.form.transfers_total_qty = total_qty;
                this.form.transfers_total_price = total_amount;
            },

            duplicateCheck() {
                var no_index = this.form.details.length;
                if (no_index > 1) {
                    for (let i = 0; i < no_index; i++) {
                        for (let j = i; j < no_index; j++) {
                            j = j + 1;
                            if (this.form.details[i].product_id == this.form.details[j].product_id) {
                                alert("Duplicate Found");
                                this.form.details[j].product_id = '';

                                var Select2 = {
                                    init: function () {
                                        $(".select2").select2({placeholder: "Please select an option"})
                                    }
                                };
                                jQuery(document).ready(function () {
                                    Select2.init()
                                });
                            }
                        }
                    }
                }
            },

            check_warehouse() {
                var _this = this;
                var from_warehouse_id = _this.form.from_warehouse_id;
                var to_warehouse_id = _this.form.to_warehouse_id;

                if (from_warehouse_id != '' && to_warehouse_id != '') {

                    if (from_warehouse_id == to_warehouse_id) {
                        alert("Please, Change warehouse");
                        _this.form.to_warehouse_id = '';

                        setTimeout(function () {

                            var Select2 = {
                                init: function () {
                                    $(".select2").select2({placeholder: "Please select an option"})
                                }
                            };
                            jQuery(document).ready(function () {
                                Select2.init()
                            });

                        }, 500);

                    }
                }
            },
        },

        mounted() {
            var _this = this;

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if (dataIndex == 'from_warehouse_id') {
                    _this.form.from_warehouse_id = selectedType.val();
                    _this.check_warehouse();
                    _this.loadProduct();
                } else if (dataIndex == 'to_warehouse_id') {
                    _this.form.to_warehouse_id = selectedType.val();
                    _this.check_warehouse();
                }
            });

            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index'),
                    selectMeasureUnit = $('option:selected', $(e.target)).attr('measure-unit'),
                    selectPrice = $('option:selected', $(e.target)).attr('unit-price'),
                    selectStock = $('option:selected', $(e.target)).attr('current-stock');

                if (selectStock == null) {
                    selectStock = 0;
                    _this.form.details[dataIndex].product_id = '';
                    alert('Please Select Warehouse');

                } else {
                    _this.form.details[dataIndex].product_id = selectedItem.val();
                    _this.form.details[dataIndex].current_qty = selectStock;
                    _this.form.details[dataIndex].unit = selectMeasureUnit;
                    _this.form.details[dataIndex].unit_price = selectPrice;
                    //_this.duplicateCheck();
                }
            });

            var Select2 = {
                init: function () {
                    $(".select2").select2({placeholder: "Please select an option"})
                }
            };
            jQuery(document).ready(function () {
                Select2.init()
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                    //startDate: '-2d',
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.transfers_date = '';
                    } else {
                        _this.form.transfers_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created() {
            var _this = this;
            _this.loadProduct();
            _this.totalQtyAndPrice();
            _this.transactionNoGenerate();
        }
    }
</script>
