<template>
    <div>
        <div class="m-portlet__body">
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2">
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
                                            <div class="logo_header" style="text-align: center">
                                                <a href="#">
                                                    <h1>Cheque Book Leaf List</h1>
                                                </a>
                                            </div>

                                            <div class="row invoice-header" style="padding-top: 24px">
                                                <div class="col-md-6">
                                                    <div class="db-data">
                                                        <p><span style="font-weight: 600">Date :</span> <span>{{ form.check_gen_date }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <p style="margin-bottom: 62px;">
                                                        <img :src="cinfo.logo"
                                                             style="width: 150px; height: 50px;float: right">
                                                    </p>
                                                    <span class="m-invoice__desc">
                                                <span>{{cinfo.name}}</span>
                                                <span>{{cinfo.address}}</span>
                                            </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-invoice__body m-invoice__body--centered">
                                        <div class="row">
                                            <div class="col-md-2">
                                            </div>
                                            <div class="col-md-8">
                                                <span style="font-weight: 700">Chart of Account (Bank) : <span
                                                    class="text-danger">
                                                    {{form.get_coa.chart_of_accounts_title}}
                                                    ({{form.get_coa.chart_of_account_code}})
                                                </span></span><br>
                                                <span style="font-weight: 700">Account Number : <span
                                                    class="text-danger">{{form.account_no}}</span></span><br>
                                                <span style="font-weight: 700">Book Number : <span class="text-danger">{{form.book_no}}</span></span><br>
                                                <br>
                                                <br>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th width="5%">SL#</th>
                                                            <!-- <th class="text-center">Account Number</th>-->
                                                            <th class="text-center">Cheque Book Leaf No</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(details, index) in form.check_leaf_details">
                                                            <td>{{++index}}</td>
                                                            <!--<td class="text-left">{{details.get_account_no?details.get_account_no.accounts_bank_accounts_number:''}} </td>-->
                                                            <td class="text-center">{{ details.leaf_number }}</td>
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

</template>

<script>
    export default {

        props: ['editId'],
        data: function () {
            return {
                view_form: true,

                cinfo: {},

                form: {
                    check_gen_date: '',
                    account_number: '',

                    check_leaf_details: {
                        leaf_number: '',
                    },
                },
                errors: {},
            };
        },

        methods: {

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
                axios.get(base_url + 'check-book-leaf/' + id)
                    .then((response) => {
                        _this.$loading(false);
                        console.log(response);
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
