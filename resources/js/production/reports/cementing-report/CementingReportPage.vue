<template>
    <div>
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="sales" id="listComponent" class="m-form m-form--fit m-form--label-align-right hide_print" >

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
        <div class="m-portlet__body" v-if="sales_report_list">
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-right">
                    <a href="#" onclick="window.print();return false" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa flaticon-technology"></i><span>Print</span></span></a>
                    <!--<a href="#" class="btn btn-accent btn-sm m-btn  m-btn m-btn&#45;&#45;icon"><span><i class="fa flaticon-doc"></i><span>PDF</span></span></a>-->
                </div>
            </div>
            <br>

            <div class="m-invoice__head print_only">
                <div class="m-invoice__container m-invoice__container--centered">
                    <div class="row invoice-header"  style="padding-top: 0px;padding-bottom:15px;">
                        <div class="col-md-4">
                            <div class="db-data">
                                <p><span style="font-weight: 600">Section :</span> <span>Cementing Stock Report</span></p>
                                <p><span style="font-weight: 600">From Date :</span> <span>{{form.from_date}}</span></p>
                                <p><span style="font-weight: 600">To Date : </span> <span>{{form.to_date}}</span></p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                        <span class="m-invoice__desc">
                             <p><span style="font-weight: 600">Production Report</span></p>
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
            <div class="table-responsive">
            <!--begin: Datatable -->
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                    <tr>
                        <th>S/L</th>
                        <th>Name Of Product</th>
                        <th>Opening</th>
                        <th>Pro. of. month</th>
                        <th>Total</th>
                        <th>Tra. To Dryer</th>
                        <th>Rej.</th>
                        <th>Total Issue</th>
                        <th>Adj</th>
                        <th>Balance</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody v-if="resultData !=''">
                    <tr v-for="(value,index) in resultData">
                        <td>{{++index}}</td>
                        <td>{{value.product_name}}</td>
                        <td>{{value.product_name}}</td>
                        <td>{{value.product_name}}</td>
                        <td>{{value.product_name}}</td>
                        <td>{{value.product_name}}</td>
                        <td>{{value.product_name}}</td>
                        <td>{{value.product_name}}</td>
                        <td>{{value.product_name}}</td>
                        <td>{{value.product_name}}</td>
                        <td>{{value.product_name}}</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="11" class="text-center">No Data Available.</td>
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

        props: [''],

        data: function () {
            return {
                sales_report_list: true,
                resultData: {},
                cinfo:{},

                form: {
                    challan_id: '',
                    product_id: '',
                    customer_id: '',
                    warehouse_id: '',
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
            sales: function () {
                var _this = this;
                axios.post(base_url + 'sales-report/sales', this.form)
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
            _this.sales(1);
            _this.company_info();
        },
    }
</script>

