<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="forming_no" class="col-lg-2 col-form-label">Glaze No <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="forming_no" v-model="form.glaze_no" class="form-control form-control-sm m-input" placeholder="Enter Glaze No" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('glaze_no'))">{{ (errors.hasOwnProperty('glaze_no')) ? errors.glaze_no[0] :'' }}</div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Glaze Date <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.glaze_date" data-dateField="glaze_date" disabled placeholder="Select date from picker" id="m_datepicker_2" autocomplete="off"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('glaze_date'))">{{ (errors.hasOwnProperty('glaze_date')) ? errors.glaze_date[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.narration"  id="narration" rows="2"></textarea>
                    </div>
                    <label class="col-lg-2 col-form-label" for="pre_mth_ovr_price">Previous month overhead price (Kg)</label>
                    <div class="col-lg-4">
                        <input type="text" id="pre_mth_ovr_price" v-model="pre_mth_ovr_price" class="form-control form-control-sm m-input" placeholder="ex: 1 kg">
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
                                    <th>Weight <span class="requiredField">*</span></th>
                                    <th>Opening / Current Qty <span class="requiredField">*</span></th>
                                    <th>Rec.F Dryer Qty <span class="requiredField">*</span></th>
                                    <th>Trans to Kiln Qty<span class="requiredField">*</span></th>
                                    <th>Trans Weight <span class="requiredField">*</span></th>
                                    <th>Waste Qty <span class="requiredField">*</span></th>
                                    <th>Waste Weight <span class="requiredField">*</span></th>
                                    <th class="width-160">Balance <span class="requiredField">*</span></th>
                                    <th>Unit Price <span class="requiredField">*</span></th>
                                    <th>Overhead Price <span class="requiredField">*</span></th>
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
                                    <td class="width-210">
                                        <select class="form-control select2 select-product width-210" v-bind:data-index="index" v-model="details.product_id" style="width:100%" disabled>
                                            <option v-for="(value,index) in product_lists"
                                                :value="value.id"
                                                :data-glaze-weight="value.glaze_weight"
                                                :glaze-opening-qty="value.glaze_current_qty"
                                                :glaze-receive-qty="value.glaze_receive_qty"
                                                :buying-price="value.buying_price"
                                            > <span v-if="value.is_raw_material_status==1">[RM]</span> {{value.product_name}}</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{ errors['details.'+index+'.product_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm m-input width-80" v-model="details.remarks" placeholder="" >
                                        <div class="requiredField" v-if="errors['details.'+index+'.remarks']">{{ errors['details.'+index+'.remarks'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.glaze_weight" @input="totalTransQtyWeightCalc()" placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.glaze_weight']">{{ errors['details.'+index+'.glaze_weight'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.current_qty" placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.current_qty']">{{ errors['details.'+index+'.current_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.receive_qty" @input="totalTransQtyWeightCalc()" placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.receive_qty']">{{ errors['details.'+index+'.receive_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.trans_kiln_qty" @input="totalTransQtyWeightCalc()" placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.trans_kiln_qty']">{{ errors['details.'+index+'.trans_kiln_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.trans_kiln_weight" placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.trans_kiln_weight']">{{ errors['details.'+index+'.trans_kiln_weight'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.waste_qty" @input="totalTransQtyWeightCalc()" placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.waste_qty']">{{ errors['details.'+index+'.waste_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.waste_weight" placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.waste_weight']">{{ errors['details.'+index+'.waste_weight'][0] }}</div>
                                    </td>
                                    <td class="width-160">
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-160" v-model="details.remain_qty" @input="totalTransQtyWeightCalc()" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.remain_qty']">{{ errors['details.'+index+'.remain_qty'][0] }}</div>
                                    </td>

                                    <td>
                                        <input type="text" v-model="details.unit_price" @input="totalTransQtyWeightCalc()" class="form-control form-control-sm m-input number">
                                        <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{ errors['details.'+index+'.unit_price'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.overhead_price" class="form-control form-control-sm m-input number" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.overhead_price']">{{ errors['details.'+index+'.overhead_price'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.total_price" class="form-control form-control-sm m-input number" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.total_price']">{{ errors['details.'+index+'.total_price'][0] }}</div>
                                    </td>
                                    <td scope="row" class="width-100 text-center">
                                        <a  @click="removeItem(index,details.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Total</td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_receive_qty" class="form-control form-control-sm m-input" placeholder="Total Rec. Qty" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_trans_to_kiln_qty" class="form-control form-control-sm m-input" placeholder="Total Kiln Qty" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_trans_to_kiln_weight" class="form-control form-control-sm m-input" placeholder="Total Kiln Weight" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_waste_qty" class="form-control form-control-sm m-input" placeholder="Waste Qty" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_waste_weight" class="form-control form-control-sm m-input" placeholder="Waste Weight" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_remain_qty" class="form-control form-control-sm m-input" placeholder="Balance" readonly>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <input type="text" v-model="form.total_amount" class="form-control form-control-sm m-input" placeholder="Total Amount" readonly>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="8" class="text-right">Process Loss</td>
                                    <td colspan="1">
                                        <input type="number" step="any" v-model="form.process_loss_percentage" @input="totalTransQtyWeightCalc()" class="form-control form-control-sm m-input" placeholder="2%">
                                    </td>
                                    <td colspan="1">
                                        <input type="number" step="any" v-model="form.process_loss_weight" class="form-control form-control-sm m-input" placeholder="Loss Weight" readonly>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="text-right">Total Waste</td>

                                    <td colspan="1">
                                        <input type="text" v-model="form.weight_after_process_loss" class="form-control form-control-sm m-input" placeholder="Total Waste" readonly>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
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
                        <div class="col-md-6">
                        </div>
                        <div class="col-lg-6 m--align-right">
                            <button type="submit" class="btn btn-sm btn-danger" @click.prevent="update(form.id,1)" ><i class="la la-check"></i> <span>Save Correction</span></button>
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

        props:['permissions','product_lists','editId'],

        data:function(){
            return{

                data_list:false,
                add_form:false,
                edit_form:true,
                view_form:false,

                pre_mth_ovr_price:'2.5',

                form:{
                    glaze_no: '',
                    glaze_date: '',
                    narration: '',
                    total_trans_to_kiln_qty: '',
                    total_trans_to_kiln_weight: '',
                    total_waste_qty: '',
                    total_waste_weight: '',
                    total_receive_qty: '',
                    total_remain_qty: '',
                    total_amount: '',
                    process_loss_percentage: '',
                    process_loss_weight: '',
                    weight_after_process_loss: '',
                    approve_status: '',
                    status: '',
                    details: [
                        {
                            product_id: '',
                            remarks: '',
                            glaze_weight: '',
                            current_qty: '',
                            receive_qty: '',
                            trans_kiln_qty: '',
                            trans_kiln_weight: '',
                            remain_qty: '',
                            waste_qty: '',
                            waste_weight: '',
                            unit_price: '',
                            overhead_price: '',
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
                    glaze_weight: '',
                    current_qty: '',
                    receive_qty: '',
                    trans_kiln_qty: '',
                    trans_kiln_weight: '',
                    remain_qty: '',
                    waste_qty: '',
                    waste_weight: '',
                    unit_price: '',
                    overhead_price: '',
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
                    _this.totalTransQtyWeightCalc();
                }
            },

            edit(id) {
                var _this = this;
                axios.get(base_url+'glaze-section/'+id+'/edit')
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
            update(id,app){

                var _this = this;
                _this.form.approve_status = app;

                axios.put(base_url+'glaze-section/'+id+'?isSuperAdmin=correction', this.form).then( (response) => {
                    this.showMassage(response.data);
                    //EventBus.$emit('data-changed');
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

            totalTransQtyWeightCalc(){
                var _this = this;
                var total_trans_qty = 0;
                var total_trans_weight = 0;

                var total_remain_qty = 0;
                var total_receive_qty = 0;

                var total_waste_qty = 0;
                var total_waste_weight = 0;
                var total_weight = 0;

                var remain_qty = 0;
                var receive_qty = 0;
                var overhead_price =0;
                var totalPrice = 0;
                var totalAmount = 0;

                var waste_qty = 0;
                var waste_weight = 0;
                var processLossWeight = 0;
                var weightAfterProcessLoss =0;
                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    total_trans_weight = Number(details[i].glaze_weight) * Number(details[i].trans_kiln_qty);
                    _this.form.details[i].trans_kiln_weight = total_trans_weight;

                    waste_qty = Number(details[i].waste_qty);
                    remain_qty = Number(details[i].current_qty) + Number(details[i].receive_qty) - Number(details[i].trans_kiln_qty) - waste_qty;
                    _this.form.details[i].remain_qty = remain_qty;


                    // _this.form.details[i].waste_qty = waste_qty;

                    receive_qty = Number(details[i].receive_qty);

                    waste_weight = Number(details[i].glaze_weight) * Number(details[i].waste_qty);
                    _this.form.details[i].waste_weight = waste_weight;

                     // Over head calculation
                    overhead_price = Number(details[i].glaze_weight) * Number(details[i].trans_kiln_qty)*_this.pre_mth_ovr_price;
                    _this.form.details[i].overhead_price = overhead_price;

                    total_trans_qty = Number(details[i].trans_kiln_qty) + total_trans_qty;
                    total_weight = total_trans_weight + total_weight;

                    total_receive_qty = receive_qty + total_receive_qty;
                    total_remain_qty = remain_qty + total_remain_qty;

                    total_waste_qty = waste_qty + total_waste_qty;
                    total_waste_weight = waste_weight + total_waste_weight;

                    totalPrice = Number(details[i].trans_kiln_qty) * Number(details[i].unit_price) + overhead_price;
                    _this.form.details[i].total_price = totalPrice;

                    totalAmount = Number(totalPrice) + totalAmount;
                }

                _this.form.total_trans_to_kiln_qty = total_trans_qty;
                _this.form.total_trans_to_kiln_weight = total_weight;

                _this.form.total_receive_qty = total_receive_qty;
                _this.form.total_remain_qty = total_remain_qty;

                _this.form.total_waste_qty = total_waste_qty;
                _this.form.total_waste_weight = total_waste_weight;

                 processLossWeight =  Number(_this.form.process_loss_percentage)/100 * total_waste_weight;
                _this.form.process_loss_weight = processLossWeight.toFixed( 2 );
                 weightAfterProcessLoss = total_waste_weight - processLossWeight;
                _this.form.weight_after_process_loss = weightAfterProcessLoss.toFixed( 2 );
                _this.form.total_amount = totalAmount;
            },

            duplicateCheck(selectedValue){

                var no_index = this.form.details.length;
                for(let i=0; i<no_index; i++){
                    if(this.form.details[i].product_id == selectedValue){
                        alert("Duplicate Found");
                        return true;
                    }
                }
                return false;
            },
        },

        mounted(){

            $('form').on('focus', 'input[type=number]', function (e) {
                $(this).on('mousewheel.disableScroll', function (e) {
                    e.preventDefault()
                })
            })
            $('form').on('blur', 'input[type=number]', function (e) {
                $(this).off('mousewheel.disableScroll')
            })

            var _this = this;

            $('#editComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                var selectGlazeWeight = $('option:selected', $(e.target)).attr('data-glaze-weight');
                var openingQty = $('option:selected', $(e.target)).attr('glaze-opening-qty');
                var receiveQty = $('option:selected', $(e.target)).attr('glaze-receive-qty');
                var buyingPrice = $('option:selected', $(e.target)).attr('buying-price');

                _this.form.details[dataIndex].glaze_weight  = selectGlazeWeight ? selectGlazeWeight : 0;
                _this.form.details[dataIndex].current_qty  = openingQty ? openingQty : 0;
                _this.form.details[dataIndex].receive_qty  = receiveQty ? receiveQty : 0;
                _this.form.details[dataIndex].unit_price = buyingPrice ? buyingPrice : 0;

                _this.totalTransQtyWeightCalc();

                var isDuplicated = _this.duplicateCheck(selectedItem.val());
                if (isDuplicated) {
                    _this.form.details[dataIndex].product_id = '';
                    setTimeout(function () {
                        var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                        jQuery(document).ready(function() {Select2.init()});
                    },250);
                } else {
                    _this.form.details[dataIndex].product_id = selectedItem.val();
                }
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
                        _this.form.glaze_date = '';

                    }else {
                        _this.form.glaze_date = moment(ev.date).format('DD/MM/YYYY');

                    }

                });
            });

        },

        created(){
            var _this = this;
            _this.edit(_this.editId);
            _this.totalTransQtyWeightCalc();
        }


    }
</script>
