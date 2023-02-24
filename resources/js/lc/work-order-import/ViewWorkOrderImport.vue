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
                                                    <h1 class="print-head-title">LC Work Order</h1>
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
                                                    <span style="font-weight: 600">Work Order No :</span>
                                                    <span>
                                                        {{form.work_order_no}}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-md-6" style="float:left;width:50%">
                                                <p>
                                                    <span style="font-weight: 600">Order Date :</span>
                                                    <span>
                                                        {{form.order_date}}
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
                                                    <span style="font-weight: 600">Narration :</span>
                                                    <span>
                                                        {{form.narration}}
                                                    </span>
                                                </p>
                                            </div>
                                            <div class="col-md-6" style="float:left;width:50%">
                                                <p>
                                                    <span style="font-weight: 600">COA :</span>
                                                    <span>
                                                        {{form.get_coa ? form.get_coa.chart_of_accounts_title : ''}}
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
                                                                {{details.get_product ? details.get_product.product_name : ''}}
                                                            </td>
                                                            <td>
                                                                {{details.remarks}}
                                                            </td>
                                                            <td>
                                                                {{details.get_currency ? details.get_currency.name : ''}}
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
                                                            <td colspan="5" class="text-right">Total </td>
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
    import {EventBus} from '../../vue-assets';
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
                    work_order_no: '',
                    supplier_id: '',
                    order_date: '',
                    narration: '',
                    total_qty: 0,
                    total_amount: 0,
                    total_amount_taka: 0,
                    approve: 0,
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

            company_info(){
                var _this = this;
                var id = 1;
                axios.get(base_url + 'Company-Info/' + id)
                    .then((response) => {
                        _this.cinfo = response.data;
                    });
            },

            show(id) {
                var _this = this;
                axios.get(base_url + 'work-order-import/' + id)
                    .then((response) => {
                        _this.form = response.data.data;
                    });
            },

        },

        mounted(){
            var _this = this;

        },

        created(){
            var _this = this;
            _this.show(_this.editId);
            _this.company_info();

        }

    }
</script>
