<template>
    <div>
        <div class="m-portlet__body">
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-right">
                    <a href="#" onclick="window.print();return false"
                       class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i
                        class="fa flaticon-technology"></i><span>Print</span></span></a>
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
                                                        <h1 class="print-head-title">LC Stock Entry</h1>
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
                                                        {{form.entry_date}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Supplier :</span>
                                                        <span>
                                                        {{form.get_supplier ? form.get_supplier.purchase_supplier_name : ''}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <p>
                                                        <span style="font-weight: 600">Warehouse :</span>
                                                        <span>
                                                        {{form.get_warehouse ? form.get_warehouse.purchase_ware_houses_name : ''}}
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
                                                                <th><b>SL</b></th>
                                                                <th><b>Product</b></th>
                                                                <th><b>H.S Code</b></th>
                                                                <th><b>LC Unit Price (BDT)</b></th>
                                                                <th><b>Stock Entry Qty </b></th>
                                                                <th><b>Unit</b></th>
                                                                <th><b>Gross Weight</b></th>
                                                                <th><b>Net Weight</b></th>
                                                                <th><b>Total Amount (BDT)</b></th>
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
                                                                    {{details.hs_code}}
                                                                </td>
                                                                <td>
                                                                    {{details.unit_price}}
                                                                </td>
                                                                <td>
                                                                    {{details.qty}}
                                                                </td>
                                                                <td>
                                                                    {{details.get_measure_unit ?
                                                                    details.get_measure_unit.measure_unit : ''}}
                                                                </td>
                                                                <td>
                                                                    {{details.gross_weight}}
                                                                </td>
                                                                <td>
                                                                    {{details.net_weight}}
                                                                </td>
                                                                <td>
                                                                    {{details.total_amount}}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="4" class="text-right">Total</td>
                                                                <td class="">
                                                                    {{form.total_qty}}
                                                                </td>
                                                                <td></td>
                                                                <td class="">
                                                                    {{form.total_gross_weight}}
                                                                </td>
                                                                <td class="">
                                                                    {{form.total_net_weight}}
                                                                </td>
                                                                <td>
                                                                    {{form.total_amount}}
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
                edit_form: false,
                view_form: true,
                cinfo: {},
                form: {
                    lc_opening_info_id: '',
                    lc_commercial_invoice_id: '',
                    warehouse_id: '',
                    supplier_id: '',
                    entry_date: '',
                    narration: '',
                    total_qty: '',
                    total_net_weight: '',
                    total_gross_weight: '',
                    total_amount: '',
                    status: 1,
                    details: [
                        {
                            product_id: '',
                            hs_code: '',
                            unit_price: '',
                            qty: '',
                            measure_unit_id: '',
                            gross_weight: '',
                            net_weight: '',
                            total_amount: 0,
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
                axios.get(base_url + 'acc-lc-stock-entry/' + id)
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
