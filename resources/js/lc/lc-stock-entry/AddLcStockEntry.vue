<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right"  >
            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <div class="col-md-4">
                        <label for="lc_commercial_invoice_id" class="col-form-label">CI No <span class="requiredField">*</span></label>
                        <select class="form-control m-select2 select2 select-ci-no" id="lc_commercial_invoice_id" data-selectField="lc_commercial_invoice_id" v-model="form.lc_commercial_invoice_id" >
                            <option v-for="(svalue,sindex) in ci_lists" :value="svalue.id" :lc_opening_info_id="svalue.lc_opening_info_id" :supplier_id="svalue.get_lc_no ? svalue.get_lc_no.supplier_id : ''">
                                {{ svalue.ci_no }} ({{svalue.get_lc_no ? svalue.get_lc_no.lc_no : ''}})</option>
                        </select>
                        <div class="requiredField" v-if="errors['lc_commercial_invoice_id']">{{ errors['lc_commercial_invoice_id'][0] }}</div>
                    </div>                   
                    <div class="col-md-4">
                        <label for="entry_date" class="col-form-label">Date <span class="requiredField">*</span></label>
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.entry_date" data-dateField="entry_date" readonly placeholder="Select date from picker" id="entry_date" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('entry_date'))">{{ (errors.hasOwnProperty('entry_date')) ? errors.entry_date[0] :'' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label" for="narration">Narration </label>
                        <textarea class="form-control m-input" v-model="form.narration"  id="narration" rows="1"></textarea>
                    </div> 
                    <div class="col-md-4">
                        <label class="col-form-label" for="warehouse_id">Warehouse  <span class="requiredField">*</span> </label>
                        <select class="form-control m-select2 select2" id="warehouse_id" data-selectField="warehouse_id" v-model="form.warehouse_id" >
                            <option v-for="(wvalue,windex) in warehouse_lists" :value="wvalue.id" > {{wvalue.purchase_ware_houses_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['warehouse_id']">{{ errors['warehouse_id'][0] }}</div>                        
                    </div>                    
                </div>
                <br>
                <!--begin::Portlet-->

                <div class="form-group m-form__group row add-manage-section">
                    <div class="m-section__content col-lg-12">
                        <div class ="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse" >
                                <tr>
                                    <th class="width-210">Product <span class="requiredField">*</span></th>
                                    <th>H.S Code</th>
                                    <th class="width-210">Landed Unit Price (BDT) <span class="requiredField">*</span></th>
                                    <th> <mark>Stock Entry Qty <span class="requiredField">*</span></mark> </th>
                                    <th class="width-120">Unit</th>
                                    <th>Gross Weight</th>
                                    <th>Net Weight</th>
                                    <th style="width: 150px;">Total Amount (BDT)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <td class="width-210">
                                        <select class="form-control m-select2 select2  select-product width-210" v-bind:data-index="index" v-model="details.product_id" style="width:150px" disabled >
                                            <option v-for="(value,index) in product_lists" :value="value.id" :get_measure_unit_id="value.measure_unit_id"> {{value.product_name}}</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{ errors['details.'+index+'.product_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.hs_code" class="form-control form-control-sm m-input" placeholder="Enter H.S Code">
                                    </td>
                                    <td style="width: 120px;">
                                        <input style="width: 120px;" type="number" step="any" min="0" v-model="details.unit_price" @input="totalQtyAndPrice()" class="form-control form-control-sm m-input" placeholder="Unit Price" readonly />
                                        <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{ errors['details.'+index+'.unit_price'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="details.qty" @input="totalQtyAndPrice()" class="form-control form-control-sm m-input" placeholder="Quantity" readonly />
                                        <div class="requiredField" v-if="errors['details.'+index+'.qty']">{{ errors['details.'+index+'.qty'][0] }}</div>
                                    </td>
                                    <td class="width-120">
                                        <select class="form-control m-select2 select2 select-measure-unit" v-bind:data-index="index" v-model="details.measure_unit_id" disabled>
                                            <option v-for="(value,index) in measure_unit_lists" :value="value.id" > {{value.measure_unit}}</option>                                      
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="details.gross_weight" @input="totalGrossNetWeight()" class="form-control form-control-sm m-input" placeholder="Gross Weight">
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="details.net_weight" @input="totalGrossNetWeight()" class="form-control form-control-sm m-input" placeholder="Net Weight">
                                    </td>
                                    <td style="width: 150px;">
                                        <input style="width: 150px;" type="text" v-model="details.total_amount" class="form-control form-control-sm m-input text-right" readonly>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right">Total </td>
                                    <td class="">
                                        <input type="text" v-model="form.total_qty" class="form-control form-control-sm m-input" placeholder="Total Qty" readonly>
                                    </td>
                                    <td></td>
                                    <td  class="">
                                        <input type="text" v-model="form.total_gross_weight" class="form-control form-control-sm m-input" placeholder="Total Gross Weight" readonly>
                                    </td>
                                    <td  class="">
                                        <input type="text" v-model="form.total_net_weight" class="form-control form-control-sm m-input" placeholder="Total Net Weight" readonly>
                                    </td>
                                    <td>
                                        <input type="text" v-model="form.total_amount" class="form-control form-control-sm m-input text-right" placeholder="Total Amount" readonly>
                                    </td>
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

                            <button type="submit" class="btn btn-sm btn-info" @click.prevent="store(0)"><i class="la la-save"></i> <span>Save</span></button>
                            <button v-if="permissions.indexOf('lc-stock-entry.approve') !=-1" type="submit" class="btn btn-sm btn-success" @click.prevent="store(1)"><i class="la la-check"></i> <span>Approve</span></button>
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

        props:['permissions','ci_lists','product_lists','measure_unit_lists','warehouse_lists'],

        data:function(){
            return{
                data_list:false,
                add_form:true,
                edit_form:false,

                form:{
                    lc_opening_info_id: '',
                    lc_commercial_invoice_id: '',
                    warehouse_id: '',
                    supplier_id: '',
                    entry_date: '',
                    narration: '',
                    total_qty: '',
                    total_net_weight: '',
                    total_gross_weight: '',
                    total_amount: '',
                    status: 1,
                    approve: 0,
                    details: [
                        {
                            product_id:'',
                            hs_code:'',
                            unit_price: '',
                            qty: '',
                            measure_unit_id: '',
                            gross_weight: '',
                            net_weight: '',
                            total_amount:0,
                        }
                    ],


                },
                errors: {},
            };
        },

        methods:{

            store:function(approval){
                var _this = this;
                _this.form.approve = approval;
                axios.post(base_url+'lc-stock-entry', this.form).then( (response) => {
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

            loadDetails(){
                var _this = this;
                var id= this.form.lc_commercial_invoice_id;

                axios.get(base_url+"commercial-invoice-entry/"+id+"/ci-product-list").then((response) => {

                    if(response.data.length > 0){
                        this.form.details = response.data;

                        setTimeout(function () {
                            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                            jQuery(document).ready(function() {
                                Select2.init();
                                _this.updateLandedPrice();
                                _this.totalQtyAndPrice();
                                _this.totalGrossNetWeight();
                            });
                        },250);
                    }
                });
            },

            updateLandedPrice(){
                var _this = this;
                var details = _this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    _this.form.details[i].unit_price = _this.form.details[i].landed_unit_price;
                }
            },

            totalQtyAndPrice(){
                var total_qty = 0;
                var total_amount = 0;
                var total_amount_taka = 0;
                var total_price = 0;
                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    total_price = Number(details[i].qty) * Number(details[i].unit_price);
                    this.form.details[i].total_amount = total_price;
                    total_qty = Number(details[i].qty) + total_qty;
                    total_amount = total_price + total_amount;
                }
                this.form.total_qty = total_qty;
                this.form.total_amount = total_amount;
                this.form.amount = total_amount;
            },

            totalGrossNetWeight(){
                var total_gross_weight = 0;
                var total_net_weight = 0;
                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    total_gross_weight = Number(details[i].gross_weight) + total_gross_weight;
                    total_net_weight = Number(details[i].net_weight) + total_net_weight;
                }
                this.form.total_gross_weight = total_gross_weight;
                this.form.total_net_weight = total_net_weight;
            },

            duplicateCheck(selectedValue){
                var no_index = this.form.details.length;
                for(let i=0; i<no_index; i++){
                    if(this.form.details[i].product_id == selectedValue){
                        alert("Duplicate Found");
                    }
                }
            },

        },

        mounted(){

            var _this = this;

            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
            jQuery(document).ready(function() {Select2.init()});

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if( selectField == 'lc_commercial_invoice_id' ){
                    _this.form.lc_commercial_invoice_id= selectedItem.val();

                    var get_lc_id = $('option:selected', this).attr('lc_opening_info_id'); 
                    _this.form.lc_opening_info_id= get_lc_id;

                    var supplier_id = $('option:selected', this).attr('supplier_id'); 
                    _this.form.supplier_id= supplier_id; 

                    _this.loadDetails();
                }else if( selectField == 'warehouse_id'){
                    _this.form.warehouse_id= selectedItem.val();
                }
            });
            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.duplicateCheck( selectedItem.val() );
                _this.form.details[dataIndex].product_id = selectedItem.val();
                _this.form.details[dataIndex].measure_unit_id = $('option:selected', this).attr('get_measure_unit_id');

                var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                jQuery(document).ready(function() {Select2.init()});
            });

            $('#addComponent').on('change', '.select-measure-unit', function (e) {
                var selectedItem = $(this),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.details[dataIndex].measure_unit_id = selectedItem.val();
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    var selectField = $(ev.currentTarget).attr('data-dateField');
                    if(ev.date == undefined){
                        _this.form.entry_date = '';
                    }else if(selectField == 'entry_date'){
                        _this.form.entry_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });

        },

        created() {
            var _this = this;
            _this.totalQtyAndPrice();
            _this.form.entry_date = moment().format('DD/MM/YYYY');
        }

    }
</script>