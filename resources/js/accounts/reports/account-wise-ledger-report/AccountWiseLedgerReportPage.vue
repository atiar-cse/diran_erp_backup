<template>
    <div>
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="sales" id="listComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-12 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">
                            <div class="col-md-5">
                                <div class="m-form__group m-form__group">
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

                            <div class="col-md-5">
                                <div class="m-form__group m-form__group">
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
                                <div class="m-form__group m-form__group">
                                    <label class="m-label m-label--single"></label>
                                    <div class="m-input-icon m-input-icon--left">
                                        <button type="submit" class="btn btn-sm btn-success"><i
                                            class="la la-filter"></i> <span>Search</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="m-portlet__body" v-if="sales_report_list">
            <!--begin: Datatable -->
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                <tr>
                    <th>S/L</th>
                    <th>Date</th>
                    <th>Account</th>
                    <th>Debit</th>
                    <th>Credit</th>
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
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="6" class="text-center">No Data Available.</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';

    export default {

        props: [''],

        data: function () {
            return {
                sales_report_list: true,
                resultData: {},
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
            sales: function () {
                var _this = this;
                _this.$loading(true);
                axios.post(base_url + 'sales-report/sales', this.form)
                    .then((response) => {
                        _this.$loading(false);
                        _this.resultData = response.data;
                    })
                    .catch(error => {
                        _this.$loading(false);
                        if (error.response.status == 422) {
                            this.errors = error.response.data.errors;
                        } else {
                            this.showMassage(error);
                        }
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
                            _this.form.from_date = moment(ev.date).format('DD/MM/YYYY');
                        } else if (dateField == 'end_date') {
                            _this.form.end_date = moment(ev.date).format('DD/MM/YYYY');
                        }
                    }
                });
            });
        },

        created() {
            var _this = this;
            _this.sales(1);
        },
    }
</script>

