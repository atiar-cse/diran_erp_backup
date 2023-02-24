<template>
    <div>
        <div class="m-portlet__body">
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-right">
                    <a href="#" onclick="window.print();return false"
                       class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i
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
                                                        <h1 class="print-head-title">LC Proforma Invoice</h1>
                                                        <p v-if="form.approve_status == 0" class="m--font-danger"><span
                                                            style="font-weight: 600">Awaiting Approval</span>
                                                            <span> </span></p>
                                                        <p v-if="form.approve_status == 1" class="m--font-success"><span
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
                                                        <span style="font-weight: 600">PI No :</span>
                                                        <span>
                                                        {{form.pi_no}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Work Order No :</span>
                                                        <span>
                                                        {{form.get_work_order.work_order_no}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Supplier / Exporter :</span>
                                                        <span>
                                                        {{form.get_supplier ? form.get_supplier.purchase_supplier_name : ''}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">PI Date :</span>
                                                        <span>
                                                        {{form.pi_date}}
                                                    </span>
                                                    </p>
                                                </div>

                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">USD A/C No :</span>
                                                        <span>
                                                        {{form.usd_account_no}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Consignee :</span>
                                                        <span>
                                                        {{form.consignee}}
                                                    </span>
                                                    </p>
                                                </div>

                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Swift Code :</span>
                                                        <span>
                                                        {{form.swift_code}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Terms Of Loading :</span>
                                                        <span>
                                                        {{form.terms_of_loading}}
                                                    </span>
                                                    </p>
                                                </div>

                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Port of Discharge :</span>
                                                        <span>
                                                        {{form.port_of_discharger}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Delivery Time :</span>
                                                        <span>
                                                        {{form.delivery_time}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Terms Of Delivery :</span>
                                                        <span>
                                                        {{form.terms_of_delivery}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Terms of Payment :</span>
                                                        <span>
                                                        {{form.terms_of_payment}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Origin Of Goods :</span>
                                                        <span>
                                                        {{form.get_country ? form.get_country.country_name : ''}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Final Destination :</span>
                                                        <span>
                                                        {{form.final_destination}}
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
                                                            <thead>
                                                            <tr>
                                                                <th><b>SL</b></th>
                                                                <th><b>Product</b></th>
                                                                <th><b>Remarks</b></th>
                                                                <th><b>Currency</b></th>
                                                                <th><b>Unit Price</b></th>
                                                                <th><b>Qty</b></th>
                                                                <th><b>Total Price</b></th>
                                                                <th><b>BDT Rate</b></th>
                                                                <th><b>Total Taka</b></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tr v-for="(details, index) in form.details">
                                                                <td scope="row">
                                                                    {{++index}}
                                                                </td>
                                                                <td>
                                                                    {{details.get_product ?
                                                                    details.get_product.product_name : ''}}
                                                                </td>
                                                                <td>
                                                                    {{details.remarks}}
                                                                </td>
                                                                <td>
                                                                    {{details.get_currency ? details.get_currency.name :
                                                                    ''}}
                                                                </td>
                                                                <td>
                                                                    {{details.unit_price}}
                                                                </td>
                                                                <td>
                                                                    {{details.qty}}
                                                                </td>
                                                                <td>
                                                                    {{details.total_price}}
                                                                </td>
                                                                <td>
                                                                    {{details.bdt_convert_rate}}
                                                                </td>
                                                                <td>
                                                                    {{details.total_amount_taka}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="5" class="text-right">Total</td>
                                                                <td class="">
                                                                    {{form.total_qty}}
                                                                </td>
                                                                <td class="">
                                                                    {{form.total_amount}}
                                                                </td>
                                                                <td></td>
                                                                <td>
                                                                    {{form.total_amount_taka}}
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
                product_list: false,
                add_form: false,
                edit_form: false,
                view_form: true,
                cinfo: {},

                form: {
                    pi_no: '',
                    work_order_id: '',
                    supplier_id: '',
                    supplier_info_display: '',
                    usd_account_no: '',
                    pi_date: '',
                    consignee: '',
                    swift_code: '',
                    terms_of_loading: '',
                    port_of_discharger: '',
                    delivery_time: '',
                    terms_of_delivery: '',
                    terms_of_payment: '',
                    origin_of_goods: '',
                    final_destination: '',
                    narration: '',
                    total_qty: 0,
                    total_amount: 0,
                    total_amount_taka: 0,
                    approve: '',
                    details: [
                        {
                            product_id: '',
                            remarks: '',
                            currency_id: '1',
                            unit_price: '',
                            qty: '',
                            total_price: 0,
                            bdt_convert_rate: '',
                            total_amount_taka: 0,
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
                axios.get(base_url + 'proforma-invoice-entry/' + id)
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
