<template>
    <div>
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="ledger" id="listComponent" class="m-form m-form--fit m-form--label-align-right hide_print" >

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

                            <div class="col-md-3">
                                <div class="">
                                    <label class="m-label m-label--single">Sub Code 2:</label>
                                    <div class="m-form__control">
                                        <select class="form-control select2" id="sub_code2_id" data-selectField="sub_code2_id"  v-model="form.sub_code2_id">
                                            <option v-for="(avalue,aindex) in sub_code2_list":value="avalue.id" > {{ avalue.sub_code_title2 +' ('+ avalue.sub_code2 +')'}}</option>
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

                        </div>
                    </div>
                    <div class="col-xl-12 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">

                            <div class="col-md-3 col-md-offset-3">
                                <div class="">
                                    <label class="m-label m-label--single">Type:</label>
                                    <div class="m-form__control">
                                        <select class="form-control select2" id="type" data-selectField="type"  v-model="form.type" >
                                            <option value="customer">Customer</option>
                                            <option value="supplier" >Supplier</option>
                                            <option value="employee" >Employee</option>
                                            <option value="lc" >LC</option>
                                            <option value="project" >Project</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>
                            <div class="col-md-3 col-md-offset-3">
                                <div class="">
                                    <label class="m-label m-label--single"> {{ type_name_title }} </label>
                                    <div class="m-form__control">
                                        <select class="form-control select2" id="type_id" data-selectField="type_id"  v-model="form.type_id" >
                                            <option v-for="(value,index) in type_list" :value="value.id">
                                                {{value.type_name}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div class="col-md-3 col-md-offset-3">
                                <div class="">
                                    <label class="m-label m-label--single"></label>
                                    <div class="m-input-icon m-input-icon--left">
                                        <button type="submit" class="btn btn-sm btn-success" ><i class="la la-search" id="icon_submit"></i> <span>Search</span></button>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div id="m-portlet" class="m-portlet__body" v-if="ledger_report_list">

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
                            <p><span style="font-weight: 600">Ledger Report</span></p>
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


             <div class="" v-if="ledger_report_list">
                <!--begin: Datatable -->
               <div class="table-responsive">
                <table class="table table-bordered table-hover " id="m_table_1">
                    <thead>
                        <tr>
                            <th>Dates</th>
                            <th>Voucher No</th>
                            <th>Account Type</th>
                            <th>Cheque Info</th>
                            <th>Narration</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th v-if="! form.type" >Balance</th>
                        </tr>
                    </thead>
                    <tbody v-if="resultData !=''">
                        <tr v-for="(value,index) in resultData">
                            <td>{{value.date}}</td>
                            <td>{{value.ref_no}}</td>
                            <td>{{value.type}}</td>
                            <td>
                                <span v-html="value.cheque_info"></span>
                            </td>
                            <td>{{value.detail}}</td>
                            <td class="text-right">
                                <span v-if="value.debit >0"> {{value.debit}}</span>
                            </td>
                            <td class="text-right">
                                <span v-if="value.credit >0"> {{value.credit}}</span>
                            </td>
                            <td  class="text-right" v-if="! form.type">{{value.balance}}</td>
                        </tr>
                        <tr v-if="totalDebit>0 || totalCredit>0">
                           <td colspan="5" class="text-right"><strong>Total: </strong></td>
                           <td class="text-right"> <strong> {{ totalDebit }} </strong> </td>
                           <td class="text-right"> <strong> {{ totalCredit }} </strong> </td>
                           <td v-if="! form.type"></td>
                        </tr>
                    </tbody>
                    <tbody  v-else>
                        <tr>
                           <td colspan="7" class="text-center" v-if="form.type">No Data Available.</td>
                           <td colspan="8" class="text-center" v-else >No Data Available.</td>
                        </tr>
                    </tbody>
                </table>
               </div>
              </div>
        </div>
    </div>
</template>

<script>
    import {EventBus} from '../../../vue-assets';
    import moment from 'moment';

    export default {

        props: ['account_list','sub_code2_list'],

        data: function () {
            return {
                ledger_report_list: true,
                resultData: {},
                cinfo:{},

                type_name_title: 'Type Name',
                type_list: [],

                form: {
                    sub_code2_id:'',
                    account_id:'',
                    from_date: '',
                    end_date: '',
                    coa_title: '',
                    type: '',
                    type_id: '',
                },
                totalDebit: '',
                totalCredit: '',

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

            ledger: function () {
                var _this = this;
                if(_this.form.account_id == ''){ _this.form.account_id = 0}
                // if (_this.form.account_id) {
                if (true) {
                    $('#icon_submit').addClass('la-spin');
                    axios.post(base_url + 'ledger-report/ledger-index', this.form)
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
                    $('#account_id').focus()
                    var data = {message: 'Please select Chart Of A/C Head first'};
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

            doExportPdf() {
                var _this = this;
                var type = _this.form.type;
                var type_id  = _this.form.type_id;
                var account_id = _this.form.account_id;
                var from_date = _this.form.from_date;
                var end_date = _this.form.end_date;
                if(account_id == ''){ account_id = 0}
                if (true) {
                    window.open(base_url + 'ledger-report?export=pdf&account_id='+account_id +'&from_date='+from_date +'&end_date='+ end_date +'&type='+type+'&type_id='+type_id, "_blank");
                } else {
                    $('#account_id').focus()
                    var data = {message: 'Please select Chart Of A/C Head first'};
                    this.showMassage(data);

                }
            },

            doExportExcel() {
                var _this = this;
                if(_this.form.account_id == ''){ _this.form.account_id = 0}
                if (true) {
                    window.open(base_url + 'ledger-report?export=excel&account_id='+ _this.form.account_id +'&from_date='+ _this.form.from_date +'&end_date='+ _this.form.end_date +'', "_blank");
                } else {
                    $('#account_id').focus()
                    var data = {message: 'Please select Chart Of A/C Head first'};
                    this.showMassage(data);
                }
            },

            loadTypeDetails(){

                var _this = this;
                var type= _this.form.type;

                axios.get(base_url+"get_account_type_list/"+type).then((response) => {

                    _this.type_name_title = response.data.type_name_title;
                    _this.type_list = response.data.type_list;

                    _this.initSelect2();
                });
            },

            loadSob2COADetails(){

                var _this = this;
                var sub_code2_id= _this.form.sub_code2_id;

                axios.get(base_url+"get_coa_list_by_sub2/"+sub_code2_id).then((response) => {

                    _this.account_list = response.data.account_list;

                    _this.initSelect2();
                });
            },

            initSelect2(){
                setTimeout(function () {
                    var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                    jQuery(document).ready(function() {Select2.init();});
                },250);
            },
        },

        mounted(){
            var _this = this;
            _this.initSelect2();

            $('#listComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'account_id'){
                    _this.form.account_id = selectedType.val();
                    _this.form.coa_title = $("#account_id option:selected").text();
                }else if(dataIndex == 'sub_code2_id'){
                    _this.form.sub_code2_id = selectedType.val();
                    _this.loadSob2COADetails();
                }else if(dataIndex == 'type'){
                    _this.form.type= selectedType.val();
                    _this.loadTypeDetails();
                }else if(dataIndex == 'type_id'){
                    _this.form.type_id= selectedType.val();
                }
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
