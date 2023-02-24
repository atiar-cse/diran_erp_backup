<template>
    <div>
        <!--begin::Form-->
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="po_return_no" class="col-lg-2 col-form-label">Purchase Return No </label>
                    <div class="col-lg-4">
                        <input type="text" disabled id="po_return_no"  v-model="form.po_return_no" class="form-control form-control-sm m-input" placeholder="Enter Purchase Return No">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('po_return_no'))">{{ (errors.hasOwnProperty('po_return_no')) ? errors.po_return_no[0] :'' }}</div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Return Date </label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.po_return_date" data-dateField="po_return_date" readonly placeholder="Select date from picker" id="m_datepicker_2" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('po_return_date'))">{{ (errors.hasOwnProperty('po_return_date')) ? errors.po_return_date[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label">Supplier </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-indexRtn="po_rtn_supplier_id" v-model="form.po_rtn_supplier_id" >
                            <option v-for="(svalue,sindex) in supplier_lists" :value="svalue.id" > {{svalue.purchase_supplier_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['po_rtn_supplier_id']">{{ errors['po_rtn_supplier_id'][0] }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label">Warehouse</label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-indexRtn="po_rtn_warehouse_id" v-model="form.po_rtn_warehouse_id" >
                            <option v-for="(wvalue,windex) in warehouse_lists" :value="wvalue.id" > {{wvalue.purchase_ware_houses_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['po_rtn_warehouse_id']">{{ errors['po_rtn_warehouse_id'][0] }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label">Return Issue Type </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-indexRtn="po_rtn_issue_type_id" v-model="form.po_rtn_issue_type_id" >
                            <option v-for="(tvalue,tindex) in issue_type_lists" :value="tvalue.id" > {{tvalue.purchase_return_types_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['po_rtn_issue_type_id']">{{ errors['po_rtn_issue_type_id'][0] }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.po_rtn_narration"  id="narration" rows="2"></textarea>
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
                                    <th class="width-210">Product </th>
                                    <th>Remarks</th>
                                    <th>Measure Unit</th>
                                    <th>Unit Price</th>
                                    <th>Qty</th>
                                    <th class="width-210">Total Price</th>
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
                                        <select class="form-control m-select2 select2  select-product width-210" v-bind:data-index="index" v-model="details.po_rtnd_product_id" >
                                            <option v-for="(value,index) in product_lists" :value="value.id" :measure-unit="value.measure_unit"  :unit-price="value.buying_price" > {{value.product_name}}</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.po_rtnd_product_id']">{{ errors['details.'+index+'.po_rtnd_product_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.po_rtnd_remarks" class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.po_rtnd_messure_unit" class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.po_rtnd_unit_price" @keyup="totalAmount()" class="form-control form-control-sm m-input" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.po_rtnd_unit_price']">{{ errors['details.'+index+'.po_rtnd_unit_price'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.po_rtnd_qty" @keyup="totalAmount()" class="form-control form-control-sm m-input" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.po_rtnd_qty']">{{ errors['details.'+index+'.po_rtnd_qty'][0] }}</div>
                                    </td>
                                    <td class="width-160">
                                        <input type="text" v-model="details.po_rtnd_total_price" class="form-control form-control-sm m-input width-160">
                                        <div class="requiredField" v-if="errors['details.'+index+'.po_rtnd_total_price']">{{ errors['details.'+index+'.po_rtnd_total_price'][0] }}</div>
                                    </td>
                                    <td>
                                        <a  @click="removeItem(index,details.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Total </td>
                                    <td class="">
                                        <input type="text" v-model="form.po_rtn_total_qty" class="form-control form-control-sm m-input" readonly>
                                    </td>
                                    <td  class="">
                                        <input type="text" v-model="form.po_rtn_total_price" class="form-control form-control-sm m-input" readonly>
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
                            <button v-if="permissions.indexOf('po-return.approve') !=-1" type="submit" class="btn btn-sm btn-success" @click.prevent="update(form.id,1)" ><i class="la la-check"></i> <span>Approve</span></button>
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

        props:['permissions','editId','supplier_lists','warehouse_lists','issue_type_lists','product_lists',],
        data:function(){
            return{
                product_list:false,
                add_form:false,
                edit_form:true,

                form:{
                    po_return_no: '',
                    po_return_date: '',
                    po_rtn_supplier_id: '',
                    po_rtn_warehouse_id: '',
                    po_rtn_issue_type_id: '',
                    po_rtn_narration: '',
                    po_rtn_docs: '',
                    po_rtn_total_qty: '',
                    po_rtn_total_price: '',
                    approve:'',
                    deleteID: [],
                    details: [
                        {
                            po_rtnd_product_id:'',
                            po_rtnd_remarks:'',
                            po_rtnd_messure_unit:'',
                            po_rtnd_unit_price:'',
                            po_rtnd_qty:'',
                            po_rtnd_total_price:''
                        }
                    ],
                },
                errors: {},
            };
        },

        methods:{
            addNewItem(){
                this.form.details.push({
                    po_rtnd_product_id:'',
                    po_rtnd_remarks:'',
                    po_rtnd_messure_unit:'',
                    po_rtnd_unit_price:'',
                    po_rtnd_qty:'',
                    po_rtnd_total_price:''
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
                _this.totalAmount();
            },

            edit(id) {
                var _this = this;
                axios.get(base_url+'po-return/'+id+'/edit')
                    .then( (response) => {
                        _this.form  = response.data.data;
                        setTimeout(function () {
                            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                            jQuery(document).ready(function() {Select2.init()});
                        },100);
                    });
            },

            update(id,app){

                var _this = this;
                _this.form.approve = app;

                axios.put(base_url+'po-return/'+id+'/update',
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

            totalAmount(){
                var _this = this;
                var total_qty = 0;
                var total_amount = 0;
                var total_price = 0;
                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    total_price = Number(details[i].po_rtnd_qty) * Number(details[i].po_rtnd_unit_price);
                    _this.form.details[i].po_rtnd_total_price = total_price.toFixed(2);
                    total_qty = Number(details[i].po_rtnd_qty) + total_qty;
                    total_amount = total_price + total_amount;
                }
                _this.form.po_rtn_total_qty = total_qty;
                _this.form.po_rtn_total_price = total_amount.toFixed(2);
            },

            duplicateCheck(){
                var _this = this;
                var no_index = _this.form.details.length;
                var details  = _this.form.details;

                /*for(let i=0; i<no_index; i++){
                    for(let j=i+1; j<no_index ; j++){
                        if(details[i].po_rtnd_product_id == details[j].po_rtnd_product_id){
                            alert("Duplicate Found");
                            details[j].po_rtnd_product_id = '';
                            details[j].po_rtnd_messure_unit = '';
                            details[j].po_rtnd_unit_price = '';
                            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                            jQuery(document).ready(function() {Select2.init()});
                        }
                    }
                }*/
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
                    this.form.po_rtn_docs =e.target.result;
                };
                reader.readAsDataURL(file);
            }

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
                _this.form.details[dataIndex].po_rtnd_product_id = selectedItem.val();
                _this.form.details[dataIndex].po_rtnd_messure_unit = selectMeasureUnit;
                _this.form.details[dataIndex].po_rtnd_unit_price = selectPrice;
                _this.duplicateCheck();
            });

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-indexRtn');

                if(dataIndex == 'po_rtn_supplier_id'){
                    _this.form.po_rtn_supplier_id = selectedItem.val();
                }else if(dataIndex == 'po_rtn_warehouse_id'){
                    _this.form.po_rtn_warehouse_id = selectedItem.val();
                }else if(dataIndex == 'po_rtn_issue_type_id'){
                    _this.form.po_rtn_issue_type_id = selectedItem.val();
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
                        _this.form.po_return_date = '';
                    }else {
                        _this.form.po_return_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });

        },

        created(){
            var _this = this;
            _this.edit(_this.editId);
            _this.totalAmount();
        }

    }
</script>
