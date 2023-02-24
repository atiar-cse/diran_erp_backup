<template>
    <div>
        <div class="m-portlet__body">
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-right">
                    <a href="#" onclick="window.print();return false" class="btn btn-success btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa flaticon-technology"></i><span>Print</span></span></a>
                    <a href="#" @click="doExportPdf()" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa fa-file-pdf"></i><span>PDF</span></span></a>
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
                                                <div class="col-md-9">
                                                    <div class="db-data">
                                                        <h1 class="print-head-title">Packing</h1>
                                                        <p><span style="font-weight: 600">Packing No :</span> <span>{{ form.packing_no }}</span></p>
                                                        <p><span style="font-weight: 600">Packing Date :</span> <span>{{ form.packing_date }}</span></p>
                                                        <p><span style="font-weight: 600">Warehouse :</span> <span>{{ form.warehouse_id }}</span></p>
                                                        <p><span style="font-weight: 600">Packing Types :</span> <span>{{ form.packing_types }}</span></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-md-offset-3">
                                                    <p style="margin-bottom: 62px;">
                                                        <!--<img src="img/1200px-Channel-i.svg.png" height="30px" width="30px">-->
                                                        <img :src="cinfo.logo" style="width: 150px; height: 50px;float: right">
                                                    </p>
                                                    <span class="m-invoice__desc">
                                                        <h3><span>{{cinfo.name}}</span></h3>
                                                        <span>{{cinfo.address}}</span>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="m-invoice__items">
                                                <div class="m-invoice__item ">
                                                    <span class="m-invoice__subtitle">Finishing Product</span>
                                                    <span class="m-invoice__text">{{ form.finishing_product_id ? form.finishing_product_id : '' }}</span>
                                                </div>
                                                <div class="m-invoice__item text-center">
                                                    <span class="m-invoice__subtitle">Store qty</span>
                                                    <span class="m-invoice__text">{{ form.trans_to_store_qty }}</span>
                                                </div>
                                                <div class="m-invoice__item text-center">
                                                    <span class="m-invoice__subtitle">Unit Price</span>
                                                    <span class="m-invoice__text">{{ form.unit_price }}</span>
                                                </div>
                                                <div class="m-invoice__item text-center">
                                                    <span class="m-invoice__subtitle">Total Price</span>
                                                    <span class="m-invoice__text">{{ form.total_price }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-invoice__body m-invoice__body--centered">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th class="text-center">Remarks</th>
                                                    <th class="text-right">Unit Price</th>
                                                    <th class="text-right">Current qty</th>
                                                    <th class="text-right">Used Qty </th>
                                                    <th class="text-right">Total used Qty </th>
                                                    <th class="text-right">Total Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(details, index) in form.details">
                                                    <td v-if="details.product_id != NULL">{{ details.product.product_name }}</td>
                                                    <td v-else></td>
                                                    <td class="text-left">{{ details.remarks }}</td>
                                                    <td class="text-center">{{ details.unit_price }}</td>
                                                    <td class="text-right">{{ details.current_qty }}</td>
                                                    <td class="text-right">{{ details.used_qty }}</td>
                                                    <td class="text-right">{{ details.total_used_qty }}</td>
                                                    <td class="text-right">{{ details.total_price }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right m--font-danger" colspan="5">Total</td>
                                                    <td class="text-right m--font-danger">{{ form.total_rm_qty }}</td>
                                                    <td class="m--font-danger">{{ form.total_rm_price }}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>


                                    <!-- Inventory Materials -->
                                    <div class="m-invoice__body m-invoice__body--centered">
                                         <strong>Packing Materials (Inventory)</strong>
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Product Name</th>
                                                    <th class="text-right"> Remarks</th>
                                                    <th>Qty</th>
                                                    <th class="text-right">Unit Price</th>
                                                    <th class="text-right"> Total Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(details, index) in form.inv_details">
                                                    <td v-if="details.product_id != NULL">{{ details.product.product_name }}</td>
                                                    <td v-else></td>
                                                    <td class="text-left">{{ details.remarks }}</td>
                                                    <td class="text-center">{{ details.qty }}</td>
                                                    <td class="text-center">{{ details.unit_price }}</td>
                                                    <td class="text-center">{{ details.total_price }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right m--font-danger" colspan="3">Total</td>
                                                    <td class="text-right m--font-danger">{{ form.inv_total_qty }}
                                                    </td>
                                                    <td class="text-right m--font-danger">{{ form.inv_total_amount }}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="description-section">
                                            <p>
                                                <span style="font-weight: 600">Narration :</span>
                                                <span>
                                                    {{ form.narration }}
                                                </span>
                                            </p>
                                        </div>
                                        <div style="clear:both;width:100%;margin-top:170px;"></div>

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
                                    <!-- //Inventory Materials -->

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
                data_list:false,
                add_form:false,
                edit_form:false,
                view_form:true,
                cinfo:{},
                form:{
                    packing_no: '',
                    packing_date: '',
                    warehouse_id: '',
                    packing_types: '',
                    finishing_product_id: '',
                    trans_to_store_qty: '',
                    unit_price: '',
                    total_price: '',
                    narration: '',
                    total_rm_qty: '',
                    total_rm_price: '',
                    status: '',
                    details: [
                        {
                            product: '',
                            remarks: '',
                            unit_price: '',
                            current_qty: '',
                            used_qty: '',
                            total_price: '',
                        }
                    ],
                    inv_details: [
                        {
                            product: '',
                            remarks: '',
                            unit_price: '',
                            current_qty: '',
                            used_qty: '',
                            total_price: '',
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

                axios.get(base_url+'packing-section/'+id)
                    .then( (response) => {
                        _this.form  = response.data.data;
                    });
            },

            doExportPdf() {
                var _this = this;
                var id = _this.editId;
                window.open(base_url + 'packing-section?export=pdf&id='+id +'', "_blank");

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
