<template>
    <div>
        <div class="m-portlet__body">
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-right mt-1 mr-0.4">
                    <a href="#" onclick="window.print();return false"
                       class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i
                        class="fa flaticon-technology"></i><span>Print</span></span></a>
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
                                    <div class="m-invoice__container m-invoice__container--centered">
                                        <div class="logo_header" style="text-align: center">
                                            <a href="#">
                                                <h1>View Costs</h1>
                                            </a>
                                        </div>
                                        <div class="row invoice-header" style="padding-top: 24px">
                                            <div class="col-md-6">
                                                <div class="db-data">
                                                    <h1 class="print-head-title">Purchase Related Cost</h1>
                                                    <p><span style="font-weight: 600">Order NO :</span> <span>{{ form.po_order_receive_id }}</span>
                                                    </p>
                                                    <p><span style="font-weight: 600">Date :</span> <span>{{ form.po_cost_date }}</span>
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
                                                        <span class="m-invoice__subtitle">Order NO: </span>
                                                        <span class="m-invoice__text">
                                                        {{ form.po_order_receive_id }}
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" align="right">
                                                    <div class="m-invoice__item">
                                                        <span class="m-invoice__subtitle">Date</span>
                                                        <span class="m-invoice__text">{{ form.po_cost_date }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>

                                            <div class="row">
                                                <div class="col-md-12" align="left">
                                                    <div class="m-invoice__item">
                                                        <span class="m-invoice__subtitle"></span>
                                                        <span class="m-invoice__text">Narration	        :&nbsp;&nbsp;&nbsp;&nbsp;{{ form.po_cost_narration }}</span>
                                                        <span class="m-invoice__text">Total Cost	:&nbsp;&nbsp;&nbsp;&nbsp;{{ form.po_total_cost }}</span>
                                                        <span class="m-invoice__text">Credit Acc	:&nbsp;&nbsp;&nbsp;&nbsp; {{form.credit_ac_id}}</span>
                                                        <span class="m-invoice__text">Approve Status	:&nbsp;&nbsp;&nbsp;&nbsp;
                                                            <span class="text-danger" v-if='form.approve == 0'> Not Approve</span>
                                                            <span v-if='form.approve == 1' class="text-success">Approve</span>
                                                        </span>
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
                                                    <th>Cost Name</th>
                                                    <th>Remarks</th>
                                                    <th>Cost Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr class="medium" v-for="(details, index) in form.details">
                                                    <td>{{details.get_type.purchase_cost_types_name}}</td>
                                                    <td>{{details.remarks}}</td>
                                                    <td>{{details.po_cost_amount}}</td>
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
                    id: '',
                    po_order_receive_id: '',
                    po_cost_date: '',
                    po_cost_narration: '',
                    po_total_cost: '',
                    credit_ac_id: '',
                    approve: '',
                    details: [
                        {
                            po_cost_id: '',
                            purchase_cost_types_name: '',
                            po_cost_type_id: '',
                            remarks: '',
                            po_cost_amount: '',
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
                axios.get(base_url + 'po-cost-entry/' + id)
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
