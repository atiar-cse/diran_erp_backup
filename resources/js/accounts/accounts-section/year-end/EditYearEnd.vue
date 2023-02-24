<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="forming_no" class="col-lg-2 col-form-label">Assembling No <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="forming_no" v-model="form.assembling_no" class="form-control form-control-sm m-input" placeholder="Enter Assembling No">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('assembling_no'))">{{ (errors.hasOwnProperty('assembling_no')) ? errors.assembling_no[0] :'' }}</div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Assembling Date <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.assembling_date" data-dateField="assembling_date" readonly placeholder="Select date from picker" id="m_datepicker_2" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('assembling_date'))">{{ (errors.hasOwnProperty('assembling_date')) ? errors.assembling_date[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="packingTypes">Assembling Types <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" v-model="form.assembling_types" data-selectField="assembling_types" id="packingTypes">
                            <option> Production</option>
                            <option> Rejects</option>
                        </select>
                    </div>
                    <label class="col-lg-2 col-form-label" for="finishingProduct">Finishing Product <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" v-model="form.finishing_product_id" data-selectField="forming_type" id="finishingProduct">
                            <option v-for="(value,index) in finish_product_lists" :value="value.id"> {{value.product_name}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="trans_to_packing_qty">Trans to Packing Qty <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="trans_to_packing_qty" v-model="form.trans_to_packing_qty" @change="totalQtyPriceCalc()" class="form-control form-control-sm m-input" placeholder="Enter Trans to Packing Qty">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('trans_to_packing_qty'))">{{ (errors.hasOwnProperty('trans_to_packing_qty')) ? errors.trans_to_packing_qty[0] :'' }}</div>
                    </div>
                    <label for="unit_price" class="col-lg-2 col-form-label">Unit Price <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="unit_price" v-model="form.unit_price" @change="totalQtyPriceCalc()" class="form-control form-control-sm m-input" placeholder="Enter Unit Price">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('unit_price'))">{{ (errors.hasOwnProperty('unit_price')) ? errors.unit_price[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">

                    <label class="col-lg-2 col-form-label" for="total_price">Total Price <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="total_price" v-model="form.total_price" @change="totalQtyPriceCalc()" class="form-control form-control-sm m-input" placeholder="Enter Total Price">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('total_price'))">{{ (errors.hasOwnProperty('total_price')) ? errors.total_price[0] :'' }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.narration"  id="narration" rows="2"></textarea>
                    </div>
                </div>
                <br><br>
                <!--begin::Portlet-->

                <div class="form-group m-form__group row">
                    <div class="m-section__content col-lg-12">
                        <div class ="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse" >
                                <tr>
                                    <th></th>
                                    <th>Product <span class="requiredField">*</span></th>
                                    <th>Remarks</th>
                                    <th>Price <span class="requiredField">*</span></th>
                                    <th>Current qty <span class="requiredField">*</span></th>
                                    <th>Used Qty <span class="requiredField">*</span></th>
                                    <th>Total Price <span class="requiredField">*</span></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <th scope="row">
                                        <a href="javascript:void(0)"  @click="addNewItem" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </th>
                                    <td class="tdWidth">
                                        <select class="form-control select2 select-product" v-bind:data-index="index" v-model="details.product_id" style="width:100%">
                                            <option v-for="(value,index) in product_lists" :value="value.id" :data-roll-weight="value.forming_weight"> {{value.product_name}}</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{ errors['details.'+index+'.product_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm m-input" v-model="details.remarks" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.remarks']">{{ errors['details.'+index+'.remarks'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm m-input" v-model="details.unit_price" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{ errors['details.'+index+'.unit_price'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm m-input" v-model="details.current_qty" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.current_qty']">{{ errors['details.'+index+'.current_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm m-input" v-model="details.used_qty" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.used_qty']">{{ errors['details.'+index+'.used_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm m-input" v-model="details.total_price" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.total_price']">{{ errors['details.'+index+'.total_price'][0] }}</div>
                                    </td>
                                    <td scope="row" class="width-100 text-center">
                                        <a  @click="removeItem(index,details.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Total</td>
                                    <td>
                                        <input type="text" v-model="form.total_rm_qty" class="form-control form-control-sm m-input" placeholder="Used Qty">
                                    </td>
                                    <td>
                                        <input type="text" v-model="form.total_rm_price" class="form-control form-control-sm m-input" placeholder="Total Price">
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
                            <button type="reset" class="btn btn-sm btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save</span></button>
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save and Go List</span></button>
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

    export default {

        props:['product_lists','finish_product_lists','editId'],

        data:function(){
            return{

                data_list:false,
                add_form:true,
                edit_form:false,

                form:{
                    assembling_no: '',
                    assembling_date: '',
                    assembling_types: '',
                    finishing_product_id: '',
                    trans_to_packing_qty: '',
                    unit_price: '',
                    total_price: '',
                    narration: '',
                    total_rm_qty: '',
                    total_rm_price: '',
                    status: '',
                    details: [
                        {
                            product_id: '',
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

            addNewItem(){
                this.form.details.push({
                    product_id: '',
                    remarks: '',
                    unit_price: '',
                    current_qty: '',
                    used_qty: '',
                    total_price: '',
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

            removeItem(index,deletedId){
                var _this = this;
                if(_this.form.details.length > 1){
                    _this.form.details.splice(index, 1);
                    setTimeout(function () {
                        var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                        jQuery(document).ready(function() {Select2.init();});
                    },100);

                    if (deletedId) {
                     _this.form.deleteID.push(deletedId);
                     }
                    _this.totalQtyPriceCalc();
                }
            },

            edit(id) {
                var _this = this;
                axios.get(base_url+'assembling-section/'+id+'/edit')
                    .then( (response) => {
                        _this.form  = response.data.data;
                        setTimeout(function () {
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
                        },100);

                    });
            },
            update(id){
                axios.put(base_url+'assembling-section/'+id, this.form).then( (response) => {
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

            totalQtyPriceCalc(){

                var _this = this;

                var totalPackedPrice = 0;
                totalPackedPrice =  Number(_this.form.unit_price) * Number(_this.form.trans_to_packing_qty);
                _this.form.total_price = totalPackedPrice.toFixed( 2 );

                var totalRmPrice = 0;
                var totalRmAmount = 0;
                var totalUsedQty = 0;
                var usedQty = 0;

                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    usedQty = Number(_this.form.trans_to_packing_qty) * Number(details[i].used_qty);
                    totalRmPrice = Number(details[i].unit_price) * usedQty;

                    _this.form.details[i].used_qty = usedQty.toFixed( 2 );
                    _this.form.details[i].total_price = totalRmPrice.toFixed( 2 );

                    totalUsedQty = Number(details[i].used_qty) + totalUsedQty;
                    totalRmAmount = totalRmPrice + totalRmAmount;
                }
                _this.form.total_rm_qty = totalUsedQty.toFixed( 2 );
                _this.form.total_rm_price = totalRmAmount.toFixed( 2 );

            },
        },

        mounted(){
            var _this = this;

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if( selectField == 'assembling_types' ){
                    _this.form.assembling_types= selectedItem.val();
                }else if(selectField == 'finishing_product_id'){

                    var finishProductBuyingPrice = $('option:selected', $(e.target)).attr('finish-product-buying-price');
                    _this.form.finishing_product_id= selectedItem.val();
                    _this.form.unit_price  = finishProductBuyingPrice;
                    _this.changeFinishProduct();
                }
            });

            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.details[dataIndex].product_id = selectedItem.val();
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



            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.assembling_date = '';

                    }else {
                        _this.form.assembling_date = moment(ev.date).format('DD-MM-YYYY');

                    }

                });
            });

        },

        created(){
            var _this = this;
            _this.edit(_this.editId);
            _this.totalQtyPriceCalc();
        }


    }
</script>