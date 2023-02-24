<template>
    <div>
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="purchase" id="listComponent" class="m-form m-form--fit m-form--label-align-right" >
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
                                    <label>Entry No:</label>
                                    <div class="m-form__control">
                                        <select class="form-control m-select2 select2" data-selectField="pur_stock_id"  v-model="form.pur_stock_id"  >
                                            <option v-for="(value,index) in pur_stock_lists" :value="value.id" > {{value.po_order_no}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div class="col-md-6">
                                <div class="m-form__group m-form__group">
                                    <label class="m-label m-label--single">Supplier:</label>
                                    <div class="m-form__control">
                                        <select class="form-control m-select2 select2" data-selectField="supplier_id"  v-model="form.supplier_id" >
                                            <option v-for="(cvalue,cindex) in supplier_lists" :value="cvalue.id" > {{cvalue.purchase_supplier_name}}</option>
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

                            <div class="col-md-6">
                                <label class="col-form-label col-lg-6 col-sm-12">Average Price? </label>
                                <div class="col-md-6">
                                    <span class="m-switch m-switch--icon  pull-left">
                                        <label>
                                            <input type="checkbox" checked="checked" v-model="form.avg_price">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>

                        </div>
                    </div>


                </div>
            </div>
        </form>


        <div class="m-portlet__body" v-if="purchase_report_list && form.avg_price == 0">
            <!--begin: Datatable -->
            <div class="table-responsive">
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                    <tr>
                        <th>S/L</th>
                        <th>Date</th>
                        <th>Entry No</th>
                        <th>Warehouse</th>
                        <th>Supplier</th>
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
                        <td>{{format_date(value.po_receive_date)}} </td>
                        <td>{{value.po_order_no}}</td>
                        <td>{{value.purchase_ware_houses_name}}</td>
                        <td>{{value.purchase_supplier_name}}</td>
                        <td>{{value.product_name}}</td>
                        <td>{{value.po_narration}}</td>
                        <td>{{value.pod_unit}}</td>
                        <td>{{value.pod_qty}}</td>
                        <td>{{value.pod_unit_price}}</td>
                        <td>{{value.po_total_price}}</td>
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

        <div class="m-portlet__body" v-if="form.avg_price == 1">
            <!--begin: Datatable -->
            <div class="table-responsive">
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                    <tr>
                        <th>S/L</th>
                        <th>Product ID</th>
                        <th>Product Name</th>                        
                        <th>Total Qty</th>
                        <th>Avg Unit Price</th>
                        <th>Total Amount</th>
                    </tr>
                </thead>
                <tbody v-if="avgData !=''">
                    <tr v-for="(value,index) in avgData">
                        <td>{{++index}}</td>
                        <td>{{value.prod_id}}</td>
                        <td>{{value.product_name}}</td>
                        <td>{{value.total_qty}}</td>                        
                        <td>{{ (value.total_price/value.total_qty).toFixed(2) }}</td>
                        <td>{{value.total_price}}</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="6" class="text-center">No Avg Data Available.</td>
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

        props:['permissions','pur_stock_lists','product_lists','supplier_lists','warehouse_lists'],

        data: function(){
            return {
                purchase_report_list:true,
                resultData: {},
                avgData: {},

                form:{
                    pur_stock_id: '',
                    product_id: '',
                    supplier_id:'',
                    warehouse_id: '',
                    from_date: '',
                    end_date: '',

                    avg_price: 0,
                },
                errors: {},
            };
        },

        methods: {

            purchase:function(){
                var _this = this;
                _this.$loading(true);
                axios.post(base_url+'purchase-report/purchase', this.form)
                    .then( (response) => {
                        _this.$loading(false);
                        if(_this.form.avg_price == 1){
                            _this.avgData  = response.data;
                        } else {
                            _this.resultData  = response.data;
                        }                        
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
                if (_this.form.pur_stock_id) {
                    window.open(base_url + 'purchase-report/purchase?export=pdf&pur_stock_id=' + _this.form.pur_stock_id + '&supplier_id=' + _this.form.supplier_id + '&warehouse_id=' + _this.form.warehouse_id + '&product_id=' + _this.form.product_id + '&from_date=' + _this.form.from_date + '&end_date=' + _this.form.end_date + '', "_blank");
                }else {
                        $('#pur_stock_id').focus()
                        var data = {message: 'Please select Entry No'};
                        this.showMassage(data);

                    }
            },

            doExportExcel() {
                var _this = this;
                if (_this.form.pur_stock_id) {
                window.open(base_url + 'purchase-report/purchase?export=excel&pur_stock_id='+ _this.form.pur_stock_id +'&supplier_id='+_this.form.supplier_id +'&warehouse_id='+_this.form.warehouse_id+'&product_id='+_this.form.product_id +'&from_date='+ _this.form.from_date +'&end_date='+ _this.form.end_date +'', "_blank");
                }else {
                    $('#pur_stock_id').focus()
                    var data = {message: 'Please select Entry No'};
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
                if(dataIndex == 'pur_stock_id'){
                    _this.form.pur_stock_id = selectedType.val();
                }else if(dataIndex == 'product_id'){
                    _this.form.product_id = selectedType.val();
                }else if(dataIndex == 'supplier_id'){
                    _this.form.supplier_id = selectedType.val();
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
                _this.purchase_report_list = true;
                $('#listButton').hide();
            });

            EventBus.$on('data-changed', (id) => {
                _this.purchase_report_list = true;
                $('#listButton').hide();
                //_this.purchase(1);
            });

            //_this.purchase(1);
        },
    }
</script>

