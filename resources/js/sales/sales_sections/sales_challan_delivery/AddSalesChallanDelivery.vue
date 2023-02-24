<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="sales_delivery_no" class="col-lg-2 col-form-label">Invoice No <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text"  id="sales_delivery_no"  v-model="form.sales_delivery_no" class="form-control form-control-sm m-input small" placeholder="Enter Delivery Challan No">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_delivery_no'))">{{ (errors.hasOwnProperty('sales_delivery_no')) ? errors.sales_delivery_no[0] :'' }}</div>
                    </div>
                    <label for="date" class="col-lg-2 col-form-label">Delivery Date <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" id="date" class="form-control form-control-sm m-input datepicker" v-model="form.sales_delivery_date" data-dateField="sales_delivery_date" placeholder="Select date from picker" readonly />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_delivery_date'))">{{ (errors.hasOwnProperty('sales_delivery_date')) ? errors.sales_delivery_date[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label for="sales_invoice_id" class="col-lg-2 col-form-label"> Sales challan No </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="sales_invoice_id" id="sales_invoice_id" v-model="form.sales_invoice_id" @change="loadDetails()" >
                            <option v-for="(svalue,sindex) in invoice_lists" :value="svalue.id" > {{svalue.sales_invoices_no}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['sales_invoice_id']">{{ errors['sales_invoice_id'][0] }}</div>
                    </div>
                    <label for="sales_warehouse_id" class="col-lg-2 col-form-label">Warehouse </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="sales_warehouse_id" id="sales_warehouse_id" v-model="form.sales_warehouse_id" disabled >
                            <option v-for="(wvalue,windex) in warehouse_lists" :value="wvalue.id" > {{wvalue.purchase_ware_houses_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['sales_warehouse_id']">{{ errors['sales_warehouse_id'][0] }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label for="sales_customer_id" class="col-lg-2 col-form-label">Customer </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="sales_customer_id" id="sales_customer_id" v-model="form.sales_customer_id" >
                            <option v-for="(cvalue,cindex) in customer_lists" :value="cvalue.id" > {{cvalue.sales_customer_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['sales_customer_id']">{{ errors['sales_customer_id'][0] }}</div>
                    </div>
                    <label  class="col-lg-2 col-form-label">Tender Number </label>
                    <div class="col-lg-4">
                        <input type="text" v-model="form.sales_delivery_tender" class="form-control form-control-sm m-input" >
                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_delivery_tender'))">{{ (errors.hasOwnProperty('sales_delivery_tender')) ? errors.sales_delivery_tender[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea id="narration" class="form-control m-input" v-model="form.sales_delivery_narration" rows="2"></textarea>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Delivery Address <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <textarea id="locstion" class="form-control m-input" v-model="form.sales_delivery_location" rows="2"></textarea>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_delivery_location'))">{{ (errors.hasOwnProperty('sales_delivery_location')) ? errors.sales_delivery_location[0] :'' }}</div>
                    </div>

                </div>
                <br><br>
                <!--begin::Portlet-->
                <div class="form-group m-form__group row add-manage-section">
                    <div class="m-section__content col-lg-12">
                        <div class ="table-responsive small" style="font-size: 11px">
                            <table class="table table-sm m-table table-bordered borderless"  >
                                <thead class="thead-inverse" >
                                    <tr>
                                        <th></th>
                                        <th class="width-210">Product </th>
                                        <th>Remarks</th>
                                        <th>M Unit</th>
                                        <th>Unit Price</th>
                                        <th>Current Stock</th>
                                        <th>Order Qty</th>
                                        <th>Pre. Delivery Qty</th>
                                        <th>Discount Type</th>
                                        <th>Discount Amount</th>
                                        <th>Vat Type</th>
                                        <th>Qty</th>
                                        <th style="width: 160px;">Total Price</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(details, index) in form.details" >
                                        <td scope="row">
                                            <a href="javascript:void(0)"  @click="addNewItem" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Add More">
                                                <i class="la la-plus"></i>
                                            </a>
                                        </td>
                                        <td>
                                            <select class="form-control m-select2 select2 select-product"  v-bind:data-index="index" v-model="details.sales_delivery_details_product_id"  style="font-size: 11px; width: 170px;">
                                                <option v-for="(value,index) in pid_lists" :value="value.id" :measure-unit="value.measure_unit" :unit-price="value.selling_price" :current-stock="value.inventory_stocks_current_qty"> {{value.product_name}}</option>
                                            </select>
                                            <div class="requiredField" v-if="errors['details.'+index+'.sales_delivery_details_product_id']">{{ errors['details.'+index+'.sales_delivery_details_product_id'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.sales_delivery_details_description" class="form-control form-control-sm m-input" placeholder="" style="font-size: 11px">
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.sales_delivery_details_unit" class="form-control form-control-sm m-input" placeholder="" style="font-size: 11px">
                                            <div class="requiredField" v-if="errors['details.'+index+'.sales_delivery_details_unit']">{{ errors['details.'+index+'.sales_delivery_details_unit'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.sales_delivery_details_unit_price" @keyup="totalQtyAndPrice()" class="form-control form-control-sm m-input number" style="font-size: 11px" >
                                            <div class="requiredField" v-if="errors['details.'+index+'.sales_delivery_details_unit_price']">{{ errors['details.'+index+'.sales_delivery_details_unit_price'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.current_qty" class="form-control form-control-sm m-input" placeholder="" disabled style="color: red;font-size: 11px;">
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.sales_delivery_details_order_qty"  class="form-control form-control-sm small text-danger"  readonly style="font-size: 11px">
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.sales_delivery_details_pervious_qty"  class="form-control form-control-sm small text-danger"  readonly style="font-size: 11px"> <!--style="background: #9d9d9d;"-->
                                        </td>
                                        <td>
                                            <select class="" v-model="details.sales_delivery_details_discount_type" @change="totalQtyAndPrice()" style="font-size: 11px">
                                                <option>Pct(%)</option>
                                                <option>Fixed</option>
                                            </select>
                                        </td>
                                        <td><input type="text" v-model="details.sales_delivery_details_discount" class="form-control form-control-sm m-input number" @keyup="totalQtyAndPrice()" style="font-size: 11px"/></td>
                                        <td>
                                            <input type="number" v-model="details.sales_delivery_details_vat_sub" @change="totalQtyAndPrice()" class="form-control form-control-sm small text-danger"  placeholder="write the % Ex 7.5" style="font-size: 11px">
                                            <!--<select class="" v-model="details.sales_delivery_details_vat_sub" @change="totalQtyAndPrice()" style="font-size: 11px">
                                                <option>No Vat</option>
                                                <option>15%</option>
                                            </select>-->
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.sales_delivery_details_qty" @keyup="totalQtyAndPrice()" class="form-control form-control-sm m-input number" placeholder="" style="font-size: 11px">
                                            <div class="requiredField" v-if="errors['details.'+index+'.sales_delivery_details_qty']">{{ errors['details.'+index+'.sales_delivery_details_qty'][0] }}</div>
                                        </td>
                                        <td style="width: 160px;">
                                            <input type="text" readonly v-model="details.sales_delivery_details_total_price" class="form-control form-control-sm m-input number" style="font-size: 11px;width: 160px;">
                                            <div class="requiredField" v-if="errors['details.'+index+'.sales_delivery_details_total_price']">{{ errors['details.'+index+'.sales_delivery_details_total_price'][0] }}</div>
                                        </td>
                                        <td>
                                            <a  @click="removeItem(index)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="11" class="text-right">Sub Total </td>
                                        <td class="">
                                            <input readonly type="text" v-model="form.sales_delivery_total_qty" class="form-control form-control-sm m-input number" style="font-size: 11px">
                                            <div class="requiredField" v-if="(errors.hasOwnProperty('sales_delivery_total_qty'))">{{ (errors.hasOwnProperty('sales_delivery_total_qty')) ? errors.sales_delivery_total_qty[0] :'' }}</div>
                                        </td>
                                        <td  class="">
                                            <input readonly type="text" v-model="form.sales_delivery_sub_total_price" class="form-control form-control-sm m-input number" style="font-size: 11px">
                                            <div class="requiredField" v-if="(errors.hasOwnProperty('sales_delivery_sub_total_price'))">{{ (errors.hasOwnProperty('sales_delivery_sub_total_price')) ? errors.sales_delivery_sub_total_price[0] :'' }}</div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="12" class="text-right">AIT </td>
                                        <td class="">
                                            <input type="text" v-model="form.sales_delivery_ati" class="form-control form-control-sm m-input number" @keyup="totalQtyAndPrice()" style="font-size: 11px">
                                            <div class="requiredField" v-if="(errors.hasOwnProperty('sales_delivery_ati'))">{{ (errors.hasOwnProperty('sales_delivery_ati')) ? errors.sales_delivery_ati[0] :'' }}</div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="12" class="text-right">Charges </td>
                                        <td class="">
                                            <input type="text" v-model="form.sales_delivery_commission" class="form-control form-control-sm m-input number" @keyup="totalQtyAndPrice()" style="font-size: 11px">
                                            <div class="requiredField" v-if="(errors.hasOwnProperty('sales_delivery_total_price'))">{{ (errors.hasOwnProperty('sales_delivery_total_price')) ? errors.sales_delivery_total_price[0] :'' }}</div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="12" class="text-right">Discount </td>
                                        <td  class="">
                                            <input readonly type="text" v-model="form.sales_delivery_discount" class="form-control form-control-sm m-input number" @keyup="totalQtyAndPrice()" style="font-size: 11px">
                                            <div class="requiredField" v-if="(errors.hasOwnProperty('sales_delivery_discount'))">{{ (errors.hasOwnProperty('sales_delivery_discount')) ? errors.sales_delivery_discount[0] :'' }}</div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="12" class="text-right">Vat </td>
                                        <td class="">
                                            <input type="text" readonly v-model="form.sales_delivery_vat" class="form-control form-control-sm m-input number" @keyup="totalQtyAndPrice()" style="font-size: 11px">
                                            <div class="requiredField" v-if="(errors.hasOwnProperty('sales_delivery_vat'))">{{ (errors.hasOwnProperty('sales_delivery_vat')) ? errors.sales_delivery_vat[0] :'' }}</div>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="11" class="text-right"> Total </td>
                                        <td class="">
                                            <select class="" v-model="form.sales_delivery_vat_type" data-selectField="forming_type" id="vat_inc_exclude" @change="totalQtyAndPrice()" style="font-size: 11px">
                                                <option>Incl Vat</option>
                                                <option>Excl Vat</option>
                                            </select>
                                        </td>
                                        <td  class="" width="10%">
                                            <input type="text" readonly v-model="form.sales_delivery_total_price" class="form-control form-control-sm m-input number" style="font-size: 11px">
                                            <div class="requiredField" v-if="(errors.hasOwnProperty('sales_delivery_total_price'))">{{ (errors.hasOwnProperty('sales_delivery_total_price')) ? errors.sales_delivery_total_price[0] :'' }}</div>
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
                            <button v-if="permissions.indexOf('sales-delivery-challan.approve') !=-1" type="submit" class="btn btn-sm btn-success"  @click.prevent="store(1)"><i class="la la-check"></i> <span>Approve</span></button>


                            <!--<button type="reset" class="btn btn-sm btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save and Go List</span></button>-->
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
        props:['permissions','invoice_lists','customer_lists','warehouse_lists'],

        data:function(){
            return{
                product_list:false,
                add_form:true,
                edit_form:false,

                pid_lists: [],

                form:{
                    sales_delivery_no: '',
                    sales_delivery_date: '',
                    sales_invoice_id: '',
                    sales_customer_id:'',
                    sales_delivery_tender:'',
                    sales_warehouse_id: '',

                    sales_delivery_location: '',

                    sales_delivery_narration: '',
                    sales_delivery_total_qty: '',
                    sales_delivery_sub_total_price: '',

                    sales_delivery_ati: '',

                    sales_delivery_commission: '',
                    sales_delivery_discount: '',
                    sales_delivery_vat: '',
                    sales_delivery_vat_type: '',
                    sales_delivery_total_price: '',

                    approve: '',

                    details: [
                        {
                            sales_delivery_details_product_id:'',
                            sales_delivery_details_description:'',
                            sales_delivery_details_unit:'',
                            sales_delivery_details_unit_price:'',

                            current_qty:'',

                            sales_delivery_details_order_qty:'',
                            sales_delivery_details_pervious_qty: '',
                            sales_delivery_details_qty:0,
                            sales_delivery_details_total_price:'',
                            sales_delivery_details_discount_type:'',
                            sales_delivery_details_discount:'',
                            sales_delivery_details_vat_sub:'',
                        }
                    ],
                },
                errors: {},
            };
        },

        methods:{

            transactionNoGenerate(){
                var _this = this;
                axios.get(base_url+'sales-challan-no')
                    .then( (response) => {
                        _this.form.sales_delivery_no = response.data;
                    });
            },

            loadProduct(){
                var _this = this;
                var wid= _this.form.sales_warehouse_id;
                if(wid==''){wid='__';}
                axios.get(base_url+wid+'/'+'load-sales-product')
                    .then((response) => {
                        _this.pid_lists = response.data;
                    });
            },

            addNewItem(){
                this.form.details.push({
                    sales_delivery_details_product_id:'',
                    sales_delivery_details_description:'',
                    sales_delivery_details_unit:'',
                    sales_delivery_details_unit_price:'',
                    sales_delivery_details_order_qty:'',
                    sales_delivery_details_pervious_qty: '',
                    sales_delivery_details_qty:0,
                    sales_delivery_details_total_price:'',
                    sales_delivery_details_discount_type:'',
                    sales_delivery_details_discount:'',
                    sales_delivery_details_vat_sub:'',
                });
                 var Select2= {
                     init:function() {
                         $(".select2").select2( {
                             placeholder: "Please select an option"
                         })
                     }
                 };
                jQuery(document).ready(function() {
                    Select2.init()
                });
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

                axios.post(base_url+'sales-delivery-challan/store', this.form).then( (response) => {
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
                var acl_total = 0;

                var row_discount = 0;
                var total_discount = 0;

                var row_vat = 0;
                var total_vat = 0;
                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    var dis_type = details[i].sales_delivery_details_discount_type;
                    var dis_amount = Number(details[i].sales_delivery_details_discount);
                    var vat_sub = details[i].sales_delivery_details_vat_sub;


                    total_price = Number(details[i].sales_delivery_details_qty) * Number(details[i].sales_delivery_details_unit_price);
                    this.form.details[i].sales_delivery_details_total_price = total_price.toFixed(3);

                    if(dis_type == 'Pct(%)'){row_discount = total_price * (dis_amount/100);}
                    else if(dis_type == 'Fixed'){row_discount = dis_amount;}
                    else{row_discount=0;}
                    total_discount =total_discount + row_discount;


                    if(vat_sub > 0)
                    {
                        row_vat = (total_price * vat_sub) / 100;
                    }
                    else
                    {
                        row_vat = 0;
                    }

                    total_vat = total_vat + row_vat;

                    total_qty = Number(details[i].sales_delivery_details_qty) + total_qty;
                    total_amount = total_price + total_amount;

                }
                this.form.sales_delivery_sub_total_price = total_amount.toFixed(3);
                this.form.sales_delivery_total_qty = total_qty;

                this.form.sales_delivery_discount = total_discount.toFixed(3);
                this.form.sales_delivery_vat = total_vat.toFixed(3);

                var ati = this.form.sales_delivery_ati;
                if(ati ==''){ati =0;}else{ati = ati;}

                var commision = this.form.sales_delivery_commission;
                if(commision ==''){commision =0;}else{commision = commision;}

                var vat_type = this.form.sales_delivery_vat_type;
                if(vat_type == 'Incl Vat'){
                    acl_total =  Number(total_amount) + Number(total_vat) + Number(commision) + Number(ati) - Number(total_discount);
                }else{
                    acl_total = (Number(total_amount) + Number(commision) + Number(ati) - Number(total_discount));
                }

                this.form.sales_delivery_total_price = acl_total.toFixed(3);
            },

            duplicateCheck(){
                var no_index = this.form.details.length;
                var details = this.form.details;
                if (no_index > 1) {
                    for (let i = 0; i < no_index; i++) {
                        for (let j = i+1; j < no_index; j++) {
                            if (details[i].sales_delivery_details_product_id == details[j].sales_delivery_details_product_id) {
                                alert("Duplicate Found");
                                details[j].sales_delivery_details_product_id = '';

                                var Select2 = {init: function () {$(".select2").select2({placeholder: "Please select an option"})}};
                                jQuery(document).ready(function () {Select2.init()});
                            }
                        }
                    }
                }
            },

            loadDetails(){
                var _this = this;
                var id= this.form.sales_invoice_id;
                var AddEditId = 0;
                var challan_no = this.form.sales_delivery_no;

                axios.get(base_url+"sales-delivery-challan/"+challan_no+"/"+AddEditId+"/"+id+"/sales-invoice-list")
                    .then((response) => {

                        _this.form  = response.data.data.add_mode;
                        _this.pid_lists  = response.data.data.pid_list;
                        this.totalQtyAndPrice();

                        setTimeout(function () {

                            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                            jQuery(document).ready(function() {Select2.init()});
                        },100);
                    });
            },
        },

        mounted(){
            var _this = this;

            var Select2= {init:function(){$(".select2").select2({placeholder: "Please select an option"})}};
            jQuery(document).ready(function() {Select2.init()});

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'sales_invoice_id'){
                    _this.form.sales_invoice_id = selectedType.val();
                    _this.loadDetails();
                }else if(dataIndex == 'sales_customer_id'){
                    _this.form.sales_customer_id = selectedType.val();
                }else if(dataIndex == 'sales_warehouse_id'){
                    _this.form.sales_warehouse_id = selectedType.val();
                    _this.loadProduct();
                }
            });

            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index'),
                    selectMeasureUnit = $('option:selected', $(e.target)).attr('measure-unit'),
                    selectPrice = $('option:selected', $(e.target)).attr('unit-price'),
                    selectStock = $('option:selected', $(e.target)).attr('current-stock');

                if(selectStock == null){
                    selectStock=0;
                    _this.form.details[dataIndex].sales_delivery_details_product_id = '';
                    alert('Please Select Warehouse');
                }
                else{
                    _this.form.details[dataIndex].sales_delivery_details_product_id = selectedItem.val();
                    _this.form.details[dataIndex].current_qty = selectStock;
                    _this.form.details[dataIndex].sales_delivery_details_unit = selectMeasureUnit;
                    _this.form.details[dataIndex].sales_delivery_details_unit_price = selectPrice;
                    _this.duplicateCheck();
                }
            });



            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.sales_delivery_date = '';
                    }else {
                        _this.form.sales_delivery_date = moment(ev.date).format('DD/MM/YYYY');
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
            _this.loadProduct();
            _this.totalQtyAndPrice();
            _this.transactionNoGenerate();
        }

    }

</script>


