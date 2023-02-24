<template>
    <div>
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="ledger" id="listComponent" class="m-form m-form--fit m-form--label-align-right hide_print" >

            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-12 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">

                            <!--<div class="col-md-3">
                                <div class="">
                                    <label class="m-label m-label&#45;&#45;single">SubCode2:</label>
                                    <div class="m-form__control">
                                        <select class="form-control select2" id="sub_code2_id" data-selectField="sub_code2_id"  v-model="form.sub_code2_id" @change="loadChartOfAcctList()">
                                            <option v-for="(svalue,sindex) in sub_code2_list" :value="svalue.id" > {{ svalue.sub_code_title2 +' ('+ svalue.sub_code2 +')'}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m&#45;&#45;margin-bottom-10"></div>
                            </div>-->

                            <div class="col-md-3">
                                <div class="">
                                    <label class="m-label m-label--single">LC No:</label>
                                    <div class="m-form__control">
                                        <select class="form-control select2" id="lc_no" data-selectField="lc_no"  v-model="form.lc_no" required>
                                            <option v-for="(avalue,aindex) in lc_no_list":value="avalue.id" > {{ avalue.lc_no}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div class="col-md-3">
                                <div class="">
                                    <label class="m-label m-label--single">Chart Of A/C Head:</label>
                                    <div class="m-form__control">
                                        <select class="form-control select2" id="account_id" data-selectField="account_id"  v-model="form.account_id" >
                                            <option v-for="(avalue,aindex) in account_list":value="avalue.id" > {{ avalue.chart_of_accounts_title +' ('+ avalue.chart_of_account_code +')'}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div class="col-md-3">
                                <div class="">
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

                            <div class="col-md-3">
                                <div class="">
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
                            <div  class="col-md-3">
                                <div class="">
                                    <label class="m-label m-label--single"></label>
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
        <div id="m-portlet" class="m-portlet__body" v-if="ledger_report_list">
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-right" style="margin-top:3px;">
                    <a href="#" onclick="window.print();return false" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa flaticon-technology"></i><span>Print</span></span></a>
                    <!--<a href="#" onclick="window.print();return false" class="btn btn-accent btn-sm m-btn  m-btn m-btn&#45;&#45;icon"><span><i class="fa flaticon-doc"></i><span>PDF</span></span></a>-->
                </div>
            </div>
            <br>

            <div class="m-invoice__head print_only">
                <div class="m-invoice__container m-invoice__container--centered">
                    <div class="row invoice-header"  style="padding-top: 0px;padding-bottom:15px;">
                        <div class="col-md-4">
                            <div class="db-data">
                                <p><span style="font-weight:600">Chart Of A/C Head :</span> <span style="font-size:10px;">{{form.coa_title}}</span></p>
                                <p><span style="font-weight: 600">From Date :</span> <span>{{format_date(form.from_date)}}</span></p>
                                <p><span style="font-weight: 600">To Date : </span> <span>{{format_date(form.end_date)}}</span></p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                        <span class="m-invoice__desc">
                            <p><span style="font-weight: 600">LC Ledger Report</span></p>
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

            <h3 align="left"> <b>LC No:</b> <span style="font-family: san-serif">{{form.lc_no_title}}</span></h3>

             <div class="" v-if="ledger_report_list">
                <!--begin: Datatable -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover " id="m_table_1">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>COA Title</th>
                                <th>Voucher No</th>
                                <th>Date</th>
                                <th>Voucher Type</th>
                                <th>Narration</th>
                                <th>Party</th>
                                <th>Ref. Type</th>
                                <th>Ref. Code</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <!--<th>Balance</th>-->
                            </tr>
                        </thead>
                        <tbody v-if="resultData !=''">
                            <tr v-for="(value,index) in resultData">
                                <td>{{value.sl}}</td>
                                <td><b>{{value.coa}}</b></td>
                                <td>{{value.voucher_no}}</td>
                                <td>{{format_date(value.date)}}</td>
                                <td>{{value.voucher_type}}</td>
                                <td>{{value.narration}}</td>
                                <td>{{value.party}}</td>
                                <td>{{value.ref_type}}</td>
                                <td>{{value.ref_code}}</td>
                                <td>
                                    {{value.debit}}
                                </td>
                                <td>
                                    {{value.credit}}
                                </td>
                                <!--<td>{{value.balance}}</td>-->
                            </tr>
                        </tbody>
                        <tbody  v-else>
                            <tr>
                               <td colspan="11" class="text-center">No Data Available.</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr v-if="totalDebit>0 || totalCredit>0">
                                <td colspan="9">Total:</td>
                                <td class="text-right"> <strong> {{ totalDebit }} </strong> </td>
                                <td class="text-right"> <strong> {{ totalCredit }} </strong> </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
              </div>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../../vue-assets';
    import moment from 'moment';

    export default {

        props: ['account_list','lc_no_list'],

        data: function () {
            return {
                ledger_report_list: true,
                resultData: {},
                cinfo:{},
               // account_list:[],

                form: {
                    //sub_code2_id:'',
                    account_id:'',
                    lc_no:'',
                    from_date: '',
                    end_date: '',
                    coa_title: '',
                    lc_no_title: ''
                },

                totalDebit: '',
                totalCredit: '',

                errors: {},
            };
        },

        methods: {

            calcTotal(){
                var _this = this;

                var totalDebit = 0;
                var totalCredit = 0;

                var details = this.resultData;
                var length = details.length;
                for(let i = 0; i < length; i++) {
                    totalDebit = Number(details[i].debit) + totalDebit;
                    totalCredit = Number(details[i].credit) + totalCredit;
                }

                _this.totalDebit = totalDebit.toFixed(2);
                _this.totalCredit = totalCredit.toFixed(2);
            },

            company_info(){
                var _this = this;
                var id = 1;
                axios.get(base_url+'Company-Info/'+id)
                    .then( (response) => {
                        _this.cinfo  = response.data;
                    });
                  },

            ledger: function () {
                var _this = this;
                if (_this.form.lc_no) {
                    $('#icon_submit').addClass('la-spin');
                    axios.post(base_url + 'lc-ledger-report/lc-ledger-index', this.form)
                        .then((response) => {
                            _this.resultData = response.data;
                            $('#icon_submit').removeClass('la-spin');
                            _this.calcTotal();
                        }).catch(error => {
                        if (error.response.status == 422) {
                            this.errors = error.response.data.errors;
                        } else {
                            this.showMassage(error);
                        }
                        $('#icon_submit').removeClass('la-spin');
                    });
                } else {
                    $('#lc_no').focus()
                    var data = {message: 'Please select LC No first'};
                    this.showMassage(data);

                }

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
        },

        mounted(){
            var _this = this;

            $('#listComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                /*if(dataIndex == 'sub_code2_id'){
                    _this.form.sub_code2_id = selectedType.val();
                    _this.loadChartOfAcctList();
                }else */
                if(dataIndex == 'account_id'){
                    _this.form.account_id = selectedType.val();
                    _this.form.coa_title = $("#account_id option:selected").text();

                }
                else if(dataIndex == 'lc_no'){
                    _this.form.lc_no = selectedType.val();
                    _this.form.lc_no_title = $("#lc_no option:selected").text();

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
            _this.company_info();
        },
    }
</script>
