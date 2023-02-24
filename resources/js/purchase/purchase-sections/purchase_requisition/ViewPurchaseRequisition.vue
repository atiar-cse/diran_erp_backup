<template>
    <div>
        <div class="m-portlet__body">
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-right mt-1 mr-0.4">
                    <a href="#" onclick="window.print();return false"
                       class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i
                        class="fa flaticon-technology"></i><span>Print</span></span></a>
                    <!--<a href="#" class="btn btn-accent btn-sm m-btn  m-btn m-btn&#45;&#45;icon"><span><i class="fa flaticon-doc"></i><span>PDF</span></span></a>-->
                </div>
            </div>
            <br>

            <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update_approval(form.id)"
                  id="viewComponent" class="m-form m-form--fit m-form--label-align-right">

                <div class="m-portlet" id="m-portlet">
                    <div class="m-portlet__body m-portlet__body--no-padding">
                        <div class="m-invoice-2">
                            <div class="m-invoice__wrapper">
                                <div class="m-invoice__head">
                                    <!--style="background-image: url(data:../../assets/app/media/img//logos/bg-6.jpg);"  class="m-invoice__logo" -->
                                    <div class="m-invoice__container m-invoice__container--centered">
                                        <div class="logo_header" style="text-align: center">
                                            <a href="#">
                                                <h1>View Requisition</h1>
                                            </a>
                                        </div>

                                        <div class="row invoice-header" style="padding-top: 24px">
                                            <div class="col-md-6">
                                                <div class="db-data">
                                                    <h1 class="print-head-title">Requisition</h1>
                                                    <p><span style="font-weight: 600">Requisition No :</span> <span>{{ form.purchase_requisition_no }}</span>
                                                    </p>
                                                    <p><span style="font-weight: 600">Requisition Date :</span> <span>{{ form.purchase_requisition_date }}</span>
                                                    </p>
                                                    <!--<p><span style="font-weight: 600">Warehouse :</span> <span>{{ form.warehouse_id }}</span></p>-->
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <p style="margin-bottom: 62px;">
                                                    <img :src="cinfo.logo"
                                                         style="height: 70px; padding-right: 30px; float: right">
                                                </p>
                                                <span class="m-invoice__desc">
                                                <span>{{cinfo.name}}</span>
                                                <span>{{cinfo.address}}</span>
                                            </span>
                                            </div>
                                        </div>


                                        <div class="m-invoice__items">
                                            <div class="row">
                                                <div class="col-md-6" align="left">
                                                    <div class="m-invoice__item">
                                                        <span class="m-invoice__subtitle">Reuistion No: </span>
                                                        <span class="m-invoice__text">
                                                        {{ form.purchase_requisition_no }}
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" align="right">
                                                    <div class="m-invoice__item">
                                                        <span class="m-invoice__subtitle">Date</span>
                                                        <span class="m-invoice__text">{{ form.purchase_requisition_date }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>

                                            <div class="row">
                                                <div class="col-md-12" align="left">
                                                    <div class="m-invoice__item">
                                                        <span class="m-invoice__subtitle"></span>
                                                        <span class="m-invoice__text">Narration	        :&nbsp;&nbsp;&nbsp;&nbsp;{{ form.purchase_requisition_narration }}</span>
                                                        <span class="m-invoice__text">Supervisor Narration	:&nbsp;&nbsp;&nbsp;&nbsp;{{ form.purchase_requisition_supervisor_narration }}</span>
                                                        <span class="m-invoice__text">Approve Status	:&nbsp;&nbsp;&nbsp;&nbsp; <div
                                                            class="text-danger"
                                                            v-if='form.approve == 0'> Not Approve</div><div
                                                            v-if='form.approve == 1' class="text-success">Approve</div>  </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-2"></div>
                                    <div class="col-md-8">
                                        <div class="table-responsive">
                                            <table class="table table-bordered ">
                                                <thead>
                                                <tr>
                                                    <th>Item No.</th>
                                                    <th>Type</th>
                                                    <th>Remarks</th>
                                                    <th>Unit</th>
                                                    <th>Quantity</th>
                                                    <th>Unit Price</th>
                                                    <th>Total Price</th>

                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr class="medium" v-for="(details, index) in form.details">
                                                    <td>{{details.get_product ? details.get_product.product_name
                                                        : 'Not Available'}}
                                                    </td>
                                                    <td>{{details.product_type}}</td>
                                                    <td>{{details.pr_remarks}}</td>
                                                    <td>{{details.pr_unit}}</td>
                                                    <td>{{details.pr_qty}}</td>
                                                    <td>{{details.pr_unit_price}}</td>
                                                    <td>{{details.pr_total_price}}</td>
                                                </tr>

                                                <tr>
                                                    <td colspan="4" align="left">Qty</td>
                                                    <td>{{form.purchase_requisition_total_qty}}</td>
                                                    <td align="left">Total</td>
                                                    <td> {{form.purchase_requisition_total_price}}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" align="left">Net Payable Amount</td>
                                                    <td> {{form.purchase_requisition_total_price}}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" align="left">Inwards</td>
                                                    <td> {{form.total_pricein_word}}</td>
                                                </tr>

                                                </tbody>
                                            </table>
                                            <br/><br/>
                                        </div>
                                    </div>
                                    <div class="col-md-2"></div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </form>


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
                    purchase_requisition_no: '',
                    purchase_requisition_date: '',
                    purchase_requisition_narration: '',
                    purchase_requisition_supervisor_narration: '',
                    purchase_requisition_total_qty: '',
                    purchase_requisition_total_price: '',
                    approve: '',
                    details: [
                        {
                            pr_product_id: '',
                            product_type: '',
                            pr_remarks: '',
                            pr_qty: '',
                            pr_unit: '',
                            pr_unit_price: '',
                            pr_total_price: '',
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {

            printDiv(divName) {
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
            },

            comany_info() {
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
                axios.get(base_url + 'pr-requisition/' + id)
                    .then((response) => {
                        _this.$loading(false);
                        _this.form = response.data.data;
                    });
            },

            showMassage(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, 'Success Alert');
                } else if (data.status == 'error') {
                    toastr.error(data.message, 'Error Alert');
                } else {
                    toastr.error(data.message, 'Error Alert');
                }
            },
        },

        mounted() {
            var _this = this;
        },

        created() {
            var _this = this;
            _this.show(_this.editId);
            _this.comany_info();
        }
    }
</script>
