<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="inventory_stock_adjusts_no" class="col-lg-2 col-form-label">Stock Adjust No <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text"  id="inventory_stock_adjusts_no"  v-model="form.inventory_stock_adjusts_no" class="form-control form-control-sm m-input" placeholder="Enter Invoice NO ">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('inventory_stock_adjusts_no'))">{{ (errors.hasOwnProperty('inventory_stock_adjusts_no')) ? errors.inventory_stock_adjusts_no[0] :'' }}</div>
                    </div>

                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Return Date </label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.inventory_stock_adjusts_date" data-dateField="inventory_stock_adjusts_date" readonly placeholder="Select date from picker" id="m_datepicker_2" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('inventory_stock_adjusts_date'))">{{ (errors.hasOwnProperty('inventory_stock_adjusts_date')) ? errors.inventory_stock_adjusts_date[0] :'' }}</div>
                    </div>

                </div>

                <div class="form-group m-form__group row">
                    <label for="inventory_stock_adjusts_warehouse_id" class="col-lg-2 col-form-label">Warehouse</label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="inventory_stock_adjusts_warehouse_id" id="inventory_stock_adjusts_warehouse_id" v-model="form.inventory_stock_adjusts_warehouse_id" >
                            <option v-for="(wvalue,windex) in warehouse_lists" :value="wvalue.id" > {{wvalue.purchase_ware_houses_name}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('inventory_stock_adjusts_warehouse_id'))">{{ (errors.hasOwnProperty('inventory_stock_adjusts_warehouse_id')) ? errors.inventory_stock_adjusts_warehouse_id[0] :'' }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.inventory_stock_adjusts_narration"  id="narration" rows="2"></textarea>
                    </div>
                </div>

                <br><br>
                <!--begin::Portlet-->

                <div class="form-group m-form__group row add-manage-section">
                    <div class="m-section__content col-lg-12">
                        <div class ="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse" >
                                <tr>
                                    <th></th>
                                    <th class="width-210">Product Name</th>
                                    <th>Remarks</th>
                                    <th class="width-210">Adjust rule</th>
                                    <th>Unit</th>
                                    <th>Qty</th>
                                    <!--<th>Price</th>
                                    <th class="width-160">Total Price</th>-->
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <th scope="row">
                                        <a href="javascript:void(0)"  @click="addNewItem" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </th>
                                    <td class="width-210">
                                        <!--<select class="form-control m-select2 select2 select-product"  v-bind:data-index="index" v-model="details.inventory_stock_adjust_details_product_id" v-on:change="duplicateCheck">-->
                                        <select class="form-control m-select2 select2 select-product width-210"  v-bind:data-index="index" v-model="details.inventory_stock_adjust_details_product_id"  >
                                            <option v-for="(value,index) in product_lists" :value="value.id" :measure-unit="value.measure_unit"> {{value.product_name}}</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.inventory_stock_adjust_details_product_id']">{{ errors['details.'+index+'.inventory_stock_adjust_details_product_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.inventory_stock_adjust_details_remarks" class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td class="width-210">
                                        <select class="form-control m-select2 select2 select-adjust-rule width-210" v-bind:data-index="index" v-model="details.inventory_stock_adjust_rule">
                                            <option v-for="(value,index) in enum_val" :value="value"> {{value}}</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.inventory_stock_adjust_rule']">{{ errors['details.'+index+'.inventory_stock_adjust_rule'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.inventory_stock_adjust_details_unit" class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.inventory_stock_adjust_details_qty" class="form-control form-control-sm m-input" placeholder=""><!--@input="totalQtyAndPrice()" -->
                                        <div class="requiredField" v-if="errors['details.'+index+'.inventory_stock_adjust_details_qty']">{{ errors['details.'+index+'.inventory_stock_adjust_details_qty'][0] }}</div>
                                    </td>
                                    <!--<td>
                                        <input type="text" v-model="details.inventory_stock_adjust_details_unit_price" @input="totalQtyAndPrice()" class="form-control form-control-sm m-input">
                                        <div class="requiredField" v-if="errors['details.'+index+'.inventory_stock_adjust_details_unit_price']">{{ errors['details.'+index+'.inventory_stock_adjust_details_unit_price'][0] }}</div>
                                    </td>
                                    <td style="width: 160px;">
                                        <input style="width: 160px;" type="text" v-model="details.inventory_stock_adjust_details_total_price" class="form-control form-control-sm m-input">
                                        <div class="requiredField" v-if="errors['details.'+index+'.inventory_stock_adjust_details_total_price']">{{ errors['details.'+index+'.inventory_stock_adjust_details_total_price'][0] }}</div>
                                    </td>
-->
                                    <td>
                                        <a  @click="removeItem(index)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <!--<tr>
                                    <td colspan="3" class="text-right">Total Qty</td>
                                    <td class="">
                                        <input type="text" v-model="form.inventory_stock_adjusts_total_qty" class="form-control form-control-sm m-input">
                                    </td>
                                    <td class="text-right">Total Amount</td>
                                    <td  class="">
                                        <input type="text" v-model="form.inventory_stock_adjusts_total_price" class="form-control form-control-sm m-input">
                                    </td>
                                    <td></td>
                                </tr>-->
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
                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="store(0)"><i class="la la-check"></i> <span>Save</span></button>
                            <button v-if="permissions.indexOf('pr-requisition.approve') !=-1" type="submit" class="btn btn-sm btn-success"  @click.prevent="store(1)"><i class="la la-check"></i> <span>Approve</span></button>
                            <!--<button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save</span></button>-->
                            <!--<button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save and Go List</span></button>-->
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
</template>

