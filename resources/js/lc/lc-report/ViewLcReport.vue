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
                                                    <h3 class="print-head-title">LC-CI-Landed Cost Calculation</h3>
                                                    <p v-if="form.lc_commercial.ci_landed_status == 0" class="m--font-danger"><span
                                                            style="font-weight: 600">Awaiting Approval</span>
                                                        <span> </span></p>
                                                    <p v-if="form.lc_commercial.ci_landed_status == 1" class="m--font-success"><span
                                                            style="font-weight: 600">Approved</span> <span> </span></p>
                                                </div>

                                                <div class="invoice-payment">
                                                    <p>FOR THE YEAR-{{the_year}}</p>
                                                    <ul class="list-unstyled">
                                                        <li><strong>IMPORT REGISTER NO (IR) #:</strong>
                                                            {{form.lc_work_order.work_order_no}}
                                                        </li>
                                                        <li><strong>SUPPLIERS NAME :</strong>
                                                            {{ form.lc_work_order.get_supplier.purchase_supplier_name}}
                                                        </li>
                                                        <li><strong>LETTER OF CREDIT NO. :</strong>
                                                            {{form.lc_opening.lc_no}}
                                                        </li>
                                                        <li><strong>LETTER OF CREDIT VALUE USD $ :</strong>
                                                            {{form.lc_work_order.total_amount}}
                                                        </li>
                                                        <li><strong>CONVERTION RATE  TK. :</strong>
                                                            {{form.lc_work_order.total_amount_taka / form.lc_work_order.total_amount}}
                                                        </li>
                                                        <li><strong>LETTER OF CREDIT VALUE TK. :</strong>
                                                            {{form.lc_work_order.total_amount_taka}}
                                                        </li>
                                                        <li><strong>INVOICE NO. & DATE :</strong>
                                                            {{form.lc_commercial.invoice_no}}, {{form.lc_commercial.invoice_date}}
                                                        </li>
                                                        <li><strong>INVOICE VALUE :</strong>
                                                            {{form.lc_commercial.total_amount}}
                                                        </li>
                                                        <li><strong>INVOICE QUANTITY :</strong>
                                                            {{form.lc_commercial.total_qty}}
                                                        </li>
                                                        <li><strong>PORT :</strong> {{form.lc_commercial.arrived_port}}
                                                        </li>
                                                        <li>
                                                            <strong>GOODS NAME :</strong>
                                                            <span v-if="form.lc_commercial.goods_name">{{form.lc_commercial.goods_name}}</span>
                                                            <span v-else>RAW MATERIALS</span>
                                                        </li>
                                                    </ul>
                                                    <p>{{form.lc_opening.get_opening_bank.accounts_bank_names}}</p>
                                                    <p>
                                                        <span style="font-weight: 600">Delivery Type :</span>
                                                        <span v-if="form.lc_commercial.ci_delivery_type == 1"
                                                              class="badge badge-pill badge-info">All Products</span>
                                                        <span v-if="form.lc_commercial.ci_delivery_type == 2"
                                                              class="badge badge-pill badge-warning">Partial Products</span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-md-offset-3">
                                                <p style="margin-bottom: 62px;">
                                                    <img :src="cinfo.logo" style="width: 150px; height: 50px;float: right">
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
                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-hover" id="m_table_1">
                                                        <thead>
                                                        <tr>
                                                            <th colspan="4"></th>
                                                            <th v-for="(pi_item, index) in form.lc_commercial_details">
                                                                <strong>{{pi_item.get_product.product_name}}</strong>
                                                            </th>
                                                            <th class="width-100 text-right text-success"><strong>Total TK</strong>
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="4"></th>
                                                            <th v-for="(ci_item, index) in form.lc_commercial_details">
                                                                <strong>{{ci_item.qty}} {{ci_item.get_measure_unit.measure_unit}}</strong>
                                                                ({{ci_item.net_weight}} KG)
                                                            </th>
                                                            <th class="width-100 text-right text-success">
                                                                <strong>{{form.lc_commercial.total_net_weight}} KG</strong>
                                                            </th>
                                                        </tr>
                                                        <tr>
                                                            <th><strong>Particulars</strong></th>
                                                            <th></th>
                                                            <th><strong>Details</strong></th>
                                                            <th></th>
                                                            <th v-for="(pi_item, index) in form.lc_commercial_details">
                                                                <strong>{{(pi_item.total_amount / form.bdt_convert_rate.bdt_convert_rate).toFixed(2)}} USD</strong>
                                                            </th>
                                                            <th class="width-100 text-success">
                                                                <strong>{{(form.lc_commercial.total_amount / form.bdt_convert_rate.bdt_convert_rate).toFixed(2)}} USD</strong>
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr>
                                                            <td>C& F VALUE:</td>
                                                            <td v-if="form.lc_commercial.ci_delivery_type == 1">
                                                                MARGIN <span v-if="form.lc_commercial.ci_delivery_type==1">{{form.lc_margin.margin_percentage}}%</span>
                                                            </td>
                                                            <td v-else>MARGIN <span v-if="form.lc_commercial.ci_delivery_type==1">{{form.lc_margin.margin_percentage}}</span> </td>
                                                            <td></td>
                                                            <td>{{form.lc_commercial.ci_margin}}</td>
                                                            <td v-for="(ci_item, index) in form.lc_commercial_details">
                                                                {{Number(form.lc_commercial.ci_margin / form.lc_commercial.total_net_weight * ci_item.net_weight).toFixed(2)}}
                                                            </td>
                                                            <td scope="row" class="width-100 text-right text-success">
                                                                {{form.lc_commercial.ci_margin}}
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td></td>
                                                            <td v-if="form.lc_commercial.ci_delivery_type == 1">
                                                                LATR: <span v-if="form.lc_latr != null">{{form.lc_latr.latr_percentage}}%</span>
                                                                <span v-else>N/A</span>
                                                            </td>
                                                            <td v-else>LATR: N/A </td>
                                                            <td></td>
                                                            <td v-if="form.lc_latr != null">{{form.lc_latr.latr_total_amount}}</td>
                                                            <td v-else>N/A</td>
                                                            <td v-for="(ci_item, index) in form.lc_commercial_details">
                                                                <span v-if="form.lc_latr != null">{{Number(form.lc_latr.latr_total_amount / form.lc_commercial.total_net_weight * ci_item.net_weight).toFixed(2)}}</span>
                                                                <span v-else>N/A</span>
                                                            </td>
                                                            <td scope="row" class="width-100 text-right text-success">
                                                                <span v-if="form.lc_latr != null">{{form.lc_latr.latr_total_amount}}</span>
                                                                <span v-else>N/A</span>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>LC OPENING CHARGES</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>{{form.lc_commercial.ci_opening_charge}}</td>
                                                            <td v-for="(ci_item, index) in form.lc_commercial_details">
                                                                {{Number(form.lc_commercial.ci_opening_charge / form.lc_commercial.total_net_weight * ci_item.net_weight).toFixed(2)}}
                                                            </td>
                                                            <td scope="row" class="width-100 text-right text-success">
                                                                {{form.lc_commercial.ci_opening_charge}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>BANK EXPENSES</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>{{form.lc_commercial.ci_bank_expenses}}</td>
                                                            <td v-for="(ci_item, index) in form.lc_commercial_details">
                                                                {{Number(form.lc_commercial.ci_bank_expenses / form.lc_commercial.total_net_weight * ci_item.net_weight).toFixed(2)}}
                                                            </td>
                                                            <td scope="row" class="width-100 text-right text-success">
                                                                {{form.lc_commercial.ci_bank_expenses}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>INSURANCE</td>
                                                            <td></td>
                                                            <td></td>
                                                            <td>{{form.lc_commercial.ci_insurance}}</td>
                                                            <td v-for="(ci_item, index) in form.lc_commercial_details">
                                                                {{Number(form.lc_commercial.ci_insurance / form.lc_commercial.total_net_weight * ci_item.net_weight).toFixed(2)}}
                                                            </td>
                                                            <td scope="row" class="width-100 text-right text-success">
                                                                {{form.lc_commercial.ci_insurance}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"></td>
                                                            <td class="text-info">{{grand_total_basic_expenses}}</td>
                                                            <td v-for="(ci_item, index) in form.lc_commercial_details">

                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"></td>
                                                            <td> &nbsp;</td>
                                                            <td v-for="(ci_item, index) in form.lc_commercial_details">
                                                            </td>
                                                            <td></td>
                                                        </tr>

                                                        <tr v-for="(cust_charge, index) in form.lc_custom_duty_charges">
                                                            <td v-if="index ==0">CUSTOM DUTY</td>
                                                            <td v-else></td>

                                                            <td>{{cust_charge.get_custom_duty.duty_cost_name}}</td>
                                                            <td></td>
                                                            <td>{{cust_charge.total_cost}}</td>
                                                            <td v-for="(cust_charge_item, index) in cust_charge.get_details">
                                                                {{cust_charge_item.cost_value}}
                                                            </td>
                                                            <td scope="row" class="width-100 text-right text-success">
                                                                {{cust_charge.total_cost}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"></td>
                                                            <td class="text-info">{{grand_total_customs_expenses}}</td>
                                                            <td v-for="(ci_item, index) in form.lc_commercial_details">

                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"></td>
                                                            <td> &nbsp;</td>
                                                            <td v-for="(ci_item, index) in form.lc_commercial_details">
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                        </tbody>

                                                        <tbody v-for="(oth_charge, index) in form.lc_others_charges">

                                                        <tr v-for="(oth_charge_item, index2) in oth_charge.get_details">
                                                            <td v-if="index2 ==0">
                                                                {{oth_charge.get_cost_cat.cost_category_name}}
                                                            </td>
                                                            <td v-else></td>

                                                            <td>{{oth_charge_item.get_cost_name.cost_name}}</td>
                                                            <td></td>
                                                            <td>{{oth_charge_item.cost_value}}</td>
                                                            <td v-for="(ci_item, index) in form.lc_commercial_details">
                                                                {{Number(oth_charge_item.cost_value / form.lc_commercial.total_net_weight * ci_item.net_weight).toFixed(2)}}
                                                            </td>
                                                            <td scope="row" class="width-100 text-right text-success">
                                                                {{oth_charge_item.cost_value}}
                                                            </td>
                                                        </tr>
                                                        </tbody>

                                                        <tbody>
                                                        <tr>
                                                            <td colspan="4" class="text-right"></td>
                                                            <td v-for="(ci_item, index) in form.lc_commercial_details">
                                                            </td>
                                                            <td class="text-info">{{grand_total_taka}}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="4" class="text-right">
                                                                TOTAL LANDED COST (TAKA)
                                                            </td>
                                                            <td v-for="(item, index) in grand_total_products"
                                                                class="text-success">
                                                                {{item}}
                                                            </td>
                                                            <td class="text-info">{{grand_total_products_taka.toFixed(2)}}</td>
                                                        </tr>

                                                        <tr style="background: #f1f1f1;">
                                                            <td colspan="4" class="text-right"> <<< PER UNIT COST WITH VAT >>>
                                                            </td>
                                                            <td v-for="(ci_item, index) in form.lc_commercial_details">
                                                                {{Number(grand_total_products[index] / ci_item.qty).toFixed(2)}}
                                                            </td>
                                                            <td scope="row" class="width-100 text-right text-success">
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="4" class="text-right">Less VAT</td>
                                                            <td v-for="(ci_item, index) in form.lc_commercial_details">
                                                                <span v-if="form.vat_products.length">
                                                                    {{Number(form.vat_products[index].cost_value / ci_item.qty).toFixed(2)}}
                                                                </span>
                                                                <span v-else >n/a</span>
                                                            </td>
                                                            <td scope="row" class="width-100 text-right text-success">
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td colspan="4" class="text-right">
                                                                PER UNIT COST WITHOUT VAT
                                                            </td>
                                                            <td v-for="(ci_item, index) in form.lc_commercial_details">
                                                                <span v-if="form.vat_products.length">
                                                                    {{ Number((grand_total_products[index] / ci_item.qty ) - (form.vat_products[index].cost_value / ci_item.qty )).toFixed(2)}}
                                                                </span>
                                                                <span v-else >n/a</span>
                                                            </td>
                                                            <td scope="row" class="width-100 text-right text-success">
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

        data:function(){
            return {
                product_list: false,
                add_form: false,
                edit_form: false,
                view_form: true,

                the_year: '',
                grand_total_basic_expenses: '',
                grand_total_customs_expenses: '',
                grand_total_taka: 0,
                grand_total_products_taka: 0,

                products_margin: {},
                products_latr: {},

                products_opening_charges: {},
                products_bank_expenses: {},
                products_insurance: {},

                products_customs: {},
                products_others: {},

                grand_total_products: {},

                cinfo:{},

                form: {
                    lc_opening_info_id: '',
                    supplier_id: '',
                    closing_date: '',
                    narration: '',
                    total_cost: '',
                    landed_cost: '',
                    status: 1,
                    approval: 0,

                    amount: '',
                    debit_account_id: '',
                    credit_account_id: '',
                    // narration: '',

                    lc_work_order: {
                        work_order_no: '',
                        get_supplier: {purchase_supplier_name: ''},
                        total_amount: '',
                        total_amount_taka: '',
                    },
                    lc_proforma_inv: {
                        total_amount: '',
                    },
                    lc_opening: {
                        lc_no: '',
                        get_opening_bank: {accounts_bank_names: ''},
                    },
                    lc_margin: {
                        margin_percentage: '',
                    },
                    lc_commercial: {
                        invoice_no: '',
                        invoice_date: '',
                        total_amount: '',
                        total_qty: '',
                        arrived_port: '',
                        goods_name: '',
                    },
                    lc_latr: {
                        latr_percentage: '',
                    },
                    bdt_convert_rate: {
                        bdt_convert_rate: '',
                    },
                    products_landed_cost: [
                        // {
                        //     ci_detail_id:'',
                        //     landed_unit_price:'',
                        // }
                    ],
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

            show(id) {
                var _this = this;
                axios.get(base_url + 'lc-ci-landed-cost/' + id)
                    .then((response) => {
                        _this.form = response.data.data;

                        setTimeout(function () {
                            jQuery(document).ready(function () {

                                    _this.calcGrandTotalTaka();
                                    _this.calcGrandTotalProductsBreakdown();
                                    _this.the_year = moment(_this.form.lc_work_order.order_date).format('YYYY');
                                }
                            );
                        }, 900);
                    });
            },

            calcGrandTotalTaka(){
                var _this = this;
                let sum_taka = 0;
                let sum_customs_taka = 0;

                var lc_margin = _this.form.lc_commercial.ci_margin;
                sum_taka += parseFloat(lc_margin);

                var lc_latr = _this.form.lc_latr.latr_total_amount;
                sum_taka += parseFloat(lc_latr);

                // var lc_opening = _this.form.lc_opening;////////////////////////////////
                sum_taka += parseFloat(_this.form.lc_commercial.ci_opening_charge);
                sum_taka += parseFloat(_this.form.lc_commercial.ci_bank_expenses);
                sum_taka += parseFloat(_this.form.lc_commercial.ci_insurance);

                _this.grand_total_basic_expenses = sum_taka.toFixed(2); //Basic Sub-Total

                var lc_custom_duty_charges = _this.form.lc_custom_duty_charges;
                lc_custom_duty_charges.forEach(function (item) {

                    sum_taka += parseFloat(item.total_cost);
                    sum_customs_taka += parseFloat(item.total_cost);
                });

                this.grand_total_customs_expenses = sum_customs_taka.toFixed(2); //Customs Sub-Total

                var lc_others_charges = _this.form.lc_others_charges;
                lc_others_charges.forEach(function (item) {
                    var details = item.get_details;
                    details.forEach(function (item2) {

                        sum_taka += parseFloat(item2.cost_value);
                    });
                });

                this.grand_total_taka = sum_taka.toFixed(2); //Grand-Total
            },

            calcGrandTotalProductsBreakdown(){
                var _this = this;

                var lc_commercial_details = _this.form.lc_commercial_details;
                //LC Margin
                for (let i = 0; i < lc_commercial_details.length; i++) {
                    var sum = 0;
                    if (_this.products_margin[i] > 0) {

                        sum = Number(_this.products_margin[i]) + Number(_this.form.lc_commercial.ci_margin / _this.form.lc_commercial.total_net_weight * lc_commercial_details[i].net_weight).toFixed(2);
                    } else {

                        sum = Number(_this.form.lc_commercial.ci_margin / _this.form.lc_commercial.total_net_weight * lc_commercial_details[i].net_weight).toFixed(2);
                    }
                    _this.products_margin[i] = sum;
                }

                //LC LATR
                for (let i = 0; i < lc_commercial_details.length; i++) {
                    var sum = 0;
                    if (_this.products_latr[i] > 0) {

                        sum = Number(_this.products_latr[i]) + Number(_this.form.lc_latr.latr_total_amount / _this.form.lc_commercial.total_net_weight * lc_commercial_details[i].net_weight).toFixed(2);

                    } else {

                        sum = Number(_this.form.lc_latr.latr_total_amount / _this.form.lc_commercial.total_net_weight * lc_commercial_details[i].net_weight).toFixed(2);
                    }
                    _this.products_latr[i] = sum;
                }

                //LC Opening Charges
                for (let i = 0; i < lc_commercial_details.length; i++) {
                    var sum = 0;
                    if (_this.products_opening_charges[i] > 0) {

                        sum = Number(_this.products_opening_charges[i]) + Number(_this.form.lc_commercial.ci_opening_charge / _this.form.lc_commercial.total_net_weight * lc_commercial_details[i].net_weight).toFixed(2);

                    } else {

                        sum = Number(_this.form.lc_commercial.ci_opening_charge / _this.form.lc_commercial.total_net_weight * lc_commercial_details[i].net_weight).toFixed(2);
                    }
                    _this.products_opening_charges[i] = sum;
                }

                //LC Bank Expenses
                for (let i = 0; i < lc_commercial_details.length; i++) {
                    var sum = 0;
                    if (_this.products_bank_expenses[i] > 0) {

                        sum = Number(_this.products_bank_expenses[i]) + Number(_this.form.lc_commercial.ci_bank_expenses / _this.form.lc_commercial.total_net_weight * lc_commercial_details[i].net_weight).toFixed(2);

                    } else {

                        sum = Number(_this.form.lc_commercial.ci_bank_expenses / _this.form.lc_commercial.total_net_weight * lc_commercial_details[i].net_weight).toFixed(2);
                    }
                    _this.products_bank_expenses[i] = sum;
                }

                //LC Insurance
                for (let i = 0; i < lc_commercial_details.length; i++) {
                    var sum = 0;
                    if (_this.products_insurance[i] > 0) {

                        sum = Number(_this.products_insurance[i]) + Number(_this.form.lc_commercial.ci_insurance / _this.form.lc_commercial.total_net_weight * lc_commercial_details[i].net_weight).toFixed(2);

                    } else {

                        sum = Number(_this.form.lc_commercial.ci_insurance / _this.form.lc_commercial.total_net_weight * lc_commercial_details[i].net_weight).toFixed(2);
                    }
                    _this.products_insurance[i] = sum;
                }

                //LC CUSTOMS

                var lc_others_charges = _this.form.lc_others_charges;
                //LC Other Charges
                lc_others_charges.forEach(function (oth_charge) {
                    oth_charge.get_details.forEach(function (oth_charge_item) {
                        let i = 0;
                        lc_commercial_details.forEach(function (ci_item) {
                            var sum = 0;
                            if (parseFloat(_this.products_others[i]) > 0) {
                                sum = parseFloat(_this.products_others[i]) + parseFloat(oth_charge_item.cost_value / _this.form.lc_commercial.total_net_weight * ci_item.net_weight);
                            } else {
                                sum = parseFloat(oth_charge_item.cost_value / _this.form.lc_commercial.total_net_weight * ci_item.net_weight);
                            }
                            _this.products_others[i] = sum.toFixed(2);
                            i++;
                        });
                    });
                });


                //***Products GRAND TOTAL
                for (let i = 0; i < lc_commercial_details.length; i++) {
                    var sum = 0;
                    sum = Number(_this.products_margin[i]) + Number(_this.products_latr[i]) + Number(_this.products_opening_charges[i]) + Number(_this.products_bank_expenses[i]) + Number(_this.products_insurance[i]) + Number(_this.form.temp_products_customs[i].cost_value) + Number(_this.products_others[i]);
                    _this.grand_total_products[i] = sum.toFixed(2);
                    _this.grand_total_products_taka += parseFloat(sum.toFixed(2));

                    var landed_unit_price = (sum/lc_commercial_details[i].qty).toFixed(2);
                    let landedCost = {
                      "ci_detail_id" : lc_commercial_details[i].id,
                      "landed_unit_price" : landed_unit_price
                    }
                    _this.form.products_landed_cost.push(landedCost);
                }
            }
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
