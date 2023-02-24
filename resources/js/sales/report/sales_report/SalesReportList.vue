<template>
    <div>
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="sales" id="listComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="row print_links">
                <div class="col-md-6"></div>
                <div class="col-md-6 text-right" style="margin-top:3px; ">
                    <a href="#" @click="doExportPdf()" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa fa-file-pdf"></i><span>PDF</span></span></a>
                    <a href="#" @click="doExportExcel()" class="btn btn-accent btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa fa-file-excel"></i><span>Excel</span></span></a>
                </div>
            </div>


            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-12 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">


                            <div class="col-md-6">

                                <div class="m-form__group m-form__group" >
                                    <label>Challan No:</label>
                                    <div class="m-form__control">
                                        <select class="form-control m-select2 select2" data-selectField="challan_id"  v-model="form.challan_id"  >
                                            <option v-for="(value,index) in challan_lists" :value="value.id" > {{value.sales_delivery_no}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div class="col-md-6">
                                <div class="m-form__group m-form__group">
                                    <label class="m-label m-label--single">Customer:</label>
                                    <div class="m-form__control">
                                        <select class="form-control m-select2 select2" data-selectField="customer_id"  v-model="form.customer_id" >
                                            <option v-for="(cvalue,cindex) in customer_lists" :value="cvalue.id" > {{cvalue.sales_customer_name}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div class="col-md-6">
                                <div class="m-form__group m-form__group">
                                    <label class="m-label m-label--single">WareHouse:</label>
                                    <div class="m-form__control">
                                        <select class="form-control m-select2 select2" data-selectField="warehouse_id" v-model="form.warehouse_id" >
                                            <option v-for="(wvalue,windex) in warehouse_lists" :value="wvalue.id" > {{wvalue.purchase_ware_houses_name}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div class="col-md-6">
                                <div class="m-form__group m-form__group">
                                    <label class="m-label m-label--single">Product:</label>
                                    <div class="m-form__control">
                                        <select class="form-control m-select2 select2" data-selectField="product_id" v-model="form.product_id" >
                                            <option v-for="(pvalue,pindex) in product_lists" :value="pvalue.id" > {{pvalue.product_name}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div class="col-md-6">
                                <div class="m-form__group m-form__group">
                                    <label class="m-label m-label--single">From:</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control form-control-sm m-input datepicker from_date" v-model="form.from_date" id="from_date" readonly placeholder="Select date from picker" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div class="col-md-6">
                                <div class="m-form__group m-form__group">
                                    <label class="m-label m-label--single">To:</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control form-control-sm m-input datepicker end_date" v-model="form.end_date" id="end_date" readonly placeholder="Select date from picker" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div  class="col-md-6">
                                <div class="m-form__group m-form__group">
                                    <div class="m-input-icon m-input-icon--left">
                                        <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Search</span></button>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>


                </div>
            </div>
        </form>

        <div class="m-portlet__body" v-if="sales_report_list">
            <!--begin: Datatable -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="m_table_1">
                    <thead>
                        <tr>
                            <th>S/L</th>
                            <th>Date</th>
                            <th>Challan No</th>
                            <th>Warehouse</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Narration</th>
                            <th>Unit</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody v-if="resultData !=''">
                        <tr v-for="(value,index) in resultData">
                            <td>{{++index}}</td>
                            <td>{{format_date(value.sales_delivery_date)}} </td>
                            <td>{{value.sales_delivery_no}}</td>
                            <td>{{value.purchase_ware_houses_name}}</td>
                            <td>{{value.sales_customer_name}}</td>
                            <td>{{value.product_name}}</td>
                            <td>{{value.sales_delivery_details_description}}</td>
                            <td>{{value.sales_delivery_details_unit}}</td>
                            <td>{{value.sales_delivery_details_qty}}</td>
                            <td>{{value.sales_delivery_details_unit_price}}</td>
                            <td>{{value.sales_delivery_details_total_price}}</td>
                        </tr>
                    <tr style="font-weight: bold;text-align: right">
                        <td colspan="8">Total:</td>
                        <td>{{total_qty}}</td>
                        <td></td>
                        <td>{{total_price}}</td>
                    </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="11" class="text-center">No Data Available.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>



    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import moment from 'moment';

    export default {

        props:['permissions','challan_lists','product_lists','customer_lists','warehouse_lists'],

        data: function(){
            return {
                sales_report_list:true,
                resultData: {},
                total_qty: '',
                total_price: '',
                form:{
                    challan_id: '',
                    product_id: '',
                    customer_id:'',
                    warehouse_id: '',
                    from_date: '',
                    end_date: '',
                },
                errors: {},
            };
        },

        methods: {
            sales:function(){
                var _this = this;
                axios.post(base_url+'sales-report/sales', this.form)
                    .then( (response) => {
                        _this.resultData  = response.data;

                        _this.calculateTotal();

                    }).catch(error => {
                        if(error.response.status == 422){
                            this.errors = error.response.data.errors;
                        }else{
                            this.showMassage(error);
                        }
                    });
            },


            format_date(value){
                if (value) {
                    return moment(String(value)).format('MM/DD/YYYY')
                }
            },

            showMassage(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, 'Success Alert', {timeOut: 5000});
                } else {
                    toastr.error(data.message, 'Error Alert', {timeOut: 5000});
                }
            },

            doExportPdf() {
                var _this = this;
                  if(_this.form.challan_id){
                window.open(base_url + 'sales-report/sales?export=pdf&challan_id='+ _this.form.challan_id +'&customer_id='+_this.form.customer_id +'&warehouse_id='+_this.form.warehouse_id+'&product_id='+_this.form.product_id +'&from_date='+ _this.form.from_date +'&end_date='+ _this.form.end_date +'', "_blank");
                }else {
                 $('#challan_id').focus()
                 var data = {message: 'Please select Challan No'};
                 this.showMassage(data);

                 }


            },

            calculateTotal(){

                var _this = this;
                let total_qty  = 0;
                let total_price  = 0;

                var balance1 = _this.resultData;

                balance1.forEach(function(item) {
                    total_qty += parseFloat(item.sales_delivery_details_qty);
                    total_price += parseFloat(item.sales_delivery_details_total_price);
                });
                _this.total_qty = total_qty.toFixed(2);
                _this.total_price = total_price.toFixed(2);
            },

            doExportExcel() {
                var _this = this;
                if(_this.form.challan_id) {
                window.open(base_url + 'sales-report/sales?export=excel&challan_id=' + _this.form.challan_id + '&customer_id=' + _this.form.customer_id + '&warehouse_id=' + _this.form.warehouse_id + '&product_id=' + _this.form.product_id + '&from_date=' + _this.form.from_date + '&end_date=' + _this.form.end_date + '', "_blank");
                }else {
                 $('#challan_id').focus()
                 var data = {message: 'Please select Challan No'};
                 this.showMassage(data);

                 }
            },



        },

        mounted(){
            var _this =this;
            $('#listComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'challan_id'){
                    _this.form.challan_id = selectedType.val();
                }else if(dataIndex == 'product_id'){
                    _this.form.product_id = selectedType.val();
                }else if(dataIndex == 'customer_id'){
                    _this.form.customer_id = selectedType.val();
                }else if(dataIndex == 'warehouse_id'){
                    _this.form.warehouse_id = selectedType.val();
                }
            });


            var Select2= {
                init:function() {
                    $(".select2").select2( {
                        placeholder: "Please select an option"
                    })
                }
            };
            jQuery(document).ready(function() {
                Select2.init()
            });

            $(document).on("focus", ".datepicker", function () {

                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');

                    var dateField = $(this).attr('id');

                    if(ev.date == undefined){
                        if(dateField == 'from_date'){
                            _this.form.from_date = '';
                        }else if(dateField == 'end_date'){
                            _this.form.end_date = '';
                        }
                    }else {
                        if(dateField == 'from_date'){
                            _this.form.from_date = moment(ev.date).format('DD/MM/YYYY');
                        }else if(dateField == 'end_date'){
                            _this.form.end_date = moment(ev.date).format('DD/MM/YYYY');
                        }
                    }

                });
            });
        },

        created(){

            var _this = this;

            $('body').on('click', '#listButton', function() {
                _this.add_form = false;
                _this.sales_report_list = true;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').show();
                $('#listButton').hide();
            });

            EventBus.$on('data-changed', (id) => {
                _this.add_form = false;
                _this.sales_report_list = true;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').show();
                $('#listButton').hide();
               // _this.sales(1);
            });

            //_this.sales(1);
        },
    }
</script>

