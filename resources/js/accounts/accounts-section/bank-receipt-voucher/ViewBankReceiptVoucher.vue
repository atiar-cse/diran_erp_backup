 <template>
    <div>
        <div class="m-portlet__body inv-fullheight">
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-right">
                    <a href="#" onclick="window.print();return false" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa flaticon-technology"></i><span>Print</span></span></a>
                    <a href="#" @click="prinWithoutHead()" class="btn btn-success btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa flaticon-technology"></i><span>Print(PAD)</span></span></a>
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
                                            <div class="row invoice-header"  style="padding-top: 24px">
                                                <div class="col-md-12" id="cinfo_head" >
                                                    <div class="row" style="border:1px solid #000000; width:100%; height:100%; ">
                                                        <div style="width:75%;float: left; margin-top: 3px;">
                                                            <div style="width: 60px;float: left; margin-left:5px">
                                                                <p>
                                                                    <img :src="cinfo.logo" style="width: 50px; height: 50px;float: left">
                                                                </p>
                                                            </div>
                                                            <div class="" style="margin-bottom: 10px; width: 490px;float: left">
                                                                <h3 style="margin-bottom:5px;font-weight: 600;font-size:20px; ">{{cinfo.name}}</h3>
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
                                                    <span class="m-invoice__subtitle text-center">Bank Receive</span>
                                                </div>
                                            </div>

                                            <div class="m-invoice__items" style="margin-top:-100px;">
                                                <div class="m-invoice__item ">
                                                    <span class="m-invoice__subtitle">Voucher No : <span >{{form.receipt_transaction_no}}</span></span><br>
                                                   <!-- <span class="m-invoice__subtitle">Others Reference :<span></span></span>-->

                                                </div>
                                                <div class="m-invoice__item">
                                                    <span class="m-invoice__subtitle text-right">Date :<span>{{form.reciept_date}}   </span></span>
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
                                                    <th class="text-center">Particular</th>
                                                    <!--<th class="text-center">Cheque No</th>-->
                                                    <!--<th class="text-center">Cheque Date</th>-->
                                                    <th class="text-center">Remarks</th>
                                                    <th class="text-center">Debit </th>
                                                    <th class="text-center">Credit</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(details, index) in form.details">
                                                    <td>{{++index}}</td>
                                                    <td class="text-left">{{ details.get_debit?details.get_debit.chart_of_account_code :'' }}{{ details.get_credit?details.get_credit.chart_of_account_code :'' }}</td>
                                                    <td class="text-left">{{ details.get_debit?details.get_debit.chart_of_accounts_title :'' }}{{ details.get_credit?details.get_credit.chart_of_accounts_title :'' }}</td>
                                                   <!-- <td class="text-left">{{ details.get_debit?details.get_debit.chart_of_account_code :'' }}{{ details.get_credit?details.get_credit.sub_code2.sub_code2 :'' }}</td>
                                                    <td class="text-left">{{ details.get_debit?details.get_debit.chart_of_accounts_title :'' }}{{ details.get_credit?details.get_credit.sub_code2.sub_code_title2 :'' }}</td>-->

                                                    <td class="text-left">{{ details.remarks}}</td>
                                                    <td class="text-right"><span v-if="details.debit_amount >0">{{ details.debit_amount }}</span></td>
                                                    <td class="text-right"><span v-if="details.credit_amount >0">{{ details.credit_amount }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right m--font-danger" colspan="4">Total</td>
                                                    <td class="text-right m--font-danger">{{ form.total_debit_amount }}</td>
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
                                                    <th class="text-right">Party Code</th>
                                                    <th class="text-right">Particular</th>
                                                    <th class="text-right">Debit </th>
                                                    <th class="text-right">Credit</th>
                                                    <th class="text-right">Ref. Type</th>
                                                    <th class="text-right">Ref. Code</th>
                                                    <th class="text-right">Cheque No</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td class="text-left">{{form.sub2_details.get_debit?form.sub2_details.get_debit.sub_code2.sub_code_title2:''}}</td>
                                                    <td class="text-left">{{form.sub2_details.get_debit?form.sub2_details.get_debit.sub_code2.sub_code2:''}}</td>
                                                   <!-- <td class="text-left">{{form.sub2_details.get_credit?form.sub2_details.get_credit.chart_of_account_code :''}}</td>-->
                                                    <td class="text-left">{{form.sub2_details.get_debit?form.sub2_details.get_debit.chart_of_accounts_title:''}}</td>
                                                    <td class="text-right">{{form.sub2_details.get_debit?form.sub2_details.debit_amount:''}}</td>
                                                    <td class="text-right">{{form.sub2_details.get_debit? form.total_credit_amount:''}}</td>
                                                    <td class="text-right">{{form.type}}</td>
                                                    <td class="text-left">
                                                        <div v-if="form.type=='customer'">
                                                            {{form.ref_code_details.get_customer_code ? form.ref_code_details.get_customer_code.sales_customer_id:''}}
                                                        </div>
                                                        <div v-if="form.type== 'supplier'">
                                                            {{form.ref_code_details.get_supplier_code ? form.ref_code_details.get_supplier_code.purchase_supplier_id:''}}
                                                        </div>
                                                        <div v-if="form.type== 'lc'">
                                                            {{form.ref_code_details.get_lc_code ? form.ref_code_details.get_lc_code.lc_no:''}}
                                                        </div>
                                                        <div v-if="form.type== 'employee'">
                                                            {{form.ref_code_details.type_desc}} ({{form.ref_code_details.get_employee_id ? form.ref_code_details.get_employee_id.emp_id:''}})
                                                        </div>
                                                    </td>
                                                    <td class="text-right">{{form.sub2_details.cheque_no}}</td>
                                                    <!--<td class="text-left">{{form.sub2_details.get_credit?form.sub2_details.get_credit.sub_code2.sub_code2:''}}</td>-->
                                                    <!--<td class="text-right">{{form.sub2_details.get_credit? form.total_credit_amount:''}}</td>-->
                                                </tr>
                                               <!-- <tr>
                                                    <td class="text-right m&#45;&#45;font-danger" colspan="6">Total</td>
                                                    <td class="m&#45;&#45;font-danger">{{form.sub2_details.debit_amount}}</td>
                                                    <td class="m&#45;&#45;font-danger">{{ form.total_credit_amount }}</td>
                                                </tr>-->
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="description-section">
                                            <p>
                                                <span style="font-weight: 600">Amount (In Words) :</span>
                                                <span>
                                                    {{form.total_amount_word}}
                                                </span>
                                            </p>
                                            <p>
                                                <span style="font-weight: 600">Narration :</span>
                                               <!-- <span>{{ remarksLine}}</span>-->
                                                <span>{{ form.narration}}</span>
                                            </p>
                                            <br>
                                            <br>
                                            <br>
                                        </div>

                                       <!-- <div class="voucher_top_space"></div>-->
                                        <div style="clear:both;width:100%;margin-top:170px;"></div>



                                       <!-- <div class="m-invoice__items signature-section hidden">
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
    import { EventBus } from '../../../vue-assets';
    export default {

        props:['editId'],
        data:function(){
            return{
                data_list:false,
                add_form:false,
                edit_form:false,
                view_form:true,

                cinfo:{},
                //remarksLine:'',

                form:{
                    receipt_transaction_no: '',
                    reciept_date: '',
                    type: '',
                    cost_center_id: '',
                    branch_id: '',
                    total_debit_amount: '',
                    total_credit_amount: '',
                    narration: '',
                    approve: '',

                    sub2_details:{
                        credit_amount:'',
                        debit_amount:'',
                        //cheque_no:'',

                        get_debit:{
                            chart_of_accounts_title: '',
                            chart_of_account_code:'',

                            sub_code2:{
                                sub_code_title2:'',
                                sub_code2:'',
                            },
                        },
                    },

                    ref_code_details:{
                        get_customer_code:{
                            sales_customer_id:'',
                        },
                        get_supplier_code:{
                            purchase_supplier_id:'',
                        },

                        get_employee_id:{
                            emp_id:'',
                        },

                        get_lc_code:{
                            lc_no:'',
                        },
                    },

                    details: [
                        {
                            bank_receipt_id: '',
                            debit_account_id: '',
                            credit_account_id: '',
                            remarks: '',
                            cheque_no: '',
                            cheque_date: '',
                            debit_amount: '',
                            credit_amount: '',
                            get_debit:{
                                chart_of_account_code:'',
                                chart_of_accounts_title:'',
                            },
                            get_credit:{
                                chart_of_account_code:'',
                                chart_of_accounts_title:'',

                                sub_code2:{
                                    sub_code_title2:'',
                                    sub_code2:'',
                                },
                            },
                        },
                       ],


                },
                errors: {},
            };
        },

        methods:{

            prinWithoutHead() {
                document.getElementById('cinfo_head').style.display = 'none';
                window.print();

            },

            company_info(){
                var _this = this;
                var id = 1;
                axios.get(base_url+'Company-Info/'+id)
                    .then( (response) => {
                        _this.cinfo  = response.data;
                    });
            },

            format_date(value){
                if (value) {
                    return moment(String(value)).format('MM/DD/YYYY')
                }
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

                axios.get(base_url+'bank-receipt-voucher/'+id)
                    .then( (response) => {
                        _this.form  = response.data.data;
                        setTimeout(function () {
                            jQuery(document).ready(function() {
                                    //_this.lineSlash();
                                }
                            );
                        },900);
                    });
            },

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
