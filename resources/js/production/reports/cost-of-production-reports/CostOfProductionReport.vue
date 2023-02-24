<template>
    <div>
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="search" id="listComponent" class="m-form m-form--fit m-form--label-align-right hide_print" >

            <div class="row print_links">
                <div class="col-md-6"></div>
                <div class="col-md-6 text-right" style="margin-top:3px; ">
                    <!-- <a href="#" @click="doExportPdf()" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa fa-file-pdf"></i><span>PDF</span></span></a>
                    <a href="#" @click="doExportExcel()" class="btn btn-accent btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa fa-file-excel"></i><span>Excel</span></span></a> -->
                </div>
            </div>


            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-12 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">

                            <div class="col-md-3">
                                <div class="">
                                    <label class="m-label m-label--single">Product:</label>
                                    <div class="m-form__control">
                                        <select class="form-control select2" id="product_id" data-selectField="product_id"  v-model="form.product_id" >
                                            <option v-for="(avalue,aindex) in product_list":value="avalue.id" > {{ avalue.product_name }}</option>
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
                                        <input type="text" class="form-control form-control-sm m-input datepicker to_date" v-model="form.to_date" id="to_date" readonly placeholder="Select date from picker" />
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
        <div id="m-portlet" class="m-portlet__body" v-if="show_report_list">

            <div class="m-invoice__head print_only">
                <div class="m-invoice__container m-invoice__container--centered">
                    <div class="row invoice-header"  style="padding-top: 0px;padding-bottom:15px;">
                        <div class="col-md-4">
                            <div class="db-data">
                                <p><span style="font-weight:600">Product :</span> <span style="font-size:10px;">{{form.product_title}}</span></p>
                                <p><span style="font-weight: 600">From Date :</span> <span>{{format_date(form.from_date)}}</span></p>
                                <p><span style="font-weight: 600">To Date : </span> <span>{{format_date(form.to_date)}}</span></p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                        <span class="m-invoice__desc">
                            <p><span style="font-weight: 600">Cost of Production Report</span></p>
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


             <div class="" v-if="show_report_list">
                <!--begin: Datatable -->
                <table class="table table-bordered table-hover " id="m_table_1">
                    <thead>
                        <tr>
                            <th class="text-center">Name of Product</th>
                            <th class="text-center">Raw Materials</th>
                            <th class="text-center">Direct Expense</th>
                            <th class="text-center">Indirect Expense</th>
                            <th class="text-center">Total Value</th>
                            <th class="text-center">Unit Cost</th>
                        </tr>
                    </thead>
                    <tbody v-if="resultData !=''">
                        <tr v-for="(value,index) in resultData">
                            <td class="text-right">{{value.product_name}}</td>                            
                            <td class="text-right">{{value.rm_price}}</td>                            
                            <td class="text-right">{{value.direct_expense}}</td>
                            <td class="text-right">
                                {{value.indirect_expense}}
                            </td>
                            <td class="text-right">
                                {{value.total_price}}
                            </td>
                            <td class="text-right">{{value.unit_price}}</td>
                        </tr>
                    </tbody>
                    <tbody  v-else>
                        <tr>
                           <td colspan="6" class="text-center">No Data Available.</td>
                        </tr>
                    </tbody>
                </table>

              </div>
        </div>
    </div>
</template>

<script>
    import {EventBus} from '../../../vue-assets';
    import moment from 'moment';

    export default {

        props: ['product_list'],

        data: function () {
            return {
                show_report_list: true,
                resultData: {},
                cinfo:{},

                form: {
                    product_id:'',
                    from_date: '',
                    to_date: '',
                    product_title: '',
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

            search: function () {
                var _this = this;
                var product_id = _this.form.product_id;
                var from_date = _this.form.from_date;
                var to_date = _this.form.to_date;

                $('#icon_submit').addClass('la-spin');
                axios.get(base_url + 'cost-of-production?product_id='+product_id +'&from_date='+from_date +'&to_date='+ to_date +'')
                    .then((response) => {
                        _this.resultData = response.data;
                        $('#icon_submit').removeClass('la-spin');
                    }).catch(error => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    } else {
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

            doExportPdf() {
                var _this = this;
                var product_id = _this.form.product_id;
                var from_date = _this.form.from_date;
                var to_date = _this.form.to_date;

                window.open(base_url + 'cost-of-production?export=pdf&product_id='+product_id +'&from_date='+from_date +'&to_date='+ to_date +'', "_blank");
            },

            doExportExcel() {
                var _this = this;
                window.open(base_url + 'cost-of-production?export=excel&product_id='+ _this.form.product_id +'&from_date='+ _this.form.from_date +'&to_date='+ _this.form.to_date +'', "_blank");
            },
        },

        mounted(){
            var _this = this;
            $('#listComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'product_id'){
                    _this.form.product_id = selectedType.val();
                    _this.form.product_title = $("#product_id option:selected").text();
                }
            });

            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
            jQuery(document).ready(function() {Select2.init();});

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'YYYY-MM-DD',
                    todayHighlight: true,
                    clearBtn: true,
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    var dateField = $(this).attr('id');

                    if (ev.date == undefined) {
                        if (dateField == 'from_date') {
                            _this.form.from_date = '';
                        } else if (dateField == 'to_date') {
                            _this.form.to_date = '';
                        }
                    } else {
                        if (dateField == 'from_date') {
                            _this.form.from_date = moment(ev.date).format('YYYY-MM-DD');
                        } else if (dateField == 'to_date') {
                            _this.form.to_date = moment(ev.date).format('YYYY-MM-DD');
                        }
                    }
                });
            });
        },

        created(){
            var _this = this;
            _this.company_info();
            _this.form.from_date = moment().format('YYYY-MM-01');
            _this.form.to_date = moment().format('YYYY-MM-DD');

            setTimeout(function () {
                _this.search();
            },250);
        },
    }
</script>