<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="sales_delivery_no" class="col-lg-2 col-form-label">Invoice No <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text"  id="sales_delivery_no"  v-model="form.sales_delivery_no" class="form-control form-control-sm m-input" placeholder="Enter Delivery Challan No">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_delivery_no'))">{{ (errors.hasOwnProperty('sales_delivery_no')) ? errors.sales_delivery_no[0] :'' }}</div>
                    </div>
                    <label for="sales_delivery_date" class="col-lg-2 col-form-label">Delivery Date </label>
                    <div class="col-lg-4">
                        <div class="input-group date"><!--"disableFormate.disabledDates" -->
                            <vuejs-datepicker :disabledDates="sdate.disabledDates" id ="sales_delivery_date" v-model="form.sales_delivery_date" :format="customFormatter"  @selected="onDateSelect"></vuejs-datepicker>
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
                    <label for="sales_invoice_id" class="col-lg-2 col-form-label"> Challan No </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="sales_invoice_id" id="sales_invoice_id" v-model="form.sales_invoice_id" @change="loadDetails()" >
                            <option v-for="(svalue,sindex) in invoice_lists" :value="svalue.id" > {{svalue.sales_invoices_no}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['sales_invoice_id']">{{ errors['sales_invoice_id'][0] }}</div>
                    </div>
                    <label for="sales_customer_id" class="col-lg-2 col-form-label">Customer </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="sales_customer_id" id="sales_customer_id" v-model="form.sales_customer_id" >
                            <option v-for="(cvalue,cindex) in customer_lists" :value="cvalue.id" > {{cvalue.sales_customer_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['sales_customer_id']">{{ errors['sales_customer_id'][0] }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="sales_customer_id" class="col-lg-2 col-form-label">Warehouse </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="sales_warehouse_id" id="sales_warehouse_id" v-model="form.sales_warehouse_id" >
                            <option v-for="(wvalue,windex) in warehouse_lists" :value="wvalue.id" > {{wvalue.purchase_ware_houses_name}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['sales_warehouse_id']">{{ errors['sales_warehouse_id'][0] }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.sales_delivery_narration"  id="narration" rows="2"></textarea>
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
                                    <th>M Unit</th>
                                    <th>Unit Price</th>
                                    <th>Order Qty</th>
                                    <th>Previouse Delivery Qty</th>
                                    <th>Qty</th>
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
                                        <select class="form-control m-select2 select2 select-product width-210"  v-bind:data-index="index" v-model="details.sales_delivery_details_product_id"  >
                                            <option v-for="(value,index) in product_lists" :value="value.id" :measure-unit="value.measure_unit"> {{value.product_name}}</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.sales_delivery_details_product_id']">{{ errors['details.'+index+'.sales_delivery_details_product_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.sales_delivery_details_description" class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.sales_delivery_details_unit" class="form-control form-control-sm m-input" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.sales_delivery_details_unit']">{{ errors['details.'+index+'.sales_delivery_details_unit'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.sales_delivery_details_unit_price" @keyup="totalQtyAndPrice()" class="form-control form-control-sm m-input">
                                        <div class="requiredField" v-if="errors['details.'+index+'.sales_delivery_details_unit_price']">{{ errors['details.'+index+'.sales_delivery_details_unit_price'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.sales_delivery_details_order_qty"  class="form-control form-control-sm small text-danger" style="background: #9d9d9d;" readonly>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.sales_delivery_details_pervious_qty"  class="form-control form-control-sm small text-danger" style="background: #9d9d9d;" readonly>
                                    </td>
                                    <!--<td> <span> {{details.sales_delivery_details_order_qty}}</span></td>-->
                                    <td>
                                        <input type="text" v-model="details.sales_delivery_details_qty" @keyup="totalQtyAndPrice()" class="form-control form-control-sm m-input" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.sales_delivery_details_qty']">{{ errors['details.'+index+'.sales_delivery_details_qty'][0] }}</div>
                                    </td>
                                    <td class="width-160">
                                        <input type="text" v-model="details.sales_delivery_details_total_price" class="form-control form-control-sm m-input width-160">
                                        <div class="requiredField" v-if="errors['details.'+index+'.sales_delivery_details_total_price']">{{ errors['details.'+index+'.sales_delivery_details_total_price'][0] }}</div>
                                    </td>
                                    <td>
                                        <a  @click="removeItem(index)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-right">Total </td>
                                    <td class="">
                                        <input type="text" v-model="form.sales_delivery_total_qty" class="form-control form-control-sm m-input">
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_delivery_total_qty'))">{{ (errors.hasOwnProperty('sales_delivery_total_qty')) ? errors.sales_delivery_total_qty[0] :'' }}</div>
                                    </td>
                                    <td  class="">
                                        <input type="text" v-model="form.sales_delivery_total_price" class="form-control form-control-sm m-input">
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
                            <button type="reset" class="btn btn-sm btn-secondary">Cancel</button>
                           <!-- <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save</span></button>-->
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save and Go List</span></button>
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
        components: {
            vuejsDatepicker
        },

        props:['sales_challan_delivery_no','product_lists','invoice_lists','customer_lists','warehouse_lists'],

        data:function(){
            return{
                product_list:false,
                add_form:true,
                edit_form:false,

                form:{
                    sales_delivery_no: this.sales_challan_delivery_no,
                    sales_delivery_date:  new Date(),
                    sales_invoice_id: '',
                    sales_customer_id:'',
                    sales_warehouse_id: '',
                    sales_delivery_narration: '',
                    sales_delivery_total_qty: '',
                    sales_delivery_total_price: '',

                    details: [
                        {
                            sales_delivery_details_product_id:'',
                            sales_delivery_details_description:'',
                            sales_delivery_details_unit:'',
                            sales_delivery_details_unit_price:'',
                            sales_delivery_details_order_qty:'',
                            sales_delivery_details_pervious_qty: '',
                            sales_delivery_details_qty:0,
                            sales_delivery_details_total_price:'',
                        }
                    ],
                },
                sdate: {
                    disabledDates: {
                        //1000(mililisec) * 60 sec* 60min * 24hr
                        to: new Date((new Date() - 1000 * 60 * 60 * 24 * 7)),//.setDate(new Date().getDate() - 2),
                            //new Date(Date.now() - 2*8640000),
                    },
                },
                errors: {},
            };
        },

        methods:{
            customFormatter(date) {

                 return moment(date).format('DD/MM/YYYY');
            },
            onDateSelect(date){

                this.form.sales_delivery_date = moment(date).format('DD/MM/YYYY');
            },

           /* disableFormate(){
                var statd = {
                    disabledDates: {
                        to: new Date(Date.now() - 8640000),
                    }
                }
                alert(statd);
                console.log(statd);
                return statd;
            },*/



            /*disabledDates: {
                to: new Date(2016, 0, 5), // Disable all dates up to specific date
                from: new Date(2016, 0, 26), // Disable all dates after specific date
                days: [6, 0], // Disable Saturday's and Sunday's
                daysOfMonth: [29, 30, 31], // Disable 29th, 30th and 31st of each month
                dates: [ // Disable an array of dates
                    new Date(2016, 9, 16),
                    new Date(2016, 9, 17),
                    new Date(2016, 9, 18)
                ],
                ranges: [{ // Disable dates in given ranges (exclusive).
                    from: new Date(2016, 11, 25),
                    to: new Date(2016, 11, 30)
                }, {
                    from: new Date(2017, 1, 12),
                    to: new Date(2017, 2, 25)
                }],
                // a custom function that returns true if the date is disabled
                // this can be used for wiring you own logic to disable a date if none
                // of the above conditions serve your purpose
                // this function should accept a date and return true if is disabled
                customPredictor: function(date) {
                    // disables the date if it is a multiple of 5
                    if(date.getDate() % 5 == 0){
                        return true
                    }
                }
            },*/


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

                }
            },

            store:function(){
                var _this = this;
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
                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    total_price = Number(details[i].sales_delivery_details_qty) * Number(details[i].sales_delivery_details_unit_price);
                    this.form.details[i].sales_delivery_details_total_price = total_price;
                    total_qty = Number(details[i].sales_delivery_details_qty) + total_qty;
                    total_amount = total_price + total_amount;
                }
                this.form.sales_delivery_total_qty = total_qty;
                this.form.sales_delivery_total_price = total_amount;
            },

            duplicateCheck(){
                var no_index = this.form.details.length;
                //alert(no_index);
                if (no_index > 1) {

                for (let i = 0; i < no_index; i++) {
                    for (let j = i; j < no_index; j++) {
                        j = j + 1;
                        //alert(this.form.details[i].sales_invoice_details_product_id+'-------'+this.form.details[j].sales_invoice_details_product_id);
                        if (this.form.details[i].sales_invoice_details_product_id == this.form.details[j].sales_invoice_details_product_id) {
                            alert("Duplicate Found");
                            this.form.details[j].sales_invoice_details_product_id = '';
                            var Select2 = {
                                init: function () {
                                    $(".select2").select2({
                                            placeholder: "Please select an option"
                                        }
                                    )
                                }
                            };
                            jQuery(document).ready(function () {
                                    Select2.init()
                                }
                            );
                        }
                    }
                }

            }

            },

            loadDetails(){
                var _this = this;
                var id= this.form.sales_invoice_id;
                var AddEditId = 0;
                //alert(id);

                axios.get(base_url+"sales-delivery-challan/"+AddEditId+"/"+id+"/sales-invoice-list").then((response) => {
                    if(response.data.length > 0){
                        this.form.details = response.data;
                        this.totalQtyAndPrice();
                        console.log(response.data)
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
                    }
                });
            },
        },

        mounted(){
            var _this = this;

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
                }
            });

            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index'),
                    selectMeasureUnit = $('option:selected', $(e.target)).attr('measure-unit');
                _this.form.details[dataIndex].sales_delivery_details_product_id = selectedItem.val();
                _this.form.details[dataIndex].sales_delivery_details_unit = selectMeasureUnit;
                //_this.duplicateCheck();
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


        created() {
            var _this = this;
            _this.totalQtyAndPrice();
        }

    }

</script>


