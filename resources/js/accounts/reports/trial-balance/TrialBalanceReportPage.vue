<template>
    <div>
        <br>
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="search" id="listComponent" class="m-form m-form--fit m-form--label-align-right hide_print" >

            <div class="row print_links">
                <div class="col-md-6"></div>
                <div class="col-md-6 text-right" style="margin-top:3px; ">
                   <!-- <a href="#" onclick="window.print();return false" class="btn btn-primary btn-sm m-btn  m-btn m-btn&#45;&#45;icon"><span><i class="fa flaticon-technology"></i><span>Print</span></span></a>-->
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
                                    <label id="from_date" class="m-label m-label--single">From:</label>
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
                                    <label id="end_date" class="m-label m-label--single">To:</label>
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
                            <p><span style="font-weight: 600">Trial Balance</span></p>
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

            <div class="m-portlet__body" v-if="trial_balance_report_list">
                <div class="alert alert-info">
                  <strong>Info!</strong> [[[NEGATIVE]]] marked balanced will be transferred Dr to Cr vice versa.
                </div>                 
                <!--begin: Datatable -->
                <table class="table table-bordered table-hover" id="m_table_1">
                    <thead>
                        <tr class="text-center">
                            <th>Sl#</th>
                            <th>SubCode2</th>
                            <th>Account Name</th>
                            <!--<th>Opening Balance</th>-->
                            <th>Debit</th>
                            <th>Credit</th>
                            <!--<th>Balance</th>-->
                        </tr>
                    </thead>
                    <tbody v-if="resultData !=''">
                         <tr v-for="(value,index) in resultData">
                             <td>{{++index}}</td>
                             <td>{{value.sub2_title}}</td>
                             <td>{{value.chart_of_accounts_title}}</td>
                             <!--<td class="text-right">{{value.opening_balance}}</td>-->
                             <td class="text-right">{{value.total_debit}}</td>
                             <td class="text-right">{{value.total_credit}}</td>
                             <!--<td class="text-right">{{value.current_balance}}</td>-->
                        </tr>
                        <tr>
                            <td class="text-right m-timeline-2__item-text--bold"></td>
                            <td class="text-right m-timeline-2__item-text--bold"></td>
                            <td class="text-right m-timeline-2__item-text--bold">Total</td>
                            <!--<td class="text-right">
                               <span class="text-danger">{{grand_total_opening_balance}}</span>
                            </td>-->
                            <td class="text-right">
                               <span class="text-danger">{{grand_total_debit}}</span>
                            </td>
                            <td class="text-right">
                                <span class="text-danger">{{grand_total_credit}}</span>
                            </td>
                            <!--<td class="text-right">
                                <span class="text-danger">{{grand_total_current_balance}}</span>
                            </td>-->
                        </tr>
                    </tbody>
                    <tbody v-else>
                        <tr>
                            <td colspan="5" class="text-center">No Data Available.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>


</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import moment from 'moment';

    export default {

        props:[''],

        data: function(){
            return {
                trial_balance_report_list: true,
                resultData: {},
                cinfo:{},
                grand_total_opening_balance:'',
                grand_total_debit:'',
                grand_total_credit:'',
                grand_total_current_balance:'',
                form: {
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

            trial_balance(){
                var _this = this;
                axios.post(base_url + 'trial-balance-report/trial-balance-index', this.form)
                    .then((response) => {
                        _this.resultData = response.data;
                        setTimeout(function () {
                            jQuery(document).ready(function() {
                                _this.calculateTotalDebitCredit();
                                }
                            );
                        },900);
                    }).catch(error => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        this.showMassage(error);
                    }
                });
            },

            search:function(){
                var _this = this;
                //alert('as');
                $('#icon_submit').addClass('la-spin');
                axios.post(base_url + 'trial-balance-report/trial-balance-report', this.form)
                    .then((response) => {
                        _this.resultData = response.data;
                        setTimeout(function () {
                            jQuery(document).ready(function() {
                                    _this.calculateTotalDebitCredit();
                                    $('#icon_submit').removeClass('la-spin');
                                }
                            );
                        },900);
                    }).catch(error => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        this.showMassage(error);
                    }
                    $('#icon_submit').removeClass('la-spin');
                });
            },

            calculateTotalDebitCredit(){

                var _this = this;
                let total_open_sum  = 0;
                let total_debit_sum  = 0;
                let total_credit_sum = 0;
                let total_current_sum = 0;

                var balance1 = _this.resultData;

                balance1.forEach(function(item) {
                    total_open_sum += parseFloat(item.opening_balance);
                    
                    total_debit_sum += parseFloat(item.total_debit);
                    total_credit_sum += parseFloat(item.total_credit);

                    total_current_sum += parseFloat(item.current_balance);
                });
                _this.grand_total_opening_balance = total_open_sum.toFixed(2);
                _this.grand_total_debit = total_debit_sum.toFixed(2);
                _this.grand_total_credit = total_credit_sum.toFixed(2);
                _this.grand_total_current_balance = total_current_sum.toFixed(2);
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
                //if (_this.form.from_date) {
                    window.open(base_url + 'trial-balance-report?export=pdf&from_date='+ _this.form.from_date +'&end_date='+ _this.form.end_date +'', "_blank");
               /* } else {
                    $('#from_date').focus()
                    var data = {message: 'Please select date.'};
                    this.showMassage(data);
                }*/
            },

            doExportExcel() {
                var _this = this;
                //if (_this.form.from_date) {
                    window.open(base_url + 'trial-balance-report?export=excel&from_date='+ _this.form.from_date +'&end_date='+ _this.form.end_date +'', "_blank");
                /*} else {
                    $('#from_date').focus()
                    var data = {message: 'Please select date'};
                    this.showMassage(data);
                }*/
            },


           
        },

        mounted(){
            var _this =this;

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
            _this.trial_balance(1);
            // _this.search();
            _this.company_info();

        },
    }
</script>

