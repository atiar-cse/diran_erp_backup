<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right"  >
            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <label for="po_order_no" class="col-lg-2 col-form-label">Goods Receive No </label>
                    <div class="col-lg-4">
                        <input type="text"  id="po_order_no"  v-model="form.po_order_no" class="form-control form-control-sm m-input" placeholder="Enter Purchase Order No" disabled>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('po_order_no'))">{{ (errors.hasOwnProperty('po_order_no')) ? errors.po_order_no[0] :'' }}</div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Purchase Date </label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.po_receive_date" data-dateField="po_receive_date" readonly placeholder="Select date from picker" id="m_datepicker_2" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('po_receive_date'))">{{ (errors.hasOwnProperty('po_receive_date')) ? errors.po_receive_date[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="po_order_no" class="col-lg-2 col-form-label">Requisition No  <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="po_requisition_id"  v-model="form.po_requisition_id"  @change="loadDetails()" >
                            <option v-for="(rvalue,rindex) in requisition_lists" :value="rvalue.id" > {{rvalue.purchase_requisition_no}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('po_requisition_id'))">{{ (errors.hasOwnProperty('po_requisition_id')) ? errors.po_requisition_id[0] :'' }}</div>
                    </div>

                    <label for="po_warehouse_id" class="col-lg-2 col-form-label">Warehouse <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" id="po_warehouse_id" data-selectField="po_warehouse_id" v-model="form.po_warehouse_id" >
                            <option v-for="(wvalue,windex) in warehouse_lists" :value="wvalue.id" > {{wvalue.purchase_ware_houses_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['po_warehouse_id']">{{ errors['po_warehouse_id'][0] }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="po_supplier_id" class="col-lg-2 col-form-label">Supplier <span class="requiredField">*</span> </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" id="po_supplier_id" data-selectField="po_supplier_id" v-model="form.po_supplier_id" >
                            <option v-for="(svalue,sindex) in supplier_lists" :value="svalue.id" > {{svalue.purchase_supplier_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['po_supplier_id']">{{ errors['po_supplier_id'][0] }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.po_narration"  id="narration" rows="2"></textarea>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label">Purchase Order Docs</label>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <input type="file" class="form-control" @change="documentUpload" ref="files">
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('po_order_docs'))">{{ (errors.hasOwnProperty('po_order_docs')) ? errors.po_order_docs[0] :'' }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-4"></div>
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
                                        <th>Unit</th>

                                        <th>Order Qty</th>
                                        <th>Qty</th>
                                        <th>Price</th>

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
                                            <select class="form-control m-select2 select2  select-product width-210" v-bind:data-index="index" v-model="details.pod_product_id"  >
                                                <option v-for="(value,index) in product_lists" :value="value.id" :measure-unit="value.measure_unit" :unit-price="value.buying_price" > {{value.product_name}}</option>
                                            </select>
                                            <div class="requiredField" v-if="errors['details.'+index+'.pod_product_id']">{{ errors['details.'+index+'.pod_product_id'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.pod_remarks" class="form-control form-control-sm m-input" placeholder="">
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.pod_unit" class="form-control form-control-sm m-input" placeholder="">
                                        </td>
                                        <td><input type="text" v-model="details.po_order_qty" class="form-control form-control-sm m-input" readonly> </td>
                                        <td>
                                            <input type="text" v-model="details.pod_qty" @input="totalQtyAndPrice()" class="form-control form-control-sm m-input" placeholder="">
                                            <div class="requiredField" v-if="errors['details.'+index+'.pdo_qty']">{{ errors['details.'+index+'.pod_qty'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.pod_unit_price" @input="totalQtyAndPrice()" class="form-control form-control-sm m-input">
                                            <div class="requiredField" v-if="errors['details.'+index+'.pod_unit_price']">{{ errors['details.'+index+'.pod_unit_price'][0] }}</div>
                                        </td>

                                        <td class="width-160">
                                            <input type="text" v-model="details.pod_total_price" class="form-control form-control-sm m-input width-160" readonly>
                                            <div class="requiredField" v-if="errors['details.'+index+'.pod_total_price']">{{ errors['details.'+index+'.pod_total_price'][0] }}</div>
                                        </td>

                                        <td>
                                            <a  @click="removeItem(index)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="text-right">Total Qty </td>
                                        <td class="">
                                            <input type="text" v-model="form.po_total_qty" class="form-control form-control-sm m-input" readonly>
                                        </td>
                                        <td class="text-right">Total Price </td>
                                        <td  class="">
                                            <input type="text" v-model="form.po_total_price" class="form-control form-control-sm m-input" readonly>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <br><br>
                    </div>
                </div>

                <!-- Other Costs -->
                <!-- <hr>

                <div class="form-group m-form__group row">
                    <div class="m-section__content col-lg-12">
                        <div class ="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse" >
                                    <tr>
                                        <th></th>
                                        <th width="30%">Other Cost Name</th>
                                        <th width="40%">Remarks</th>
                                        <th width="30%">Cost Amount</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(other_costs, index) in form.other_costs">
                                    <td scope="row">
                                        <a href="javascript:void(0)"  @click="addNewOtherCostItem" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <select class="form-control m-select2 select2  select-cost-type" v-bind:data-index="index" v-model="other_costs.po_cost_type_id"  >
                                            <option v-for="(value,index) in cost_type_lists" :value="value.id" > {{value.purchase_cost_types_name}}({{value.cost_types_code}})</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['other_costs.'+index+'.po_cost_type_id']">{{ errors['other_costs.'+index+'.po_cost_type_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="other_costs.remarks" class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td>
                                        <input type="text" v-model="other_costs.po_cost_amount"  @input="totalAmount()"  class="form-control form-control-sm m-input text-right">
                                        <div class="requiredField" v-if="errors['other_costs.'+index+'.po_cost_amount']">{{ errors['other_costs.'+index+'.po_cost_amount'][0] }}</div>
                                    </td>
                                    <td>
                                        <a  @click="removeOtherCostItem(index)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td  colspan="3"class="text-right">Total Amount</td>
                                    <td  class="">
                                        <input type="text" v-model="form.po_total_cost" class="form-control form-control-sm m-input text-right" readonly>
                                    </td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <br>
                    </div>
                </div> -->                
                <!-- //Other Costs -->
            </div>

            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="saveDocs(0);"><i class="la la-check"></i> <span>Save</span></button><!--@click.prevent="saveDocs"-->
                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="saveDocs(1)" ><i class="la la-check"></i> <span>Approve</span></button>
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
        props:['permissions','product_lists','supplier_lists','warehouse_lists','cost_type_lists'],

        data:function(){
            return{
                files: [],
                data_list:false,
                add_form:true,
                edit_form:false,

                requisition_lists: [],

                form:{
                    po_order_no: '',

                    po_warehouse_id:'',
                    po_supplier_id:'',
                    po_requisition_id:'',

                    po_receive_date: '',
                    po_narration: '',

                    po_total_qty: '',
                    po_total_price: '',

                    po_order_docs: '',

                    approve: '',
                    details: [
                        {
                            pod_product_id:'',
                            pod_remarks:'',
                            pod_unit:'',
                            pod_unit_price:0,
                            pod_qty:0,
                            pod_total_price:0,
                            po_order_qty:0,
                        }
                    ],
                    other_costs: [
                        {
                            po_cost_type_id:'',
                            remarks:'',
                            po_cost_amount: '',
                        }
                    ],
                },
                errors: {},
            };
        },

        methods:{

            transactionNoGenerate(){
                var _this = this;
                axios.get(base_url+'receive-no')
                    .then( (response) => {
                        _this.form.po_order_no = response.data;
                    });
            },

            req_list(){
                var _this = this;
                axios.get(base_url+'pr-requisition?token_req_list_gen=1')
                    .then( (response) => {
                        _this.requisition_lists = response.data;
                    });
            },

            documentUpload(){
                let uploadedFiles = this.$refs.files.files;
                for(var i = 0; i < uploadedFiles.length; i++) {
                    this.files.push(uploadedFiles[i]);
                }
            },

            addNewItem(){
                var _this = this;
                _this.form.details.push({
                    pod_product_id:'',
                    pod_remarks:'',
                    pod_unit:'',
                    pod_unit_price:0,
                    pod_qty:0,
                    pod_total_price:0,
                    po_order_qty:0,
                });
                _this.initSelect2();
            },

            removeItem(index){
                var _this = this;
                if(_this.form.details.length > 1){
                    _this.form.details.splice(index, 1);
                }
            },

            addNewOtherCostItem(){
                var _this = this;
                this.form.other_costs.push({
                    po_cost_type_id:'',
                    remarks: '',
                    po_cost_amount: '',
                });
                _this.initSelect2();
            },
            removeOtherCostItem(index){
                var _this = this;
                if(_this.form.other_costs.length > 1){
                    _this.form.other_costs.splice(index, 1);
                    setTimeout(function () {
                        _this.initSelect2();
                    },100);
                }
            },
            totalAmount(){
                var total_amount = 0;
                var details = this.form.other_costs;
                var length = details.length;
                for(let i = 0; i < length; i++) {
                    total_amount = Number(details[i].po_cost_amount) + total_amount;
                }
                this.form.po_total_cost = total_amount;
            },

            store(app){
                var _this = this;
                _this.form.approve = app;

                axios.post(base_url+'po-receive/store', this.form).then( (response) => {
                    this.showMassage(response.data);
                    EventBus.$emit('data-changed');
                })
                .catch(error => {
                    if(error.response.status == 422){
                        console.log(error.response.data.errors);
                        this.errors = error.response.data.errors;
                    }else{
                        this.showMassage(error);
                    }
                });
            },

            saveDocs(app){
                if (this.files.length > 0){
                    let formData = new FormData();
                    formData.append('file', this.files[0]);

                    axios.post(base_url+'po-receive/store', formData).then(function(response) {
                        this.files = [];
                        this.form.po_order_docs = response.data.file_path;
                        this.store(app);
                    }.bind(this)).catch(function(data) {
                        console.log('error');
                    });
                }else {
                    this.store(app);
                }
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

            loadDetails(){
                var _this = this;
                var id= this.form.po_requisition_id;

                axios.get(base_url+"po-receive/"+id+"/po-requisition-list").then((response) => {
                    if(response.data.length > 0){
                        _this.form.details = response.data;
                        _this.totalQtyAndPrice();
                        setTimeout(function () {
                            _this.initSelect2();
                        },100);
                    }
                });
            },

            totalQtyAndPrice(){
                var total_qty = 0;
                var total_amount = 0;
                var total_price = 0;
                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    total_price = Number(details[i].pod_qty) * Number(details[i].pod_unit_price);
                    this.form.details[i].pod_total_price = total_price.toFixed(2);
                    total_qty = Number(details[i].pod_qty) + total_qty;
                    total_amount = total_price + total_amount;
                }
                this.form.po_total_qty = total_qty;
                this.form.po_total_price = total_amount.toFixed(2);
            },

            //Other Cost Duplicate check
            duplicateCheck(selectedValue){
                var no_index = this.form.other_costs.length;
                for(let i=0; i<no_index; i++){
                    if(this.form.other_costs[i].po_cost_type_id == selectedValue){
                        alert("Duplicate Found");
                        return true;
                    }
                }
                return false;
            },

            initSelect2(){
                setTimeout(function () {
                    var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                    jQuery(document).ready(function() {Select2.init();});
                },250);                 
            },            
        },

        mounted(){

            var _this = this;
            _this.initSelect2();

            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index'),
                    selectMeasureUnit = $('option:selected', $(e.target)).attr('measure-unit'),
                    selectPrice = $('option:selected', $(e.target)).attr('unit-price');

                _this.form.details[dataIndex].pod_product_id = selectedItem.val();
                _this.form.details[dataIndex].pod_unit = selectMeasureUnit;
                _this.form.details[dataIndex].pod_unit_price = selectPrice;
                // _this.duplicateCheck();
            });

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'po_supplier_id'){
                    _this.form.po_supplier_id = selectedType.val();
                }else if(dataIndex == 'po_requisition_id'){
                    _this.form.po_requisition_id = selectedType.val();
                    _this.loadDetails();
                }else if(dataIndex == 'po_warehouse_id'){
                    _this.form.po_warehouse_id = selectedType.val();
                }else if(dataIndex == 'po_warehouse_id'){
                    _this.form.po_warehouse_id = selectedType.val();
                }
            });

            $('#addComponent').on('change', '.select-cost-type', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.other_costs[dataIndex].po_cost_type_id = selectedType.val();
                _this.duplicateCheck();
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
                        _this.form.po_receive_date = '';
                    }else {
                        _this.form.po_receive_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created() {
            var _this = this;

            _this.transactionNoGenerate();
            _this.req_list();
            _this.totalQtyAndPrice();
        }
    }
</script>
