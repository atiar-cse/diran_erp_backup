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
                                                        <h1 class="print-head-title">LC Other Charges</h1>
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
                                                        <span style="font-weight: 600">LC Others Charge No :</span>
                                                        <span>
                                                        {{form.lc_others_charge_no ? form.lc_others_charge_no : ''}}
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
                                                        {{form.others_charge_date}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Cost Category :</span>
                                                        <span>
                                                        {{form.get_cost_cat ? form.get_cost_cat.cost_category_name : ''}}
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
                                                    <h4>Charges</h4>
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered ">
                                                            <thead class="thead-inverse">
                                                            <tr>
                                                                <th><b>SL.</b></th>
                                                                <th class="text-left"><b>Cost Name </b></th>
                                                                <th><b>Cost Amount </b></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr v-for="(details, index) in form.details">
                                                                <td scope="row">
                                                                    {{++index}}.
                                                                </td>
                                                                <td class="text-left">
                                                                    {{details.get_cost_name ?
                                                                    details.get_cost_name.cost_name : ''}}
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
                    lc_others_charge_no: '',
                    lc_opening_info_id: '',
                    lc_commercial_invoice_id: '',
                    cost_category_id: '',
                    others_charge_date: '',
                    total_cost: '',
                    narration: '',
                    status: 1,
                    details: [
                        {
                            lc_cost_name_id: '',
                            cost_value: '',
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
                _this.$loading(true);
                axios.get(base_url + 'Company-Info/' + id)
                    .then((response) => {
                        _this.$loading(false);
                        _this.cinfo = response.data;
                    });
            },

            show(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'others-charge-entry/' + id)
                    .then((response) => {
                        _this.$loading(false);
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
