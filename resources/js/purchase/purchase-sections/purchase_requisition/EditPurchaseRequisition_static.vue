<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="product_name" class="col-lg-2 col-form-label">Product Name </label>
                    <div class="col-lg-4">
                        <input type="text" id="product_name" v-model="form.product_name"
                               class="form-control form-control-sm m-input" placeholder="Enter product name">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('product_name'))">{{
                            (errors.hasOwnProperty('product_name')) ? errors.product_name[0] :'' }}
                        </div>
                    </div>
                    <label for="product_code" class="col-lg-2 col-form-label">Product Code </label>
                    <div class="col-lg-4">
                        <input type="text" id="product_code" v-model="form.product_code"
                               class="form-control form-control-sm m-input" placeholder="Enter product code">
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="category">Category </label>
                    <div class="col-lg-4">
                        <select class="form-control" id="category" v-model="form.category_id">
                            <option>----Select Category----</option>
                            <option value="1">New</option>
                            <option value="2">Old</option>
                        </select>
                    </div>
                    <label class="col-lg-2 col-form-label" for="brand">Brand </label>
                    <div class="col-lg-4">
                        <select class="form-control" id="brand" v-model="form.brand_id">
                            <option>----Select Brand----</option>
                            <option value="3">Mustard</option>
                            <option value="4">Ketchup</option>
                        </select>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="measure_unit">Measure Unit </label>
                    <div class="col-lg-4">
                        <select class="form-control" id="measure_unit" v-model="form.measure_unit_id">
                            <option>----Select Measure----</option>
                            <option value="1">KG</option>
                            <option value="2">PCS</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('product_name'))">{{
                            (errors.hasOwnProperty('product_name')) ? errors.product_name[0] :'' }}
                        </div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="country_of_origin">Country of Origin </label>
                    <div class="col-lg-4">
                        <select class="form-control" id="country_of_origin" v-model="form.country_id">
                            <option>----Select Country----</option>
                            <option value="5">Bangladesh</option>
                            <option value="6">India</option>
                        </select>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="credit_limit">Credit Limit </label>
                    <div class="col-lg-4">
                        <input type="text" id="credit_limit" v-model="form.credit_limit"
                               class="form-control form-control-sm m-input" placeholder="Enter credit limit">
                    </div>
                    <label class="col-lg-2 col-form-label" for="weight">Complete Weight </label>
                    <div class="col-lg-4">
                        <input type="text" id="weight" v-model="form.complete_product_weight"
                               class="form-control form-control-sm m-input" placeholder="Enter weight">
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="buying_price">Buying Price </label>
                    <div class="col-lg-4">
                        <input type="text" id="buying_price" v-model="form.buying_price"
                               class="form-control form-control-sm m-input" placeholder="Enter buying price">
                    </div>
                    <label class="col-lg-2 col-form-label" for="selling_price">Selling Price </label>
                    <div class="col-lg-4">
                        <input type="text" id="selling_price" v-model="form.selling_price"
                               class="form-control form-control-sm m-input" placeholder="Enter selling price">
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="forming_weight">Forming Weight </label>
                    <div class="col-lg-4">
                        <input type="text" id="forming_weight" v-model="form.forming_weight"
                               class="form-control form-control-sm m-input" placeholder="Enter forming weight">
                    </div>
                    <label class="col-lg-2 col-form-label" for="shapping_weight">Shapping Weight </label>
                    <div class="col-lg-4">
                        <input type="text" id="shapping_weight" v-model="form.shapping_weight"
                               class="form-control form-control-sm m-input" placeholder="Enter shapping weight">
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="dryer_weight">Dryer Weight </label>
                    <div class="col-lg-4">
                        <input type="text" id="dryer_weight" v-model="form.dryer_weight"
                               class="form-control form-control-sm m-input" placeholder="Enter dryer weight">
                    </div>
                    <label class="col-lg-2 col-form-label" for="glaze_weight">Glaze Weight </label>
                    <div class="col-lg-4">
                        <input type="text" id="glaze_weight" v-model="form.glaze_weight"
                               class="form-control form-control-sm m-input" placeholder="Enter glaze weight">
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="firirng_weight">Kiln Weight </label>
                    <div class="col-lg-4">
                        <input type="text" id="firirng_weight" v-model="form.kiln_weight"
                               class="form-control form-control-sm m-input" placeholder="Enter kiln weight">
                    </div>
                    <label class="col-lg-2 col-form-label" for="description">Description </label>
                    <div class="col-lg-4">
                        <textarea class="form-control form-control-sm" id="description"
                                  v-model="form.product_description" placeholder="" rows="2"></textarea>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-4">
                        <div class="m-checkbox-list">
                            <label class="m-checkbox">
                                <input type="checkbox" v-model="form.is_raw_material_status"> is row material ?
                                <span></span>
                            </label>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('is_raw_material_status'))">{{
                            (errors.hasOwnProperty('is_raw_material_status')) ? errors.is_raw_material_status[0] :'' }}
                        </div>
                    </div>
                    <label class="col-2 col-form-label">Active Status ?</label>
                    <div class="col-lg-4">
                        <span class="m-switch m-switch--icon">
                            <label>
                                <input type="checkbox" checked="checked" v-model="form.status">
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
            </div>
            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="reset" class="btn btn-sm btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i> <span>Update</span>
                            </button>
                            <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i> <span>Update and Go List</span>
                            </button>

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

        props: ['editId', 'product_lists', 'pr_req_no_generate', 'enum_val'],

        data: function () {
            return {
                product_list: false,
                add_form: false,
                edit_form: true,

                form: {
                    purchase_requisition_no: this.pr_req_no_generate,
                    purchase_requisition_date: '',
                    purchase_requisition_narration: '',
                    purchase_requisition_total_qty: '',
                    purchase_requisition_total_price: '',
                    details: [
                        {
                            pr_product_id: '',
                            product_type: this.enum_val,
                            pr_remarks: '',
                            pr_qty: 0,
                            pr_unit: '',
                            pr_unit_price: 0,
                            pr_total_price: 0,
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {

            addNewItem() {
                this.form.details.push({
                    pr_product_id: '',
                    product_type: this.enum_val,
                    pr_remarks: '',
                    pr_qty: 0,
                    pr_unit: '',
                    pr_unit_price: 0,
                    pr_total_price: 0,
                });
                var Select2 = {
                    init: function () {
                        $(".select2").select2({
                                placeholder: "Please select an option"
                            }
                        )
                    }
                };
                jQuery(document).ready(function () {
                        Select2.init()
                    }
                );
            },

            removeItem(index, deletedId) {
                var _this = this;
                if (_this.form.details.length > 1) {
                    _this.form.details.splice(index, 1);
                    if (deletedId) {
                        _this.form.deleteID.push(deletedId);
                    }
                }
            },

            edit(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'pr-requisition/' + id + '/edit')
                    .then((response) => {
                        _this.$loading(false);
                        _this.form = response.data.data;
                        setTimeout(function () {
                            var Select2 = {
                                init: function () {
                                    $(".select2").select2({
                                            placeholder: "Please select an option"
                                        }
                                    )
                                }
                            };
                            jQuery(document).ready(function () {
                                    Select2.init()
                                }
                            );
                        }, 100);
                    });
            },

            update(id) {
                var _this = this;
                _this.$loading(true);
                axios.put(base_url + 'pr-requisition/' + id + '/update', _this.form)
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
                    total_price = Number(details[i].pr_qty) * Number(details[i].pr_unit_price);
                    this.form.details[i].pr_total_price = total_price;
                    total_qty = Number(details[i].pr_qty) + total_qty;
                    total_amount = total_price + total_amount;
                }
                this.form.purchase_requisition_total_qty = total_qty;
                this.form.purchase_requisition_total_price = total_amount;
            },
        },

        mounted() {
            var _this = this;

            $('#editComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index'),
                    selectMeasureUnit = $('option:selected', $(e.target)).attr('measure-unit');


                _this.form.details[dataIndex].pr_product_id = selectedItem.val();
                _this.form.details[dataIndex].pr_unit = selectMeasureUnit;
            });

            $('#editComponent').on('change', '.select-product_type', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                //alert(selectedType.val());
                _this.form.details[dataIndex].product_type = selectedType.val();
            });

            var BootstrapDatepicker = function () {
                var t;
                t = mUtil.isRTL() ? {
                        leftArrow: '<i class="la la-angle-right"></i>', rightArrow: '<i class="la la-angle-left"></i>'
                    }
                    : {
                        leftArrow: '<i class="la la-angle-left"></i>', rightArrow: '<i class="la la-angle-right"></i>'
                    }
                ;
                return {
                    init: function () {
                        $("#m_datepicker_2, .datepicker").datepicker({

                            onSelect: function (date, e) {
                                var dateField = $(this).attr('data-dateField');
                                if (dateField == 'requisition_date') {
                                    _this.form.requisition_date = date;
                                }
                            },
                            todayHighlight: !0,
                            orientation: "bottom left",
                            templates: t,
                            autoclose: true,
                            format: 'dd/mm/yyyy'
                        });
                    }
                }
            }
            ();
            jQuery(document).ready(function () {
                    BootstrapDatepicker.init()
                }
            );

            var Select2 = {
                init: function () {
                    $(".select2").select2({
                            placeholder: "Please select an option"
                        }
                    )
                }
            };

            jQuery(document).ready(function () {
                    Select2.init()
                }
            );
        },

        created() {
            var _this = this;
            _this.edit(_this.editId);
            _this.totalQtyAndPrice();
        }
    }
</script>