<script>
    import { EventBus } from '../../vue-assets';
    export default {

        props:['inventory_stock_adjust_no','product_lists','enum_val','warehouse_lists'],

        data:function(){
            return{
                product_list:false,
                add_form:true,
                edit_form:false,

                form:{
                    inventory_stock_adjusts_no: this.inventory_stock_adjust_no,
                    inventory_stock_adjusts_date: '',
                    inventory_stock_adjusts_warehouse_id: '',
                    inventory_stock_adjusts_narration: '',
                    //inventory_stock_adjusts_total_qty: '',
                    //inventory_stock_adjusts_total_price: '',

                    approve: '',

                    details: [
                        {
                            inventory_stock_adjust_details_product_id:'',
                            inventory_stock_adjust_details_remarks:'',
                            inventory_stock_adjust_rule:'',
                            inventory_stock_adjust_details_unit:'',
                           // inventory_stock_adjust_details_unit_price:'',
                            inventory_stock_adjust_details_qty:'',
                            //inventory_stock_adjust_details_total_price:'',
                        }
                    ],
                },
                errors: {},
            };
        },

        methods:{

            addNewItem(){
                this.form.details.push({
                    inventory_stock_adjust_details_product_id:'',
                    inventory_stock_adjust_details_remarks:'',
                    inventory_stock_adjust_rule:'',
                    inventory_stock_adjust_details_unit:'',
                   //inventory_stock_adjust_details_unit_price:'',
                    inventory_stock_adjust_details_qty:'',
                    //inventory_stock_adjust_details_total_price:'',
                });
                var Select2 = {init: function () {$(".select2").select2({placeholder: "Please select an option"})}};
                jQuery(document).ready(function () {Select2.init()});
            },

            removeItem(index){
                var _this = this;
                if(_this.form.details.length > 1){
                    _this.form.details.splice(index, 1);
                    setTimeout(function () {
                        var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                        jQuery(document).ready(function() {Select2.init();});
                    },100);

                }
            },

            store(app){
                var _this = this;
                _this.form.approve = app;
                axios.post(base_url+'stock-adjust/store', this.form).then( (response) => {
                    this.showMassage(response.data);
                    EventBus.$emit('data-changed');
                })
                .catch(error => {
                    if(error.response.status == 422){
                        this.errors = error.response.data.errors;
                    }else{
                        this.showMassage(error);
                    }
                });
            },

            showMassage(data){
                if(data.status == 'success'){
                    toastr.success(data.message, 'Success Alert');
                }else if(data.status == 'error'){
                    toastr.error(data.message, 'Error Alert');
                }else{
                    toastr.error(data.message, 'Error Alert');
                }
            },

            /*totalQtyAndPrice(){
                var total_qty = 0;
                var total_amount = 0;
                var total_price = 0;
                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    total_price = Number(details[i].inventory_stock_adjust_details_qty) * Number(details[i].inventory_stock_adjust_details_unit_price);
                    this.form.details[i].inventory_stock_adjust_details_total_price = total_price;

                    if(details[i].inventory_stock_adjust_rule == 'Inc'){
                        total_qty = Number(details[i].inventory_stock_adjust_details_qty) + total_qty;
                        total_amount = total_price + total_amount;
                    }else{
                        total_qty = total_qty - Number(details[i].inventory_stock_adjust_details_qty)  ;
                        total_amount = total_amount - total_price;
                    }
                }
                this.form.inventory_stock_adjusts_total_qty = total_qty;
                this.form.inventory_stock_adjusts_total_price = total_amount;
            },*/

            /*duplicateCheck(){
                var no_index = this.form.details.length;
                if (no_index > 1) {
                    for (let i = 0; i < no_index; i++) {
                        for (let j = i+1 ; j < no_index; j++) {
                            if (this.form.details[i].inventory_stock_adjust_details_product_id == this.form.details[j].inventory_stock_adjust_details_product_id) {
                                alert("Duplicate Found");
                                this.form.details[j].inventory_stock_adjust_details_product_id = '';
                                var Select2 = {init: function () {$(".select2").select2({placeholder: "Please select an option"})}};
                                jQuery(document).ready(function () {Select2.init()});
                            }
                        }
                    }
                }
            },*/
        },

        mounted(){
            var _this = this;

            var Select2 = {init: function () {$(".select2").select2({placeholder: "Please select an option"})}};
            jQuery(document).ready(function () {Select2.init()});

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'inventory_stock_adjusts_warehouse_id'){
                    _this.form.inventory_stock_adjusts_warehouse_id = selectedType.val();
                }
            });

            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index'),
                    selectMeasureUnit = $('option:selected', $(e.target)).attr('measure-unit');
                _this.form.details[dataIndex].inventory_stock_adjust_details_product_id = selectedItem.val();
                _this.form.details[dataIndex].inventory_stock_adjust_details_unit = selectMeasureUnit;
                //_this.duplicateCheck();
            });

            $('#addComponent').on('change', '.select-adjust-rule', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.details[dataIndex].inventory_stock_adjust_rule = selectedType.val();
            });



            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                    //startDate: '-2d',
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.inventory_stock_adjusts_date = '';
                    }else {
                        _this.form.inventory_stock_adjusts_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created() {
            var _this = this;
            //_this.totalQtyAndPrice();
        }

    }
</script>
