<template>
    <div>
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="search" id="listComponent"
              class="m-form m-form--fit m-form--label-align-right hide_print">

            <div class="row print_links">
                <div class="col-md-6"></div>
                <div class="col-md-6 text-right" style="margin-top:3px; ">
                    <a href="#" @click="doExportPdf()" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i
                        class="fa fa-file-pdf"></i><span>PDF</span></span></a>
                    <a href="#" @click="doExportExcel()" class="btn btn-accent btn-sm m-btn  m-btn m-btn--icon"><span><i
                        class="fa fa-file-excel"></i><span>Excel</span></span></a>
                </div>
            </div>


            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-12 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">

                            <div class="col-md-4">
                                <div class="">
                                    <label id="from_date2" class="m-label m-label--single">From:</label>
                                    <div class="m-form__control">
                                        <div class="input-group date">
                                            <input type="text"
                                                   class="form-control form-control-sm m-input datepicker from_date"
                                                   v-model="form.from_date" data-dateField="from_date" readonly
                                                   placeholder="Select date from picker" id="from_date"/>
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
                                    <label id="end_date2" class="m-label m-label--single">To:</label>
                                    <div class="m-form__control">
                                        <div class="input-group date">
                                            <input type="text"
                                                   class="form-control form-control-sm m-input datepicker end_date"
                                                   v-model="form.end_date" data-dateField="end_date" readonly
                                                   placeholder="Select date from picker" id="end_date"/>
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
                                <br/>
                                <div class="">
                                    <div class="m-input-icon m-input-icon--left">
                                        <button type="submit" class="btn btn-sm btn-success"><i class="la la-search "
                                                                                                id="icon_submit"></i>
                                            <span>Search</span></button>
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
                    <div class="row invoice-header" style="padding-top: 0px;padding-bottom:15px;">
                        <div class="col-md-4">
                            <div class="db-data">
                                <p><span style="font-weight: 600">From Date :</span> <span>{{form.from_date}}</span></p>
                                <p><span style="font-weight: 600">To Date : </span> <span>{{form.end_date}}</span></p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <span class="m-invoice__desc">
                                <p><span style="font-weight: 600">Loss & Profit Statements</span></p>
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
            <!-- Income -->
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                <tr>
                    <th colspan="4" valign="middle" class="text-center">Income</th>
                </tr>
                <tr>
                    <th>S/L</th>
                    <th>PARTICULARS</th>
                    <th>NOTES</th>
                    <th> VALUE IN TAKA</th>
                </tr>
                </thead>

                <tbody v-if="resultDataIncome !=''">
                <tr v-for="(value,index) in resultDataIncome">
                    <td>{{++index}}</td>
                    <td>{{value.sub_code_title2}}</td>
                    <td>{{value.sub_code2}}</td>
                    <td class="text-right">{{value.credit_amount}}</td>
                </tr>
                <tr>
                    <td colspan="3" align="right"><strong>Total Income: </strong></td>
                    <td class="text-right"><strong> {{totalAmountIncome.toFixed(2)}} </strong></td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="11" class="text-center">No Data Available.</td>
                </tr>
                </tbody>

                <thead>
                <tr>
                    <th colspan="4" valign="middle" class="text-center">Expense</th>
                </tr>
                <tr>
                    <th>S/L</th>
                    <th>PARTICULARS</th>
                    <th>NOTES</th>
                    <th> VALUE IN TAKA</th>
                </tr>
                </thead>
                <tbody v-if="resultDataExpense !=''">
                <tr v-for="(value,index) in resultDataExpense">
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
                <tfoot v-if="resultDataExpense !=''">
                <tr>
                    <td colspan="3" align="right"><strong>Total Expense: </strong></td>
                    <td class="text-right"><strong> {{totalAmountExpense.toFixed(2)}} </strong></td>
                </tr>
                <tr>
                    <td colspan="3" align="right"><strong>Net Profit: </strong></td>
                    <td class="text-right"><strong> {{netProfit.toFixed(2)}} </strong></td>
                </tr>
                </tfoot>
            </table>

            <!-- Expense -->
            <table class="table table-bordered table-hover" id="m_table_1">

            </table>
        </div>

    </div>
</template>

<script>

    import {EventBus} from '../../../vue-assets';
    import moment from 'moment';

    export default {

        props: ['permissions'],

        data: function () {
            return {
                customer_ledeger_report_list: true,

                totalAmountIncome: 0,
                totalAmountExpense: 0,
                netProfit: 0,

                resultDataIncome: {},
                resultDataExpense: {},
                cinfo: {},
                form: {
                    from_date: '',
                    end_date: '',
                },
                errors: {},
            };
        },

        methods: {

            company_info() {
                var _this = this;
                var id = 1;
                _this.$loading(true);
                axios.get(base_url + 'Company-Info/' + id)
                    .then((response) => {
                        _this.$loading(false);
                        _this.cinfo = response.data;
                    });
            },

            search: function () {
                var _this = this;

                $('#icon_submit').addClass('la-spin');
                _this.$loading(true);
                axios.post(base_url + 'loss-profit-statement-report', this.form)
                    .then((response) => {
                        _this.$loading(false);
                        _this.resultDataIncome = response.data.income;
                        _this.resultDataExpense = response.data.expense;
                        //EventBus.$emit('data-changed');
                        _this.calcTotalIncome();
                        _this.calcTotalExpense();
                        $('#icon_submit').removeClass('la-spin');
                    })
                    .catch(error => {
                        _this.$loading(false);
                        if (error.response.status == 422) {
                            this.errors = error.response.data.errors;
                        } else {
                            this.showMassage(error);
                        }
                        $('#icon_submit').removeClass('la-spin');
                    });
            },

            format_date(value) {
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

            calcTotalIncome() {
                var _this = this;

                var total = 0;
                _this.resultDataIncome.forEach(function (item) {

                    total = total + parseFloat(item.credit_amount);
                });

                _this.totalAmountIncome = total;
            },

            calcTotalExpense() {
                var _this = this;
                var total = 0;
                _this.resultDataExpense.forEach(function (item) {
                    total = total + parseFloat(item.debit_amount);
                });
                _this.totalAmountExpense = total;
                _this.netProfit = _this.totalAmountIncome - _this.totalAmountExpense;
            },

            doExportPdf() {
                var _this = this;
                window.open(base_url + 'loss-profit-statement-report?export=pdf&from_date=' + _this.form.from_date + '&end_date=' + _this.form.end_date + '', "_blank");
            },

            doExportExcel() {
                var _this = this;
                window.open(base_url + 'loss-profit-statement-report?export=excel&from_date=' + _this.form.from_date + '&end_date=' + _this.form.end_date + '', "_blank");
            },
        },

        mounted() {
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
                            _this.form.from_date = moment(ev.date).format('YYYY-MM-DD');
                        } else if (dateField == 'end_date') {
                            _this.form.end_date = moment(ev.date).format('YYYY-MM-DD');
                        }
                    }
                });
            });
        },

        created() {

            var _this = this;

            $('body').on('click', '#listButton', function () {
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
                _this.search(1);
            });

            _this.search(1);
            _this.company_info();
        },
    }
</script>

