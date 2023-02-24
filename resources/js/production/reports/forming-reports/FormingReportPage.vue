<template>
    <div>
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="forming" id="listComponent" class="m-form m-form--fit m-form--label-align-right hide_print" >

            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30 ">
                <div class="row align-items-center">
                    <div class="col-xl-12 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">
                            <div class="col-md-4">
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

                            <div class="col-md-4">
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


                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="m-portlet__body" v-if="forming_report_list">

            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-right">
                    <a href="#" onclick="window.print();return false" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa flaticon-technology"></i><span>Print</span></span></a>
                    <!--<a href="#" class="btn btn-accent btn-sm m-btn  m-btn m-btn&#45;&#45;icon"><span><i class="fa flaticon-doc"></i><span>PDF</span></span></a>-->
                </div>
            </div>
            <br>
            <div class="m-invoice__head print_only">
                <div class="m-invoice__container m-invoice__container--centered">
                    <div class="row invoice-header"  style="padding-top: 24px">
                        <div class="col-md-4">
                            <div class="db-data">
                                <p><span style="font-weight: 600">Section :</span> <span>Forming</span></p>
                                <p><span style="font-weight: 600">Date</span> <span>31/01/2016</span></p>
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
                        <th>Pro. of. month</th>
                        <th>Total</th>
                        <th>Tra. To Shapping</th>
                        <th>Rej.</th>
                        <th>Total Issue</th>
                        <th>Adj</th>
                        <th>Balance</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody v-if="resultData !=''">
                    <tr v-for="(value,index) in resultData.prod_forming_details">
                        <td>{{++index}}</td>
                        <td>{{value.product.product_name}}</td>
                        <td>{{value.opening_qty}}</td>
                        <td>{{value.prod_of_month}}</td>
                        <td>{{value.total}}</td>
                        <td>{{value.trans_to_shapping_qty}}</td>
                        <td>{{value.shapping_westage_qty}}</td>
                        <td>{{value.total_issue}}</td>
                        <td><!--{{value.adj}}--></td>
                        <td>{{value.balance}}</td>
                        <td>{{value.narration}}</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="11" class="text-center">No Data Available.</td>
                    </tr>
                </tbody>
            </table>

             <!-- <div class="m-invoice__items signature-section" style="display: none">
                <div class="m-invoice__item">
                    <span class="m-invoice__subtitle">Manager</span>
                </div>
                <div class="m-invoice__item text-center">
                    <span class="m-invoice__subtitle signature-center">DGM</span>
                </div>
                <div class="m-invoice__item text-right">
                    <span class="m-invoice__subtitle signature-right">GM</span>
                </div>
            </div>-->
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
                forming_report_list: true,
                resultData: {},
                cinfo:{},
                form: {
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
            forming: function () {
                var _this = this;
                var from_date = this.form.from_date;
                var to_date   = this.form.to_date;

                axios.get(base_url+"forming-report/?from_date="+from_date+"&to_date="+to_date).then((response) => {
                    _this.resultData = response.data;

                    setTimeout(function () {
                        jQuery(document).ready(function() {

                                _this.formatData();
                            }
                        );
                    },900);
                });
            },

            formatData(){
                var _this = this;

                var opening_products_qty = _this.resultData.opening_products_qty;
                var prod_forming_details = _this.resultData.prod_forming_details;
                prod_forming_details.forEach(function(item) {

                    item.opening_qty = opening_products_qty[item.product_id];
                    item.total = parseFloat(item.prod_of_month) + parseFloat(item.opening_qty);
                    item.total_issue = parseFloat(item.trans_to_shapping_qty) + parseFloat(item.shapping_westage_qty);
                    item.balance = parseFloat(item.total) - parseFloat(item.total_issue);
                });
            },

            format_date(value){
                if (value) {
                    return moment(String(value)).format('MM/DD/YYYY')
                }
            },

            formatNumber(value) {
                let val = (value/1).toFixed(2).replace(',', '.')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
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
            _this.forming(1);
            _this.company_info();
        },
    }
</script>

