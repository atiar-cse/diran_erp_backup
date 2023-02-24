<template>
    <div>
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="ledger_subcode2" id="listComponent"
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

                            <div class="col-md-3">
                                <div class="">
                                    <label class="m-label m-label--single">SubCode2:</label>
                                    <div class="m-form__control">
                                        <select class="form-control select2" id="sub_code2_id"
                                                data-selectField="sub_code2_id" v-model="form.sub_code2_id">
                                            <option v-for="(svalue,sindex) in sub_code2_list" :value="svalue.id"> {{
                                                svalue.sub_code_title2 +' ('+ svalue.sub_code2 +')'}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <label class="m-label m-label--single">From Date:</label>
                                    <div class="input-group date">
                                        <input type="text"
                                               class="form-control form-control-sm m-input datepicker from_date"
                                               v-model="form.from_date" id="from_date" readonly
                                               placeholder="Select date from picker"/>
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
                                        <input type="text"
                                               class="form-control form-control-sm m-input datepicker end_date"
                                               v-model="form.end_date" id="end_date" readonly
                                               placeholder="Select date from picker"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>
                            <div class="col-md-2">
                                <div class="">
                                    <label class="m-label m-label--single"></label>
                                    <div class="m-input-icon m-input-icon--left">
                                        <button type="submit" class="btn btn-sm btn-success"><i class="la la-search"
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
        <div id="m-portlet" class="m-portlet__body" v-if="ledger_report_list">

            <div class="m-invoice__head print_only">
                <div class="m-invoice__container m-invoice__container--centered">
                    <div class="row invoice-header" style="padding-top: 0px;padding-bottom:15px;">
                        <div class="col-md-4">
                            <div class="db-data">
                                <p><span style="font-weight:600">Sub Code2 :</span> <span style="font-size:10px;">{{form.sub_code_title2}}</span>
                                </p>
                                <p><span style="font-weight: 600">From Date :</span> <span>{{format_date(form.from_date)}}</span>
                                </p>
                                <p><span style="font-weight: 600">To Date : </span>
                                    <span>{{format_date(form.end_date)}}</span></p>
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
                                <!--<img src="img/1200px-Channel-i.svg.png" style="width: 150px;float: right">-->
                                <img :src="cinfo.logo" style="width: 150px; height: 50px;float: right">
                            </p>
                        </div>
                    </div>
                </div>
            </div>


            <div class="" v-if="ledger_report_list">
                <!--begin: Datatable -->
                <table class="table table-bordered table-hover " id="m_table_1">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Chart Of A/C Head</th>
                        <!-- <th>Remarks</th>
                         <th>Voucher No</th>-->
                        <th>Opening Balance</th>
                        <th>Debit</th>
                        <th>Credit</th>
                        <th>Balance</th>
                    </tr>
                    </thead>
                    <tbody v-if="resultData !=''">
                    <tr v-for="(value,index) in resultData">
                        <td>{{value.date}}</td>
                        <td>{{value.chart_of_accounts_title}} {{ value.chart_of_account_code}}</td>
                        <!--  <td>{{value.remarks}}</td>
                          <td>{{ value.voucher_no}}</td>-->
                        <td>{{ value.opening_balance}}</td>
                        <td>
                            <span v-if="value.debit >0"> {{value.debit}}</span>
                        </td>
                        <td>
                            <span v-if="value.credit >0"> {{value.credit}}</span>
                        </td>
                        <td>{{value.balance}}</td>
                    </tr>
                    </tbody>
                    <tbody v-else>
                    <tr>
                        <td colspan="6" class="text-center">No Data Available.</td>
                    </tr>
                    </tbody>
                    <tfoot v-if="resultData !=''">
                    <tr>

                        <td class="text-right text-danger" colspan="3"><strong>Total : </strong></td>
                        <td class="text-danger"><strong>{{totalDebitAmount.toFixed(2)}}</strong></td>
                        <td class="text-danger"><strong> {{totalCreditAmount.toFixed(2)}} </strong></td>
                        <td></td>


                    </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';

    export default {

        props: ['sub_code2_list'],

        data: function () {
            return {
                ledger_report_list: true,
                totalCreditAmount: 0,
                totalDebitAmount: 0,
                resultData: {},
                cinfo: {},

                form: {
                    sub_code2_id: '',
                    from_date: '',
                    end_date: '',
                    sub_code_title2: '',

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

            ledger_subcode2: function () {
                var _this = this;

                var sub_code2_id = _this.form.sub_code2_id;
                var sub_code_title2 = _this.form.sub_code_title2;
                var from_date = _this.form.from_date;
                var end_date = _this.form.end_date;

                if (sub_code2_id) {
                    $('#icon_submit').addClass('la-spin');
                    _this.$loading(true);
                    axios.post(base_url + "ledger-subcode2-report/?sub_code2_id=" + sub_code2_id + "&sub_code_title2=" + sub_code_title2 + "&from_date=" + from_date + "&end_date=" + end_date)
                        .then((response) => {
                            _this.$loading(false);
                            _this.resultData = response.data;
                            $('#icon_submit').removeClass('la-spin');
                            _this.calcTotal();
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
                } else {
                    $('#sub_code2_id').focus()
                    var data = {message: 'Please select Sub Code2 Head first'};
                    this.showMassage(data);
                }
            },

            /*ledger_subcode2: function () {
                var _this = this;
                    axios.post(base_url + 'ledger-subcode2-report/ledger-index', this.form)
                        .then((response) => {
                            _this.resultData = response.data;

                            _this.calcTotal();
                        })
                    },*/


            calcTotal() {
                var _this = this;
                var totalCredit = 0;
                var totalDebit = 0;
                _this.resultData.forEach(function (item) {

                    totalCredit = totalCredit + Number(item.credit);

                });
                _this.totalCreditAmount = totalCredit;

                _this.resultData.forEach(function (item) {
                    totalDebit = totalDebit + Number(item.debit);
                });
                _this.totalDebitAmount = totalDebit;
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

            doExportPdf() {
                var _this = this;
                var sub_code2_id = _this.form.sub_code2_id;
                var sub_code_title2 = _this.form.sub_code_title2;
                var from_date = _this.form.from_date;
                var end_date = _this.form.end_date;

                if (sub_code2_id) {
                    window.open(base_url + "ledger-subcode2-report/?export=pdf&sub_code2_id=" + sub_code2_id + "&sub_code_title2=" + sub_code_title2 + "&from_date=" + from_date + "&end_date=" + end_date, "_blank");
                } else {

                    $('#sub_code2_id').focus()
                    var data = {message: 'Please select Chart Of A/C Head first'};
                    this.showMassage(data);
                }
            },

            doExportExcel() {
                var _this = this;
                var sub_code2_id = _this.form.sub_code2_id;
                var sub_code_title2 = _this.form.sub_code_title2;
                var from_date = _this.form.from_date;
                var end_date = _this.form.end_date;

                if (sub_code2_id) {
                    window.open(base_url + "ledger-subcode2-report/?export=excel&&sub_code2_id=" + sub_code2_id + "&sub_code_title2=" + sub_code_title2 + "&from_date=" + from_date + "&end_date=" + end_date, "_blank");
                } else {

                    $('#sub_code2_id').focus()
                    var data = {message: 'Please select Chart Of A/C Head first'};
                    this.showMassage(data);
                }
            },
        },

        mounted() {
            var _this = this;

            $('#listComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if (dataIndex == 'sub_code2_id') {
                    _this.form.sub_code2_id = selectedType.val();
                    _this.form.sub_code_title2 = $("#sub_code2_id option:selected").text();
                }
            });

            var Select2 = {
                init: function () {
                    $(".select2").select2({
                        placeholder: "Please select an option"
                    })
                }
            };
            jQuery(document).ready(function () {
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
            _this.company_info();
        },
    }
</script>
