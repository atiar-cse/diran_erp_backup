<template>
    <div>
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="search" id="listComponent" class="m-form m-form--fit m-form--label-align-right hide_print" >

            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-12 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">
                            <div class="col-md-5">
                                <div class="m-form__group m-form__group">
                                    <label class="m-label m-label--single">From Date:</label>
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

                            <div class="col-md-5">
                                <div class="m-form__group m-form__group">
                                    <label class="m-label m-label--single">To Date :</label>
                                    <div class="input-group date">
                                        <input type="text" class="form-control form-control-sm m-input datepicker to_date" v-model="form.to_date" id="to_date" readonly placeholder="Select date from picker" />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>


                            <div  class="col-md-2">
                                <div class="m-form__group m-form__group">
                                    <label class="m-label m-label--single"></label>
                                    <div class="m-input-icon m-input-icon--left">
                                        <button type="submit" class="btn btn-sm btn-success" ><i class="la la-filter"></i> <span>Search</span></button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="m-form__group m-form__group">
                                    <p class="bg-info text-white"> <strong>Info : </strong> Semi Finished Stock Items are just Temporary Stock Count to Process Finished Goods via either 'Finished Stock / Assembling' Section.</p>
                                </div>                       
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div id="m-portlet" class="m-portlet__body" v-if="sales_report_list">
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-right">
                    <a href="#" onclick="window.print();return false" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa flaticon-technology"></i><span>Print</span></span></a>
                </div>
            </div>
            <br>

            <div class="m-invoice__head print_only">
                <div class="m-invoice__container m-invoice__container--centered">
                    <div class="row invoice-header"  style="padding-top: 0px;padding-bottom:15px;">
                        <div class="col-md-4">
                            <div class="db-data">
                                <p><span style="font-weight: 600">Section :</span> <span>Semi Finished Stock</span></p>
                                <p><span style="font-weight: 600">From Date :</span> <span>{{form.from_date}}</span></p>
                                <p><span style="font-weight: 600">To Date : </span> <span>{{form.to_date}}</span></p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                        <span class="m-invoice__desc">
                            <p><span style="font-weight: 600">Production Report</span></p>
                             <span>{{cinfo.name}}</span><br>
                             <span>{{cinfo.address}}</span>
                        </span>
                      </div>

                        <div class="col-md-4">
                        <p style="margin-bottom: 62px;">
                            <!--<img src="img/1200px-Channel-i.svg.png" style="width: 150px;float: right">-->
                            <img :src="cinfo.logo" style="width: 150px; height: 50px;float: right">
                        </p>
                      </div>
                    </div>
                </div>
            </div>

            <!--begin: Datatable -->
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                    <tr>
                        <th>S/L</th>
                        <th>Name Of Product</th>
                        <th>Opening</th>
                        <th>Rec.F H/V</th>
                        <th>Adj</th>
                        <th>Total</th>

                        <th>Tra.To F/S</th>
                        <th>Rej</th>
                        <th>Total Issus.</th>
                        <th>Balance</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody v-if="resultData !=''">
                    <tr v-for="(value,index) in resultData.prod_testing_details">
                        <td>{{++index}}</td>
                        <td>{{value.product.product_name}}</td>
                        <td> {{value.opening_qty}} </td>
                        <td>{{value.prod_of_month}}</td>
                        <td> {{value.adj_qty}} </td>
                        <td>{{value.total}}</td>

                        <td>{{value.trans_to_store_qty}}</td>
                        <td>{{value.reject_qty}}</td>
                        <td>{{value.total_issue}}</td>
                        <td> {{value.balance}} </td>
                        <td></td>
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
</template>

<script>
    import {EventBus} from '../../../vue-assets';
    import moment from 'moment';

    export default {

        props: [''],

        data: function () {
            return {
                sales_report_list: true,
                resultData: {},
                cinfo:{},
                form:{
                    from_date: '',
                    to_date: '',
                },
                errors: {},
            };
        },

        methods: {

            company_info(){
                var _this = this;
                var id = 1;
                axios.get(base_url+'Company-Info/'+id)
                    .then( (response) => {
                        _this.cinfo  = response.data;
                    });
            },

            search: function () {

                var _this = this;
                var from_date = this.form.from_date;
                var to_date = this.form.to_date;

                axios.get(base_url+"semi-finished-stock/?from_date="+from_date+"&to_date="+to_date).then((response) => {
                    _this.resultData = response.data;

                        setTimeout(function () {
                            jQuery(document).ready(function() {

                                    _this.formatFormData();
                                }
                            );
                        },900);                    
                });                    
            },

            formatFormData(){
                var _this = this;

                var opening_products_qty = _this.resultData.opening_products_qty;
                var prod_testing_details = _this.resultData.prod_testing_details;

                var finished_items = _this.resultData.finished_items;
                var assembled_items = _this.resultData.assembled_items;
                var rejected_items = _this.resultData.rejected_items;

                prod_testing_details.forEach(function(item) {

                    var product_id = item.product_id;

                    var total = parseFloat(item.prod_of_month) + parseFloat(item.opening_qty);
                    item.total = total ? total : 0;

                    var finished_qty = finished_items[product_id] ? parseFloat(finished_items[product_id]) : 0;
                    var assembled_qty = assembled_items[product_id] ? parseFloat(assembled_items[product_id]) : 0;
                    var trans_to_store_qty = finished_qty + assembled_qty;
                    item.trans_to_store_qty = trans_to_store_qty;

                    var reject_qty = rejected_items[product_id] ? parseFloat(rejected_items[product_id]) : 0;
                    item.reject_qty = reject_qty; 

                    var total_issue = trans_to_store_qty + reject_qty;
                    item.total_issue = total_issue;

                    var balance = total - total_issue;
                    item.balance = balance;
                });
            },

            showMassage(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, 'Success Alert', {timeOut: 5000});
                } else {
                    toastr.error(data.message, 'Error Alert', {timeOut: 5000});
                }
            },
        },

        mounted(){
            var _this = this;
            $(document).on("focus", ".datepicker", function () {

                $(this).datepicker({
                    format: 'YYYY-MM-DD',
                    todayHighlight: true,
                    clearBtn: true,
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    var dateField = $(this).attr('id');

                    if (ev.date == undefined) {
                        if (dateField == 'from_date') {
                            _this.form.from_date = '';
                        } else if (dateField == 'to_date') {
                            _this.form.to_date = '';
                        }
                    } else {
                        if (dateField == 'from_date') {
                            _this.form.from_date = moment(ev.date).format('YYYY-MM-DD');
                        } else if (dateField == 'to_date') {
                            _this.form.to_date = moment(ev.date).format('YYYY-MM-DD');
                        }
                    }
                });
            });
        },

        created(){

            var _this = this;
            _this.form.from_date = moment().format('YYYY-MM-01');
            _this.form.to_date = moment().format('YYYY-MM-DD');
            _this.search(1);
            _this.company_info();
        },
    }
</script>

