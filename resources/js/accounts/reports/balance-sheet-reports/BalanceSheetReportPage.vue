<template>
    <div>
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="search" id="listComponent"
              class="m-form m-form--fit m-form--label-align-right hide_print">

            <div class="row print_links">
                <div class="col-md-6"></div>
                <div class="col-md-6 text-right" style="margin-top:3px; ">
                    <!-- <a href="#" onclick="window.print();return false" class="btn btn-primary btn-sm m-btn  m-btn m-btn&#45;&#45;icon"><span><i class="fa flaticon-technology"></i><span>Print</span></span></a>-->
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
                                    <label id="from_dates" class="m-label m-label--single">From:</label>
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
                                    <label id="end_dates" class="m-label m-label--single">To:</label>
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
                            <p><span style="font-weight: 600">Balance Sheet</span></p>
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
                    <th colspan="4" valign="middle" class="text-center">Assets</th>
                </tr>
                <tr>
                    <th>S/L</th>
                    <th>PARTICULARS</th>
                    <th>NOTES</th>
                    <th> VALUE IN TAKA</th>
                </tr>
                </thead>

                <tbody v-if="resultDataAssets !=''">
                <tr v-for="(value,index) in resultDataAssets">
                    <td>{{++index}}</td>
                    <td>{{value.sub_code_title}}</td>
                    <td>{{value.sub_code}}</td>
                    <td class="text-right">{{parseFloat(value.balance).toFixed(2)}}</td>
                </tr>
                <tr>
                    <td colspan="3" align="right"><strong>Total Assets: </strong></td>
                    <td class="text-right"><strong> {{totalAmountAssets.toFixed(2)}} </strong></td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="11" class="text-center">No Data Available.</td>
                </tr>
                </tbody>

                <thead>
                <tr>
                    <th colspan="4" valign="middle" class="text-center">Liabilities</th>
                </tr>
                <tr>
                    <th>S/L</th>
                    <th>PARTICULARS</th>
                    <th>NOTES</th>
                    <th> VALUE IN TAKA</th>
                </tr>
                </thead>
                <tbody v-if="resultDataLiabilities !=''">
                <tr v-for="(value,index) in resultDataLiabilities">
                    <td>{{++index}}</td>
                    <td>{{value.sub_code_title}}</td>
                    <td>{{value.sub_code}}</td>
                    <td class="text-right">{{parseFloat(value.balance).toFixed(2)}}</td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="11" class="text-center">No Data Available.</td>
                </tr>
                </tbody>
                <tfoot v-if="resultDataLiabilities !=''">
                <tr>
                    <td colspan="3" align="right"><strong>Total Liabilities: </strong></td>
                    <td class="text-right"><strong> {{totalAmountLiabilities.toFixed(2)}} </strong></td>
                </tr>
                </tfoot>
            </table>

            <!-- Expense -->
            <table class="table table-bordered table-hover" id="">

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
                totalAmountAssets: 0,
                totalAmountLiabilities: 0,
                netProfit: 0,
                resultDataAssets: {},
                cinfo: {},
                resultDataLiabilities: {},

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
                axios.get(base_url + 'Company-Info/' + id)
                    .then((response) => {
                        _this.cinfo = response.data;
                    });
            },


            search: function () {
                var _this = this;

                $('#icon_submit').addClass('la-spin');
                _this.$loading(true);
                axios.post(base_url + 'balance-sheet-report', _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        _this.resultDataAssets = response.data.assets;
                        _this.resultDataLiabilities = response.data.liabilities;
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
                _this.resultDataAssets.forEach(function (item) {
                    total = total + parseFloat(item.balance);
                });
                _this.totalAmountAssets = total;
            },

            calcTotalExpense() {
                var _this = this;
                var total = 0;
                _this.resultDataLiabilities.forEach(function (item) {
                    total = total + parseFloat(item.balance);
                });
                _this.totalAmountLiabilities = total;
                _this.netProfit = _this.totalAmountAssets - _this.totalAmountLiabilities;
            },

            doExportPdf() {
                var _this = this;
                window.open(base_url + 'balance-sheet-report?export=pdf&from_date=' + _this.form.from_date + '&end_date=' + _this.form.end_date + '', "_blank");
            },

            doExportExcel() {
                var _this = this;
                window.open(base_url + 'balance-sheet-report?export=excel&from_date=' + _this.form.from_date + '&end_date=' + _this.form.end_date + '', "_blank");
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

