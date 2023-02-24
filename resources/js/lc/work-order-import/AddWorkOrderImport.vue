<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right"  >
            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <label for="work_order_no" class="col-lg-2 col-form-label">Work Order No <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text"  id="work_order_no"  v-model="form.work_order_no" class="form-control form-control-sm m-input" placeholder="Enter Work Order No">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('work_order_no'))">{{ (errors.hasOwnProperty('work_order_no')) ? errors.work_order_no[0] :'' }}</div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Order Date <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.order_date" data-dateField="order_date" readonly placeholder="Select date from picker" id="m_datepicker_2" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('order_date'))">{{ (errors.hasOwnProperty('order_date')) ? errors.order_date[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="supplier_id" class="col-lg-2 col-form-label">Supplier/ Exporter<span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-supplier" id="supplier_id" data-selectField="supplier_id" v-model="form.supplier_id" >
                            <option v-for="(svalue,sindex) in supplier_lists" :value="svalue.id" > {{svalue.purchase_supplier_name}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('supplier_id'))">{{ (errors.hasOwnProperty('supplier_id')) ? errors.supplier_id[0] :'' }}</div>
                    </div>
                    <label for="coa_id" class="col-lg-2 col-form-label">COA</label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-coa" id="coa_id" data-selectField="coa_id" v-model="form.coa_id" >
                            <option v-for="(svalue,sindex) in coa_lists" :value="svalue.id" > {{svalue.chart_of_accounts_title}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.narration"  id="narration" rows="2"></textarea>
                    </div>
                </div>
                <br><br>
                <!--begin::Portlet-->

                <div class="form-group m-form__group row add-manage-section">
                    <div class="m-section__content col-lg-12">
                        <div class ="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse" >
                                <tr>
                                    <th></th>
                                    <th class="width-210">Product <span class="requiredField">*</span></th>
                                    <th>Remarks</th>
                                    <th class="width-210">Currency <span class="requiredField">*</span></th>
                                    <th>Unit Price <span class="requiredField">*</span></th>
                                    <th>Qty <span class="requiredField">*</span></th>
                                    <th style="width: 170px;">Total Price </th>
                                    <th>BDT Rate <span class="requiredField">*</span></th>
                                    <th style="width: 170px;">Total Taka(BDT) </th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <td scope="row">
                                        <a href="javascript:void(0)"  @click="addNewItem" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </td>
                                    <td class="width-210">
                                        <select class="form-control m-select2 select2  select-product width-210" v-bind:data-index="index" v-model="details.product_id" >
                                            <option v-for="(value,index) in product_lists" :value="value.id" > {{value.product_name}} - {{value.product_code}}</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{ errors['details.'+index+'.product_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.remarks" class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td class="width-210">
                                        <select class="form-control m-select2 select2  select-currency width-210" v-bind:data-index="index" v-model="details.currency_id" style="width:100%" >
                                            <option v-for="(value,index) in currency_lists" :value="value.id" > {{value.symbol}} ({{value.name}})</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.currency_id']">{{ errors['details.'+index+'.currency_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="details.unit_price" @input="totalQtyAndPriceAndRate()" class="form-control form-control-sm m-input" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{ errors['details.'+index+'.unit_price'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="details.qty" @input="totalQtyAndPriceAndRate()" class="form-control form-control-sm m-input">
                                        <div class="requiredField" v-if="errors['details.'+index+'.qty']">{{ errors['details.'+index+'.qty'][0] }}</div>
                                    </td>
                                    <td style="width: 170px;">
                                        <input style="width: 170px;" type="text" v-model="details.total_price" class="form-control form-control-sm m-input" placeholder="" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="details.bdt_convert_rate" @input="totalQtyAndPriceAndRate()" class="form-control form-control-sm m-input">
                                        <div class="requiredField" v-if="errors['details.'+index+'.bdt_convert_rate']">{{ errors['details.'+index+'.bdt_convert_rate'][0] }}</div>
                                    </td>

                                    <td style="width: 170px;">
                                        <input style="width: 170px;" type="text" v-model="details.total_amount_taka" class="form-control form-control-sm m-input" readonly>
                                    </td>

                                    <td>
                                        <a  @click="removeItem(index)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Total </td>
                                    <td class="">
                                        <input type="text" v-model="form.total_qty" class="form-control form-control-sm m-input" placeholder="Qty" readonly>
                                    </td>
                                    <td class="">
                                        <input type="text" v-model="form.total_amount" class="form-control form-control-sm m-input" placeholder="Total Price" readonly>
                                    </td>
                                    <td  class="">
                                    </td>
                                    <td  class="">
                                        <input type="text" v-model="form.total_amount_taka" class="form-control form-control-sm m-input" placeholder="Amount in Taka" readonly>
                                    </td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <br><br>
                    </div>
                </div>
            </div>


            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">

                            <button type="submit" class="btn btn-sm btn-info" @click.prevent="store(0)"><i class="la la-save"></i> <span>Save</span></button>
                            <button v-if="permissions.indexOf('work-order-import.approve') !=-1" type="submit" class="btn btn-sm btn-success" @click.prevent="store(1)"><i class="la la-check"></i> <span>Approve</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
</template>

<script>
    import { EventBus } from '../../vue-assets';
    export default {
        props:['permissions','product_lists','supplier_lists','currency_lists','coa_lists'],

        data:function(){
            return{
                product_list:false,
                add_form:true,
                edit_form:false,

                form:{
                    work_order_no: '',
                    supplier_id:'',
                    order_date: '',
                    narration: '',
                    total_qty:0,
                    total_amount:0,
                    total_amount_taka:0,
                    approve:0,
                    details: [
                        {
                            product_id:'',
                            remarks:'',
                            currency_id:'1',
                            unit_price:'',
                            qty:'',
                            total_price:0,
                            bdt_convert_rate:'',
                            total_amount_taka:0,
                        }
                    ],
                },
                errors: {},
            };
        },

        methods:{

            workOrderNoGenerate(){
                var _this = this;
                axios.get(base_url+'work-order-no-generate')
                    .then( (response) => {
                        _this.form.work_order_no = response.data;
                    });
            },

            addNewItem(){
                this.form.details.push({
                    product_id:'',
                    remarks:'',
                    currency_id:'1',
                    unit_price:'',
                    qty:'',
                    total_price:0,
                    bdt_convert_rate:'',
                    total_amount_taka:0,
                });
                var Select2= {
                    init:function() {
                        $(".select2").select2( {
                                placeholder: "Please select an option"
                            }
                        )
                    }
                };
                jQuery(document).ready(function() {
                        Select2.init()
                    }
                );
            },

            removeItem(index){
                var _this = this;
                if(_this.form.details.length > 1){
                    _this.form.details.splice(index, 1);
                    setTimeout(function () {
                        var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                        jQuery(document).ready(function() {Select2.init();});
                    },100);

                    _this.totalQtyAndPriceAndRate();
                }
            },

            store:function(approval){
                var _this = this;
                _this.form.approve = approval;
                axios.post(base_url+'work-order-import', this.form).then( (response) => {
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

            totalQtyAndPriceAndRate(){
                var total_qty = 0;
                var total_amount = 0;
                var total_price = 0;
                var total_amount_taka = 0;
                var grand_total_amount_taka = 0;
                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    total_price = Number(details[i].unit_price) * Number(details[i].qty);
                    this.form.details[i].total_price = total_price;
                    total_qty = Number(details[i].qty) + total_qty;
                    total_amount = total_price + total_amount;

                    total_amount_taka = total_price * Number(details[i].bdt_convert_rate);
                    this.form.details[i].total_amount_taka = total_amount_taka;
                    grand_total_amount_taka = grand_total_amount_taka + total_amount_taka;
                }
                this.form.total_price = total_price;
                this.form.total_qty = total_qty;
                this.form.total_amount = total_amount;
                this.form.total_amount_taka = grand_total_amount_taka;
            },
            duplicateCheck(selectedValue){

                var no_index = this.form.details.length;
                for(let i=0; i<no_index; i++){
                    if(this.form.details[i].product_id == selectedValue){
                        alert("Duplicate Found");
                    }
                }
            },

        },

        mounted(){

            var _this = this;
            $('#addComponent').on('change', '.select-supplier', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.form.supplier_id = selectedItem.val();
            });
            $('#addComponent').on('change', '.select-coa', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.form.coa_id = selectedItem.val();
            });
            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.duplicateCheck( selectedItem.val() );
                _this.form.details[dataIndex].product_id = selectedItem.val();
            });
            $('#addComponent').on('change', '.select-currency', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.form.details[dataIndex].currency_id = selectedItem.val();
            });         

            $(document).on("focus", ".datepicker", function () {

                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.order_date = '';

                    }else {
                        _this.form.order_date = moment(ev.date).format('DD/MM/YYYY');

                    }

                });
            });

            var Select2= {
                init:function() {
                    $(".select2").select2( {
                            placeholder: "Please select an option"
                        }
                    )
                }
            };

            jQuery(document).ready(function() {
                    Select2.init()
                }
            );
        },

        created() {
            var _this = this;
            _this.workOrderNoGenerate();
            _this.totalQtyAndPriceAndRate();
            _this.form.order_date = moment().format('DD/MM/YYYY');
        }

    }
</script>