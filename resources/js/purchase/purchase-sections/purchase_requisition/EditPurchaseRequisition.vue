<template>
    <div>
        <!--begin::Form-->
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="purchase_requisition_no" class="col-lg-2 col-form-label">Requisition No<span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text"  id="purchase_requisition_no"  v-model="form.purchase_requisition_no" class="form-control form-control-sm m-input" placeholder="Enter requisition no">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('purchase_requisition_no'))">{{ (errors.hasOwnProperty('purchase_requisition_no')) ? errors.purchase_requisition_no[0] :'' }}</div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Requisition Date<span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.purchase_requisition_date" data-dateField="purchase_requisition_date" readonly placeholder="Select date from picker" id="m_datepicker_2" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('purchase_requisition_date'))">{{ (errors.hasOwnProperty('purchase_requisition_date')) ? errors.purchase_requisition_date[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.purchase_requisition_narration"  id="narration" rows="2"></textarea>
                    </div>
                    <label class="col-lg-2 col-form-label" > Supervisior Narration</label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.purchase_requisition_supervisor_narration"  id="supervisor_narration" rows="2"
                                  placeholder="Supervisor write here something against requisition after factory change the requisition"></textarea>
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
                                    <th class="width-210">Product Name<span class="requiredField">*</span></th>
                                    <th>Remarks</th>
                                    <th class="width-210">Product Type<span class="requiredField">*</span></th>
                                    <th>Unit</th>
                                    <th>Qty<span class="requiredField">*</span></th>
                                    <th>Price<span class="requiredField">*</span></th>
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
                                        <select class="form-control m-select2 select2  select-product width-210" v-bind:data-index="index" v-model="details.pr_product_id"   >
                                            <option v-for="(value,index) in product_lists" :value="value.id" :measure-unit="value.measure_unit" :unit-price="value.buying_price" > {{value.product_name}}</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.pr_product_id']">{{ errors['details.'+index+'.pr_product_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.pr_remarks" class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td class="width-210">
                                        <select class="form-control m-select2 select2 select-product_type width-210" v-bind:data-index="index" v-model="details.product_type">
                                            <option v-for="(value,index) in enum_val" :value="value"> {{value}}</option>
                                            <div class="requiredField" v-if="errors['details.'+index+'.product_type']">{{ errors['details.'+index+'.product_type'][0] }}</div>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.pr_unit" class="form-control form-control-sm m-input" placeholder="" readonly>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.pr_qty" @input="totalQtyAndPrice()" class="form-control form-control-sm m-input" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.pr_qty']">{{ errors['details.'+index+'.pr_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.pr_unit_price" @input="totalQtyAndPrice()" class="form-control form-control-sm m-input">
                                        <div class="requiredField" v-if="errors['details.'+index+'.pr_unit_price']">{{ errors['details.'+index+'.pr_unit_price'][0] }}</div>
                                    </td>
                                    <td class="width-160">
                                        <input type="text" v-model="details.pr_total_price" class="form-control form-control-sm m-input width-160" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.pr_total_price']">{{ errors['details.'+index+'.pr_total_price'][0] }}</div>
                                    </td>

                                    <td>
                                        <a  @click="removeItem(index,details.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Total Qty</td>
                                    <td class="">
                                        <input type="text" v-model="form.purchase_requisition_total_qty" class="form-control form-control-sm m-input" readonly>
                                    </td>
                                    <td class="text-right">Total Amount</td>
                                    <td  class="">
                                        <input type="text" v-model="form.purchase_requisition_total_price" class="form-control form-control-sm m-input" readonly>
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
                            <button v-if="permissions.indexOf('pr-requisition.approve') !=-1" type="submit" class="btn btn-sm btn-success" @click.prevent="update(form.id,1)" ><i class="la la-check"></i> <span>Approve</span></button>
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

        props:['permissions','editId','product_lists','enum_val'],

        data:function(){
            return{
                data_list:false,
                add_form:false,
                edit_form:true,

                form:{
                    purchase_requisition_no: '',
                    purchase_requisition_date: '',
                    purchase_requisition_narration: '',
                    purchase_requisition_supervisor_narration: '',
                    purchase_requisition_total_qty: '',
                    purchase_requisition_total_price: '',
                    approve: '',
                    deleteID: [],
                    details: [
                        {
                            pr_product_id: '',
                            product_type:this.enum_val,
                            pr_remarks: '',
                            pr_qty: 0,
                            pr_unit: '',
                            pr_unit_price: 0,
                            pr_total_price: 0,
                        }
                    ],
                },
                errors: {},
            };
        },

        methods:{
            addNewItem(){
                this.form.details.push({
                    pr_product_id: '',
                    product_type:this.enum_val,
                    pr_remarks: '',
                    pr_qty: 0,
                    pr_unit: '',
                    pr_unit_price: 0,
                    pr_total_price: 0,
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
                axios.get(base_url+'pr-requisition/'+id+'/edit')
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
                axios.put(base_url+'pr-requisition/'+id+'/update',
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
                    total_price = Number(details[i].pr_qty) * Number(details[i].pr_unit_price);
                    this.form.details[i].pr_total_price = total_price.toFixed(2);
                    total_qty = Number(details[i].pr_qty) + total_qty;
                    total_amount = total_price + total_amount;
                }
                this.form.purchase_requisition_total_qty = total_qty;
                this.form.purchase_requisition_total_price = total_amount.toFixed(2);
            },

            duplicateCheck(){
                var no_index = this.form.details.length;
                var details = this.form.details;
                /*for(let i=0; i<no_index; i++){
                    for(let j=i+1; j<no_index ; j++){
                        if(details[i].pr_product_id == details[j].pr_product_id){
                            alert("Duplicate Found");
                            details[j].pr_product_id = '';
                            details[j].pr_unit = '';
                            details[j].pr_unit_price = '';
                            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                            jQuery(document).ready(function() {Select2.init()});
                        }
                    }
                }*/
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

                if(selectPrice){selectPrice=selectPrice;}else{selectPrice='0';}
                _this.form.details[dataIndex].pr_product_id = selectedItem.val();
                _this.form.details[dataIndex].pr_unit = selectMeasureUnit;
                _this.form.details[dataIndex].pr_unit_price = selectPrice;
                _this.duplicateCheck();
            });

            $('#editComponent').on('change', '.select-product_type', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.details[dataIndex].product_type = selectedType.val();
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.purchase_requisition_date = '';
                    }else {
                        _this.form.purchase_requisition_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });

        },

        created(){
            var _this = this;
            _this.edit(_this.editId);
            _this.totalQtyAndPrice();
        }

    }
</script>
