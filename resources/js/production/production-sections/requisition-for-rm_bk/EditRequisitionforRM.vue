<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >

            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="requisition_no" class="col-lg-2 col-form-label">Requisition No </label>
                    <div class="col-lg-4">
                        <input type="text" id="requisition_no" v-model="form.rm_requisition_no" class="form-control form-control-sm m-input" placeholder="Enter requisition no" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('rm_requisition_no'))">{{ (errors.hasOwnProperty('rm_requisition_no')) ? errors.rm_requisition_no[0] :'' }}</div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Requisition Date </label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.requisition_date" data-dateField="requisition_date" readonly placeholder="Select date from picker" id="m_datepicker_2" autocomplete="off"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('requisition_date'))">{{ (errors.hasOwnProperty('requisition_date')) ? errors.requisition_date[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.narration"  id="narration" rows="2"></textarea>
                    </div>
                    <label for="warehouse_id" class="col-lg-2 col-form-label">Warehouse </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="warehouse_id" id="warehouse_id" v-model="form.warehouse_id" >
                            <option v-for="(value,index) in warehouse_lists" :value="value.id" > {{value.purchase_ware_houses_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['warehouse_id']">{{ errors['warehouse_id'][0] }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="requisition_types">Requisition Types <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-bootstrap-select m_selectpicker" id="requisition_types" v-model="form.requisition_types" @change="getRmSetupData()">
                            <option value="Normal Requisition">Normal Requisition</option>
                            <option value="Massbody Requisition">Massbody Requisition</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('requisition_types'))">{{ (errors.hasOwnProperty('payment_method')) ? errors.requisition_types[0] :'' }}</div>
                    </div>

                    <label v-show="form.requisition_types === 'Massbody Requisition'" class="col-lg-2 col-form-label" for="estimate_qty_for_production">Estimate Qty for production <span class="requiredField">*</span></label>
                    <div class="col-lg-4" v-show="form.requisition_types === 'Massbody Requisition'">
                        <input type="text" class="form-control form-control-sm m-input number" @input="massbodyCalculation()" v-model="form.estimate_qty_for_production"   id="estimate_qty_for_production" placeholder="Enter estimate qty for production">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('estimate_qty_for_production'))">{{ (errors.hasOwnProperty('estimate_qty_for_production')) ? errors.estimate_qty_for_production[0] :'' }}</div>
                    </div>
                </div>
                <br><br>
                <br><br>
                <!--begin::Portlet-->

                <div class="form-group m-form__group row add-manage-section">
                    <div class="m-section__content col-lg-12">
                        <div class ="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse" >
                                <tr>
                                    <th></th>
                                    <th class="width-210">Product Name</th>
                                    <th>Remarks</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th class="width-160">Total Price</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(details, index) in form.details" v-show="form.requisition_types === 'Normal Requisition'">
                                        <th scope="row">
                                            <a href="javascript:void(0)" @click="addNewItem" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Add More">
                                                <i class="la la-plus"></i>
                                            </a>
                                        </th>
                                        <td class="width-210">
                                            <select class="form-control m-select2 select2 select-product width-210" v-bind:data-index="index" v-model="details.product_id" @input="normalCalculation()" style="width: 100%">
                                                <option v-for="(value,index) in product_lists" :value="value.id"  :buying-price="value.buying_price"> {{value.product_name}}</option>
                                            </select>
                                            <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{ errors['details.'+index+'.product_id'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.remarks" class="form-control form-control-sm m-input" placeholder="">
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.qty" class="form-control form-control-sm m-input number" placeholder="" @input="normalCalculation()">
                                            <div class="requiredField" v-if="errors['details.'+index+'.qty']">{{ errors['details.'+index+'.qty'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.unit_price" class="form-control form-control-sm m-input number" @input="normalCalculation()">
                                            <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{ errors['details.'+index+'.unit_price'][0] }}</div>
                                        </td>
                                        <td class="width-160">
                                            <input type="text" v-model="details.total_price" class="form-control form-control-sm m-input number width-160" @input="normalCalculation()" readonly>
                                            <div class="requiredField" v-if="errors['details.'+index+'.total_price']">{{ errors['details.'+index+'.total_price'][0] }}</div>
                                        </td>
                                        <td>
                                            <a  @click="removeItem(index,details.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                        </td>
                                    </tr>

                                    <tr v-for="(details, index) in form.details" v-show="form.requisition_types === 'Massbody Requisition'">
                                        <th scope="row">
                                            <a href="javascript:void(0)"  @click="addNewItem" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Add More">
                                                <i class="la la-plus"></i>
                                            </a>
                                        </th>
                                        <td>
                                            <select class="form-control m-select2 select2 select-product" v-bind:data-index="index" v-model="details.product_id" style="width: 100%" disabled>
                                                <option v-for="(value,index) in product_lists" :value="value.id" :measure-unit="value.measure_unit"> {{value.product_name}}</option>
                                            </select>
                                            <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{ errors['details.'+index+'.product_id'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.remarks" class="form-control form-control-sm m-input" placeholder="">
                                        </td>
                                        <td>
                                            <input type="hidden" v-model="details.one_kg_weight" class="form-control form-control-sm m-input number" placeholder="">
                                            <input type="text" v-model="details.qty" class="form-control form-control-sm m-input number" placeholder="">
                                            <div class="requiredField" v-if="errors['details.'+index+'.qty']">{{ errors['details.'+index+'.qty'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.unit_price" class="form-control form-control-sm m-input number">
                                            <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{ errors['details.'+index+'.unit_price'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="text" v-model="details.total_price" class="form-control form-control-sm m-input number">
                                            <div class="requiredField" v-if="errors['details.'+index+'.total_price']">{{ errors['details.'+index+'.total_price'][0] }}</div>
                                        </td>

                                        <td>
                                            <a  @click="removeItem(index,details.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-right">Total Weight</td>
                                        <td class="">
                                            <input type="text" v-model="form.total_qty" class="form-control form-control-sm m-input">
                                        </td>
                                        <td class="text-right">Total Amount</td>
                                        <td  class="">
                                            <input type="text" v-model="form.total_amount" class="form-control form-control-sm m-input">
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <br><br>
                    </div>
                </div>

                <!-- Start Account -->
                <hr>
                <div class="m-form__heading">
                    <h3 class="m-form__heading-title">Accounts <i data-toggle="m-tooltip" data-width="auto" class="m-form__heading-help-icon flaticon-info" title="" data-original-title="Journal will hit once Requisition approved"></i> <small><mark><i> Journal will hit once Requisition approved. </i></mark></small> </h3>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="debit_account_id">Debit Account <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2"  id="debit_account_id" v-model="form.debit_account_id" data-selectField="debit_account_id">
                            <option v-for="(value,index) in account_lists" :value="value.id"> {{ value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('debit_account_id'))">{{ (errors.hasOwnProperty('debit_account_id')) ? errors.debit_account_id[0] :'' }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="credit_account_id">Credit Account <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2"  id="credit_account_id" v-model="form.credit_account_id" data-selectField="credit_account_id">
                            <option v-for="(value,index) in account_lists" :value="value.id"> {{ value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('credit_account_id'))">{{ (errors.hasOwnProperty('credit_account_id')) ? errors.credit_account_id[0] :'' }}</div>
                    </div>
                </div>
                <!-- //End Account -->
            </div>

            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="submit" v-if="permissions.indexOf('requisition-for-raw-material.approve') != -1" class="btn btn-sm btn-primary"  @click.prevent="update(form.id,1)"><i class="la la-check"></i> <span>Approve</span></button>
                            <button type="submit" class="btn btn-sm btn-success"  @click.prevent="update(form.id,0)"><i class="la la-check"></i> <span>Update and Go List</span></button>
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
    import moment from 'moment';
    export default {

        props:['permissions','product_lists','editId','warehouse_lists','account_lists'],
        data:function(){
            return{
                data_list:false,
                add_form:false,
                edit_form:true,
                view_form:false,

                form:{
                    rm_requisition_no: '',
                    requisition_date: '',
                    warehouse_id: '',
                    requisition_types: '',
                    narration: '',
                    estimate_qty_for_production: '',
                    total_qty: '',
                    total_amount: '',
                    approve_status:'',
                    details: [
                        {
                            product_id: '',
                            remarks: '',
                            one_kg_weight: '',
                            qty: 0,
                            unit_price: 0,
                            total_price: '',
                        }
                    ],

                    debit_account_id: '',
                    credit_account_id: '',
                },
                errors: {},
            };
        },

        methods:{

            getRmSetupData(){
                axios.get(base_url+"requisition-for-raw-material/rm-ratio-setup-list").then((response) => {
                    if (this.form.requisition_types === 'Massbody Requisition'){
                        if(response.data.length > 0){
                            this.form.details = response.data;
                        }
                    }else {
                        this.form.details = [
                            {
                                product_id: '',
                                remarks: '',
                                qty: '',
                                unit_price: 0,
                                total_price: '',
                            }
                        ]
                        this.normalCalculation();
                    }

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
                                Select2.init()
                            }
                        );
                    },100);
                });
            },

            normalCalculation(){

                var details = this.form.details;
                var length = details.length;
                var totalWeight = 0;
                var total_prices = 0;
                var totalAmount = 0;

                for(let i = 0; i < length; i++) {
                    total_prices = Number(details[i].qty) * details[i].unit_price;
                    details[i].total_price = total_prices.toFixed( 2 );
                    totalWeight = Number(details[i].qty) + totalWeight;
                    totalAmount = Number(total_prices) + totalAmount;
                }
                this.form.total_qty = totalWeight.toFixed( 2 );
                this.form.total_amount = totalAmount.toFixed( 2 );
            },

            massbodyCalculation(){

                var estimateQty = this.form.estimate_qty_for_production;
                var details = this.form.details;
                var length = details.length;
                var totalWeight = 0;
                var tQty = 0;
                var totalPrice = 0;
                var totalAmount = 0;

                for(let i = 0; i < length; i++) {
                    tQty = Number(details[i].one_kg_weight) * estimateQty;
                    details[i].qty  = tQty.toFixed( 2 );
                    totalPrice = tQty * details[i].unit_price;

                    details[i].total_price = totalPrice.toFixed( 2 );

                    totalWeight = Number(tQty) + totalWeight;
                    totalAmount = Number(totalPrice) + totalAmount;
                }
                this.form.total_qty = totalWeight.toFixed( 2 );
                this.form.total_amount = totalAmount.toFixed( 2 );
            },


            addNewItem(){
                this.form.details.push({
                    product_id: '',
                    remarks: '',
                    qty: '',
                    unit_price: '',
                    total_price: '',
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
                    _this.normalCalculation();
                    _this.massbodyCalculation();
                }
            },


            edit(id) {
                var _this = this;
                axios.get(base_url+'requisition-for-raw-material/'+id+'/edit')
                    .then( (response) => {
                        console.log(response.data.data);
                        _this.form  = response.data.data;

                        setTimeout(function () {
                            var BootstrapSelect={init:function(){$(".m_selectpicker").selectpicker('refresh')}};
                            jQuery(document).ready(function(){BootstrapSelect.init()});

                            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                            jQuery(document).ready(function() {Select2.init()});

                        },100);

                    });
            },


            update(id,app){

                var _this = this;
                _this.form.approve_status = app;

                axios.put(base_url+'requisition-for-raw-material/'+id, this.form).then( (response) => {
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
            duplicateCheck(){
                var no_index = this.form.details.length;
                var details = this.form.details;
                if (no_index > 1) {
                    for (let i = 0; i < no_index; i++) {
                        for (let j = i+1; j < no_index; j++) {
                            if(details[i].product_id == details[j].product_id) {
                                alert("Duplicate Found");
                                details[j].product_id = '';
                                var Select2 = {
                                    init: function () {
                                        $(".select2").select2({
                                            placeholder: "Please select an option"
                                        })
                                    }
                                };
                                jQuery(document).ready(function () {
                                    Select2.init()
                                });
                            }
                        }
                    }
                }
            },
        },

        mounted(){

            var _this = this;

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if(selectField == 'warehouse_id' ){
                    _this.form.warehouse_id= selectedItem.val();
                }else if(selectField == 'debit_account_id'){
                    _this.form.debit_account_id= selectedItem.val();
                }else if(selectField == 'credit_account_id'){
                    _this.form.credit_account_id= selectedItem.val();
                }
            });

            $('#editComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index'),
                    buyingPrice = $('option:selected', $(e.target)).attr('buying-price');


                _this.form.details[dataIndex].product_id = selectedItem.val();
                _this.form.details[dataIndex].unit_price = buyingPrice;
                _this.duplicateCheck();
            });

            var BootstrapSelect={init:function(){$(".m_selectpicker").selectpicker()}};
            jQuery(document).ready(function(){BootstrapSelect.init()});

            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
            jQuery(document).ready(function() {Select2.init()});

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.requisition_date = '';

                    }else {
                        _this.form.requisition_date = moment(ev.date).format('DD/MM/YYYY');

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

        created(){
            var _this = this;
            _this.edit(_this.editId);
            _this.massbodyCalculation();
            _this.getRmSetupData();
        }

    }
</script>
