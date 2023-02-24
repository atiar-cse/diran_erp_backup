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
                                                    <span class="m-invoice__subtitle text-center">Journal Voucher</span>
                                                </div>
                                            </div>

                                            <div class="m-invoice__items" style="margin-top:-100px;">
                                                <div class="m-invoice__item ">
                                                    <span class="m-invoice__subtitle">Voucher No : <span>{{form.transaction_reference_no}}</span></span><br>
                                                    <!--<span class="m-invoice__subtitle">Others Reference :<span></span></span>-->
                                                </div>
                                                <div class="m-invoice__item">
                                                    <span class="m-invoice__subtitle text-right">Date :<span>{{form.transaction_date}}   </span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="m-invoice__body m-invoice__body--centered">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>SL#</th>
                                                    <th class="text-center">Code</th>
                                                    <th class="text-right">Particular</th>
                                                    <th class="text-right"><span v-if="form.type">{{form.type.charAt(0).toUpperCase()+ form.type.slice(1)}}</span><span
                                                        v-else>Type Name</span></th>
                                                    <th class="text-right">Remarks</th>
                                                    <th class="text-right" v-if="form.is_lc_type==true">Voucher Type
                                                    </th>
                                                    <th class="text-right" v-if="form.is_lc_type==true">Voucher
                                                        Reference
                                                    </th>
                                                    <th class="text-right">Debit</th>
                                                    <th class="text-right">Credit</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(details, index) in form.details">
                                                    <td>{{++index}}</td>
                                                    <td class="text-left">{{
                                                        details.get_credit?details.get_credit.chart_of_account_code :''
                                                        }}{{ details.get_debit?details.get_debit.chart_of_account_code
                                                        :'' }}
                                                    </td>
                                                    <td class="text-left">{{
                                                        details.get_credit?details.get_credit.chart_of_accounts_title
                                                        :'' }}{{
                                                        details.get_debit?details.get_debit.chart_of_accounts_title :''
                                                        }}
                                                    </td>
                                                    <td class="text-left">
                                                        <div v-if="details.get_jrnl_entry.type== 'customer'">
                                                            {{details.get_customer_code ?
                                                            details.get_customer_code.sales_customer_name : '' }}
                                                        </div>
                                                        <div v-if="details.get_jrnl_entry.type== 'supplier'">
                                                            {{details.get_supplier_code ?
                                                            details.get_supplier_code.purchase_supplier_name : '' }}
                                                        </div>
                                                        <div v-if="details.get_jrnl_entry.type== 'employee'">
                                                            {{details.get_employee_id ?
                                                            details.get_employee_id.first_name : '' }}
                                                            {{details.get_employee_id ?
                                                            details.get_employee_id.last_name : '' }}
                                                        </div>
                                                        <div v-if="details.get_jrnl_entry.type== 'lc'">
                                                            {{details.get_lc_code ? details.get_lc_code.lc_no : '' }}
                                                        </div>
                                                    </td>
                                                    <td class="text-left">{{ details.remarks }}</td>
                                                    <td class="text-left" v-if="form.is_lc_type==true">{{
                                                        details.voucher_type }}
                                                    </td>
                                                    <td class="text-left" v-if="form.is_lc_type==true">{{
                                                        details.voucher_ref }}
                                                    </td>
                                                    <td class="text-right"><span v-if="details.debit_amount >0">{{ details.debit_amount }}</span>
                                                    </td>
                                                    <td class="text-right"><span v-if="details.credit_amount >0">{{ details.credit_amount }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right m--font-danger" colspan="7"
                                                        v-if="form.is_lc_type==true">Total
                                                    </td>
                                                    <td class="text-right m--font-danger" colspan="5"
                                                        v-if="form.is_lc_type==false">Total
                                                    </td>
                                                    <td class="text-right m--font-danger">{{form.total_debit}}</td>
                                                    <td class="m--font-danger">{{form.total_credit}}</td>
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
                                                    <th class="text-center">Party Code</th>
                                                    <th class="text-right">Party</th>
                                                    <th class="text-right">Code</th>
                                                    <th class="text-right">Particular</th>
                                                    <th class="text-right">Debit</th>
                                                    <th class="text-right">Credit</th>
                                                    <th class="text-right">Ref. Type</th>
                                                    <th class="text-right">Ref. Code</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td class="text-left">{{form.sub2_details.coa.sub_code2 ?
                                                        form.sub2_details.coa.sub_code2.sub_code2:''}}
                                                    </td>
                                                    <td class="text-left">{{form.sub2_details.coa.sub_code2 ?
                                                        form.sub2_details.coa.sub_code2.sub_code_title2:''}}
                                                    </td>
                                                    <td class="text-left">{{form.sub2_details.coa ?
                                                        form.sub2_details.coa.chart_of_account_code:''}}
                                                    </td>
                                                    <td class="text-left">{{form.sub2_details.coa ?
                                                        form.sub2_details.coa.chart_of_accounts_title:''}}
                                                    </td>

                                                    <td class="text-right">{{form.total_debit}}</td>
                                                    <!-- <td class="text-right">{{form.coa.credit_amount}}</td>-->
                                                    <td class="text-right">{{form.total_credit}}</td>
                                                    <td class="text-right">{{form.type}}</td>
                                                    <td class="text-left">
                                                        <div v-if="form.type== 'customer'">
                                                            {{form.dr_code_type.get_customer_code ?
                                                            form.dr_code_type.get_customer_code.sales_customer_id:''}}<br>
                                                            {{form.cr_code_type.get_customer_code ?
                                                            form.cr_code_type.get_customer_code.sales_customer_id:''}}
                                                        </div>
                                                        <div v-if="form.type== 'supplier'">
                                                            {{form.dr_code_type.get_supplier_code ?
                                                            form.dr_code_type.get_supplier_code.purchase_supplier_id:''}}<br>
                                                            {{form.cr_code_type.get_supplier_code ?
                                                            form.cr_code_type.get_supplier_code.purchase_supplier_id:''}}
                                                        </div>
                                                        <div v-if="form.type== 'lc'">
                                                            {{form.dr_code_type.get_lc_code ?
                                                            form.dr_code_type.get_lc_code.lc_no:''}}<br>
                                                            {{form.cr_code_type.get_lc_code ?
                                                            form.cr_code_type.get_lc_code.lc_no:''}}
                                                        </div>
                                                        <div v-if="form.type== 'employee'">
                                                            {{form.dr_code_type.get_employee_id ?
                                                            form.dr_code_type.get_employee_id.emp_id:''}}<br>
                                                            {{form.cr_code_type.get_employee_id ?
                                                            form.cr_code_type.get_employee_id.emp_id:''}}
                                                        </div>
                                                    </td>
                                                </tr>

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
                                                <span>{{ form.narration}}</span>
                                                <!-- <span>{{ remarksLine}}</span>-->
                                            </p>
                                            <br>
                                            <br>
                                            <br>
                                        </div>
                                        <div style="clear:both;width:100%;margin-top:170px;"></div>

                                        <!--<div class="m-invoice__items signature-section hidden">
                                            <div class="m-invoice__item">
                                                <span class="m-invoice__subtitle">Manager</span>
                                            </div>
                                            <div class="m-invoice__item text-center">
                                                <span class="m-invoice__subtitle signature-center">DGM</span>
                                            </div>
                                            <div class="m-invoice__item text-right">
                                                <span class="m-invoice__subtitle signature-right">Senior DGM</span>
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

                form: {
                    is_lc_type: false,

                    transaction_reference_no: '',
                    transaction_date: '',
                    type: '',
                    total_debit: '',
                    total_credit: '',
                    narration: '',
                    approve: '',
                    total_amount_word: '',

                    dr_code_type: {
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

                    cr_code_type: {
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
                            journal_entry_id: '',
                            remarks: '',
                            char_of_account_id: '',
                            debit_amount: '',
                            credit_amount: '',
                            type_desc: '',
                            type_id: '',

                            voucher_type: '',
                            voucher_ref: '',

                            get_jrnl_entry: {
                                type: '',
                            },
                            get_credit: {
                                chart_of_accounts_title: '',
                                chart_of_account_code: '',
                            },

                            get_customer_code: {
                                sales_customer_name: '',
                            },
                            get_supplier_code: {
                                purchase_supplier_id: '',
                                purchase_supplier_name: '',
                            },

                            get_employee_id: {
                                emp_id: '',
                                first_name: '',
                                last_name: '',
                            },
                            get_lc_code: {
                                lc_no: '',
                            },
                        }
                    ],

                    sub2_details: {
                        coa: {
                            chart_of_accounts_title: '',
                            chart_of_account_code: '',
                            sub_code2: {
                                sub_code_title2: '',
                                sub_code2: '',
                            },
                        },
                    },
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

            /* valueInUpperCase(value) {
                 if(value){
                     return this.value.toUpperCase();
                 }

             },*/

            show(id) {
                var _this = this;

                axios.get(base_url + 'journal-voucher/' + id)
                    .then((response) => {
                        _this.form = response.data.data;
                        setTimeout(function () {
                            jQuery(document).ready(function () {
                                    //_this.calculateTotalDebitCredit();

                                }
                            );
                        }, 900);
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
