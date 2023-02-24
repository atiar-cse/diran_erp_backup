<template>
    <div>
        <!--begin::Form-->
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="sales_invoices_no" class="col-lg-2 col-form-label">Challan NO <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text"  id="sales_invoices_no"  v-model="form.sales_invoices_no" class="form-control form-control-sm m-input" placeholder="Enter Invoice NO ">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_invoices_no'))">{{ (errors.hasOwnProperty('sales_invoices_no')) ? errors.sales_invoices_no[0] :'' }}</div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Sales Date </label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.sales_invoices_date" data-dateField="sales_invoices_date" readonly placeholder="Select date from picker" id="m_datepicker_2" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_invoices_date'))">{{ (errors.hasOwnProperty('sales_invoices_date')) ? errors.sales_invoices_date[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label for="sales_invoices_type" class="col-lg-2 col-form-label"> Type </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="sales_invoices_type" id="sales_invoices_type" v-model="form.sales_invoices_type" >
                            <option v-for="(value,index) in enum_val" :value="value"> {{value}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['sales_invoices_type']">{{ errors['sales_invoices_type'][0] }}</div>
                    </div>

                    <label for="sales_invoices_warehouse_id" class="col-lg-2 col-form-label">Warehouse
                        <span @click="loadWhList">
                            <i style="font-size: 10px;padding: 0 0 0 10px;cursor: pointer;" class="fa fa-sync"  id="icon_submit"></i>
                        </span>
                    </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="sales_invoices_warehouse_id" id="sales_invoices_warehouse_id" v-model="form.sales_invoices_warehouse_id"  disabled>
                            <option v-for="(wvalue,windex) in warehouse_lists" :value="wvalue.id" > {{wvalue.purchase_ware_houses_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['sales_invoices_warehouse_id']">{{ errors['sales_invoices_warehouse_id'][0] }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="sales_invoices_customer_id" class="col-lg-2 col-form-label">Customer </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="sales_invoices_customer_id" id="sales_invoices_customer_id" v-model="form.sales_invoices_customer_id" >
                            <option v-for="(cvalue,cindex) in customer_lists" :value="cvalue.id" > {{cvalue.sales_customer_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['sales_invoices_customer_id']">{{ errors['sales_invoices_customer_id'][0] }}</div>
                    </div>

                    <label for="sales_invoices_contract_no" class="col-lg-2 col-form-label">Contract NO </label>
                    <div class="col-lg-4">
                        <input type="text"  id="sales_invoices_contract_no"  v-model="form.sales_invoices_contract_no" class="form-control form-control-sm m-input" placeholder="Enter Contract NO ">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_invoices_contract_no'))">{{ (errors.hasOwnProperty('sales_invoices_contract_no')) ? errors.sales_invoices_contract_no[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="narrationt">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.sales_invoices_narration"  id="narrationt" rows="2"></textarea>
                    </div>
                    <label class="col-lg-2 col-form-label" > </label>
                    <div class="col-lg-4">
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
                                        <th class="20%">Product Name</th>
                                        <th>Current Qty</th>
                                        <th>Details</th>
                                        <th>Unit</th>
                                        <th>Qty</th>
                                        <th>Price</th>
                                        <th class="width-160">Total Price</th>
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
                                        <td >
                                            <select style="width: 100%;" class="form-control m-select2 select2 select-product"  v-bind:data-index="index" v-model="details.sales_invoice_details_product_id"  >
                                                <option v-for="(value,index) in pid_lists" :value="value.id" :measure-unit="value.measure_unit" :unit-price="value.selling_price" :curent-stock="value.inventory_stocks_current_qty"> {{value.product_name}}</option>
                                            </select>
                                            <div class="requiredField" v-if="errors['details.'+index+'.sales_invoice_details_product_id']">{{ errors['details.'+index+'.sales_invoice_details_product_id'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.current_qty" class="form-control form-control-sm m-input" placeholder="" disabled style="color: red;">
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.sales_invoice_details_description" class="form-control form-control-sm m-input" placeholder="">
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.sales_invoice_details_unit" class="form-control form-control-sm m-input" placeholder="">
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.sales_invoice_details_qty" @input="totalQtyAndPrice()" class="form-control form-control-sm m-input" placeholder="">
                                            <div class="requiredField" v-if="errors['details.'+index+'.sales_invoice_details_qty']">{{ errors['details.'+index+'.sales_invoice_details_qty'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.sales_invoice_details_unit_price" @input="totalQtyAndPrice()" class="form-control form-control-sm m-input">
                                            <div class="requiredField" v-if="errors['details.'+index+'.sales_invoice_details_unit_price']">{{ errors['details.'+index+'.sales_invoice_details_unit_price'][0] }}</div>
                                        </td>
                                        <td class="width-160">
                                            <input type="text" readonly v-model="details.sales_invoice_details_total_price" class="form-control form-control-sm m-input width-160">
                                            <div class="requiredField" v-if="errors['details.'+index+'.sales_invoice_details_total_price']">{{ errors['details.'+index+'.sales_invoice_details_total_price'][0] }}</div>
                                        </td>

                                        <td>
                                            <a  @click="removeItem(index,details.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right">Total Qty</td>
                                        <td class="">
                                            <input readonly type="text" v-model="form.sales_invoices_total_qty" class="form-control form-control-sm m-input">
                                        </td>
                                        <td class="text-right">Total Amount</td>
                                        <td  class="">
                                            <input readonly type="text" v-model="form.sales_invoices_total_price" class="form-control form-control-sm m-input">
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
                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="update(form.id,0)" ><i class="la la-check"></i> <span>Update</span></button>
                            <button v-if="permissions.indexOf('sales-invoice.approve') !=-1" type="submit" class="btn btn-sm btn-success" @click.prevent="update(form.id,1)" ><i class="la la-check"></i> <span>Approve</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import { EventBus } from '../../../vue-assets';
    export default {
        props:['permissions','editId','enum_val','customer_lists'],
        data:function(){
            return{
                product_list:false,
                add_form:false,
                edit_form:true,

                pid_lists:[],
                warehouse_lists:[],

                form:{
                    sales_invoices_no: '',
                    sales_invoices_contract_no: '',
                    sales_invoices_date:  '',
                    sales_invoices_type: '',
                    sales_invoices_warehouse_id: '',
                    sales_invoices_customer_id: '',
                    sales_invoices_narration: '',
                    sales_invoices_total_qty: '',
                    sales_invoices_total_price: '',
                    approve: '',
                    deleteID: [],
                    details: [
                        {
                            sales_invoice_details_product_id:'',
                            current_qty:'',
                            sales_invoice_details_description:'',
                            sales_invoice_details_unit:'',
                            sales_invoice_details_unit_price:'',
                            sales_invoice_details_qty:'',
                            sales_invoice_details_total_price:'',
                        }
                    ],
                },
                errors: {},
            };
        },

        methods:{
            loadWhList(){
                var _this = this;
                var token_wh = 'token_wh';

                $('#icon_submit').addClass('fa-spin');
                axios.get(base_url+"ware-house?token_wh="+token_wh).then( (response) => {
                    console.log(response);
                    _this.warehouse_lists= response.data;
                });
                setTimeout(function () {
                    $('#icon_submit').removeClass('fa-spin');
                    var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                    jQuery(document).ready(function() {Select2.init()});
                },250);


            },

            loadProduct(){
                var _this = this;
                var wid= _this.form.sales_invoices_warehouse_id;
                if(wid==''){wid='__';}
                axios.get(base_url+wid+'/'+'load-products-list')
                    .then((response) => {
                        _this.pid_lists = response.data;
                    });
            },

            addNewItem(){
                this.form.details.push({
                    sales_invoice_details_product_id:'',
                    sales_invoice_details_description:'',
                    sales_invoice_details_unit:'',
                    sales_invoice_details_unit_price:'',
                    sales_invoice_details_qty:'',
                    sales_invoice_details_total_price:'',
                });

                var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                jQuery(document).ready(function() {Select2.init()});
            },

            removeItem(index,deletedId){
                var _this = this;
                if(_this.form.details.length > 1){
                    _this.form.details.splice(index, 1);
                    setTimeout(function () {
                        var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                        jQuery(document).ready(function() {Select2.init();});
                    },100);

                    if (deletedId) {
                        _this.form.deleteID.push(deletedId);
                    }
                }
                _this.totalQtyAndPrice();
            },

            edit(id) {
                var _this = this;
                axios.get(base_url+'sales-invoice/'+id+'/edit')
                    .then( (response) => {
                        console.log(response.data.data);
                        _this.form  = response.data.data.edit_mode;
                        _this.pid_lists  = response.data.data.pid_list;
                        setTimeout(function () {

                            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                            jQuery(document).ready(function() {Select2.init()});

                        },100);
                    });
            },

            update(id,app){

                var _this = this;
                _this.form.approve = app;

                axios.put(base_url+'sales-invoice/'+id+'/update',
                    this.form).then( (response) => {
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
                    total_price = (Number(details[i].sales_invoice_details_qty) * Number(details[i].sales_invoice_details_unit_price)).toFixed(3);
                    this.form.details[i].sales_invoice_details_total_price = total_price;
                    total_qty = Number(details[i].sales_invoice_details_qty) + total_qty;
                    total_amount = parseFloat(total_price) + parseFloat(total_amount);
                }
                this.form.sales_invoices_total_qty = total_qty;
                this.form.sales_invoices_total_price = total_amount;
            },

            duplicateCheck(selectedValue , id){
                var no_index = this.form.details.length;
                var details = this.form.details;
                for(let i=0; i<no_index; i++){

                    if(details[i].sales_invoice_details_product_id == selectedValue && id!=i ){
                        alert("Duplicate Found");
                        details[id].sales_invoice_details_product_id = '';

                        var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                        jQuery(document).ready(function() {Select2.init()});
                    }
                }
            },


        },

        mounted(){
            var _this = this;

            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
            jQuery(document).ready(function() {Select2.init()});

            $('#editComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index'),
                    selectMeasureUnit = $('option:selected', $(e.target)).attr('measure-unit'),
                    selectPrice = $('option:selected', $(e.target)).attr('unit-price'),
                    selectStock = $('option:selected', $(e.target)).attr('curent-stock');

                if(selectPrice){selectPrice=selectPrice;}else{selectPrice='0';}
                if(selectStock == null){
                    selectStock=0;
                    _this.form.details[dataIndex].sales_invoice_details_product_id = '';
                }
                else {
                    _this.form.details[dataIndex].sales_invoice_details_product_id = selectedItem.val();
                    _this.form.details[dataIndex].current_qty = selectStock;
                    _this.form.details[dataIndex].sales_invoice_details_unit = selectMeasureUnit;
                    _this.form.details[dataIndex].sales_invoice_details_unit_price = selectPrice;
                    _this.duplicateCheck(selectedItem.val(), dataIndex);
                }
            });

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'sales_invoices_customer_id'){
                    _this.form.sales_invoices_customer_id = selectedType.val();
                }else if(dataIndex == 'sales_invoices_warehouse_id'){
                    _this.form.sales_invoices_warehouse_id = selectedType.val();
                    _this.loadProduct();
                }else if(dataIndex == 'sales_invoices_type'){
                    _this.form.sales_invoices_type = selectedType.val();
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
                        _this.form.sales_invoices_date = '';
                    }else {
                        _this.form.sales_invoices_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });

        },

        created(){
            var _this = this;
            _this.edit(_this.editId);
            _this.loadWhList();
            _this.totalQtyAndPrice();
        }

    }
</script>
