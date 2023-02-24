<template>
    <div>
        <div class="m-portlet__body">
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2">
                    <a href="#" onclick="window.print();return false"
                       class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i
                        class="fa flaticon-technology"></i><span>Print</span></span></a>
                    <a href="#" class="btn btn-accent btn-sm m-btn  m-btn m-btn--icon"><span><i
                        class="fa flaticon-doc"></i><span>PDF</span></span></a>
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
                                                <div class="col-md-6">
                                                    <div class="db-data">
                                                        <h1 class="print-head-title">Requisition RM</h1>
                                                        <p><span style="font-weight: 600">Requisition No :</span> <span>{{ form.rm_requisition_no }}</span>
                                                        </p>
                                                        <p><span style="font-weight: 600">Requisition Date :</span>
                                                            <span>{{ form.requisition_date }}</span></p>
                                                        <p><span style="font-weight: 600">Warehouse :</span> <span>{{ form.warehouse_id }}</span>
                                                        </p>
                                                        <p><span style="font-weight: 600">Requistion Types :</span>
                                                            <span>{{ form.requisition_types }}</span></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <p style="margin-bottom: 62px;">
                                                        <!--<img src="img/1200px-Channel-i.svg.png" height="30px" width="30px">-->
                                                        <img src="img/1200px-Channel-i.svg.png"
                                                             style="width: 150px; height: 50px;float: right">
                                                    </p>
                                                    <span class="m-invoice__desc">
                                                        <span>I-Venture Limited</span>
                                                        <span>Level 7, 339/B, Tejgaon I/A</span>
                                                        <span>Dhaka 1208, 1212</span>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="m-invoice__items">
                                                <div class="m-invoice__item ">
                                                    <span class="m-invoice__subtitle">Requisition Date</span>
                                                    <span class="m-invoice__text">{{ form.requisition_date }}</span>
                                                </div>
                                                <div class="m-invoice__item text-center">
                                                    <span class="m-invoice__subtitle">Requisition No</span>
                                                    <span class="m-invoice__text">{{ form.rm_requisition_no }}</span>
                                                </div>
                                                <div class="m-invoice__item text-center">
                                                    <span class="m-invoice__subtitle">Warehouse</span>
                                                    <span class="m-invoice__text">{{ form.warehouse_id }}</span>
                                                </div>
                                                <div class="m-invoice__item text-center">
                                                    <span class="m-invoice__subtitle">Requistion Types</span>
                                                    <span class="m-invoice__text">{{ form.requisition_types }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-invoice__body m-invoice__body--centered">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th class="text-center">Remarks</th>
                                                    <th class="text-center">M Unit</th>
                                                    <th class="text-right">QTY</th>
                                                    <th class="text-right">Unit Price</th>
                                                    <th class="text-right">Total Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(details, index) in form.details">
                                                    <td>{{ details.product_name }}</td>
                                                    <td class="text-left">{{ details.remarks }}</td>
                                                    <td class="text-center">{{ details.measure_unit }}</td>
                                                    <td class="text-right">{{ details.qty }}</td>
                                                    <td class="text-right">{{ details.unit_price }}</td>
                                                    <td class="text-right">{{ details.total_price }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right m--font-danger" colspan="3">Total</td>
                                                    <td class="text-right m--font-danger">{{ form.total_qty }}</td>
                                                    <td class="text-right"></td>
                                                    <td class="m--font-danger">{{ form.total_amount }}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="description-section">
                                            <p>
                                                <span style="font-weight: 600">Narration :</span>
                                                <span>
                                                    {{ form.narration }}
                                                </span>
                                            </p>
                                        </div>

                                        <div style="clear:both;width:100%;margin-top:170px;"></div>

                                        <div class="m-invoice__items signature-section hidden">
                                            <div class="m-invoice__item">
                                                <span class="m-invoice__subtitle">Prepared By</span>
                                            </div>
                                            <div class="m-invoice__item text-center">
                                                <span class="m-invoice__subtitle signature-center">Checked By</span>
                                            </div>
                                            <div class="m-invoice__item text-right">
                                                <span class="m-invoice__subtitle signature-right">Approved By</span>
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
                form: {
                    rm_requisition_no: '',
                    requisition_date: '',
                    warehouse_id: '',
                    narration: '',
                    estimate_qty_for_production: '',
                    total_qty: '',
                    total_amount: '',
                    requisition_types: '',
                    details: [
                        {
                            product: '',
                            remarks: '',
                            qty: '',
                            m_unit: '',
                            unit_price: 0,
                            total_price: '',
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {
            show(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'requisition-for-raw-material/' + id)
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
        }
    }
</script>
