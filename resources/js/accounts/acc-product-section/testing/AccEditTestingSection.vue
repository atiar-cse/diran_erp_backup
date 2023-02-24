<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >

            <div class="m-portlet__body">
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
                                                            <h1 class="print-head-title">HV Testing Voucher</h1>
                                                            <p><span style="font-weight: 600">HV No :</span> <span>{{ form.testing_no }}</span></p>
                                                            <p><span style="font-weight: 600">HV Date :</span> <span>{{ form.testing_date }}</span></p>
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
                                                        <th class="text-right"> Trans to Semi.F Qty</th>
                                                        <th class="text-right"> Total Price</th>
                                                        <th class="text-right"> Reject Qty </th>
                                                        <th class="text-right"> Balance </th>
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

                                            <div style="clear:both;width:100%;height:100px;"></div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--begin::Portlet-->

                <div class="form-group m-form__group row">
                    <div class="m-section__content col-lg-12">
                        <!-- Start Account -->
                        <section v-if="permissions.indexOf('acc-testing-voucher.approve') != -1" >
                            <hr>
                            <div class="m-form__heading">
                                <h3 class="m-form__heading-title">Accounts <i data-toggle="m-tooltip" data-width="auto" class="m-form__heading-help-icon flaticon-info" title="" data-original-title="Journal will hit once Requisition approved"></i> <small><mark><i> Journal will hit once approved. </i></mark></small> </h3>
                            </div>

                            <div class="form-group m-form__group row">
                                <label class="col-lg-2 col-form-label" for="debit_account_id">Debit Account <span class="requiredField">*</span></label>
                                <div class="col-lg-4">
                                    <select class="form-control select2"  id="debit_account_id" v-model="form.debit_account_id" data-selectField="debit_account_id">
                                        <option v-for="(value,index) in account_lists" :value="value.id"> {{ value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('debit_account_id'))">{{ (errors.hasOwnProperty('debit_account_id')) ? errors.debit_account_id[0] :'' }}</div>
                                </div>
                                <label class="col-lg-2 col-form-label" for="credit_account_id">Credit Account <span class="requiredField">*</span></label>
                                <div class="col-lg-4">
                                    <select class="form-control select2"  id="credit_account_id" v-model="form.credit_account_id" data-selectField="credit_account_id">
                                        <option v-for="(value,index) in account_lists" :value="value.id"> {{ value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}</option>
                                    </select>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('credit_account_id'))">{{ (errors.hasOwnProperty('credit_account_id')) ? errors.credit_account_id[0] :'' }}</div>
                                </div>
                            </div>
                        </section>
                        <!-- //End Account -->
                    </div>
                </div>
            </div>
            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="submit" v-if="permissions.indexOf('acc-testing-voucher.approve') != -1" class="btn btn-sm btn-primary" @click.prevent="update(form.id,1)" ><i class="la la-check"></i> <span>Approve</span></button>
                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="update(form.id,0)" ><i class="la la-check"></i> <span>Update and Go List</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
</template>

<script>
    import { EventBus } from '../../../vue-assets';
    import moment from 'moment';

    export default {

        props:['permissions','editId','account_lists'],

        data:function(){
            return{

                cinfo:{},

                data_list:false,
                add_form:false,
                edit_form:true,
                view_form:false,

                form:{
                    testing_no: '',
                    testing_date: '',
                    warehouse_id: '',
                    narration: '',

                    total_receive_qty: '',
                    total_adj_qty: '',
                    total_trans_to_store_qty: '',
                    total_amount: '',
                    total_remain_qty: '',
                    total_reject_qty: '',

                    approve_status: '',
                    status: 1,
                    details: [
                        {
                            product: '',
                            remarks: '',
                            current_qty: '',
                            receive_qty: '',
                            adj_qty: 0,
                            trans_to_finishing: '',
                            unit_price: '',
                            total_price: '',
                            remain_qty: '',
                            reject_qty: 0,
                        }
                    ],
                    debit_account_id: '',
                    credit_account_id: '',
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

            edit(id) {
                var _this = this;
                axios.get(base_url+'acc-testing-voucher/'+id+'/edit')
                    .then( (response) => {
                        _this.form  = response.data.data;
                       _this.initSelect2();
                    });
            },
            update(id,app){

                var _this = this;
                _this.form.approve_status = app;

                axios.put(base_url+'acc-testing-voucher/'+id, this.form).then( (response) => {
                    this.showMassage(response.data);
                    EventBus.$emit('data-changed');
                })
                    .catch(error => {
                        if(error.response.status == 422){
                            this.errors = error.response.data.errors;
                        }else{
                            this.showMassage(error);
                        }
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

            initSelect2(){
                setTimeout(function () {
                    var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                    jQuery(document).ready(function() {Select2.init();});
                },250);
            },
        },

        mounted(){
            var _this = this;
            _this.initSelect2();

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if(selectField == 'debit_account_id'){
                    _this.form.debit_account_id= selectedItem.val();
                }else if(selectField == 'credit_account_id'){
                    _this.form.credit_account_id= selectedItem.val();
                }
            });
        },

        created(){
            var _this = this;
            _this.edit(_this.editId);
            _this.company_info();
        }
    }
</script>
