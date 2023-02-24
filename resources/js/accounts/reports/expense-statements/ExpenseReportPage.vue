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
                            <div class="col-md-3">
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

                            <div  class="col-md-3">
                                <br/>
                                <div class="">
                                    <div class="m-input-icon m-input-icon--left">
                                        <button type="submit" class="btn btn-sm btn-success" ><i class="la la-search " id="icon_submit"></i> <span>Search</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </form>        

        <div class="m-portlet__body" v-if="customer_ledeger_report_list">

            <div class="m-invoice__head print_only">
                <div class="m-invoice__container m-invoice__container--centered">
                    <div class="row invoice-header"  style="padding-top: 0px;padding-bottom:15px;">
                        <div class="col-md-4">
                            <div class="db-data">
                                <p><span style="font-weight: 600">From Date :</span> <span>{{form.from_date}}</span></p>
                                <p><span style="font-weight: 600">To Date : </span> <span>{{form.end_date}}</span></p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                        <span class="m-invoice__desc">
                            <p><span style="font-weight: 600">Expense Statement Summary</span></p>
                            <span>{{cinfo.name}}</span><br>
                            <span>{{cinfo.address}}</span>
                        </span>
                      </div>

                        <div class="col-md-4">
                        <p style="margin-bottom: 62px;">
                            <img :src="cinfo.logo" style="width: 150px; height: 50px;float: right">
                            <!--<img src="img/1200px-Channel-i.svg.png" style="width: 150px;float: right">-->

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
                        <th>PARTICULARS</th>
                        <th>NOTES</th>
                        <th> VALUE IN TAKA</th>
                    </tr>
                </thead>
                <tbody v-if="resultData !=''">
                    <tr v-for="(value,index) in resultData">
                        <td>{{++index}}</td>
                        <td>{{value.sub_code_title2}}</td>
                        <td>{{value.sub_code2}}</td>
                        <td class="text-right">{{value.debit_amount}}</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="11" class="text-center">No Data Available.</td>
                    </tr>
                </tbody>
                <tfoot v-if="resultData !=''">
                    <tr>
                        <td colspan="3" align="right"><strong>Total : </strong></td>
                        <td class="text-right"><strong> {{totalAmount.toFixed(2)}} </strong></td>
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

        props:['permissions','account_list'],

        data: function(){
            return {
                customer_ledeger_report_list:true,
                totalAmount:0,
                resultData: {},
                cinfo:{},

                form:{
                    account_id: '',
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

            sales:function(){
                var _this = this;

                $('#icon_submit').addClass('la-spin');

                axios.post(base_url+'expense-statement-report', this.form)
                    .then( (response) => {
                        _this.resultData  = response.data;
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

                var total = 0;
                _this.resultData.forEach(function(item){

                    total = total + parseFloat(item.debit_amount);
                });

                _this.totalAmount = total;
            },

            doExportPdf() {
                var _this = this;

                window.open(base_url + 'expense-statement-report?export=pdf&account_id='+ _this.form.account_id +'&from_date='+ _this.form.from_date +'&end_date='+ _this.form.end_date +'', "_blank");
            },

            doExportExcel() {
                var _this = this;

                window.open(base_url + 'expense-statement-report?export=excel&account_id='+ _this.form.account_id +'&from_date='+ _this.form.from_date +'&end_date='+ _this.form.end_date +'', "_blank");
            },
        },

        mounted(){
            var _this =this;

            $('#listComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'account_id'){
                    _this.form.account_id = selectedType.val();
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

            $('body').on('click', '#listButton', function() {
                _this.add_form = false;
                _this.customer_ledeger_report_list = true;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').show();
                $('#listButton').hide();
            });

            EventBus.$on('data-changed', (id) => {
                _this.add_form = false;
                _this.customer_ledeger_report_list = true;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').show();
                $('#listButton').hide();
                _this.sales(1);
            });

            _this.sales(1);
            _this.company_info();
        },       
    }
</script>

