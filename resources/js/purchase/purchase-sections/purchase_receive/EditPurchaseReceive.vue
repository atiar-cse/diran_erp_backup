<template>
    <div>
        <!--begin::Form-->
        <!--<form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form&#45;&#45;fit m-form&#45;&#45;label-align-right" >-->
        <form method="POST"  enctype="multipart/form-data" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >
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
                    <label for="po_order_no" class="col-lg-2 col-form-label">Requisition No </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="rindex"  v-model="form.po_requisition_id"  @change="loadDetails()" >
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
                    <label for="po_order_no" class="col-lg-2 col-form-label">Document</label>
                    <div class="col-lg-4">
                        <a :href="form.old_document_storage_link" download="Attachment" class="btn">
                            <i class="fa fa-download"></i> Download
                        </a>
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
                                            <option v-for="(value,index) in product_lists" :value="value.id" :measure-unit="value.measure_unit" :unit-price="value.buying_price"  > {{value.product_name}}</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.pod_product_id']">{{ errors['details.'+index+'pod_product_id'][0] }}</div>
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
                                        <a  @click="removeItem(index,details.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
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
            </div>

            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="updateDocs(form.id,0)"><i class="la la-check"></i> <span>Update</span></button>
                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="updateDocs(form.id,1)"><i class="la la-check"></i> <span>Approve</span></button>
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

        props:['permissions','editId','product_lists','supplier_lists','warehouse_lists'],

        data:function(){
            return{
                files: [],
                data_list:false,
                add_form:false,
                edit_form:true,

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

                    old_document_storage_link:'',
                    old_document:'',
                    po_order_docs:'',

                    approve: '',

                    deleteID: [],
                    details: [
                        {
                            pod_product_id:'',
                            pod_remarks:'',
                            pod_unit:'',
                            pod_unit_price:'',
                            pod_qty:'',
                            pod_total_price:'',
                            po_order_qty:0,
                        }
                    ],
                },
                errors: {},
            };
        },

        methods:{

            req_list(){
                var _this = this;
                axios.get(base_url+'pr-requisition?token_req_list_gen=1')
                    .then( (response) => {
                        _this.requisition_lists = response.data;
                    });
            },

            addNewItem(){
                this.form.details.push({
                    pod_product_id:'',
                    pod_remarks:'',
                    pod_unit:'',
                    pod_unit_price:0,
                    pod_qty:0,
                    pod_total_price:0,
                    po_order_qty:0,
                });

                var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                jQuery(document).ready(function() {Select2.init()});
            },

            removeItem(index,deletedId){
                var _this = this;
                if(_this.form.details.length > 1){
                    _this.form.details.splice(index, 1);
                    if (deletedId) {
                        _this.form.deleteID.push(deletedId);
                    }
                }
                this.totalQtyAndPrice();
            },

            edit(id) {
                var _this = this;
                axios.get(base_url+'po-receive/'+id+'/edit')
                    .then( (response) => {

                        _this.form  = response.data.data;
                        _this.totalQtyAndPrice();
                        setTimeout(function () {

                            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                            jQuery(document).ready(function() {Select2.init()});

                        },100);
                    });
            },

            documentUpload(){
                var _this = this;
                setTimeout(function () {
                    let uploadedFiles = _this.$refs.files.files;
                    _this.files = [];
                    for(var i = 0; i < uploadedFiles.length; i++) {
                        _this.files.push(uploadedFiles[i]);
                    }
                }, 500, _this);
            },

            update(id,app){
                var _this = this;
                _this.form.approve = app;

                axios.put(base_url+'po-receive/'+id+'/update',
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

            updateDocs(id,app){
                if (this.files.length > 0){
                    let formData = new FormData();
                    formData.append('new_document', this.files[0]);
                    formData.append('old_document', this.form.old_document);
                    formData.append('old_document_storage_link', this.form.old_document_storage_link);
                    formData.append('_method', 'put');


                    axios.post(base_url+'po-receive/'+id+'/update',formData,this.form).then(function(response) {
                        this.files = [];
                        this.form.po_order_docs = response.data.file_path;
                        this.update(id,app);
                    }.bind(this)).catch(function(data) {
                        console.log('error');
                    });
                }else {
                    this.update(id,app);
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

                            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                            jQuery(document).ready(function() {Select2.init()});

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

            duplicateCheck(){
                var no_index = this.form.details.length;
                var details = this.form.details;
                //alert(no_index);
                if (no_index > 1) {
                    /*for (let i = 0; i < no_index; i++) {
                        for (let j = i+1; j < no_index; j++) {
                            if (details[i].pod_product_id == details[j].pod_product_id) {
                                alert("Duplicate Found");
                                details[j].pod_product_id = '';
                                details[j].pod_unit = '';
                                details[j].pod_unit_price = '';

                                var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                                jQuery(document).ready(function() {Select2.init()});
                            }
                        }
                    }*/
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
                     selectPrice = $('option:selected', $(e.target)).attr('unit-price');

                _this.form.details[dataIndex].pod_product_id = selectedItem.val();
                _this.form.details[dataIndex].pod_unit = selectMeasureUnit;
                _this.form.details[dataIndex].pod_unit_price = selectPrice;
                _this.duplicateCheck();
            });

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'sindex'){
                    _this.form.po_supplier_id = selectedType.val();
                }else if(dataIndex == 'rindex'){
                    _this.form.po_requisition_id = selectedType.val();
                    _this.loadDetails();
                }else if(dataIndex == 'po_warehouse_id'){
                    _this.form.po_warehouse_id = selectedType.val();
                }
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

        created(){
            var _this = this;
            _this.req_list();
            _this.edit(_this.editId);
            _this.totalQtyAndPrice();
        }

    }
</script>
