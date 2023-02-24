<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >

            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="po_order_receive_id" class="col-lg-2 col-form-label">Purchase Order <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" id="po_order_receive_id" data-selectField="po_order_receive_id"  v-model="form.po_order_receive_id" >
                            <option v-for="(svalue,sindex) in invoice_lists" :value="svalue.id" > {{svalue.po_order_no}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['po_order_receive_id']">{{ errors['po_order_receive_id'][0] }}</div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Cost Date<span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.po_cost_date" data-dateField="po_cost_date" readonly placeholder="Select date from picker" id="m_datepicker_2" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('po_cost_date'))">{{ (errors.hasOwnProperty('po_cost_date')) ? errors.po_cost_date[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">

                    <label class="col-lg-2 col-form-label" > Credit Account </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="credit_ac_id" v-model="form.credit_ac_id" >
                            <option v-for="(dvalue,dindex) in chart_acc_lists" :value="dvalue.id">{{dvalue.chart_of_accounts_title}} ({{dvalue.chart_of_account_code}})</option>
                        </select>
                        <div class="requiredField" v-if="errors['credit_ac_id']">{{ errors['credit_ac_id'][0] }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.po_cost_narration"  id="narration" rows="2"></textarea>
                    </div>
                </div>
                <br><br>
                <!--begin::Portlet-->

                <div class="form-group m-form__group row">
                    <div class="m-section__content col-lg-12">
                        <div class ="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse" >
                                    <tr>
                                        <th></th>
                                        <th width="35%">Cost Name<span class="requiredField">*</span></th>
                                        <th width="40%">Remarks<span class="requiredField">*</span></th>
                                        <th width="30%">Cost Amount<span class="requiredField">*</span></th>
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
                                    <td>
                                        <select class="form-control m-select2 select2  select-cost-type" v-bind:data-index="index" v-model="details.po_cost_type_id"  >
                                            <option v-for="(value,index) in cost_type_lists" :value="value.id" > {{value.purchase_cost_types_name}}({{value.cost_types_code}})</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.po_cost_type_id']">{{ errors['details.'+index+'.po_cost_type_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.remarks" class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.po_cost_amount"  @input="totalAmount()"  class="form-control form-control-sm m-input text-right">
                                        <div class="requiredField" v-if="errors['details.'+index+'.po_cost_amount']">{{ errors['details.'+index+'.po_cost_amount'][0] }}</div>
                                    </td>
                                    <td>
                                        <a  @click="removeItem(index)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
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
                        <br><br>
                    </div>
                </div>

                <!-- View Requisition Details -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="m-portlet">
                            <div class="m-portlet__body m-portlet__body--no-padding">
                                <div class="m-invoice-2">
                                    <div class="m-invoice__wrapper">
                                        <div class="m-invoice__head">
                                            <div class="m-invoice__container m-invoice__container--centered">
                                                <div class="row invoice-header"  style="padding-top: 24px">
                                                    <div class="col-md-6">
                                                        <div class="db-data">
                                                            <h3 class="print-head-title">Order Detials</h3>
                                                            <p><span style="font-weight: 600">Goods Receive No :</span> <span>{{ view_form.po_order_no }}</span></p>
                                                            <p><span style="font-weight: 600">Receive Date :</span> <span>{{ view_form.po_receive_date }}</span></p>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <!-- <p style="margin-bottom: 62px;">
                                                            <img :src="cinfo.logo" style="width: 150px; height: 50px;float: right">
                                                        </p>
                                                        <span class="m-invoice__desc">
                                                            <span>{{cinfo.name}}</span>
                                                            <span>{{cinfo.address}}</span>
                                                        </span> -->
                                                    </div>
                                                </div>


                                                <div class="m-invoice__items">
                                                    <div class="m-invoice__item ">
                                                        <span class="m-invoice__subtitle">Requisition No</span>
                                                        <span class="m-invoice__text">{{ view_form.po_requisition_id }}</span>
                                                    </div>
                                                    <div class="m-invoice__item text-center">
                                                        <span class="m-invoice__subtitle">Supplier</span>
                                                        <span class="m-invoice__text">{{ view_form.po_supplier_id }}</span>
                                                    </div>
                                                    <div class="m-invoice__item text-center">
                                                        <span class="m-invoice__subtitle">Total Price</span>
                                                        <span class="m-invoice__text">{{ view_form.po_total_price }}</span>
                                                    </div>
                                                    <div class="m-invoice__item text-center">
                                                        <span class="m-invoice__subtitle">Approve Status</span>
                                                        <span class="m-invoice__text"><div class="text-danger" v-if='view_form.approve == 0' > Not Approve</div><div  v-if='view_form.approve == 1'  class="text-success">Approve</div> </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="m-invoice__body m-invoice__body--centered">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Item No.</th>
                                                            <th>Remarks</th>
                                                            <th>Unit</th>
                                                            <th>Quantity</th>
                                                            <th>Unit Price</th>
                                                            <th>Total Price</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(details, index) in view_form.details">
                                                            <td>{{details.get_product ? details.get_product.product_name : 'Not Available'}}</td>
                                                            <td>{{details.pod_remarks}}</td>
                                                            <td>{{details.pr_unit}}</td>
                                                            <td >{{details.pod_qty}}</td>
                                                            <td>{{details.pod_unit_price}}</td>
                                                            <td >{{details.pod_total_price}}</td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="5" align="left">Total Qty </td>
                                                            <td class="m--font-success">{{view_form.po_total_qty}} </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5" align="left">Total </td>
                                                            <td class="m--font-success"> {{view_form.po_total_price}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5" align="left">Net Payable Amount </td>
                                                            <td class="m--font-success"> {{view_form.po_total_price}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="5" align="left">Inwards </td>
                                                            <td class="m--font-success"> {{view_form.total_pricein_word}} </td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="description-section">
                                                <p>
                                                    <span style="font-weight: 600">Narration :</span>
                                                    <span>
                                                        {{ view_form.po_narration }}
                                                    </span>
                                                </p>
                                            </div>

                                            <div style="clear:both;width:100%;height:10px;"></div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                
                <!-- //View Requisition Details -->
            </div>
            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="store(0)" ><i class="la la-check"></i> <span>Save</span></button>
                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="store(1)"><i class="la la-check"></i> <span>Approve</span></button>
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
        props:['invoice_lists','cost_type_lists','chart_acc_lists'],

        data:function(){
            return{
                product_list:false,
                add_form:true,
                edit_form:false,

                form:{
                    po_order_receive_id: '',
                    po_cost_date: '',
                    po_cost_narration: '',
                    po_total_cost: 0,
                    credit_ac_id:'',
                    approve: '',
                    details: [
                        {
                            po_cost_type_id:'',
                            remarks:'',
                            po_cost_amount: '',
                        }
                    ],
                },
                errors: {},
                view_form:{
                    po_order_no: '',
                    po_receive_date: '',
                    po_narration: '',
                    po_supplier_id:'',
                    po_requisition_id:'',

                    po_total_qty: '',
                    po_total_price: '',
                    approve: 'null',
                    details: [
                        {
                            pod_product_id:'',
                            pod_remarks:'',
                            pod_unit:'',
                            pod_unit_price:0,
                            pod_qty:0,
                            pod_total_price:0,
                        }
                    ],
                },                
            };
        },

        methods:{
            addNewItem(){
                var _this = this;
                this.form.details.push({
                    po_cost_type_id:'',
                    remarks:'',
                    po_cost_amount: '',
                });
                _this.initSelect2();
            },

            removeItem(index){
                var _this = this;
                if(_this.form.details.length > 1){
                    _this.form.details.splice(index, 1);
                    setTimeout(function () {
                        _this.initSelect2();
                    },100);
                }
            },

            store(app){
                var _this = this;
                _this.form.approve = app;

                axios.post(base_url+'po-cost-entry/store', this.form).then( (response) => {
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

            loadPurchaseOrderDetails(po_order_receive_id){
                var _this = this;
                axios.get(base_url+'po-receive/'+po_order_receive_id)
                    .then( (response) => {
                        _this.view_form  = response.data.data;
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
                var total_amount = 0;
                var details = this.form.details;
                var length = details.length;
                for(let i = 0; i < length; i++) {
                    total_amount = Number(details[i].po_cost_amount) + total_amount;
                }
                this.form.po_total_cost = total_amount;
            },

            duplicateCheck(){
                var _this = this;
                var no_index = this.form.details.length;
                var details = this.form.details;
                //alert(no_index);
                for(let i=0; i<no_index; i++){
                    for(let j=i+1; j<no_index ; j++){

                        if(details[i].po_cost_type_id == details[j].po_cost_type_id){
                            alert("Duplicate Found");
                            console.log("Duplicate Found");
                            details[j].po_cost_type_id = '';
                            details.splice(j, 1);
                            _this.initSelect2();
                        }
                    }
                }
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

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');

                if(dataIndex == 'po_order_receive_id'){
                    _this.form.po_order_receive_id = selectedItem.val();
                    _this.loadPurchaseOrderDetails( selectedItem.val() );
                }else if(dataIndex == 'credit_ac_id'){
                    _this.form.credit_ac_id = selectedItem.val();
                }
            });

            $('#addComponent').on('change', '.select-cost-type', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.details[dataIndex].po_cost_type_id = selectedType.val();
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
                        _this.form.po_cost_date = '';
                    }else {
                        _this.form.po_cost_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created() {
            var _this = this;
            _this.totalAmount();
        }
    }
</script>
