<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right"  >
            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <div class="col-md-4">
                        <label for="lc_opening_info_id" class="col-form-label">LC No <span class="requiredField">*</span></label>
                        <select class="form-control m-select2 select2 select-lc-no" id="lc_opening_info_id" data-selectField="lc_opening_info_id" v-model="form.lc_opening_info_id" >
                            <option v-for="(svalue,sindex) in lc_lists" 
                                :value="svalue.id" 
                                :get_pi_no="svalue.get_pi_no ? svalue.get_pi_no.pi_no : ''" 
                                :ci_margin="svalue.get_cnf_margin ? svalue.get_cnf_margin.margin_value : ''"
                                :ci_opening_charge="svalue.lc_opening_charges"  
                                :ci_bank_expenses="svalue.lc_bank_expenses"  
                                :ci_insurance="svalue.insurance_amount"  
                            > {{ svalue.lc_no }}</option>
                        </select>
                        <div class="requiredField" v-if="errors['lc_opening_info_id']">{{ errors['lc_opening_info_id'][0] }}</div>
                    </div>
                    <div class="col-md-4">
                        <label for="ci_no" class="col-form-label">CI B/L No <span class="requiredField">*</span></label>
                        <input type="text"  id="ci_no"  v-model="form.ci_no" class="form-control form-control-sm m-input" placeholder="Enter CI B/L No" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('ci_no'))">{{ (errors.hasOwnProperty('ci_no')) ? errors.ci_no[0] :'' }}</div>
                    </div>
                    <div class="col-md-4">
                        <label for="shipping_mode" class="col-form-label">Shipping Mode <span class="requiredField">*</span></label>
                        <select class="form-control select2" id="shipping_mode"  v-model="form.shipping_mode" data-selectField="shipping_mode">
                            <option value="1"> Truck</option>
                            <option value="2"> Sea</option>
                        </select>
                        <div class="requiredField" v-if="errors['shipping_mode']">{{ errors['shipping_mode'][0] }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <div class="col-md-4">
                        <label for="receive_date" class="col-form-label">Receive Date </label>
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.receive_date" data-dateField="receive_date" readonly placeholder="Select date from picker" id="receive_date" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="ci_date" class="col-form-label">CI B/L Date </label>
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.ci_date" data-dateField="ci_date" readonly placeholder="Select date from picker" id="ci_date" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label" for="cnf_agent">C&F Agent <span class="requiredField">*</span></label>
                        <select class="form-control m-select2 select2  select-cnf-agent" id="cnf_agent" data-selectField="cnf_agent" v-model="form.cnf_agent" >
                            <option v-for="(svalue,sindex) in cnf_agent_list" :value="svalue.id" > {{svalue.cnf_agent_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['cnf_agent']">{{ errors['cnf_agent'][0] }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <div class="col-md-4">
                        <label for="invoice_no" class="col-form-label">Invoice No </label>
                        <input type="text"  id="invoice_no"  v-model="form.invoice_no" class="form-control form-control-sm m-input" placeholder="Invoice No" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="ci_bill_of_entry_no" class="col-form-label">CI Bill of Entry No </label>
                        <input type="text"  id="ci_bill_of_entry_no"  v-model="form.ci_bill_of_entry_no" class="form-control form-control-sm m-input" placeholder="Enter CI Bill of Entry No">
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label" for="exp_no">Exp. No </label>
                        <input type="text"  id="exp_no"  v-model="form.exp_no" class="form-control form-control-sm m-input" placeholder="Enter Exp. No">
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <div class="col-md-4">
                        <label for="invoice_date" class="col-form-label">Invoice Date </label>
                        <div class="input-group date">
                            <div class="input-group date">
                                <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.invoice_date" data-dateField="invoice_date" readonly placeholder="Select date from picker" id="invoice_date" />
                                <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="ci_bill_entry_date" class="col-form-label">CI Bill of Entry Date <span class="requiredField">*</span></label>
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.ci_bill_entry_date" data-dateField="ci_bill_entry_date" readonly placeholder="Select date from picker" id="ci_bill_entry_date" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('ci_bill_entry_date'))">{{ (errors.hasOwnProperty('ci_bill_entry_date')) ? errors.ci_bill_entry_date[0] :'' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label" for="eta_date">ETA Date </label>
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.eta_date" data-dateField="eta_date" readonly placeholder="Select date from picker" id="eta_date" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <div class="col-md-4">
                        <label for="invoice_amount" class="col-form-label">Invoice Amount </label>
                        <input type="number" step="any" min="0"  id="invoice_amount"  v-model="form.invoice_amount" class="form-control form-control-sm m-input" placeholder="Enter Invoice Amount">
                    </div>
                    <div class="col-md-4">
                        <label for="vassel_name" class="col-form-label">Vassel Name</label>
                        <input type="text"  id="vassel_name"  v-model="form.vassel_name" class="form-control form-control-sm m-input" placeholder="Enter Vassel Name">
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label" for="shipping_line">Shipping Line </label>
                        <input type="text"  id="shipping_line"  v-model="form.shipping_line" class="form-control form-control-sm m-input" placeholder="Enter Shipping Line">
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <div class="col-md-4">
                        <label for="depart_from" class="col-form-label">Depart From </label>
                        <input type="text"  id="depart_from"  v-model="form.depart_from" class="form-control form-control-sm m-input" placeholder="Enter Depart From">
                    </div>
                    <div class="col-md-4">
                        <label for="arrived_port" class="col-form-label">Arrived Port</label>
                        <input type="text"  id="arrived_port"  v-model="form.arrived_port" class="form-control form-control-sm m-input" placeholder="Enter Arrived Port">
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label" for="present_destination">Present Destination </label>
                        <input type="text"  id="present_destination"  v-model="form.present_destination" class="form-control form-control-sm m-input" placeholder="Enter Present Destination">
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label" for="origin">Origin </label>
                        <select class="form-control m-select2 select2  select-origin" id="origin" data-selectField="origin" v-model="form.origin" >
                            <option v-for="(svalue,sindex) in country_lists" :value="svalue.id" > {{svalue.country_name}}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="col-form-label" for="narration">Narration </label>
                        <textarea class="form-control m-input" v-model="form.narration"  id="narration" rows="1"></textarea>
                    </div>                    
                </div>

                <hr>
                <div class="form-group m-form__group row">
                    <div class="col-md-3">
                        <label class="col-form-label" for="goods_name">Goods Name </label>
                        <textarea class="form-control m-input" v-model="form.goods_name"  id="goods_name" rows="1">RAW MATERIALS</textarea>
                    </div>  
                    <div class="col-md-3">
                        <label for="ci_delivery_type" class="col-form-label">Delivery Type <span class="requiredField">*</span></label>
                        <select class="form-control select2" id="ci_delivery_type"  v-model="form.ci_delivery_type" data-selectField="ci_delivery_type">
                            <option value="1"> Full (All PI Products)</option>
                            <option value="2"> Partial PI Products</option>
                        </select>
                        <div class="requiredField" v-if="errors['ci_delivery_type']">{{ errors['ci_delivery_type'][0] }}</div>
                    </div>

                    <div class="col-md-3">
                        <label for="ci_margin" class="col-form-label">Margin Amount <span class="requiredField">*</span></label>
                        <input type="text"  id="ci_margin"  v-model="form.ci_margin" class="form-control form-control-sm m-input" placeholder="C&F VALUE">
                        <div class="requiredField" v-if="errors['ci_margin']">{{ errors['ci_margin'][0] }}</div>
                    </div>
                    <div class="col-md-3">
                        <label for="ci_opening_charge" class="col-form-label">LC Opening Charge</label>
                        <input type="text"  id="ci_opening_charge"  v-model="form.ci_opening_charge" class="form-control form-control-sm m-input" placeholder="CI Opening Charge">
                    </div> 
                    <div class="col-md-3">
                        <label for="ci_bank_expenses" class="col-form-label">Bank Expenses<span class="requiredField">*</span></label>
                        <input type="text"  id="ci_bank_expenses"  v-model="form.ci_bank_expenses" class="form-control form-control-sm m-input" placeholder="Bank Expenses">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('ci_bank_expenses'))">{{ (errors.hasOwnProperty('ci_bank_expenses')) ? errors.ci_bank_expenses[0] :'' }}</div>
                    </div>
                    <div class="col-md-3">
                        <label for="ci_insurance" class="col-form-label">Insurance<span class="requiredField">*</span></label>
                        <input type="text"  id="ci_insurance"  v-model="form.ci_insurance" class="form-control form-control-sm m-input" placeholder="Insurance">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('ci_insurance'))">{{ (errors.hasOwnProperty('ci_insurance')) ? errors.ci_insurance[0] :'' }}</div>
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
                                    <th></th>
                                    <th class="width-210">Product <span class="requiredField">*</span></th>
                                    <th>H.S Code</th>
                                    <th>LC Unit Price (BDT) <span class="requiredField">*</span></th>
                                    <th>Qty <span class="requiredField">*</span></th>
                                    <th class="width-210">Unit</th>
                                    <th>Gross Weight</th>
                                    <th>Net Weight <span class="requiredField">*</span></th>
                                    <th class="width-160">Total Amount (BDT)</th>
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
                                        <select class="form-control m-select2 select2  select-product width-210" v-bind:data-index="index" v-model="details.product_id" style="width:100%" >
                                            <option v-for="(value,index) in product_lists" 
                                                :value="value.id" 
                                                :get_measure_unit_id="value.measure_unit_id" 
                                            > {{value.product_name}}</option>                                
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{ errors['details.'+index+'.product_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.hs_code" class="form-control form-control-sm m-input width-100" placeholder="Enter H.S Code">
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="details.unit_price" @input="totalQtyAndPrice()" class="form-control form-control-sm m-input width-100" placeholder="Unit Price">
                                        <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{ errors['details.'+index+'.unit_price'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="details.qty" v-if="details.qty > 0"  @input="totalQtyAndPrice()" class="form-control form-control-sm m-input width-100" placeholder="Quantity">
                                        <input type="number" step="any" min="0" v-model="details.qty" v-else readonly  @input="totalQtyAndPrice()" class="form-control form-control-sm m-input width-100" placeholder="Quantity">
                                        <div class="requiredField" v-if="errors['details.'+index+'.qty']">{{ errors['details.'+index+'.qty'][0] }}</div>
                                    </td>
                                    <td class="width-210">
                                        <select class="form-control m-select2 select2 select-measure-unit width-210" v-bind:data-index="index" v-model="details.measure_unit_id" style="width:100%" >
                                            <option v-for="(value,index) in measure_unit_lists" :value="value.id" > {{value.measure_unit}}</option>                                      
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="details.gross_weight" @input="totalGrossNetWeight()" class="form-control form-control-sm m-input width-100" placeholder="Gross Weight">
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="details.net_weight" @input="totalGrossNetWeight()" class="form-control form-control-sm m-input width-100" placeholder="Net Weight">
                                        <div class="requiredField" v-if="errors['details.'+index+'.net_weight']">{{ errors['details.'+index+'.net_weight'][0] }}</div>
                                    </td>
                                    <td class="width-160">
                                        <input type="text" v-model="details.total_amount" class="form-control form-control-sm m-input text-right width-160" readonly>
                                    </td>

                                    <td>
                                        <a  @click="removeItem(index)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right">Total </td>
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

                            <button type="submit" class="btn btn-sm btn-info" @click.prevent="store(0)"><i class="la la-save"></i> <span>Save</span></button>
                            <button v-if="permissions.indexOf('commercial-invoice-entry.approve') !=-1" type="submit" class="btn btn-sm btn-success" @click.prevent="store(1)"><i class="la la-check"></i> <span>Approve</span></button>
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

        props:['permissions','lc_lists','cnf_agent_list','product_lists','country_lists','measure_unit_lists'],

        data:function(){
            return{
                product_list:false,
                add_form:true,
                edit_form:false,

                form:{
                    lc_opening_info_id: '',
                    ci_no: '',
                    ci_date: '',
                    ci_bill_of_entry_no:'',
                    ci_bill_entry_date:'',
                    invoice_no:'',
                    invoice_date:'',
                    invoice_amount:'',
                    eta_date: '',
                    cnf_agent: '',
                    shipping_mode: '',
                    exp_no: '',
                    vassel_name: '',
                    shipping_line: '',
                    receive_date: '',
                    depart_from: '',
                    arrived_port: '',
                    present_destination: '',
                    origin: '',
                    narration: '',
                    goods_name: '',
                    total_qty: '',
                    total_net_weight: '',
                    total_gross_weight: '',
                    total_amount: '',
                    status: 1,
                    approve: 0,

                    ci_delivery_type: 1,
                    ci_margin: '',
                    ci_opening_charge: '',
                    ci_bank_expenses: '',
                    ci_insurance: '',
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

            ciNoGenerate(){
                var _this = this;
                axios.get(base_url+'ci-no-generate')
                    .then( (response) => {
                        _this.form.ci_no = response.data;
                    });
            },

            addNewItem(){
                this.form.details.push({
                    product_id:'',
                    hs_code:'',
                    unit_price: '',
                    qty: '',
                    measure_unit_id: '',
                    gross_weight: '',
                    net_weight: '',
                    total_amount:0,
                });
                var Select2= {
                    init:function() {
                        $(".select2").select2( {
                                placeholder: "Please select an option"
                            }
                        )
                    }
                };
                jQuery(document).ready(function() {
                        Select2.init()
                    }
                );
            },

            removeItem(index){
                var _this = this;
                if(_this.form.details.length > 1){
                    _this.form.details.splice(index, 1);
                    setTimeout(function () {
                        var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                        jQuery(document).ready(function() {Select2.init();});
                    },100);


                    _this.totalQtyAndPrice();
                    _this.totalGrossNetWeight();                    
                }
            },

            store:function(approval){
                var _this = this;
                _this.form.approve = approval;
                axios.post(base_url+'commercial-invoice-entry', this.form).then( (response) => {
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
                var id= this.form.lc_opening_info_id;

                axios.get(base_url+"commercial-invoice-entry/"+id+"/lc-product-list").then((response) => {

                    if(response.data.length > 0){
                        this.form.details = response.data;

                        setTimeout(function () {
                            var Select2= {
                                init:function() {
                                    $(".select2").select2( {
                                            placeholder: "Please select an option"
                                        }
                                    )
                                }
                            };
                            jQuery(document).ready(function() {
                                    Select2.init();
                                    
                                    _this.totalQtyAndPrice();
                                }
                            );
                        },100);
                    }
                });
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
                this.form.total_amount = total_amount.toFixed(2);
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

            var Select2= {
                init:function() {
                    $(".select2").select2( {
                            placeholder: "Please select an option"
                        }
                    )
                }
            };
            jQuery(document).ready(function() {
                    Select2.init()
                }
            );

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if( selectField == 'lc_opening_info_id' ){
                    _this.form.lc_opening_info_id= selectedItem.val();
                    _this.loadDetails();
                    var get_pi_no = $('option:selected', this).attr('get_pi_no'); 
                    _this.form.invoice_no= get_pi_no; 

                    _this.form.ci_margin= $('option:selected', this).attr('ci_margin');   
                    _this.form.ci_opening_charge= $('option:selected', this).attr('ci_opening_charge');   
                    _this.form.ci_bank_expenses= $('option:selected', this).attr('ci_bank_expenses');   
                    _this.form.ci_insurance= $('option:selected', this).attr('ci_insurance');   

                }else if(selectField == 'shipping_mode'){
                    _this.form.shipping_mode= selectedItem.val();
                }else if(selectField == 'cnf_agent'){
                    _this.form.cnf_agent= selectedItem.val();
                }else if(selectField == 'invoice_no'){
                    _this.form.invoice_no= selectedItem.val();
                }else if(selectField == 'origin'){
                    _this.form.origin= selectedItem.val();
                }else if(selectField == 'ci_delivery_type'){
                    _this.form.ci_delivery_type= selectedItem.val();
                }
            });
            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.duplicateCheck( selectedItem.val() );
                _this.form.details[dataIndex].product_id = selectedItem.val();

                _this.form.details[dataIndex].measure_unit_id = $('option:selected', this).attr('get_measure_unit_id');
                var Select2= {
                    init:function() {
                        $(".select2").select2( {
                                placeholder: "Please select an option"
                            }
                        )
                    }
                };
                jQuery(document).ready(function() {
                        Select2.init()
                    }
                );                
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
                        if (selectField == 'receive_date') {
                            _this.form.receive_date = '';
                        } else if(selectField == 'ci_date'){
                            _this.form.ci_date = '';
                        } else if(selectField == 'invoice_date'){
                            _this.form.invoice_date = '';
                        } else if(selectField == 'ci_bill_entry_date'){
                            _this.form.ci_bill_entry_date = '';
                        } else if(selectField == 'eta_date'){
                            _this.form.eta_date = '';
                        }
                    }else if(selectField == 'receive_date'){
                        _this.form.receive_date = moment(ev.date).format('DD/MM/YYYY');
                    }else if(selectField == 'ci_date'){
                        _this.form.ci_date = moment(ev.date).format('DD/MM/YYYY');
                    }else if(selectField == 'invoice_date'){
                        _this.form.invoice_date = moment(ev.date).format('DD/MM/YYYY');
                    }else if(selectField == 'ci_bill_entry_date'){
                        _this.form.ci_bill_entry_date = moment(ev.date).format('DD/MM/YYYY');
                    }else if(selectField == 'eta_date'){
                        _this.form.eta_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });

        },

        created() {
            var _this = this;
            _this.ciNoGenerate();
            _this.totalQtyAndPrice();
            _this.form.ci_bill_entry_date = moment().format('DD/MM/YYYY');
        }

    }
</script>
