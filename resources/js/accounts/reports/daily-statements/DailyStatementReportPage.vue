<template>
    <div>
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="sales" id="listComponent" class="m-form m-form--fit m-form--label-align-right hide_print" >

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

                            <div class="col-md-4">
                                <div class="">
                                    <label id="from_dates" class="m-label m-label--single">From:</label>
                                    <div class="m-form__control">
                                        <div class="input-group date">
                                            <input type="text" class="form-control form-control-sm m-input datepicker from_date" v-model="form.from_date" data-dateField="from_date" readonly placeholder="Select date from picker" id="from_date" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar-check-o"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="">
                                    <label id="end_dates" class="m-label m-label--single">To:</label>
                                    <div class="m-form__control">
                                        <div class="input-group date">
                                            <input type="text" class="form-control form-control-sm m-input datepicker end_date" v-model="form.end_date" data-dateField="end_date" readonly placeholder="Select date from picker" id="end_date" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar-check-o"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div  class="col-md-4">
                                <br/>
                                <div class="">
                                    <div class="m-input-icon m-input-icon--left">
                                        <button type="submit" class="btn btn-sm btn-success" ><i class="la la-search" id="icon_submit"></i> <span>Search</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </form>

        <div class="m-invoice__head print_only">
            <div class="m-invoice__container m-invoice__container--centered">
                <div class="row invoice-header"  style="padding-top: 0px;padding-bottom:15px;">
                    <div class="col-md-4">
                        <div class="db-data">
                            <!--<p><span style="font-weight:600">Chart Of A/C Head :</span> <span style="font-size:10px;">{{form.coa_title}}</span></p>-->
                            <p><span style="font-weight: 600">From Date :</span> <span>{{form.from_date}}</span></p>
                            <p><span style="font-weight: 600">To Date : </span> <span>{{form.end_date}}</span></p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <span class="m-invoice__desc">
                            <p><span style="font-weight:600">Daily Statement Report</span></p>
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
        <div class="m-portlet__body" v-if="sales_report_list">
            <!--begin: Datatable -->
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                    <tr>
                        <th>DATE</th>
                        <th>REFERENCE</th>
                        <th>PURPOSES</th>
                        <th>CHART OF ACCOUNTS</th>
                        <th>REMARKS</th>
                        <th>OPENING BALANCE</th>
                        <th> DEBIT</th>
                        <th> CREDIT</th>
                        <th>BALANCE</th>
                    </tr>
                </thead>
                <tbody v-for="(jrnl,index) in resultData">
                    <tr v-for="(row,index2) in jrnl.details">
                        <td v-if="index2 ==0">{{jrnl.transaction_date}}</td>
                        <td v-if="index2 ==0">{{jrnl.transaction_reference_no}}</td>
                        <td v-if="index2 ==0">{{jrnl.transaction_type_name}}</td>

                        <td v-if="index2 > 0" :colspan="3"></td>

                        <td>{{row.coa.chart_of_accounts_title}} ({{row.coa.chart_of_account_code}})</td>
                        <td>{{row.remarks}}</td>
                        <td  class="text-right">{{row.coa.opening_balance}}</td>
                        <td v-if="row.debit_amount > 0"  class="text-right">{{row.debit_amount}}</td>
                        <td v-else></td>
                        <td v-if="row.credit_amount > 0"  class="text-right">{{row.credit_amount}}</td>
                        <td v-else></td>
                        <td class="text-right">{{row.current_balance}}</td>
                    </tr>
                </tbody>
                <tfoot v-if="resultData !=''">
                    <tr>
                        <td colspan="5" align="right"><strong>Total : </strong></td>
                        <td class="text-right"><strong> {{totalOpening}} </strong></td>
                        <td class="text-right"><strong> {{totalDebit}} </strong></td>
                        <td class="text-right"><strong> {{totalCredit}} </strong></td>
                        <td class="text-right"><strong> {{totalCurrent}} </strong></td>
                    </tr>                    
                </tfoot>
            </table>

        </div>

    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import moment from 'moment';

    export default {

        props:['permissions'],

        data: function(){
            return {
                sales_report_list:true,

                totalOpening:0,
                totalDebit:0,
                totalCredit:0,
                totalCurrent:0,

                resultData: {},
                cinfo:{},
                form: {
                    challan_id: '',
                    product_id: '',
                    customer_id: '',
                    warehouse_id: '',
                    from_date: '',
                    end_date: '',
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

            sales: function () {
                var _this = this;
                var from_date = _this.form.from_date;
                var end_date = _this.form.end_date;
                $('#icon_submit').addClass('la-spin');
                axios.get(base_url+"daily-statement-report/?from_date="+from_date+"&end_date="+end_date)
                    .then( (response) => {
                        _this.resultData  = response.data;
                        //EventBus.$emit('data-changed');
                         _this.calcTotal();
                         $('#icon_submit').removeClass('la-spin');
                    }).catch(error => {
                        if(error.response.status == 422){
                            this.errors = error.response.data.errors;
                        }else{
                            this.showMassage(error);
                        }
                        $('#icon_submit').removeClass('la-spin');
                    });
            },

            format_date(value){
                if (value) {
                    return moment(String(value)).format('DD/MM/YYYY')
                }
            },

            showMassage(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, 'Success Alert', {timeOut: 5000});
                } else {
                    toastr.error(data.message, 'Error Alert', {timeOut: 5000});
                }
            },

            calcTotal(){
                var _this = this;

                var totalOpeningSum = 0;
                var totalDr = 0;
                var totalCr = 0;
                var totalCurrentSum = 0;
                _this.resultData.forEach(function(item){
                    item.details.forEach(function(row){
                        totalOpeningSum = totalOpeningSum + parseFloat(row.coa.opening_balance);

                        if(row.debit_amount !== null)
                            totalDr = totalDr + parseFloat(row.debit_amount);

                        if(row.credit_amount !== null)
                            totalCr = totalCr + parseFloat(row.credit_amount);

                        if(row.debit_amount !== null && row.credit_amount !== null)
                        {
                            var curr_balance = 0;
                            curr_balance = parseFloat(row.coa.opening_balance) + ( parseFloat(row.debit_amount) - parseFloat(row.credit_amount) );
                            row.current_balance = curr_balance.toFixed(2);
                            totalCurrentSum = totalCurrentSum + parseFloat(row.current_balance);
                        }
                    });
                });

                _this.totalOpening = totalOpeningSum.toFixed(2);
                _this.totalDebit = totalDr.toFixed(2);
                _this.totalCredit = totalCr.toFixed(2);
                _this.totalCurrent = totalCurrentSum.toFixed(2);
            }  ,

            doExportPdf() {
                var _this = this;

                window.open(base_url + 'daily-statement-report?export=pdf&from_date='+ _this.form.from_date +'&end_date='+ _this.form.end_date +'', "_blank");
            },

            doExportExcel() {
                var _this = this;

                window.open(base_url + 'daily-statement-report?export=excel&from_date='+ _this.form.from_date +'&end_date='+ _this.form.end_date +'', "_blank");
            },
        },

        mounted(){
            var _this =this;

            $(document).on("focus", ".datepicker", function () {

                $(this).datepicker({
                    format: 'YYYY-MM-DD',
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
                            _this.form.from_date = moment(ev.date).format('YYYY-MM-DD');
                        }else if(dateField == 'end_date'){
                            _this.form.end_date = moment(ev.date).format('YYYY-MM-DD');
                        }
                    }

                });
            });
        },

        created(){

            var _this = this;
            _this.sales(1);
            _this.company_info();
        },
    }
</script>

