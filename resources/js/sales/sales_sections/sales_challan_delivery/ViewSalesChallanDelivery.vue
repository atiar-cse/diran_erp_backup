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


                                            <!------------------------------------>
                                            <div class="m-invoice__items" style="margin-top:-50px;">
                                                <div class="m-invoice__item ">
                                                    <span class="m-invoice__subtitle text-center"><h1>Sales Invoice  </h1></span>
                                                </div>
                                            </div>

                                            <div class="row" style="font-size: 14px">
                                                <div class="col-md-6" align="left">
                                                    <div class="m-invoice__item">
                                                        <span class="m-invoice__subtitle"><b>Invoice No: </b></span>
                                                        <span class="m-invoice__text" >
                                                        {{ form.sales_delivery_no }}
                                                    </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6" align="right">
                                                    <div class="m-invoice__item">
                                                        <span class="m-invoice__subtitle"><b>Date</b></span>
                                                        <span class="m-invoice__text">{{ form.sales_delivery_date }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>

                                            <div class="row" style="font-size: 14px">
                                                <div class="col-md-12" align="left">
                                                    <div class="m-invoice__item">
                                                        <span class="m-invoice__subtitle"> </span>
                                                        <span class="m-invoice__text" ><b>Warehouse: </b> {{ form.sales_warehouse_id }}</span><br>
                                                        <span class="m-invoice__text" ><b>Order No: </b> {{ form.sales_invoice_id }}</span><br>
                                                        <span class="m-invoice__text" ><b>Customer: </b> {{ form.sales_customer_id }}</span><br>
                                                        <span class="m-invoice__text" ><b>Destination: </b> {{ form.sales_delivery_location }}</span><br>
                                                    </div>
                                                </div>
                                            <br>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <br>
                                    <br>
                                    <br>
                                    <div class="m-invoice__body m-invoice__body--centered" >
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Remarks</th>
                                                        <th>Unit</th>
                                                        <th>QTY</th>
                                                        <th>Unit Price</th>
                                                        <th>Total Price</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="medium" v-for="(details, index) in form.details">
                                                        <td>{{details.get_prod.product_name ? details.get_prod.product_name : 'Not Available'}}</td>
                                                        <td>{{details.sales_delivery_details_description}}</td>
                                                        <td>{{details.sales_delivery_details_unit}}</td>
                                                        <td >{{details.sales_delivery_details_qty}}</td>
                                                        <td>{{details.sales_delivery_details_unit_price}}</td>
                                                        <td >{{details.sales_delivery_details_total_price}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5" align="left">Sub Total Price </td>
                                                        <td > {{form.sales_delivery_sub_total_price}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5" align="left">AIT </td>
                                                        <td > {{form.sales_delivery_ait}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5" align="left">Commision </td>
                                                        <td > {{form.sales_delivery_commission}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5" align="left">Vat </td>
                                                        <td > {{form.sales_delivery_vat}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5" align="left">Discount </td>
                                                        <td > {{form.sales_delivery_discount}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5" align="left">Net Payable Amount ({{form.sales_delivery_vat_type}})</td>
                                                        <td > {{form.sales_delivery_total_price}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5" align="left">Inwards </td>
                                                        <td > {{form.total_pricein_word}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="description-section">
                                            <p>
                                                <span style="font-weight: 600">Amount (In Words) :</span>
                                                <span>{{form.total_pricein_word}} Only.</span>
                                            </p>
                                            <p>
                                                <span style="font-weight: 600">Narration :</span>
                                                <span>{{ form.sales_delivery_narration}}</span>
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
                view_form:true,
                view_without_form:false,

                cinfo:{},

                form:{
                    sales_delivery_no: '',
                    sales_delivery_date: '',
                    sales_invoice_id: '',
                    sales_customer_id: '',
                    sales_delivery_tender: '',
                    sales_warehouse_id: '',
                    sales_delivery_narration: '',
                    sales_delivery_location: '',
                    sales_delivery_total_qty: '',
                    sales_delivery_sub_total_price: '',
                    sales_delivery_commission: '',
                    sales_delivery_discount: '',
                    sales_delivery_ait: '',
                    sales_delivery_vat: '',
                    sales_delivery_vat_type: '',
                    sales_delivery_total_price: '',
                    total_pricein_word: '',
                    details: [
                        {
                            sales_delivery_details_product_id: '',
                            sales_delivery_details_description: '',
                            sales_delivery_details_unit: '',
                            sales_delivery_details_unit_price: '',
                            sales_delivery_details_discount_type: '',
                            sales_delivery_details_discount: '',
                            sales_delivery_details_vat_sub: '',
                            sales_delivery_details_qty: '',
                            sales_delivery_details_total_price: '',
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

            show(id) {
                var _this = this;

                axios.get(base_url+'sales-delivery-challan/'+id)
                    .then( (response) => {
                        _this.form  = response.data.data;
                    });
            },


            viewDataWithoutPrice(id){
                var _this = this;
                _this.add_form = false;
                _this.product_list = false;
                _this.edit_form = false;
                _this.view_form = false;
                _this.view_without_form = true;
                _this.edit_id = id;
                $('#addButton').hide();
                $('#listButton').show();
            },



        },




        mounted(){
            var _this = this;

        },
        created(){
            var _this = this;
            _this.company_info();
            _this.show(_this.editId);
        }

    }
</script>
