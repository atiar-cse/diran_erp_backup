<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="po_order_no" class="col-lg-2 col-form-label">Purchase Order No </label>
                    <div class="col-lg-4">
                        <input type="text" id="po_order_no" v-model="form.po_order_no"
                               class="form-control form-control-sm m-input" placeholder="Enter Purchase Order No">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('po_order_no'))">{{
                            (errors.hasOwnProperty('po_order_no')) ? errors.po_order_no[0] :'' }}
                        </div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Purchase Date </label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.po_receive_date" data-dateField="po_receive_date" readonly
                                   placeholder="Select date from picker" id="m_datepicker_2"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('po_receive_date'))">{{
                            (errors.hasOwnProperty('po_receive_date')) ? errors.po_receive_date[0] :'' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="po_supplier_id" class="col-lg-2 col-form-label">Supplier </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-supplier" id="po_supplier_id"
                                data-selectField="sindex" v-model="form.po_supplier_id">
                            <option v-for="(svalue,sindex) in supplier_lists" :value="svalue.id">
                                {{svalue.purchase_supplier_name}}
                            </option>
                            <div class="requiredField" v-if="errors['po_supplier_id']">{{ errors['po_supplier_id'][0]
                                }}
                            </div>
                        </select>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Purchase Order Docs</label>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <input type="file" class="custom-file-input m-input" id="customFile" autocomplete="off"
                                   @change="onFileChange">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('po_order_docs'))">{{
                            (errors.hasOwnProperty('po_order_docs')) ? errors.po_order_docs[0] :'' }}
                        </div>

                    </div>
                </div>


                <div class="form-group m-form__group row">
                    <label for="po_order_no" class="col-lg-2 col-form-label">Requisition No </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2 select-requisition" data-selectId="rindex"
                                v-model="form.po_requisition_id" @change="loadDetails">
                            <option v-for="(rvalue,rindex) in requisition_lists" :value="rvalue.id">
                                {{rvalue.purchase_requisition_no}}
                            </option>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('po_requisition_id'))">{{
                                (errors.hasOwnProperty('po_requisition_id')) ? errors.po_requisition_id[0] :'' }}
                            </div>
                        </select>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.po_narration" id="narration"
                                  rows="2"></textarea>
                    </div>
                </div>
                <br><br>
                <!--begin::Portlet-->

                <div class="form-group m-form__group row">
                    <div class="m-section__content col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse">
                                <tr>
                                    <th></th>
                                    <th>Product</th>
                                    <th>Remarks</th>
                                    <th>Unit</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total Price</th>
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
                                    <td>
                                        <select class="form-control m-select2 select2  select-product"
                                                v-bind:data-index="index" v-model="details.pod_product_id">
                                            <option v-for="(value,index) in product_lists" :value="value.id">
                                                {{value.product_name}}
                                            </option>
                                            <div class="requiredField"
                                                 v-if="errors['details.'+index+'.pod_product_id']">{{
                                                errors['details.'+index+'pod_product_id'][0] }}
                                            </div>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.pod_remarks"
                                               class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.pod_unit"
                                               class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.pod_unit_price" @input="totalQtyAndPrice()"
                                               class="form-control form-control-sm m-input">
                                        <div class="requiredField" v-if="errors['details.'+index+'.pod_unit_price']">{{
                                            errors['details.'+index+'.pod_unit_price'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.pod_qty" @input="totalQtyAndPrice()"
                                               class="form-control form-control-sm m-input" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.pdo_qty']">{{
                                            errors['details.'+index+'.pod_qty'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.pod_total_price"
                                               class="form-control form-control-sm m-input">
                                        <div class="requiredField" v-if="errors['details.'+index+'.pod_total_price']">{{
                                            errors['details.'+index+'.pod_total_price'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <a @click="removeItem(index,details.id)"
                                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Total</td>
                                    <td class="">
                                        <input type="text" v-model="form.po_total_qty"
                                               class="form-control form-control-sm m-input">
                                    </td>
                                    <td class="">
                                        <input type="text" v-model="form.po_total_price"
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
                            <button type="reset" class="btn btn-sm btn-secondary">Cancel</button>
                            <!--<button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save</span></button>-->
                            <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i> <span>Update and Go List</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import {EventBus} from '../../../vue-assets';

    export default {

        props: ['editId', 'product_lists', 'supplier_lists', 'requisition_lists'],

        data: function () {
            return {
                product_list: false,
                add_form: false,
                edit_form: true,

                form: {
                    po_order_no: '',
                    po_receive_date: '',
                    po_narration: '',
                    po_supplier_id: '',
                    po_requisition_id: '',
                    po_order_docs: '',
                    po_total_qty: '',

                    po_total_price: '',
                    deleteID: [],
                    details: [
                        {
                            pod_product_id: '',
                            pod_remarks: '',
                            pod_unit: '',
                            pod_unit_price: 0,
                            pod_qty: 0,
                            pod_total_price: 0,
                        }
                    ],

                },
                errors: {},
            };
        },

        methods: {

            addNewItem() {
                this.form.details.push({
                    pod_product_id: '',
                    pod_remarks: '',
                    pod_unit: '',
                    pod_unit_price: 0,
                    pod_qty: 0,
                    pod_total_price: 0,
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
                this.totalQtyAndPrice();
            },

            edit(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'po-receive/' + id + '/edit')
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
                axios.put(base_url + 'po-receive/' + id + '/update', _this.form)
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

            loadDetails() {
                var _this = this;
                var id = this.form.po_requisition_id;

                _this.$loading(true);
                axios.get(base_url + "po-receive/" + id + "/po-requisition-list")
                    .then((response) => {
                        _this.$loading(false);
                        if (response.data.length > 0) {
                            _this.form.details = response.data;
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
                var total_qty = 0;
                var total_amount = 0;
                var total_price = 0;
                var details = this.form.details;
                var length = details.length;

                for (let i = 0; i < length; i++) {
                    total_price = Number(details[i].pod_qty) * Number(details[i].pod_unit_price);
                    this.form.details[i].pod_total_price = total_price;
                    total_qty = Number(details[i].pod_qty) + total_qty;
                    total_amount = total_price + total_amount;
                }
                this.form.po_total_qty = total_qty;
                this.form.po_total_price = total_amount;
            },

            duplicateCheck() {
                var no_index = this.form.details.length;
                //alert(no_index);
                for (let i = 0; i < no_index; i++) {
                    for (let j = i; j < no_index; j++) {

                        if (this.form.details[i].pod_product_id == this.form.details[j].pod_product_id) {
                            alert("Duplicate Found");
                            console.log("Duplicate Found");
                        }

                    }
                }
            },

            onFileChange(e) {
                alert(e.target.files || e.dataTransfer.files);
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0]);
            },

            createImage(file) {
                let reader = new FileReader();
                var _this = this;

                reader.onload = (e) => {
                    this.form.po_order_docs = e.target.result;
                };
                reader.readAsDataURL(file);
            },
        },

        mounted() {
            var _this = this;

            $('#editComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.form.details[dataIndex].pod_product_id = selectedItem.val();
                //_this.duplicateCheck();
            });

            $('#editComponent').on('change', '.select-supplier', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if (dataIndex == 'sindex') {
                    _this.form.po_supplier_id = selectedType.val();
                }
            });

            $('#editComponent').on('change', '.select-requisition', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectId');
                if (dataIndex == 'rindex') {
                    _this.form.po_requisition_id = selectedType.val();
                    _this.loadDetails();
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
                        _this.form.po_receive_date = '';
                    } else {
                        _this.form.po_receive_date = moment(ev.date).format('DD-MM-YYYY');
                    }
                });
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

        created() {
            var _this = this;
            _this.edit(_this.editId);
            _this.totalQtyAndPrice();
        }
    }
</script>
