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
                                                    <span class="m-invoice__subtitle text-center">Bank Transfer/Deposit Voucher</span>
                                                </div>
                                            </div>

                                            <div class="m-invoice__items" style="margin-top:-100px;">
                                                <div class="m-invoice__item ">
                                                    <span class="m-invoice__subtitle">Voucher No : <span >{{form.bank_transfer_no}}</span></span><br>
                                                    <span class="m-invoice__subtitle">Payment Infomation</span>
                                                    <div v-show="form.payment_method == 1">
                                                        <span class="m-invoice__text" ><b>Paied By: </b>&nbsp;&nbsp; Cash</span><br/>
                                                    </div>
                                                    <div v-show="form.payment_method == 2 || form.payment_method == 3 || form.payment_method == 4">
                                                        <span class="m-invoice__subtitle">Cheque Info</span><br/>
                                                        <!-- <span class="m-invoice__text" ><b>Bank: </b>&nbsp;&nbsp;&nbsp;{{ form.bank_name }}</span><br/> -->
                                                        <span class="m-invoice__text" ><b>Cheque date:</b>&nbsp;&nbsp;&nbsp;&nbsp;{{ form.cheque_date }}</span><br/>
                                                        <!-- <span class="m-invoice__text" ><b>Account Name:</b>&nbsp;&nbsp;&nbsp;&nbsp;{{ form.account_name }}</span><br/> -->
                                                        <span class="m-invoice__text" ><b>Account:</b>&nbsp;&nbsp;&nbsp;&nbsp;{{ form.account_no }}</span><br/>
                                                        <!--<span class="m-invoice__text" ><b>Cheque Book:</b>&nbsp;&nbsp;&nbsp;&nbsp;{{ form.cheque_book }}</span><br/>-->
                                                        <span class="m-invoice__text" ><b>Cheque Leaf:</b>&nbsp;&nbsp;&nbsp;&nbsp;{{ form.cheque_leaf }}</span><br/>
                                                        <span class="m-invoice__text" ><b>Cheque Remarks:</b>&nbsp;&nbsp;{{ form.cheque_remarks }}</span><br/>
                                                    </div>
                                                </div>
                                                <div class="m-invoice__item">
                                                    <span class="m-invoice__subtitle text-right">Date :<span>{{form.bank_transfer_date}}   </span></span>
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
                                                    <th class="text-right">Debit </th>
                                                    <th class="text-right">Credit</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(details, index) in form.get_details">
                                                    <td>{{++index}}</td>
                                                    <td class="text-left">{{details.coa ? details.coa.chart_of_account_code:''}} </td>
                                                    <td class="text-left">{{details.coa ? details.coa.chart_of_accounts_title:''}}</td>
                                                    <td class="text-right"><span v-if="details.debit_amount >0">{{ details.debit_amount }}</span></td>
                                                    <td class="text-right"><span v-if="details.credit_amount >0">{{ details.credit_amount }}</span></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right m--font-danger" colspan="3">Total</td>
                                                    <td class="text-right m--font-danger">{{form.amount}}</td>
                                                    <td class="text-right m--font-danger">{{form.amount}}</td>
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
                                                    <th class="text-right">Debit </th>
                                                    <th class="text-right">Credit</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td class="text-left">{{form.sub2_details.coa.sub_code2 ? form.sub2_details.coa.sub_code2.sub_code2:''}}</td>
                                                    <td class="text-left">{{form.sub2_details.coa.sub_code2 ? form.sub2_details.coa.sub_code2.sub_code_title2:''}}</td>
                                                    <td class="text-left">{{form.sub2_details.coa ? form.sub2_details.coa.chart_of_account_code:''}}</td>
                                                    <td class="text-left">{{form.sub2_details.coa ? form.sub2_details.coa.chart_of_accounts_title:''}}</td>

                                                    <td class="text-right">{{form.amount}}</td>
                                                    <td class="text-right">{{form.amount}}</td>
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
                                                <span>
                                                    {{ form.narration }}
                                                </span>
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

                form:{
                    bank_transfer_no: '',
                    bank_transfer_date: '',

                    type: '',

                    amount: '',
                    narration: '',

                    payment_method: '',
                    bank_name: '',

                    cheque_date: '',
                    account_name: '',
                    account_no: '',
                    cheque_book: '',
                    cheque_leaf: '',
                    cheque_remarks: '',
                    approve: '',

                    get_details:{
                        debit_amount:'',
                        credit_amount:'',

                        coa:{
                            chart_of_accounts_title: '',
                            chart_of_account_code: '',
                        },

                    },

                    sub2_details:{
                        coa: {
                            chart_of_accounts_title: '',
                            chart_of_account_code:'',
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

            show(id) {
                var _this = this;

                axios.get(base_url+'bank-transfer-deposit/'+id)
                    .then( (response) => {
                        _this.form  = response.data.data;
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
