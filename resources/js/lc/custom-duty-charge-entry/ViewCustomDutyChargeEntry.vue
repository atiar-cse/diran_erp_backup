<template>
    <div>
        <div class="m-portlet__body">
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-right">
                    <a class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon" href="#"
                       onclick="window.print();return false"><span><i
                        class="fa flaticon-technology"></i><span>Print</span></span></a>
                    <!--<a href="#" class="btn btn-accent btn-sm m-btn  m-btn m-btn&#45;&#45;icon"><span><i class="fa flaticon-doc"></i><span>PDF</span></span></a>-->
                </div>
            </div>
            <br>

            <div class="row">
                <div class="col-lg-12">
                    <div class="m-portlet">
                        <div class="m-portlet__body m-portlet__body--no-padding">
                            <div class="m-invoice-2">
                                <div class="m-invoice__wrapper">
                                    <div class="m-invoice__head">
                                        <div class="m-invoice__container m-invoice__container--centered">
                                            <div class="row invoice-header" style="padding-top: 24px">
                                                <div class="col-md-9">
                                                    <div class="db-data">
                                                        <h1 class="print-head-title">LC Custom Duty Charge</h1>
                                                        <p class="m--font-danger" v-if="form.approve_status == 0"><span
                                                            style="font-weight: 600">Awaiting Approval</span>
                                                            <span> </span></p>
                                                        <p class="m--font-success" v-if="form.approve_status == 1"><span
                                                            style="font-weight: 600">Approved</span> <span> </span></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-md-offset-3">
                                                    <p style="margin-bottom: 62px;">
                                                        <!--<img src="img/1200px-Channel-i.svg.png" height="30px" width="30px">-->
                                                        <img :src="cinfo.logo"
                                                             style="width: 150px; height: 50px;float: right">
                                                    </p>
                                                    <span class="m-invoice__desc">
                                                         <h3><span>{{cinfo.name}}</span></h3>
                                                        <span>{{cinfo.address}}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-invoice__body m-invoice__body--centered">
                                        <div class="description-section">
                                            <div class="row">
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">LC Custom Duty No :</span>
                                                        <span>
                                                        {{form.lc_custom_duty_no ? form.lc_custom_duty_no : ''}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">LC No :</span>
                                                        <span>
                                                        {{form.get_lc_no ? form.get_lc_no.lc_no : ''}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">CI No :</span>
                                                        <span>
                                                        {{form.get_ci_no ? form.get_ci_no.ci_no : ''}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Date :</span>
                                                        <span>
                                                        {{form.custom_duty_date}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Custom Duty Cost Name :</span>
                                                        <span>
                                                        {{form.get_custom_duty ? form.get_custom_duty.duty_cost_name : ''}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%"
                                                     v-if="form.get_cnf_agent">
                                                    <p>
                                                        <span style="font-weight: 600">C&F Agent / Party :</span>
                                                        <span>
                                                        {{form.get_cnf_agent}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Narration :</span>
                                                        <span>
                                                        {{form.narration}}
                                                    </span>
                                                    </p>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead class="thead-inverse">
                                                            <tr>
                                                                <th><b>SL.</b></th>
                                                                <th class="text-left"><b>Product Name </b></th>
                                                                <th><b>Cost Value </b></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr v-for="(details, index) in form.details">
                                                                <td scope="row">
                                                                    {{++index}}.
                                                                </td>
                                                                <td class="text-left">
                                                                    {{details.get_product ?
                                                                    details.get_product.product_name : ''}}
                                                                </td>
                                                                <td>
                                                                    {{details.cost_value}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-right" colspan="2">Total Amount</td>
                                                                <td class="">
                                                                    {{form.total_cost}}
                                                                </td>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</template>

<script>
    export default {

        props: ['editId'],
        data: function () {
            return {
                data_list: false,
                add_form: false,
                edit_form: false,
                view_form: true,
                cinfo: {},
                form: {
                    lc_custom_duty_no: '',
                    lc_opening_info_id: '',
                    lc_commercial_invoice_id: '',
                    latr_date: '',
                    lc_margin_percentage: '',
                    narration: '',
                    total_qty: '',
                    total_amount: '',
                    latr_percentage: '',
                    latr_total_amount: '',

                    get_cnf_agent: '',

                    status: 1,
                    details: [
                        {
                            lc_latr_payment_entry_id: '',
                            product_id: '',
                            remarks: '',
                            hs_code: '',
                            unit_price: '',
                            qty: '',
                            measure_unit_id: '',
                            total_amount: '',
                        }
                    ],
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

            show(id) {
                var _this = this;
                axios.get(base_url + 'custom-duty-charge-entry/' + id)
                    .then((response) => {
                        _this.form = response.data.data;
                    });
            },

        },

        mounted() {
            var _this = this;

        },

        created() {
            var _this = this;
            _this.show(_this.editId);
            _this.company_info();

        }

    }
</script>
