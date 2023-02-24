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
                                                        <h1 class="print-head-title">LC Opening</h1>
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
                                                    <h3>LC Info</h3>
                                                    <p>
                                                        <span style="font-weight: 600">Supplier Party :</span>
                                                        <span>
                                                        {{form.get_supplier ? form.get_supplier.purchase_supplier_name : ''}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">PI No :</span>
                                                        <span>
                                                        {{form.get_pi_no ? form.get_pi_no.pi_no : ''}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">LC Category :</span>
                                                        <span v-if="form.lc_category == 1"
                                                              class="badge badge-pill badge-primary">Export</span>
                                                        <span v-if="form.lc_category == 2"
                                                              class="badge badge-pill badge-secondary">Import</span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">LC Type :</span>
                                                        <span v-if="form.lc_type == 1"
                                                              class="badge badge-pill badge-primary">Master LC</span>
                                                        <span v-if="form.lc_type == 2"
                                                              class="badge badge-pill badge-secondary">Deffard LC</span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">LC No :</span>
                                                        <span>
                                                        {{form.lc_no}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">LC Open Date :</span>
                                                        <span>
                                                        {{form.lc_opening_date}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">LC Opening Charges :</span>
                                                        <span>
                                                        {{form.lc_opening_charges}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">LC Opening Bank :</span>
                                                        <span>
                                                        {{form.get_opening_bank ? form.get_opening_bank.accounts_bank_names : ''}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">Bank Expenses :</span>
                                                        <span>
                                                        {{form.lc_bank_expenses}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">LC Beneficiary Bank :</span>
                                                        <span>
                                                        {{form.get_baneficiary_bank ? form.get_baneficiary_bank.accounts_bank_names : ''}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">LC Latest Shipment Date :</span>
                                                        <span>
                                                        {{form.lc_latest_shipment_date}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">LC Expire Date :</span>
                                                        <span>
                                                        {{form.lc_expire_date}}
                                                    </span>
                                                    </p>
                                                    <p>
                                                        <span style="font-weight: 600">LC Total Value (BDT) :</span>
                                                        <span>
                                                        {{form.lc_total_value}}
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
                data_list: false,
                add_form: false,
                edit_form: false,
                view_form: true,
                cinfo: {},
                form: {
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
                axios.get(base_url + 'lc-opening-section/' + id)
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
