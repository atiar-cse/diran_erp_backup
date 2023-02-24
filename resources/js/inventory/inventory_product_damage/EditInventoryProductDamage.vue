<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <label for="inventory_product_damages_no" class="col-lg-2 col-form-label">Product Damage No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="inventory_product_damages_no" v-model="form.inventory_product_damages_no"
                               class="form-control form-control-sm m-input" placeholder="Enter Product Damage No  ">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('inventory_product_damages_no'))">{{
                            (errors.hasOwnProperty('inventory_product_damages_no')) ?
                            errors.inventory_product_damages_no[0] :'' }}
                        </div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label"> Date </label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.inventory_product_damages_date"
                                   data-dateField="inventory_product_damages_date" readonly
                                   placeholder="Select date from picker" id="m_datepicker_2"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('inventory_product_damages_date'))">{{
                            (errors.hasOwnProperty('inventory_product_damages_date')) ?
                            errors.inventory_product_damages_date[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="inventory_product_damages_warehouse_id"
                           class="col-lg-2 col-form-label">Warehouse</label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2"
                                data-selectField="inventory_product_damages_warehouse_id"
                                id="inventory_product_damages_warehouse_id"
                                v-model="form.inventory_product_damages_warehouse_id">
                            <option v-for="(wvalue,windex) in warehouse_lists" :value="wvalue.id">
                                {{wvalue.purchase_ware_houses_name}}
                            </option>
                            <div class="requiredField" v-if="errors['inventory_product_damages_warehouse_id']">{{
                                errors['inventory_product_damages_warehouse_id'][0] }}
                            </div>
                        </select>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.inventory_product_damages_narration"
                                  id="narration" rows="2"></textarea>
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
                                    <th class="width-210">Product Name</th>
                                    <th>Details</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
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
                                        <!--<select class="form-control m-select2 select2 select-product"  v-bind:data-index="index" v-model="details.inventory_product_damage_details_product_id" v-on:change="duplicateCheck">-->
                                        <select class="form-control m-select2 select2 select-product width-210"
                                                v-bind:data-index="index"
                                                v-model="details.inventory_product_damage_details_product_id">
                                            <option v-for="(value,index) in product_lists" :value="value.id"
                                                    :measure-unit="value.measure_unit"> {{value.product_name}}
                                            </option>
                                            <div class="requiredField"
                                                 v-if="errors['details.'+index+'.inventory_product_damage_details_product_id']">
                                                {{
                                                errors['details.'+index+'.inventory_product_damage_details_product_id'][0]
                                                }}
                                            </div>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.inventory_product_damage_details_remarks"
                                               class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.inventory_product_damage_details_unit"
                                               class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.inventory_product_damage_details_qty"
                                               class="form-control form-control-sm m-input" placeholder="">
                                        <div class="requiredField"
                                             v-if="errors['details.'+index+'.inventory_product_damage_details_qty']">{{
                                            errors['details.'+index+'.inventory_product_damage_details_qty'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <a @click="removeItem(index,details.id)"
                                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Delete"><i class="la la-trash-o"></i></a>
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
                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="update(form.id,0)"><i
                                class="la la-check"></i> <span>Update</span></button>
                            <button v-if="permissions.indexOf('product-damage.approve') !=-1" type="submit"
                                    class="btn btn-sm btn-success" @click.prevent="update(form.id,1)"><i
                                class="la la-check"></i> <span>Approve</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import {EventBus} from '../../vue-assets';

    export default {

        props: ['permissions', 'editId', 'product_lists', 'warehouse_lists'],

        data: function () {
            return {
                product_list: false,
                add_form: false,
                edit_form: true,

                form: {
                    inventory_product_damages_no: '',
                    inventory_product_damages_date: '',
                    inventory_product_damages_warehouse_id: '',
                    inventory_product_damages_narration: '',
                    approve: '',
                    deleteID: [],
                    details: [
                        {
                            inventory_product_damage_details_product_id: '',
                            inventory_product_damage_details_remarks: '',
                            inventory_product_damage_details_unit: '',
                            inventory_product_damage_details_qty: '',
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {

            addNewItem() {
                this.form.details.push({
                    inventory_product_damage_details_product_id: '',
                    inventory_product_damage_details_remarks: '',
                    inventory_product_damage_details_unit: '',
                    inventory_product_damage_details_qty: '',
                });
                var Select2 = {
                    init: function () {
                        $(".select2").select2({placeholder: "Please select an option"})
                    }
                };
                jQuery(document).ready(function () {
                    Select2.init()
                });
                ;
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
                }
            },

            edit(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'product-damage/' + id + '/edit')
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
                                Select2.init()
                            });
                        }, 100);
                    });
            },

            update(id, app) {
                var _this = this;
                _this.form.approve = app;
                _this.$loading(true);
                axios.put(base_url + 'product-damage/' + id + '/update', _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        _this.showMassage(response.data);
                        EventBus.$emit('data-changed');
                    })
                    .catch(error => {
                        _this.$loading(false);
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

            duplicateCheck() {
                var no_index = this.form.details.length;
                //alert(no_index);
                if (no_index > 1) {
                    for (let i = 0; i < no_index; i++) {
                        for (let j = i + 1; j < no_index; j++) {
                            if (this.form.details[i].inventory_product_damage_details_product_id == this.form.details[j].inventory_product_damage_details_product_id) {
                                alert("Duplicate Found");
                                this.form.details[j].inventory_product_damage_details_product_id = '';
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

            $('#editComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index'),
                    selectMeasureUnit = $('option:selected', $(e.target)).attr('measure-unit');
                _this.form.details[dataIndex].inventory_product_damage_details_product_id = selectedItem.val();
                _this.form.details[dataIndex].inventory_product_damage_details_unit = selectMeasureUnit;
            });

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if (dataIndex == 'inventory_product_damages_warehouse_id') {
                    _this.form.inventory_product_damages_warehouse_id = selectedType.val();
                }
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                    startDate: '-2d',
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.inventory_product_damages_date = '';
                    } else {
                        _this.form.inventory_product_damages_date = moment(ev.date).format('DD/MM/YYYY');
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
