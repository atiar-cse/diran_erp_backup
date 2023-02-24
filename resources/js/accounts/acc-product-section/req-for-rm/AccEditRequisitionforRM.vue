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
                                                            <h1 class="print-head-title">Requisition Voucher</h1>
                                                            <p><span style="font-weight: 600">Requisition No :</span> <span>{{ form.rm_requisition_no }}</span></p>
                                                            <p><span style="font-weight: 600">Requisition Date :</span> <span>{{ form.requisition_date }}</span></p>
                                                            <p><span style="font-weight: 600">Warehouse :</span> <span>{{ form.warehouse_id }}</span></p>
                                                            <p><span style="font-weight: 600">Requistion Types :</span> <span>{{ form.requisition_types }}</span></p>
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
                                                        <th class="text-center">M Unit</th>
                                                        <th class="text-right">QTY</th>
                                                        <th class="text-right">Unit Price</th>
                                                        <th class="text-right">Total Price</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <tr v-for="(details, index) in form.details">
                                                        <td>{{ details.product_name }}</td>
                                                        <td class="text-left">{{ details.remarks }}</td>
                                                        <td class="text-center">{{ details.measure_unit }}</td>
                                                        <td class="text-right">{{ details.qty }}</td>
                                                        <td class="text-right">{{ details.unit_price }}</td>
                                                        <td class="text-right">{{ details.total_price }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right m--font-danger" colspan="3">Total</td>
                                                        <td class="text-right m--font-danger">{{ form.total_qty }}</td>
                                                            <td class="text-right"></td>
                                                        <td class="m--font-danger">{{ form.total_amount }}</td>
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
                                            <div style="clear:both;width:100%;height:50px;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Start Account -->
                <section v-if="permissions.indexOf('acc-req-rm-voucher.approve') != -1" >
                    <hr>
                    <div class="m-form__heading">
                        <h3 class="m-form__heading-title">Accounts <i data-toggle="m-tooltip" data-width="auto" class="m-form__heading-help-icon flaticon-info" title="" data-original-title="Journal will hit once Requisition approved"></i> <small><mark><i> Journal will hit once Requisition Voucher approved. </i></mark></small> </h3>
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

            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-md-6">
                            <!-- <p v-if="permissions.indexOf('acc-req-rm-voucher.approve') != -1" class="bg-info text-white"> On Approve : Stock will out! </p> -->
                        </div>
                        <div class="col-md-6 m--align-right">                            
                            <button type="submit" v-if="permissions.indexOf('acc-req-rm-voucher.approve') != -1" class="btn btn-sm btn-primary"  @click.prevent="update(form.id,1)"><i class="la la-check"></i> <span>Approve</span></button>
                            <button type="submit" class="btn btn-sm btn-success"  @click.prevent="update(form.id,0)"><i class="la la-check"></i> <span>Update and Go List</span></button>
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
                data_list:false,
                add_form:false,
                edit_form:true,
                view_form:false,

                cinfo:{},

                form:{
                    rm_requisition_no: '',
                    requisition_date: '',
                    warehouse_id: '',
                    requisition_types: '',
                    narration: '',
                    estimate_qty_for_production: '',
                    total_qty: '',
                    total_amount: '',
                    approve_status:'',
                    account_status:'',
                    details: [
                        {
                            product_id: '',
                            remarks: '',
                            one_kg_weight: '',
                            qty: 0,
                            unit_price: 0,
                            total_price: '',
                            current_stock_qty: '',
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
                axios.get(base_url+'acc-req-rm-voucher/'+id+'/edit').then( (response) => {
                    _this.form  = response.data.data;

                    setTimeout(function () {
                        var BootstrapSelect={init:function(){$(".m_selectpicker").selectpicker('refresh')}};
                        jQuery(document).ready(function(){BootstrapSelect.init()});

                        var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                        jQuery(document).ready(function() {Select2.init()});

                    },100);

                });
            },


            update(id,app){
                var _this = this;
                _this.form.approve_status = app;

                axios.put(base_url+'acc-req-rm-voucher/'+id, this.form).then( (response) => {
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
        },

        mounted(){

            var _this = this;

            var BootstrapSelect={init:function(){$(".m_selectpicker").selectpicker()}};
            jQuery(document).ready(function(){BootstrapSelect.init()});

            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
            jQuery(document).ready(function() {Select2.init()});

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if(selectField == 'debit_account_id'){
                    _this.form.debit_account_id= selectedItem.val();
                }else if(selectField == 'credit_account_id'){
                    _this.form.credit_account_id= selectedItem.val();
                }
            });

            $('.number').keypress(function(event) {
                if (((event.which != 46 || (event.which == 46 && $(this).val() == '')) ||
                    $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            }).on('paste', function(event) {
                event.preventDefault();
            });
        },

        created(){
            var _this = this;
            _this.edit(_this.editId);
            _this.company_info();
        }
    }
</script>
