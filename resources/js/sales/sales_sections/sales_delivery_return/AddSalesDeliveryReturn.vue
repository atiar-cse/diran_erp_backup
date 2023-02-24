<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="sales_delivery_return_no" class="col-lg-2 col-form-label">Delivery Return No <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text"  id="sales_delivery_return_no"  v-model="form.sales_delivery_return_no" class="form-control form-control-sm m-input" placeholder="Enter Delivery Return No ">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_delivery_return_no'))">{{ (errors.hasOwnProperty('sales_delivery_return_no')) ? errors.sales_delivery_return_no[0] :'' }}</div>
                    </div>
                    <label for="date" class="col-lg-2 col-form-label">Sales Date <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" id="date" class="form-control form-control-sm m-input datepicker" v-model="form.sales_delivery_return_date" data-dateField="sales_delivery_return_date" placeholder="Select date from picker" autocomplete="off"  />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_delivery_return_date'))">{{ (errors.hasOwnProperty('sales_delivery_return_date')) ? errors.sales_delivery_return_date[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="sales_delivery_return_customer_id" class="col-lg-2 col-form-label">Customer <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="sales_delivery_return_customer_id" id="sales_delivery_return_customer_id" v-model="form.sales_delivery_return_customer_id" >
                            <option v-for="(cvalue,cindex) in customer_lists" :value="cvalue.id" > {{cvalue.sales_customer_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['sales_delivery_return_customer_id']">{{ errors['sales_delivery_return_customer_id'][0] }}</div>
                    </div>
                    <label for="sales_delivery_return_warehouse_id" class="col-lg-2 col-form-label">Warehouse <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="sales_delivery_return_warehouse_id" id="sales_delivery_return_warehouse_id" v-model="form.sales_delivery_return_warehouse_id" >
                            <option v-for="(wvalue,windex) in warehouse_lists" :value="wvalue.id" > {{wvalue.purchase_ware_houses_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['sales_delivery_return_warehouse_id']">{{ errors['sales_delivery_return_warehouse_id'][0] }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="sales_delivery_return_return_types_id" class="col-lg-2 col-form-label">Return Issue Type <span class="requiredField">*</span> </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="sales_delivery_return_return_types_id" id="sales_delivery_return_return_types_id" v-model="form.sales_delivery_return_return_types_id" >
                            <option v-for="(rvalue,rindex) in return_type_lists" :value="rvalue.id" > {{rvalue.purchase_return_types_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['sales_delivery_return_return_types_id']">{{ errors['sales_delivery_return_return_types_id'][0] }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.sales_delivery_return_narration"  id="narration" rows="2"></textarea>
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
                                        <th class="width-210">Product</th>
                                        <th>Remarks</th>
                                        <th>M Unit</th>
                                        <th>Unit Price</th>
                                        <th>Qty</th>
                                        <th class="width-160">Total Price</th>
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
                                            <select class="form-control m-select2 select2 select-product width-210"  v-bind:data-index="index" v-model="details.sales_delivery_return_details_product_id"  >
                                                <option v-for="(value,index) in product_lists" :value="value.id" :measure-unit="value.measure_unit" :unit-price="value.selling_price" > {{value.product_name}}</option>
                                            </select>
                                            <div class="requiredField" v-if="errors['details.'+index+'.sales_delivery_return_details_product_id']">{{ errors['details.'+index+'.sales_delivery_return_details_product_id'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.sales_delivery_return_details_remarks" class="form-control form-control-sm m-input" placeholder="">
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.sales_delivery_return_details_unit" class="form-control form-control-sm m-input" placeholder="">
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.sales_delivery_return_details_unit_price" @input="totalQtyAndPrice()" class="form-control form-control-sm m-input number">
                                            <div class="requiredField" v-if="errors['details.'+index+'.sales_delivery_return_details_unit_price']">{{ errors['details.'+index+'.sales_delivery_return_details_unit_price'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.sales_delivery_return_details_qty" @input="totalQtyAndPrice()" class="form-control form-control-sm m-input number" placeholder="">
                                            <div class="requiredField" v-if="errors['details.'+index+'.sales_delivery_return_details_qty']">{{ errors['details.'+index+'.sales_delivery_return_details_qty'][0] }}</div>
                                        </td>
                                        <td class="width-160">
                                            <input type="text" v-model="details.sales_delivery_return_details_total_price" class="form-control form-control-sm m-input number width-160">
                                            <div class="requiredField" v-if="errors['details.'+index+'.sales_delivery_return_details_total_price']">{{ errors['details.'+index+'.sales_delivery_return_details_total_price'][0] }}</div>
                                        </td>
                                        <td>
                                            <a  @click="removeItem(index,details.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right">Total </td>
                                        <td class="">
                                            <input type="text" v-model="form.sales_delivery_return_total_qty" class="form-control form-control-sm m-input number">
                                        </td>

                                        <td  class="">
                                            <input type="text" v-model="form.sales_delivery_return_total_price" class="form-control form-control-sm m-input number">
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
                            <button type="submit" class="btn btn-sm btn-success"  @click.prevent="store(0)"><i class="la la-check"></i> <span>Save</span></button>
                            <button v-if="permissions.indexOf('sales-delivery-return.approve') !=-1" type="submit" class="btn btn-sm btn-success"  @click.prevent="store(1)"><i class="la la-check"></i> <span>Approve</span></button>
                         </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
</template>

<script>
    import { EventBus } from '../../../vue-assets';
    export default {

        props:['permissions','product_lists','return_type_lists','warehouse_lists','customer_lists'],

        data:function(){
            return{
                product_list:false,
                add_form:true,
                edit_form:false,
                form:{
                    sales_delivery_return_no: '',
                    sales_delivery_return_date: '',
                    sales_delivery_return_return_types_id: '',
                    sales_delivery_return_warehouse_id: '',
                    sales_delivery_return_customer_id: '',
                    sales_delivery_return_narration: '',
                    sales_delivery_return_total_qty: '',
                    sales_delivery_return_total_price: '',
                    approve:'',
                    details: [
                        {
                            sales_delivery_return_details_product_id:'',
                            sales_delivery_return_details_remarks:'',
                            sales_delivery_return_details_unit:'',
                            sales_delivery_return_details_unit_price:'',
                            sales_delivery_return_details_qty:0,
                            sales_delivery_return_details_total_price:0,
                        }
                    ],
                },
                errors: {},
            };
        },

        methods:{

            transactionNoGenerate(){
                var _this = this;
                axios.get(base_url+'sales-return-no')
                    .then( (response) => {
                        _this.form.sales_delivery_return_no = response.data;
                    });
            },

            addNewItem(){
                this.form.details.push({
                    sales_delivery_return_details_product_id:'',
                    sales_delivery_return_details_remarks:'',
                    sales_delivery_return_details_unit:'',
                    sales_delivery_return_details_unit_price:'',
                    sales_delivery_return_details_qty:0,
                    sales_delivery_return_details_total_price:0
                });

                var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                jQuery(document).ready(function() {Select2.init()});
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

                axios.post(base_url+'sales-delivery-return/store', this.form).then( (response) => {
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

            totalQtyAndPrice(){
                var total_qty = 0;
                var total_amount = 0;
                var total_price = 0;
                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    total_price = Number(details[i].sales_delivery_return_details_qty) * Number(details[i].sales_delivery_return_details_unit_price);
                    this.form.details[i].sales_delivery_return_details_total_price = total_price;
                    total_qty = Number(details[i].sales_delivery_return_details_qty) + total_qty;
                    total_amount = total_price + total_amount;
                }
                this.form.sales_delivery_return_total_qty = total_qty;
                this.form.sales_delivery_return_total_price = total_amount;
            },

            duplicateCheck(){
                var no_index = this.form.details.length;
                var details = this.form.details;
                if (no_index > 1) {
                    for (let i = 0; i < no_index; i++) {
                        for (let j = i+1; j < no_index; j++) {
                            if (details[i].sales_delivery_return_details_product_id == details[j].sales_delivery_return_details_product_id) {
                                alert("Duplicate Found");
                                details[j].sales_delivery_return_details_product_id = '';

                                var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                                jQuery(document).ready(function() {Select2.init()});
                            }
                        }
                    }
                }
            },
        },

        mounted(){
            var _this = this;

            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
            jQuery(document).ready(function() {Select2.init()});

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'sales_delivery_return_customer_id'){
                    _this.form.sales_delivery_return_customer_id = selectedType.val();
                }else if(dataIndex == 'sales_delivery_return_warehouse_id'){
                    _this.form.sales_delivery_return_warehouse_id = selectedType.val();
                }else if(dataIndex == 'sales_delivery_return_return_types_id'){
                    _this.form.sales_delivery_return_return_types_id = selectedType.val();
                }
            });

            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index'),
                    selectMeasureUnit = $('option:selected', $(e.target)).attr('measure-unit'),
                    selectPrice = $('option:selected', $(e.target)).attr('unit-price');

                if(selectPrice){selectPrice=selectPrice;}else{selectPrice='0';}
                _this.form.details[dataIndex].sales_delivery_return_details_product_id = selectedItem.val();
                _this.form.details[dataIndex].sales_delivery_return_details_unit = selectMeasureUnit;
                _this.form.details[dataIndex].sales_delivery_return_details_unit_price = selectPrice;
                _this.duplicateCheck();
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.sales_delivery_return_date = '';
                    }else {
                        _this.form.sales_delivery_return_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });

            $('.number').keypress(function(event) {
                if (((event.which != 46 || (event.which == 46 && $(this).val() == '')) ||
                    $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            }).on('paste', function(event) {
                event.preventDefault();
            });
        },

        created() {
            var _this = this;
            _this.totalQtyAndPrice();
            _this.transactionNoGenerate();
        }

    }
</script>
