<template>
    <div>
        <div class="row">
            <div class="col-md-10"></div>
            <div class="col-md-2">
                <button class="btn btn-info" @click="alert('Not Set Yet')">PDF</button>
                <button class="btn btn-danger" @click="printDiv('m-portlet')">Print</button>
            </div>
        </div>
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update_approval(form.id)" id="viewComponent" class="m-form m-form--fit m-form--label-align-right" >

            <div class="m-portlet" id="m-portlet">
                <div class="m-portlet__body m-portlet__body--no-padding">
                    <div class="m-invoice-2">
                        <div class="m-invoice__wrapper">
                            <div class="m-invoice__head"> <!--style="background-image: url(data:../../assets/app/media/img//logos/bg-6.jpg);"-->
                                <div class="m-invoice__container m-invoice__container--centered">
                                    <div class="m-invoice__logo">
                                        <a href="#">
                                            <h1>INVOICE</h1>
                                        </a>
                                        <a href="#">
                                            <img src="http://localhost/tamim_erp/public/assets/app/media/img//logos/logo_client_color.png">
                                        </a>
                                    </div>
                                    <span class="m-invoice__desc">
                                        <span>{{ form.customer_id }}</span>
															<!--<span>Cecilia Chapman, 711-2880 Nulla St, Mankato</span>
															<span>Mississippi 96522</span>-->
                                    </span>
                                    <div class="m-invoice__items">
                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="m-invoice__item">
                                                    <span class="m-invoice__subtitle">DATA</span>
                                                    <span class="m-invoice__text">{{ form.sales_date }}
                                                        <input style="display:none" type="text" class="form-control form-control-sm m-input datepicker" v-model="form.requisition_date" data-dateField="requisition_date" readonly placeholder="Select date from picker" id="m_datepicker_2" /></span>
                                                </div>
                                            </div>

                                            <div class="col-md-3" align="right">
                                                <div class="m-invoice__item">
                                                    <span class="m-invoice__subtitle">Requisition  NO.</span>
                                                    <span class="m-invoice__text" >
                                                        {{ form.vouchers_no }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3" align="left">
                                                <div class="m-invoice__item">
                                                    <span class="m-invoice__subtitle">Narration.</span>
                                                    <span class="m-invoice__text" >
                                                        {{ form.narration }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="m-invoice__body m-invoice__body--centered">
                                <div class="table-responsive">
                                    <table class="table">
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
                                        <tr v-for="(details, index) in form.details">
                                            <td>{{details.product_name ? details.product_name : 'Not Available'}}</td>
                                            <td>{{details.remarks}}</td>
                                            <td>{{details.unit}}</td>
                                            <td class="m--font-danger">{{details.qty}}</td>
                                            <td>{{details.unit_price}}</td>
                                            <td class="m--font-danger">{{details.total_price}}</td>
                                        </tr>
                                        <tr>
                                            <td><hr></td>
                                            <td><hr></td>
                                            <td><hr></td>
                                            <td><hr></td>
                                            <td><hr></td>
                                            <td><hr></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"></td>
                                            <td > Total Weight: {{form.total_qty}}</td>
                                            <td colspan="1"></td>
                                            <td class="m--font-danger"> Total Amount: {{form.total_price}}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </form>

       <!-- <div class="row">
            <div class="col-md-11"></div>
            <div class="col-md-1">
                <button class="m-btn btn btn-brand" @click="printDiv('m-portlet')">Print</button>
                <br><br>
            </div>
        </div>-->

    </div>

</template>

<script>
    import { EventBus } from '../../../vue-assets';
    export default {

        props:['editId'],
        data:function(){
            return{
                sales_voucher_list:false,
                add_form:false,
                edit_form:false,
                view_form:true,
                form:{
                    vouchers_no: '',
                    sales_invoice_id: '',
                    sales_challan_id: '',
                    sales_date: '',
                    customer_id: '',
                    tender_no: '',
                    dr_chart_of_account_id: '',
                    cr_chart_of_account_id: '',
                    cost_center_id: '',
                    payment_type: '',
                    narration: '',
                    bank_id: '',
                    branch_id: '',
                    acc_no_id: '',
                    total_qty: '',
                    sub_total_price: '',
                    commission: '',
                    discount: '',
                    vat: '',
                    vat_type: '',
                    total_price: '',
                    price_paid: '',
                    price_due: '',

                    details: [
                        {
                            ac_sales_voucher_id: '',
                            product_id: '',
                            remarks: '',
                            m_unit: '',
                            unit_price: '',
                            discount_type: '',
                            discount: '',
                            vat_sub: '',
                            qty: '',
                            total_price: '',
                        }
                    ],
                },
                errors: {},
            };
        },

        methods:{

            printDiv(divName) {
                var printContents = document.getElementById(divName).innerHTML;
                var originalContents = document.body.innerHTML;
                document.body.innerHTML = printContents;
                window.print();
                document.body.innerHTML = originalContents;
            },

            show(id) {
                var _this = this;
                axios.get(base_url+'sales-invoice-voucher/'+id)
                    .then( (response) => {
                        _this.form  = response.data.data;
                    });
            },


            showMassage(data){
                if(data.status == 'success'){
                    toastr.success(data.message, 'Success Alert');
                }else if(data.status == 'error'){
                    toastr.error(data.message, 'Error Alert');
                }else{
                    toastr.error(data.message, 'Error Alert');
                }
            },

        },

        mounted(){
            var _this = this;

        },

        created(){
            var _this = this;
            _this.show(_this.editId);
            //_this.edit(_this.editId);

        }

    }
</script>
