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
                                                        <h1 class="print-head-title">Forming Voucher</h1>

                                                        <p v-if="form.account_status==0" class="m--font-danger">
                                                            <span style="font-weight: 600">Awaiting Approval</span>
                                                            <span></span>
                                                        </p>
                                                        <p v-if="form.account_status==1" class="m--font-success">
                                                            <span style="font-weight: 600">Approved</span><span></span>
                                                        </p>                                                        
                                                        <p><span style="font-weight: 600">Forming No :</span> <span>{{ form.forming_no }}</span></p>
                                                        <p><span style="font-weight: 600">Forming Date :</span> <span>{{ form.forming_date }}</span></p>
                                                        <p><span style="font-weight: 600">Forming Type :</span> <span>{{ form.forming_type }}</span></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-md-offset-3">
                                                    <p style="margin-bottom: 62px;">
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
                                                    <span class="m-invoice__subtitle">Debit Account</span>
                                                    <span class="m-invoice__text">
                                                        {{ form.dr_coa_title }}
                                                        - {{ form.dr_coa_code }} 
                                                    </span>
                                                </div>
                                                <div class="m-invoice__item text-center">
                                                    <span class="m-invoice__subtitle">Credit Account</span>
                                                    <span class="m-invoice__text">
                                                        {{ form.cr_coa_title }}
                                                        - {{ form.cr_coa_code }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-invoice__body m-invoice__body--centered">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table_font_size">
                                                <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th class="text-center">Remarks</th>
                                                    <th class="text-center">Roll Weight</th>
                                                    <th class="text-right">Opening / Current Qty</th>
                                                    <th class="text-right">Roll Production Qty</th>
                                                    <th class="text-right">Trans to Shapping</th>
                                                    <th class="text-right">Trans Weight</th>
                                                    <th class="text-right">Waste Qty</th>
                                                    <th class="text-right">Waste Weight</th>
                                                    <th class="text-right">Balance</th>
                                                    <th class="text-right">Unit Price</th>
                                                    <th class="text-right">Total Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(details, index) in form.details">
                                                    <td>{{ details.product.product_name }}</td>
                                                    <td class="text-left">{{ details.remarks }}</td>
                                                    <td class="text-center">{{ details.roll_weight }}</td>
                                                    <td class="text-right">{{ details.current_qty }}</td>
                                                    <td class="text-right">{{ details.receive_qty }}</td>
                                                    <td class="text-right">{{ details.trans_to_shapping_qty }}</td>
                                                    <td class="text-right">{{ details.trans_to_shapping_weight }}</td>
                                                    <td class="text-right">{{ details.shapping_westage_qty }}</td>
                                                    <td class="text-right">{{ details.shapping_westage_weight }}</td>
                                                    <td class="text-right">{{ details.forming_remain_qty }}</td>
                                                    <td class="text-right">{{ details.unit_price }}</td>
                                                    <td class="text-right">{{ details.total_price }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right m--font-danger" colspan="5">Total</td>
                                                    <td class="text-right m--font-danger">{{ form.total_trans_to_shapping_qty }}</td>
                                                    <td class="text-right">{{ form.total_trans_to_shap_weight }}</td>
                                                    <td class="text-right">{{ form.total_waste_qty }}</td>
                                                    <td class="m--font-danger">{{ form.total_waste_weight }}</td>
                                                    <td class="text-right">{{ form.total_remain_qty }}</td>
                                                    <td></td>
                                                    <td class="text-right">{{ form.total_amount }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right m--font-danger" colspan="8">Process Loss</td>
                                                    <td class="text-right">{{ form.process_loss_percentage }}%</td>
                                                    <td class="m--font-danger">{{ form.process_loss_weight }}</td>
                                                    <td></td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right m--font-danger" colspan="9">Total Waste</td>
                                                    <td class="m--font-danger">{{ form.weight_after_process_loss }}</td>
                                                    <td></td>
                                                    <td></td>
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
                    forming_no: '',
                    forming_date: '',
                    forming_type: '',
                    narration: '',
                    total_receive_qty: '',
                    total_trans_to_shapping_qty: '',
                    total_trans_to_shap_weight: '',
                    total_waste_qty: '',
                    total_waste_weight: '',
                    process_loss_percentage: '',
                    process_loss_weight: '',
                    weight_after_process_loss: '',
                    total_amount: '',
                    account_status: '',
                    details: [
                        {
                            product: '',
                            remarks: '',
                            roll_weight: '',
                            current_qty: '',
                            receive_qty: '',
                            trans_to_shapping_qty: '',
                            trans_to_shapping_weight: '',
                            forming_westage_qty: '',
                            forming_westage_weight: '',
                            unit_price: '',
                            total_price: '',

                        }
                    ],

                    dr_coa_code: '',
                    dr_coa_title: '',
                    cr_coa_code: '',
                    cr_coa_title: '',                     
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

                axios.get(base_url+'acc-forming-voucher/'+id)
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
<style scoped>
    .table_font_size tbody tr td{
        font-size: 10px !important;
    }
</style>
