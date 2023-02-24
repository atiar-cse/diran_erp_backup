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
                                                <div class="col-md-6">
                                                    <div class="db-data">
                                                        <h1 class="print-head-title">Purchase Return</h1>

                                                        <p><span style="font-weight: 600">Return No :</span> <span>{{ form.po_return_no }}</span></p>
                                                        <p><span style="font-weight: 600">Return Date :</span> <span>{{ form.po_return_date }}</span></p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <p style="margin-bottom: 62px;">
                                                        <img :src="cinfo.logo" style="width: 150px; height: 50px;float: right">
                                                    </p>
                                                    <span class="m-invoice__desc">
                                                        <span>{{cinfo.name}}</span>
                                                        <span>{{cinfo.address}}</span>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="m-invoice__items">
                                                <div class="m-invoice__item ">
                                                    <span class="m-invoice__subtitle">Return Type</span>
                                                    <span class="m-invoice__text">{{ form.po_rtn_issue_type_id }}</span>
                                                </div>
                                                <div class="m-invoice__item text-center">
                                                    <span class="m-invoice__subtitle">Supplier</span>
                                                    <span class="m-invoice__text">{{ form.po_rtn_supplier_id }}</span>
                                                </div>
                                                <div class="m-invoice__item text-center">
                                                    <span class="m-invoice__subtitle">Warehouse</span>
                                                    <span class="m-invoice__text">{{ form.po_rtn_warehouse_id }}</span>
                                                </div>
                                                <div class="m-invoice__item text-center">
                                                    <span class="m-invoice__subtitle">Total Price</span>
                                                    <span class="m-invoice__text">{{ form.po_total_price }}</span>
                                                </div>
                                                <div class="m-invoice__item text-center">
                                                    <span class="m-invoice__subtitle">Approve Status</span>
                                                    <span class="m-invoice__text"><div class="text-danger" v-if='form.approve == 0' > Not Approve</div><div  v-if='form.approve == 1'  class="text-success">Approve</div> </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-invoice__body m-invoice__body--centered">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Item No.</th>
                                                    <th>Remarks</th>
                                                    <th>Unit</th>
                                                    <th>Quantity</th>
                                                    <th>Unit Price</th>
                                                    <th>Total Price</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class="medium" v-for="(details, index) in form.details">
                                                        <td>{{details.product_name ? details.product_name : 'Not Available'}}</td>
                                                        <td>{{details.po_rtnd_remarks}}</td>
                                                        <td>{{details.po_rtnd_messure_unit}}</td>
                                                        <td >{{details.po_rtnd_qty}}</td>
                                                        <td>{{details.po_rtnd_unit_price}}</td>
                                                        <td >{{details.po_rtnd_total_price}}</td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="5" align="left">Total Qty </td>
                                                        <td class="m--font-success">{{form.po_rtn_total_qty}} </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5" align="left">Total </td>
                                                        <td class="m--font-success"> {{form.po_rtn_total_price}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5" align="left">Net Payable Amount </td>
                                                        <td class="m--font-success"> {{form.po_rtn_total_price}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="5" align="left">Inwards </td>
                                                        <td class="m--font-success"> {{form.po_rtn_total_price}} </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="description-section">
                                            <p>
                                                <span style="font-weight: 600">Narration :</span>
                                                <span>
                                                    {{ form.po_rtn_narration }}
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
                product_list:false,
                add_form:false,
                edit_form:false,
                view_form:true,

                cinfo:{},

                form:{
                    po_return_no: '',
                    po_return_date: '',
                    po_rtn_supplier_id: '',
                    po_rtn_warehouse_id: '',
                    po_rtn_issue_type_id: '',
                    po_rtn_narration: '',
                    po_rtn_docs: '',
                    po_rtn_total_qty: '',
                    po_rtn_total_price: '',
                    approve:'',
                    details: [
                        {
                            po_rtnd_product_id:'',
                            po_rtnd_remarks:'',
                            po_rtnd_messure_unit:'',
                            po_rtnd_unit_price:'',
                            po_rtnd_qty:'',
                            po_rtnd_total_price:''
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

            comany_info(){
                var _this = this;
                axios.get(base_url+'Company-Info/'+1)
                    .then( (response) => {
                        _this.cinfo  = response.data;
                    });
            },

            show(id) {
                var _this = this;

                axios.get(base_url+'po-return/'+id)
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
            _this.comany_info();
        }

    }
</script>
