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
                                                        <h1 class="print-head-title">HV</h1>
                                                        <p><span style="font-weight: 600">HV No :</span> <span>{{ form.testing_no }}</span></p>
                                                        <p><span style="font-weight: 600">HV Date :</span> <span>{{ form.testing_date }}</span></p>
                                                        <p><span style="font-weight: 600">Warehouse :</span> <span>{{ form.warehouse_id }}</span></p>
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
                                                    <span class="m-invoice__subtitle">HV No</span>
                                                    <span class="m-invoice__text">{{ form.testing_no }}</span>
                                                </div>
                                                <div class="m-invoice__item text-center">
                                                    <span class="m-invoice__subtitle">HV Date</span>
                                                    <span class="m-invoice__text">{{ form.testing_date }}</span>
                                                </div>
                                                <!-- <div class="m-invoice__item text-center">
                                                    <span class="m-invoice__subtitle">Warehouse</span>
                                                    <span class="m-invoice__text">{{ form.warehouse_id }}</span>
                                                </div> -->
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
                                                    <th class="text-center">Opening / Current Qty</th>
                                                    <th class="text-right">Rec.F Kiln Qty</th>
                                                    <th>Adj. Qty</th>
                                                    <th class="text-right">Unit Price</th>
                                                    <th class="text-right">	Trans to Finishing Qty</th>
                                                    <th class="text-right">	Total Price</th>
                                                    <th class="text-right"> Reject Qty </th>
                                                    <th class="text-right">	Balance </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(details, index) in form.details">
                                                    <td>{{ details.product.product_name }}</td>
                                                    <td class="text-left">{{ details.remarks }}</td>
                                                    <td class="text-center">{{ details.current_qty }}</td>
                                                    <td class="text-right">{{ details.receive_qty }}</td>
                                                    <td class="text-right">{{ details.adj_qty }}</td>
                                                    <td class="text-right">{{ details.unit_price }}</td>
                                                    <td class="text-right">{{ details.trans_to_finishing }}</td>
                                                    <td class="text-right">{{ details.total_price }}</td>
                                                    <td class="text-right">{{ details.reject_qty }}</td>
                                                    <td class="text-right">{{ details.remain_qty }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right m--font-danger" colspan="3">Total</td>
                                                    <td class="text-right m--font-danger">{{ form.total_receive_qty }}</td>
                                                    <td class="text-right m--font-danger">{{ form.total_adj_qty }}</td>
                                                    <td></td>
                                                    <td class="text-right m--font-danger">{{ form.total_trans_to_store_qty }}</td>
                                                    <td class="text-right">{{ form.total_amount }}</td>
                                                    <td class="text-right">{{ form.total_reject_qty }}</td>
                                                    <td class="m--font-danger">{{ form.total_remain_qty }}</td>
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
                    testing_no: '',
                    testing_date: '',
                    warehouse_id: '',
                    narration: '',
                    total_trans_to_store_qty: '',
                    total_amount: '',
                    total_reject_qty: '',
                    status: '',
                    details: [
                        {
                            product: '',
                            remarks: '',
                            current_qty: '',
                            receive_qty: '',
                            trans_to_finishing: '',
                            unit_price: '',
                            total_price: '',
                            reject_qty: '',
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

                axios.get(base_url+'testing-section/'+id)
                    .then( (response) => {
                        _this.form  = response.data.data;
                    });
            },

            doExportPdf() {
                var _this = this;
                var id = _this.editId;
                window.open(base_url + 'testing-section?export=pdf&id='+id +'', "_blank");

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
