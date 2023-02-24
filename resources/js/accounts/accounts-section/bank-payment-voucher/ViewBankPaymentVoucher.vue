<template>
    <div>
        <div class="m-portlet__body inv-fullheight">
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-right">
                    <a class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon" href="#"
                       onclick="window.print();return false"><span><i
                        class="fa flaticon-technology"></i><span>Print</span></span></a>
                    <a @click="prinWithoutHead()" class="btn btn-success btn-sm m-btn  m-btn m-btn--icon"
                       href="#"><span><i
                        class="fa flaticon-technology"></i><span>Print(PAD)</span></span></a>
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
                                                    <span class="m-invoice__subtitle text-center">Bank Payment</span>
                                                </div>
                                            </div>

                                            <div class="m-invoice__items" style="margin-top:-100px;">
                                                <div class="m-invoice__item ">
                                                    <span class="m-invoice__subtitle">Voucher No : <span>{{form.payment_transaction_no}}</span></span><br>
                                                    <span class="m-invoice__subtitle">Type :<span class="capitalize"> {{form.type}}</span></span>
                                                    <span class="m-invoice__subtitle">Project :<span class="capitalize"> {{form.project}}</span></span>
                                                    <!--<span class="m-invoice__subtitle">Others Reference :<span></span></span>-->
                                                </div>
                                                <div class="m-invoice__item">
                                                    <span class="m-invoice__subtitle text-right">Date :<span>{{form.payment_date}}   </span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-invoice__body m-invoice__body--centered" style="margin-top: -50px;">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>SL#</th>
                                                    <th class="text-center">Code</th>
                                                    <th class="text-right">Particular</th>
                                                    <!-- <th class="text-center">Cheque  No</th>-->
                                                    <!--<th class="text-center">Cheque Date</th>-->
                                                    <th class="text-right">Remarks</th>
                                                    <th class="text-right" v-if="form.is_lc_type==true">Voucher Type
                                                    </th>
                                                    <th class="text-right" v-if="form.is_lc_type==true">Voucher Ref</th>
                                                    <th class="text-right">Debit</th>
                                                    <th class="text-right">Credit</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(details, index) in form.details">
                                                    <td>{{++index}}</td>
                                                    <td class="text-left"> {{
                                                        details.get_debit?details.get_debit.chart_of_account_code :''
                                                        }}{{ details.get_credit?details.get_credit.chart_of_account_code
                                                        :'' }}
                                                    </td>
                                                    <!--<td class="text-left"> {{ details.get_debit?details.get_debit.sub_code2.sub_code2 :'' }}{{ details.get_credit?details.get_credit.chart_of_account_code :'' }}</td>-->
                                                    <td class="text-left">{{
                                                        details.get_credit?details.get_credit.chart_of_accounts_title
                                                        :'' }} {{
                                                        details.get_debit?details.get_debit.chart_of_accounts_title :''
                                                        }}
                                                    </td>
                                                    <!--<td class="text-left">{{ details.get_credit?details.get_credit.chart_of_accounts_title :'' }}{{ details.get_debit?details.get_debit.sub_code2.sub_code_title2 :'' }}</td>-->
                                                    <!--  <td class="text-left">{{ details.check_leaf}}</td>-->
                                                    <!-- <td class="text-left">{{format_date(details.check_date)}}</td>-->
                                                    <td class="text-left">{{ details.remarks }}</td>
                                                    <td class="text-left" v-if="form.is_lc_type==true">{{
                                                        details.voucher_type }}
                                                    </td>
                                                    <td class="text-left" v-if="form.is_lc_type==true">{{
                                                        details.voucher_ref
                                                        }}
                                                    </td>
                                                    <td class="text-right"><span v-if="details.debit_amount >0">{{ details.debit_amount }}</span>
                                                    </td>
                                                    <td class="text-right"><span v-if="details.credit_amount >0">{{ details.credit_amount }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right m--font-danger" colspan="6"
                                                        v-if="form.is_lc_type==true">Total
                                                    </td>
                                                    <td class="text-right m--font-danger" colspan="4"
                                                        v-if="form.is_lc_type==false">Total
                                                    </td>
                                                    <td class="text-right m--font-danger">{{ form.total_debit_amount
                                                        }}
                                                    </td>
                                                    <td class="m--font-danger">{{ form.total_credit_amount }}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <br>

                                        <div class="table-responsive" v-if="form.type!= null">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>SL#</th>
                                                    <th class="text-center">Party</th>
                                                    <th class="text-right">Code</th>
                                                    <th class="text-right">Particular</th>
                                                    <th class="text-right">Debit</th>
                                                    <th class="text-right">Credit</th>
                                                    <th class="text-right">Ref. Type</th>
                                                    <th class="text-right">Ref. Code</th>
                                                    <th class="text-right">Cheque No</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td class="text-left">
                                                        {{form.sbc2_details.get_credit.sub_code2.sub_code_title2}}
                                                    </td>
                                                    <td class="text-left">
                                                        {{form.sbc2_details.get_credit.sub_code2.sub_code2}}
                                                    </td>
                                                    <td class="text-left">
                                                        {{form.sbc2_details.get_credit.chart_of_accounts_title}}
                                                    </td>
                                                    <td class="text-right">{{form.total_debit_amount}}</td>
                                                    <td class="text-right">{{form.sbc2_details.credit_amount}}</td>
                                                    <td class="text-right">{{form.type}}</td>
                                                    <td class="text-left">
                                                        <div v-if="form.type=='customer'">
                                                            {{form.ref_code_details.get_customer_code ?
                                                            form.ref_code_details.get_customer_code.sales_customer_id:''}}
                                                        </div>
                                                        <div v-if="form.type== 'supplier'">
                                                            {{form.ref_code_details.get_supplier_code ?
                                                            form.ref_code_details.get_supplier_code.purchase_supplier_id:''}}
                                                        </div>
                                                        <div v-if="form.type== 'lc'">
                                                            {{form.ref_code_details.get_lc_code ?
                                                            form.ref_code_details.get_lc_code.lc_no:''}}
                                                        </div>
                                                        <div v-if="form.type== 'employee'">
                                                            {{form.ref_code_details.type_desc}}
                                                            ({{form.ref_code_details.get_employee_id ?
                                                            form.ref_code_details.get_employee_id.emp_id:''}})
                                                        </div>
                                                    </td>
                                                    <!--<td class="text-right">{{form.sbc2_details.type_desc}}</td>-->
                                                    <td class="text-right">{{form.sbc2_details.check_leaf}}</td>
                                                </tr>
                                                <!--<tr>
                                                    <td class="text-right m&#45;&#45;font-danger" colspan="5">Total</td>
                                                    <td class="m&#45;&#45;font-danger">{{form.total_debit_amount}}</td>
                                                    <td class="m&#45;&#45;font-danger">{{ form.coa.credit_amount }}</td>
                                                </tr>-->
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="description-section">
                                            <p>
                                                <span style="font-weight: 600">Amount (In Words) :</span>
                                                <span>
                                                    {{form.total_amount_word}} Only.
                                                </span>
                                            </p>
                                            <p>
                                                <span style="font-weight: 600">Narration :</span>
                                                <!--<span>{{ remarksLine}}</span>-->
                                                <span>{{ form.narration}}</span>
                                            </p>
                                            <br>
                                            <br>
                                            <br>
                                        </div>

                                        <!-- <div class="voucher_top_space"></div>-->
                                        <div style="clear:both;width:100%;margin-top:170px;"></div>


                                        <!--<div class="m-invoice__items signature-section hidden">
                                            <div class="m-invoice__item">
                                                <span class="m-invoice__subtitle">Prepared By</span>
                                            </div>
                                            <div class="m-invoice__item text-center">
                                                <span class="m-invoice__subtitle signature-center">Checked By</span>
                                            </div>
                                            <div class="m-invoice__item text-right">
                                                <span class="m-invoice__subtitle signature-right">Approved By</span>
                                            </div>
                                        </div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="inv-signature-row">
                <div class="signature-item">
                    <span>Prepared By</span>
                </div>
                <div class="signature-item">
                    <span>Checked By</span>
                </div>
                <div class="signature-item">
                    <span>Approved By</span>
                </div>
            </div>
            <!-----------Signature End-------------->
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
                //remarksLine:'',

                form: {
                    is_lc_type: false,

                    payment_transaction_no: '',
                    payment_date: '',
                    type: '',
                    project: '',
                    cost_center_id: '',
                    branch_id: '',
                    total_debit_amount: '',
                    total_credit_amount: '',
                    narration: '',
                    approve: '',

                    sbc2_details: {
                        credit_amount: '',
                        debit_amount: '',
                        type_desc: '',
                        check_leaf: '',

                        get_credit: {
                            chart_of_accounts_title: '',
                            chart_of_account_code: '',

                            sub_code2: {
                                sub_code_title2: '',
                                sub_code2: '',
                            },
                        },
                    },

                    ref_code_details: {
                        get_customer_code: {
                            sales_customer_id: '',
                        },
                        get_supplier_code: {
                            purchase_supplier_id: '',
                        },

                        get_employee_id: {
                            emp_id: '',
                        },

                        get_lc_code: {
                            lc_no: '',
                        },
                    },

                    details: [
                        {
                            bank_payment_id: '',
                            debit_account_id: '',
                            credit_account_id: '',
                            remarks: '',
                            check_leaf: '',
                            check_date: '',
                            debit_amount: '',
                            credit_amount: '',

                            get_debit: {
                                chart_of_accounts_title: '',
                                chart_of_account_code: '',
                            },

                            get_credit: {
                                chart_of_accounts_title: '',
                                chart_of_account_code: '',
                            },

                            voucher_type_id: '',
                            voucher_type: '',
                            voucher_ref_id: '',
                            voucher_ref: '',
                        },
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
                axios.get(base_url + 'Company-Info/' + id)
                    .then((response) => {
                        _this.cinfo = response.data;
                    });
            },

            /* lineSlash(){
               var _this = this;
               let remks ='';
               var details = this.form.details;
               var length = details.length;

               for(let i = 0; i < length; i++) {
                   if (details[i].remarks == null) {
                       remks = remks;
                   } else {
                       remks += details[i].remarks + ' ' + '/';
                   }
               }

               this.remarksLine = remks.slice(0,-1);

           },*/

            show(id) {
                var _this = this;

                axios.get(base_url + 'bank-payment-voucher/' + id)
                    .then((response) => {
                        _this.form = response.data.data;
                        setTimeout(function () {
                            jQuery(document).ready(function () {
                                    //_this.lineSlash();
                                }
                            );
                        }, 900);
                    });
            },

            format_date(value) {
                if (value) {
                    return moment(String(value)).format('DD/MM/YYYY')
                }
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
