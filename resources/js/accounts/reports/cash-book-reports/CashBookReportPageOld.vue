<template>
    <div>
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="report" id="listComponent" class="m-form m-form--fit m-form--label-align-right  hide_print" >
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
                                <div class="m-form__group m-form__group">
                                    <label class="m-label m-label--single">Chart Of A/C Head:</label>
                                    <div class="m-form__control">
                                        <select class="form-control m-select2 select2" id="account_id" data-selectField="account_id"  v-model="form.account_id" >
                                            <option v-for="(avalue,aindex) in account_lists" :value="avalue.id" > {{ avalue.chart_of_accounts_title +'('+ avalue.chart_of_account_code +')'}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

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
        <div class="m-portlet__body" v-if="cash_book_list">

            <div class="m-invoice__head print_only">
                <div class="m-invoice__container m-invoice__container--centered">
                    <div class="row invoice-header"  style="padding-top: 24px">
                        <div class="col-md-12" >
                            <div class="row" style="border:1px solid #000000; width:100%; height: 125px; ">
                                <div style="width:70%;float: left">
                                    <div style="width: 60px;float: left">
                                        <p>
                                            <img :src="cinfo.logo" style="width: 50px; height: 50px;float: left">
                                        </p>
                                    </div>
                                    <div class="" style="margin-bottom: 10px; width: 490px;float: left">
                                        <h3 style="margin-bottom:5px;font-weight: 600;font-size:20px; ">{{cinfo.name}}</h3>
                                        <p>{{cinfo.address}}</p>
                                        <p>Factory: Nilnagar,Konabari,joydevpur, Gazipur</p>
                                    </div>
                                </div>
                                <div style="width:250px; margin-left:0px;">
                                    <div class="db-data" style="line-height:1.6;">
                                        <span>Phone :</span> <span> 9003516,9026211</span><br>
                                        <span>Mobile:</span> <span></span><br>
                                        <span>Fax :</span> <span> 880-2-9008491</span><br>
                                        <span>Email :</span> <span>diraninsulator@gmail.com </span> <br>
                                        <span>Web :</span> <span>www.dirangroup.net </span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="m-invoice__items" style="text-align: center; margin-top:10px; ">
                        <div class="m-invoice__item ">
                            <span class="m-invoice__subtitle text-center" style="font-weight:600; font-size:20px; ">Cash Book</span>
                        </div>
                    </div>
                </div>
            </div>

            <br><br>
            <!--begin: Datatable -->
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                <tr>
                    <th>S/L</th>
                    <th>Date</th>
                    <th>Acc Code</th>
                    <th>Particular</th>
                    <th>Voucher No</th>
                    <th>Remarks</th>
                    <th style="width: 10%">Debit/Received</th>
                    <th style="width: 10%">Credit/Payment</th>
                    <th style="width: 10%">Balance</th>
                </tr>
                </thead>
                <tbody v-if="resultData !=''">
                <tr v-for="(value,index) in resultData">
                    <td>{{++index}}</td>
                    <td>{{value.payment_date}}</td>
                    <td>{{value.chart_of_account_code}}</td>
                    <td>{{value.chart_of_accounts_title }}</td>
                    <td>{{value.voucher_no }}</td>
                    <td>{{value.particular }}</td>
                    <td>{{value.debit}}</td>
                    <td>{{value.credit}}</td>

                    <td>{{toFloat(value.balance)}}</td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="9" class="text-center">No Data Available.</td>
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

        props: ['account_lists'],

        data: function () {
            return {
                cash_book_list: true,
                resultData: {},
                cinfo:{},
                form: {
                    account_id:'',
                    from_date: '',
                    end_date: '',
                    coa_title: '',
                },
                errors: {},
            };
        },

        methods: {
            comany_info(){
                var _this = this;
                var id = 1;
                axios.get(base_url+'Company-Info/'+id)
                    .then( (response) => {
                        _this.cinfo  = response.data;
                    });
            },


            report: function () {
                var _this = this;

                var account_id = _this.form.account_id;
                var coa_title = _this.form.coa_title;
                var from_date = _this.form.from_date;
                var end_date = _this.form.end_date;

                if (account_id) {

                    $('#icon_submit').addClass('la-spin');
                    axios.get(base_url+"cash-book-report/?account_id="+account_id+"&coa_title="+coa_title+"&from_date="+from_date+"&end_date="+end_date)
                        .then( (response) => {

                            _this.resultData  = response.data;
                            $('#icon_submit').removeClass('la-spin');
                        }).catch(error => {
                        if(error.response.status == 422){
                            this.errors = error.response.data.errors;
                        }else{
                            this.showMassage(error);
                        }
                        $('#icon_submit').removeClass('la-spin');
                    });
                } else {

                    $('#account_id').focus()
                    var data = {message: 'Please select Chart Of A/C Head first'};
                    this.showMassage(data);
                }
            },

            /*report: function () {
                var _this = this;
                axios.post(base_url + 'cash-book-report/report-index', this.form)
                    .then((response) => {
                        _this.resultData = response.data;
                    }).catch(error => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        this.showMassage(error);
                    }
                });
            },*/

            format_date(value){
                if (value) {
                    return moment(String(value)).format('MM/DD/YYYY')
                }
            },

             toFloat(value, places = 2) {
                if(value){
                    return parseFloat(value).toFixed(places);
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

                var account_id = _this.form.account_id;
                var coa_title = _this.form.coa_title;
                var from_date = _this.form.from_date;
                var end_date = _this.form.end_date;

                if (account_id) {

                    window.open(base_url+"cash-book-report/?export=pdf&account_id="+account_id+"&coa_title="+coa_title+"&from_date="+from_date+"&end_date="+end_date, "_blank");
                }else {
                $('#account_id').focus()
                    var data = {message: 'Please select Chart Of A/C Head first'};
                    this.showMassage(data);

                    }
            },

            doExportExcel() {
                var _this = this;

                var account_id = _this.form.account_id;
                var coa_title = _this.form.coa_title;
                var from_date = _this.form.from_date;
                var end_date = _this.form.end_date;

                if (account_id) {

                    window.open(base_url+"cash-book-report/?export=excel&account_id="+account_id+"&coa_title="+coa_title+"&from_date="+from_date+"&end_date="+end_date, "_blank");
                } else {

                    $('#account_id').focus()
                    var data = {message: 'Please select Chart Of A/C Head first'};
                    this.showMassage(data);
                }
            },
        },

        mounted(){
            var _this = this;

            $('#listComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'account_id'){
                    _this.form.account_id = selectedType.val();
                    _this.form.coa_title = $("#account_id option:selected").text();
                }
            });


            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
            jQuery(document).ready(function() {Select2.init()});


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
                            _this.form.from_date = moment(ev.date).format('YYYY-MM-DD');
                        } else if (dateField == 'end_date') {
                            _this.form.end_date = moment(ev.date).format('YYYY-MM-DD');
                        }
                    }
                });
            });
        },

        created(){

            var _this = this;
            //_this.report(1);
            _this.comany_info();
        },
    }
</script>

