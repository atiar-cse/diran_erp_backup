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
                                                        <h1 class="print-head-title">LC Margin Value</h1>
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
                                                    <h3>LC Info</h3>
                                                    <p>
                                                        <span style="font-weight: 600">Supplier Party :</span>
                                                        <span>
                                                        {{form.get_supplier ? form.get_supplier.purchase_supplier_name : ''}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">LC Margin No :</span>
                                                        <span>
                                                        {{form.lc_margin_no ? form.lc_margin_no : ''}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">LC No :</span>
                                                        <span>
                                                        {{form.get_lc_no ? form.get_lc_no.lc_no : ''}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">LC Open Date :</span>
                                                        <span>
                                                        {{form.get_lc_no ? form.get_lc_no.lc_opening_date : ''}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">LC Total Value (BDT) :</span>
                                                        <span>
                                                        {{form.get_lc_no ? form.get_lc_no.lc_total_value : ''}}
                                                    </span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6" style="float:left;width:50%">
                                                    <h3>LC Margin Info</h3>
                                                    <p>
                                                        <span style="font-weight: 600">Margin Entry Date :</span>
                                                        <span>
                                                        {{form.margin_entry_date}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">Margin Percentage :</span>
                                                        <span>
                                                        {{form.margin_percentage}}%
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">Margin value :</span>
                                                        <span>
                                                        {{form.margin_value}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">Narration :</span>
                                                        <span>
                                                        {{form.narration}}
                                                    </span>
                                                    </p>
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
                    lc_margin_no: '',
                    lc_no: '',
                    supplier_id: '',
                    proforma_invoice_id: '',
                    lc_category: '',
                    lc_type: '',
                    lc_opening_date: '',
                    lc_opening_charges: '',
                    lc_opening_bank_id: '',
                    lc_bank_expenses: '',
                    baneficiary_bank: '',
                    lc_latest_shipment_date: '',
                    lc_expire_date: '',
                    lc_total_value: '',
                    insurance_company: '',
                    insurance_date: '',
                    insurance_no: '',
                    insurance_amount: '',
                    insurance_paid_status: 1,
                    lc_status: 1,
                    status: '1',
                    approve: ''
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
                axios.get(base_url + 'lc-cf-value-margin-entry/' + id)
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
