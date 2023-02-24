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
                                                        <h1 class="print-head-title">Production Indirect Costs</h1>
                                                        <br>
                                                        <p v-if="form.approve_status == 0" class="m--font-danger"><span
                                                            style="font-weight: 600">Awaiting Approval</span>
                                                        <span> </span></p>
                                                        <p v-if="form.approve_status == 1" class="m--font-success"><span
                                                            style="font-weight: 600">Approved</span> <span> </span></p>                                                        
                                                        <br><br>
                                                        <p><span style="font-weight: 600">S/L No :</span> <span>{{ form.indir_no }}</span></p>
                                                        <p><span style="font-weight: 600">Date :</span> <span>{{ form.date }}</span></p>
                                                        <p><span style="font-weight: 600">Total Amount :</span> <span>{{ form.total_amount }}</span></p>
                                                        <p>
                                                            <span style="font-weight: 600">Remarks :</span>
                                                            <span>
                                                                {{ form.remarks }}
                                                            </span>
                                                        </p>
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
                                                <!-- <div class="m-invoice__item">
                                                    <span class="m-invoice__subtitle">Total Amount</span>
                                                    <span class="m-invoice__text">{{ form.total_amount }}</span>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-invoice__body m-invoice__body--centered">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table_font_size">
                                                <thead>
                                                <tr>
                                                    <th>Indirect Costs Type</th>
                                                    <th class="text-center">Remarks</th>
                                                    <th class="text-center">Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(details, index) in form.details">
                                                    <td>
                                                        {{ details.get_indirect_cost_type ? details.get_indirect_cost_type.indirect_cost_type : '' }}
                                                    </td>
                                                    <td class="text-left">{{ details.remarks }}</td>
                                                    <td class="text-right">{{ details.amount }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right m--font-danger" colspan="2">Total</td>
                                                    <td class="text-right m--font-danger">{{ form.total_amount }}</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="description-section">
                                            
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
                    indir_no: '',
                    date: '',
                    total_amount: '',
                    remarks: '',
                    approve_status: '',
                    details: [
                        {
                            get_indirect_cost_type: '',
                            prod_indir_costs_type_id: '',
                            remarks: '',
                            amount: '',
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
                axios.get(base_url+'indirect-costs/'+id)
                    .then( (response) => {
                        _this.form  = response.data.data;
                    });
            },

            doExportPdf() {
                var _this = this;
                var id = _this.editId;
                window.open(base_url + 'indirect-costs?export=pdf&id='+id +'', "_blank");
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
