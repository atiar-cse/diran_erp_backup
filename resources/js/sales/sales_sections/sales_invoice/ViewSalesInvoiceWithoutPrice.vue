<template>
    <div>
        <!-- Start view from here -->
        <div class="m-portlet__body">
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-right">
                    <a href="#" onclick="window.print();return false" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa flaticon-technology"></i><span>Print</span></span></a>
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
                                                <div class="col-md-12" >
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
                                                    <span class="m-invoice__subtitle text-center"><h1>Sales Requisition</h1></span>
                                                </div>
                                            </div>

                                            <div class="m-invoice__items" style="margin-top:-100px;font-size: 14px">
                                                <div class="m-invoice__item ">
                                                    <span class="m-invoice__subtitle"><b>Voucher No : </b><span >{{form.sales_invoices_no}}</span></span><br>
                                                    <span class="m-invoice__text"><b>Invoice Type : </b><span >{{form.sales_invoices_type}}</span></span>
                                                    <span class="m-invoice__text"><b>Warehouse : </b><span >{{form.sales_invoices_warehouse_id}}</span></span>
                                                    <span class="m-invoice__text"><b>Customer : </b><span >{{form.sales_invoices_customer_id}}</span></span>
                                                </div>
                                                <div class="m-invoice__item">
                                                    <span class="m-invoice__subtitle text-right">Date :<span>{{form.sales_invoices_date}}   </span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-invoice__body m-invoice__body--centered" style="margin-top: -50px;">

                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Remarks</th>
                                                        <th>Unit</th>
                                                        <th>QTY</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(details, index) in form.details">
                                                        <td>{{details.get_prod.product_name ? details.get_prod.product_name : 'Not Available'}}</td>
                                                        <td>{{details.sales_invoice_details_description}}</td>
                                                        <td>{{details.sales_invoice_details_unit}}</td>
                                                        <td class="m&#45;&#45;font-danger">{{details.sales_invoice_details_qty}}</td>
                                                    </tr>
                                                <tr>
                                                    <td colspan="3" class="text-right text-danger">Total</td>
                                                    <td class="text-danger">{{form.sales_invoices_total_qty}}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="description-section">
                                            <p>
                                                <span style="font-weight: 600">Narration :</span>
                                                <span>{{ form.sales_invoices_narration}}</span>
                                            </p>
                                        </div>

                                        <div class="voucher_top_space"></div>



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
    import { EventBus } from '../../../vue-assets';
    export default {

        props:['editId'],
        data:function(){
            return{
                product_list:false,
                add_form:false,
                edit_form:false,
                view_form:false,
                view_without_form:true,


                cinfo:{},

                form:{
                    sales_invoices_no: '',
                    sales_invoices_contract_no: '',
                    sales_invoices_date:  '',
                    sales_invoices_type: '',
                    sales_invoices_warehouse_id: '',
                    sales_invoices_customer_id: '',
                    sales_invoices_narration: '',
                    sales_invoices_total_qty: '',

                    details: [
                        {
                            sales_invoice_details_product_id:'',
                            sales_invoice_details_description:'',
                            sales_invoice_details_unit:'',
                            sales_invoice_details_qty:'',

                        }
                    ],
                },
                errors: {},
            };
        },

        methods:{



            company_info(){
                var _this = this;
                var id = 1;
                axios.get(base_url+'Company-Info/'+id)
                    .then( (response) => {
                        _this.cinfo  = response.data;
                    });
            },

            show_without_price(id) {
                var _this = this;
                axios.get(base_url+'sales-invoice/'+id)
                    .then( (response) => {
                        _this.form  = response.data.data;
                    });
            },


            /*showMassage(data){
                if(data.status == 'success'){
                    toastr.success(data.message, 'Success Alert');
                }else if(data.status == 'error'){
                    toastr.error(data.message, 'Error Alert');
                }else{
                    toastr.error(data.message, 'Error Alert');
                }
            },*/

        },

        mounted(){
            var _this = this;

        },

        created(){
            var _this = this;
            _this.company_info();
            _this.show_without_price(_this.editId);
        }

    }
</script>
