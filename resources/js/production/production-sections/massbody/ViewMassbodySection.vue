<template>
    <div>
        <div class="m-portlet__body">
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-right">
                    <a href="#" onclick="window.print();return false" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa flaticon-technology"></i><span>Print</span></span></a>
                    <!--<a href="#" class="btn btn-accent btn-sm m-btn  m-btn m-btn&#45;&#45;icon"><span><i class="fa flaticon-doc"></i><span>PDF</span></span></a>-->
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
                                                        <h1 class="print-head-title">Massbody</h1>
                                                        <p><span style="font-weight: 600">Massbody No :</span> <span>{{ form.massbody_no }}</span></p>
                                                        <p><span style="font-weight: 600">Massbody Date :</span> <span>{{ form.massbody_date }}</span></p>
                                                        <p><span style="font-weight: 600">Requisition No :</span> <span>{{ form.requisition.rm_requisition_no }}</span></p>
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
                                                    <span class="m-invoice__subtitle">Massbody No</span>
                                                    <span class="m-invoice__text">{{ form.massbody_no }}</span>
                                                </div>
                                                <div class="m-invoice__item text-center">
                                                    <span class="m-invoice__subtitle">Massbody Date</span>
                                                    <span class="m-invoice__text">{{ form.massbody_date }}</span>
                                                </div>
                                                <div class="m-invoice__item text-center">
                                                    <span class="m-invoice__subtitle">Requisition No</span>
                                                    <span class="m-invoice__text">{{ form.requisition.rm_requisition_no }}</span>
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
                                                    <th class="text-right">Percentage (%)</th>
                                                    <th class="text-right">Weight</th>
                                                    <th>Avg. Unit Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>{{ form.green_chip_name }}</td>
                                                    <td class="text-right">{{ form.green_chip_percentage }}</td>
                                                    <td class="text-right">{{ form.green_chip_weight }}</td>
                                                    <td>{{form.green_chip_unit_price}}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ form.recycle_chip_name }}</td>
                                                    <td class="text-right">{{ form.recycle_chip_percentage }}</td>
                                                    <td class="text-right">{{ form.recycle_chip_weight }}</td>
                                                    <td>{{form.recycle_chip_unit_price}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-right m--font-danger" colspan="1">Total</td>
                                                    <td class="text-right m--font-danger">{{ form.total_percentage }}</td>
                                                    <td class="m--font-danger">{{ form.total_weight_qty }}</td>
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
                    massbody_no: '',
                    massbody_date: '',
                    requisition: '',
                    narration: '',
                    green_chip_name: 'Green Chip',
                    green_chip_percentage: 0,
                    green_chip_weight: 0,
                    green_chip_unit_price: 0,
                    recycle_chip_name: 'Recycle Chip',
                    recycle_chip_percentage: 0,
                    recycle_chip_weight: 0,
                    recycle_chip_unit_price: 0,
                    total_percentage: 100,
                    total_weight_qty: 0,
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

                axios.get(base_url+'massbody-section/'+id)
                    .then( (response) => {
                        _this.form  = response.data;
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
