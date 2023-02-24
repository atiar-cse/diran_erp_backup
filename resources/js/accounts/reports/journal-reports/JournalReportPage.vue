<template>
    <div>
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="journal" id="listComponent" class="m-form m-form--fit m-form--label-align-right hide_print" >
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


        <div class="m-portlet__body" v-if="journal_report_list" >
            <!--<div class="m-invoice__head print_only">
                <div class="m-invoice__container m-invoice__container&#45;&#45;centered">
                    <div class="row invoice-header"  style="padding-top: 0px;padding-bottom:15px;">
                        <div class="col-md-4">
                            <div class="db-data">
                                <p><span style="font-weight: 600">From Date :</span> <span>{{form.from_date}}</span></p>
                                <p><span style="font-weight: 600">To Date : </span> <span>{{form.end_date}}</span></p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                        <span class="m-invoice__desc">
                            <p><span style="font-weight: 600">Journal Entry</span></p>
                            <span>{{cinfo.name}}</span>
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
            </div>-->
            <!--begin: Datatable -->
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                    <tr>
                        <th>S/L</th>
                        <th>Date</th>
                        <th>Account</th>
                        <th>Opening Balance</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody v-if="resultData !=''">
                    <tr v-for="(value,index) in resultData">
                        <td>{{++index}}</td>
                        <td>{{format_date(value.transaction_date)}}</td>
                        <td>{{value.chart_of_accounts_title + ' ('+value.chart_of_account_code +')'}}</td>
                        <td>{{value.opening_balance}}</td>
                        <td>
                            <span v-if="value.debit_amount > 0">
                                {{value.debit_amount}}
                            </span>
                        </td>
                        <td>
                            <span v-if="value.credit_amount > 0">
                                {{value.credit_amount}}
                            </span>

                        </td>
                        <td> {{ calculation(value.chart_of_account_code,value.opening_balance,value.debit_amount,value.credit_amount) }}</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="7" class="text-center">No Data Available.</td>
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
                journal_report_list: true,
                resultData: {},
                cinfo:{},
                form: {
                    from_date: '',
                    end_date: '',
                },
                errors: {},
            };
        },

        methods: {

            calculation(charOfAccId,opn_blnc,dr,cr)
            {
                opn_blnc = Number(opn_blnc);
                dr = Number(dr);
                cr = Number(cr);

                var val = 0;
                var digit = charOfAccId.toString()[0];

                if(digit == 2 || digit == 3)
                {
                    val = opn_blnc + (dr - cr);
                }

                if(digit == 1 || digit == 4)
                {
                    val = opn_blnc + (cr - dr);
                }

                return val.toFixed(2);
            },

            comany_info(){
                var _this = this;
                var id = 1;
                axios.get(base_url+'Company-Info/'+id)
                    .then( (response) => {
                        _this.cinfo  = response.data;
                    });
            },

            journal: function () {
                var _this = this;
                axios.post(base_url + 'journal-report/journal-index', this.form)
                    .then((response) => {
                        _this.resultData = response.data;
                    }).catch(error => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        this.showMassage(error);
                    }
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

            doExportPdf() {
                var _this = this;
                if (_this.form.from_date) {
                    window.open(base_url + 'journal-report?export=pdf&from_date=' + _this.form.from_date + '&end_date=' + _this.form.end_date + '', "_blank");
                }else {
                    $('#from_date').focus()
                    var data = {message: 'Please select form date'};
                    this.showMassage(data);

                }
            },

            doExportExcel() {
                var _this = this;
                if (_this.form.from_date) {
                    window.open(base_url + 'journal-report?export=excel&from_date='+ _this.form.from_date +'&end_date='+ _this.form.end_date +'', "_blank");
                }else {
                    $('#from_date').focus()
                    var data = {message: 'Please select form date'};
                    this.showMassage(data);

                }
            },
        },

        mounted(){
            var _this = this;
            $(document).on("focus", ".datepicker", function () {

                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    var dateField = $(this).attr('id');

                    if (ev.date == undefined) {
                        if (dateField == 'from_date') {
                            _this.form.from_date = '';
                        } else if (dateField == 'end_date') {
                            _this.form.end_date = '';
                        }
                    } else {
                        if (dateField == 'from_date') {
                            _this.form.from_date = moment(ev.date).format('DD/MM/YYYY');
                        } else if (dateField == 'end_date') {
                            _this.form.end_date = moment(ev.date).format('DD/MM/YYYY');
                        }
                    }
                });
            });
        },

        created(){

            var _this = this;
            _this.journal(1);
            _this.comany_info();
        },
    }
</script>

