<template>
    <div>
        <div class="row print_links inv-fullheight">
            <div class="col-md-10"></div>
            <div class="col-md-2 text-right">
                <a href="#" onclick="window.print();return false"
                   class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i
                    class="fa flaticon-technology"></i><span>Print</span></span></a>
                <a href="#" @click="prinWithoutHead()" class="btn btn-success btn-sm m-btn  m-btn m-btn--icon"><span><i
                    class="fa flaticon-technology"></i><span>Print(PAD)</span></span></a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="m-portlet">
                    <div class="m-portlet__body m-portlet__body--no-padding">
                        <div class="m-invoice-2">
                            <div class="m-invoice__wrapper">
                                <div class="m-invoice__head">
                                    <div class="m-invoice__container m-invoice__container--centered">
                                        <div class="row invoice-header" style="padding-top: 24px">
                                            <div class="col-md-12" id="cinfo_head">
                                                <div class="row"
                                                     style="border:1px solid #000000; width:100%; height:100%; ">
                                                    <div style="width:75%;float: left; margin-top: 3px;">
                                                        <div style="width: 60px;float: left; margin-left:5px">
                                                            <p>
                                                                <img :src="cinfo.logo"
                                                                     style="width: 50px; height: 50px;float: left">
                                                            </p>
                                                        </div>
                                                        <div class=""
                                                             style="margin-bottom: 10px; width: 490px;float: left">
                                                            <h3 style="margin-bottom:5px;font-weight: 600;font-size:20px; ">
                                                                {{cinfo.name}}</h3>
                                                            <p>{{cinfo.address}}</p>
                                                            <p v-if="cinfo.address2">Factory:{{cinfo.address2}} </p>
                                                        </div>
                                                    </div>
                                                    <div style="width:25%; margin-top: 3px;">
                                                        <div class="db-data" style="line-height:1.6;">
                                                            <span>Phone :</span> <span> {{cinfo.phone}}</span><br>
                                                            <span>Mobile:</span> <span>{{cinfo.mobile}}</span><br>
                                                            <span>Fax :</span> <span> {{cinfo.fax}}</span><br>
                                                            <span>Email :</span> <span>{{cinfo.email}} </span> <br>
                                                            <span>Web :</span> <span>{{cinfo.web}} </span>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="m-invoice__items" style="margin-top:-50px;">
                                            <div class="m-invoice__item ">
                                                <span
                                                    class="m-invoice__subtitle text-center">Fixed Asset Depreciation</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-invoice__body m-invoice__body--centered">
                                    <div class="description-section">
                                        <div class="row">
                                            <div class="col-md-6" style="float:left;width:50%">
                                                <p>
                                                    <span style="font-weight: 600">Depreciation No :</span>
                                                    <span>
                                                        {{form.get_lc_no ? form.get_lc_no.lc_no : ''}}
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
                                                            <th width="20%">Sub Code 2</th>
                                                            <th width="40%">Account</th>
                                                            <th width="10%">Current Amount</th>
                                                            <th width="10%">Depreciation Amount</th>
                                                            <th width="10%">Amount after depreciation</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(details, index) in form.details">

                                                            <td class="text-left" v-if="details.show == 0 "
                                                                :rowspan="details.span">
                                                                {{details.sub_code_title2}}({{details.sub_code2}})
                                                            </td>
                                                            <td class="text-left">
                                                                {{details.chart_of_accounts_title}}({{details.chart_of_account_code}})
                                                            </td>
                                                            <td class="text-right">
                                                                {{details.amount}}
                                                            </td>
                                                            <td class="text-right">
                                                                {{details.depreciations}}
                                                            </td>
                                                            <td class="text-right">
                                                                {{details.current_amount}}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" class="text-right"><b>Total</b></td>
                                                            <td class="">
                                                                {{form.total_amount}}
                                                            </td>
                                                            <td class="">
                                                                {{form.total_depreciations}}
                                                            </td>
                                                            <td class="">
                                                                {{form.total_current_amount}}
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
                    depreciations_no: '',
                    depreciations_date: '',
                    sub_code_id: '',
                    depreciations_narration: '',
                    total_amount: 0,
                    total_depreciations: 0,
                    total_current_amount: 0,

                    account_status: 0,
                    status: 1,
                    approve: '',

                    details: [
                        {
                            depreciation_id: '',
                            sub_code2_id: '',
                            show: 0,
                            span: 1,
                            chart_acc_id: '',
                            amount: 0,
                            depreciations: 0,
                            current_amount: 0,
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {
            prinWithoutHead() {
                document.getElementById('cinfo_head').style.display = 'none';
                window.print();
            },

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
                axios.get(base_url + 'fixed-asset-depreciation/' + id)
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
            console.log(55);
            _this.show(_this.editId);
            _this.company_info();
        }
    }
</script>
