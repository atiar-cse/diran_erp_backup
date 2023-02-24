<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="forming_no" class="col-lg-2 col-form-label">Kiln No <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="forming_no" v-model="form.kiln_no"
                               class="form-control form-control-sm m-input" placeholder="Enter Kiln No" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('kiln_no'))">{{
                            (errors.hasOwnProperty('kiln_no')) ? errors.kiln_no[0] :'' }}
                        </div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Kiln Date <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.kiln_date" data-dateField="kiln_date" disabled
                                   placeholder="Select date from picker" id="m_datepicker_2" autocomplete="off"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('kiln_date'))">{{
                            (errors.hasOwnProperty('kiln_date')) ? errors.kiln_date[0] :'' }}
                        </div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.narration" id="narration"
                                  rows="2"></textarea>
                    </div>
                    <label class="col-lg-2 col-form-label" for="pre_mth_ovr_price">Previous month overhead price (Kg)</label>
                    <div class="col-lg-4">
                        <input type="text" id="pre_mth_ovr_price" v-model="pre_mth_ovr_price" class="form-control form-control-sm m-input" placeholder="ex: 1 kg">
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
                                    <th>Weight (Per Unit) <span class="requiredField">*</span></th>
                                    <th>Opening / Current Qty <span class="requiredField">*</span></th>
                                    <th>Rec.F Glaze Qty <span class="requiredField">*</span></th>
                                    <th>Trans to HV Testing Qty <span class="requiredField">*</span></th>
                                    <th>Total Weight <span class="requiredField">*</span></th>
                                    <th>Reject Qty <span class="requiredField">*</span></th>
                                    <th>Reject Weight <span class="requiredField">*</span></th>
                                    <th class="width-160">Balance <span class="requiredField">*</span></th>
                                    <th>Unit Price <span class="requiredField">*</span></th>
                                    <th>Overhead Price <span class="requiredField">*</span></th>
                                    <th>Total Price <span class="requiredField">*</span></th>
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
                                                style="width:100%" disabled>
                                            <option v-for="(value,index) in product_lists"
                                                    :value="value.id"
                                                    :data-roll-weight="value.forming_weight"
                                                    :kiln-opening-qty="value.kiln_current_qty"
                                                    :kiln-receive-qty="value.kiln_receive_qty"
                                            > {{value.product_name}}
                                            </option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{
                                            errors['details.'+index+'.product_id'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm m-input width-80"
                                               v-model="details.remarks" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.remarks']">{{
                                            errors['details.'+index+'.remarks'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-80"
                                               v-model="details.kiln_weight" @input="totalTransQtyWeightCalc()"
                                               placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.kiln_weight']">{{
                                            errors['details.'+index+'.kiln_weight'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-80"
                                               v-model="details.current_qty" placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.current_qty']">{{
                                            errors['details.'+index+'.current_qty'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-80"
                                               v-model="details.receive_qty" @input="totalTransQtyWeightCalc()"
                                               placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.receive_qty']">{{
                                            errors['details.'+index+'.receive_qty'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-80"
                                               v-model="details.trans_to_hv_qty" @input="totalTransQtyWeightCalc()"
                                               placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.trans_to_hv_qty']">{{
                                            errors['details.'+index+'.trans_to_hv_qty'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-80"
                                               v-model="details.trans_to_hv_weight" input="totalTransQtyWeightCalc()"
                                               placeholder="" readonly>
                                        <div class="requiredField"
                                             v-if="errors['details.'+index+'.trans_to_hv_weight']">{{
                                            errors['details.'+index+'.trans_to_hv_weight'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-80"
                                               v-model="details.reject_qty" input="totalTransQtyWeightCalc()"
                                               placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.reject_qty']">{{
                                            errors['details.'+index+'.reject_qty'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-80"
                                               v-model="details.reject_weight" input="totalTransQtyWeightCalc()"
                                               placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.reject_weight']">{{
                                            errors['details.'+index+'.reject_weight'][0] }}
                                        </div>
                                    </td>
                                    <td class="width-160">
                                        <input type="number" step="any"
                                               class="form-control form-control-sm m-input width-160"
                                               v-model="details.remain_qty" @input="totalTransQtyWeightCalc()" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.remain_qty']">{{
                                            errors['details.'+index+'.remain_qty'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.unit_price"
                                               @input="totalTransQtyWeightCalc()"
                                               class="form-control form-control-sm m-input number">
                                        <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{
                                            errors['details.'+index+'.unit_price'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.overhead_price" class="form-control form-control-sm m-input number" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.overhead_price']">{{ errors['details.'+index+'.overhead_price'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.total_price"
                                               class="form-control form-control-sm m-input number" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.total_price']">{{
                                            errors['details.'+index+'.total_price'][0] }}
                                        </div>
                                    </td>
                                    <td scope="row" class="width-100 text-center">
                                        <a @click="removeItem(index,details.id)"
                                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Total</td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_receive_qty"
                                               class="form-control form-control-sm m-input" placeholder="Total Rec. Qty"
                                               readonly>
                                    </td>
                                    <td>
                                        <input type="text" v-model="form.total_trans_to_hv_qty"
                                               class="form-control form-control-sm m-input"
                                               placeholder="Total Testing Qty" readonly>
                                    </td>
                                    <td>
                                        <input type="text" v-model="form.total_trans_weight"
                                               class="form-control form-control-sm m-input"
                                               placeholder="Total Testing Weight" readonly>
                                    </td>
                                    <td>
                                        <input type="text" v-model="form.total_reject_qty"
                                               class="form-control form-control-sm m-input" placeholder="Waste Qty"
                                               readonly>
                                    </td>
                                    <td>
                                        <input type="text" v-model="form.total_reject_weight"
                                               class="form-control form-control-sm m-input" placeholder="Waste Weight"
                                               readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_remain_qty"
                                               class="form-control form-control-sm m-input" placeholder="Balance"
                                               readonly>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <input type="text" v-model="form.total_amount"
                                               class="form-control form-control-sm m-input" placeholder="Total Amount"
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
                            <button type="submit" class="btn btn-sm btn-danger" @click.prevent="update(form.id,1)"><i
                                class="la la-check"></i> <span>Save Correction</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
</template>

<script>
    import moment from 'moment';

    export default {

        props: ['permissions', 'product_lists', 'editId'],

        data: function () {
            return {

                data_list: false,
                add_form: false,
                edit_form: true,
                view_form: false,
                pre_mth_ovr_price:'2.5',

                form: {
                    kiln_no: '',
                    kiln_date: '',
                    narration: '',
                    total_trans_to_hv_qty: '',
                    total_trans_weight: '',
                    total_receive_qty: '',
                    total_remain_qty: '',
                    total_reject_qty: '',
                    total_reject_weight: '',
                    total_amount: '',
                    approve_status: '',
                    status: '',
                    details: [
                        {
                            product_id: '',
                            remarks: '',
                            kiln_weight: '',
                            current_qty: '',
                            receive_qty: '',
                            trans_to_hv_qty: '',
                            trans_to_hv_weight: '',
                            remain_qty: '',
                            reject_qty: '',
                            reject_weight: '',
                            unit_price: '',
                            overhead_price: '',
                            total_price: '',
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {

            addNewItem() {
                this.form.details.push({
                    product_id: '',
                    remarks: '',
                    kiln_weight: '',
                    current_qty: '',
                    receive_qty: '',
                    remain_qty: '',
                    trans_to_hv_qty: '',
                    trans_to_hv_weight: '',
                    reject_qty: '',
                    reject_weight: '',
                    unit_price: '',
                    overhead_price: '',
                    total_price: '',
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
                    _this.totalTransQtyWeightCalc();
                }
            },

            edit(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'kiln-section/' + id + '/edit')
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

            update(id, app) {
                var _this = this;
                _this.form.approve_status = app;

                _this.$loading(true);
                axios.put(base_url + 'kiln-section/' + id + '?isSuperAdmin=correction', _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        _this.showMassage(response.data);
                        //EventBus.$emit('data-changed');
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

            totalTransQtyWeightCalc() {
                var _this = this;
                var total_trans_qty = 0;
                var total_trans_weight = 0;

                var total_remain_qty = 0;
                var total_receive_qty = 0;

                var total_waste_qty = 0;
                var total_waste_weight = 0;
                var total_weight = 0;

                var remain_qty = 0;
                var receive_qty = 0;

                var waste_qty = 0;
                var waste_weight = 0;
                var overhead_price =0;
                var totalPrice = 0;
                var totalAmount = 0;

                var details = this.form.details;
                var length = details.length;

                for (let i = 0; i < length; i++) {
                    total_trans_weight = Number(details[i].kiln_weight) * Number(details[i].trans_to_hv_qty);
                    _this.form.details[i].trans_to_hv_weight = total_trans_weight;

                    receive_qty = Number(details[i].receive_qty);
                    waste_qty = Number(details[i].reject_qty);

                    remain_qty = Number(details[i].current_qty) + receive_qty - Number(details[i].trans_to_hv_qty) - waste_qty;
                    _this.form.details[i].remain_qty = remain_qty;

                    waste_weight = Number(details[i].kiln_weight) * Number(details[i].reject_qty);
                    _this.form.details[i].reject_weight = waste_weight;

                    // Over head calculation 
                    overhead_price = Number(details[i].kiln_weight) * Number(details[i].trans_to_hv_qty)*_this.pre_mth_ovr_price; 
                    _this.form.details[i].overhead_price = overhead_price;

                    total_trans_qty = Number(details[i].trans_to_hv_qty) + total_trans_qty;
                    total_weight = total_trans_weight + total_weight;

                    total_receive_qty = receive_qty + total_receive_qty;
                    total_remain_qty = remain_qty + total_remain_qty;

                    total_waste_qty = waste_qty + total_waste_qty;
                    total_waste_weight = waste_weight + total_waste_weight;

                    totalPrice = Number(details[i].trans_to_hv_qty) * Number(details[i].unit_price) + overhead_price;
                    _this.form.details[i].total_price = totalPrice;

                    totalAmount = Number(totalPrice) + totalAmount;
                }

                _this.form.total_trans_to_hv_qty = total_trans_qty;
                _this.form.total_trans_weight = total_weight;

                _this.form.total_receive_qty = total_receive_qty;
                _this.form.total_remain_qty = total_remain_qty;

                _this.form.total_reject_qty = total_waste_qty;
                _this.form.total_reject_weight = total_waste_weight;
                _this.form.total_amount = totalAmount;
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
        },

        mounted() {
            var _this = this;

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if (selectField == 'forming_type') {
                    _this.form.forming_type = selectedItem.val();
                }
            });

            $('#editComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                var selectKilnWeight = $('option:selected', $(e.target)).attr('data-kiln-weight');
                var openingQty = $('option:selected', $(e.target)).attr('kiln-opening-qty');
                var receiveQty = $('option:selected', $(e.target)).attr('kiln-receive-qty');

                _this.form.details[dataIndex].kiln_weight = selectKilnWeight ? selectKilnWeight : 0;
                _this.form.details[dataIndex].current_qty = openingQty ? openingQty : 0;
                _this.form.details[dataIndex].receive_qty = receiveQty ? receiveQty : 0;

                _this.totalTransQtyWeightCalc();

                var isDuplicated = _this.duplicateCheck(selectedItem.val());
                if (isDuplicated) {
                    _this.form.details[dataIndex].product_id = '';
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
                } else {
                    _this.form.details[dataIndex].product_id = selectedItem.val();
                }
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

            $(document).on("focus", ".datepicker", function () {

                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.kiln_date = '';

                    } else {
                        _this.form.kiln_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created() {
            var _this = this;
            _this.edit(_this.editId);
            _this.totalTransQtyWeightCalc();
        }
    }
</script>
