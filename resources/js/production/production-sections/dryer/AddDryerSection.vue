<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >

            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="forming_no" class="col-lg-2 col-form-label">Dryer No <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="forming_no" v-model="form.dryer_no" class="form-control form-control-sm m-input" placeholder="Enter Dryer No" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('dryer_no'))">{{ (errors.hasOwnProperty('dryer_no')) ? errors.dryer_no[0] :'' }}</div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Dryer Date <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.dryer_date" data-dateField="dryer_date" readonly placeholder="Select date from picker" id="m_datepicker_2" autocomplete="off"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('dryer_date'))">{{ (errors.hasOwnProperty('dryer_date')) ? errors.dryer_date[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.narration"  id="narration" rows="2"></textarea>
                    </div>
                    <label class="col-lg-2 col-form-label" for="overhead_kg_price">Previous month overhead price (Kg)</label>
                    <div class="col-lg-4">
                        <input type="text" id="overhead_kg_price" v-model="overhead_kg_price" class="form-control form-control-sm m-input" placeholder="ex: 1 kg" readonly>
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
                                    <th width="10%">Product <span class="requiredField">*</span></th>
                                    <th>Remarks</th>
                                    <th width="8%">Weight <span class="requiredField">*</span></th>
                                    <th style="width: 160px;">Opening / Current Qty <span class="requiredField">*</span></th>
                                    <th>Rec.F Shapping Qty <span class="requiredField">*</span></th>
                                    <th>Trans to Glaze Qty <span class="requiredField">*</span></th>
                                    <th>Trans Weight <span class="requiredField">*</span></th>
                                    <th>Waste Qty <span class="requiredField">*</span></th>
                                    <th>Waste Weight <span class="requiredField">*</span></th>
                                    <th style="width: 160px;">Balance <span class="requiredField">*</span></th>
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
                                    <td class="tdWidth">
                                        <select class="form-control select2 select-product" v-bind:data-index="index" v-model="details.product_id" style="width:100%">
                                            <option v-for="(value,index) in product_lists" 
                                                :value="value.id" 
                                                :data-dryer-weight="value.dryer_weight" 
                                                :dryer-opening-qty="value.dryer_current_qty"
                                                :dryer-receive-qty="value.dryer_receive_qty"
                                            > {{value.product_name}} ({{value.product_category_code}})</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{ errors['details.'+index+'.product_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm m-input" v-model="details.remarks" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.remarks']">{{ errors['details.'+index+'.remarks'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input" v-model="details.dryer_product_weight" @input="totalWeightCalc()" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.dryer_product_weight']">{{ errors['details.'+index+'.dryer_product_weight'][0] }}</div>
                                    </td>
                                    <td style="width: 160px;">
                                        <input style="width: 160px;" type="number" step="any" class="form-control form-control-sm m-input" v-model="details.current_qty"  @input="totalWeightCalc()" >
                                        <div class="requiredField" v-if="errors['details.'+index+'.current_qty']">{{ errors['details.'+index+'.current_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input" v-model="details.receive_qty" placeholder="" @input="totalWeightCalc()" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.receive_qty']">{{ errors['details.'+index+'.receive_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input" v-model="details.trans_to_glaze_qty" @input="totalWeightCalc()" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.trans_to_glaze_qty']">{{ errors['details.'+index+'.trans_to_glaze_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm m-input" v-model="details.trans_to_glaze_weight" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.trans_to_glaze_weight']">{{ errors['details.'+index+'.trans_to_glaze_weight'][0] }}</div>
                                    </td>

                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input" v-model="details.glaze_westage_qty" @input="totalWeightCalc()" placeholder="" >
                                        <div class="requiredField" v-if="errors['details.'+index+'.glaze_westage_qty']">{{ errors['details.'+index+'.glaze_westage_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input" v-model="details.glaze_westage_weight" placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.glaze_westage_weight']">{{ errors['details.'+index+'.glaze_westage_weight'][0] }}</div>
                                    </td>
                                    <td style="width: 160px;">
                                        <input style="width: 160px;" type="number" step="any" class="form-control form-control-sm m-input" v-model="details.remain_qty" @input="totalWeightCalc()" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.remain_qty']">{{ errors['details.'+index+'.remain_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.unit_price" class="form-control form-control-sm m-input number" @input="totalWeightCalc()">
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
                                    <td scope="row" class="text-center">
                                        <a  @click="removeItem(index)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Total</td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_receive_qty" class="form-control form-control-sm m-input" placeholder="Recive Qty" readonly>
                                    </td>
                                    <td>
                                        <input type="text" v-model="form.total_trans_to_glaze_qty" class="form-control form-control-sm m-input" placeholder="Total Glaze Qty" readonly>
                                    </td>
                                    <td>
                                        <input type="text" v-model="form.total_trans_to_glaze_weight" class="form-control form-control-sm m-input" placeholder="Total Weight" readonly>
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
                                        <input type="number" step="any" v-model="form.total_amount" class="form-control form-control-sm m-input" placeholder="total amount" readonly>
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
                            <button type="submit" v-if="permissions.indexOf('dryer-section.approve') != -1" class="btn btn-sm btn-primary" @click.prevent="store(1)" ><i class="la la-check"></i> <span>Approve</span></button>
                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="store(0)" ><i class="la la-check"></i> <span>Save and Go List</span></button>
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

        props:['permissions','product_lists'],

        data:function(){
            return{

                data_list:false,
                add_form:true,
                edit_form:false,
                view_form:false,

                overhead_kg_price: 0,

                form:{
                    dryer_no: '',
                    dryer_date: '',
                    narration: '',

                    total_receive_qty: '',
                    total_remain_qty: '',

                    total_trans_to_glaze_qty: '',
                    total_trans_to_glaze_weight: '',

                    total_waste_qty: 0,
                    total_waste_weight: '',
                    total_amount: '',

                    status: '',
                    approve_status: '',
                    details: [
                        {
                            product_id: '',
                            remarks: '',

                            dryer_product_weight: '',
                            current_qty: '',
                            receive_qty: '',
                            remain_qty: '',


                            trans_to_glaze_qty: '',
                            trans_to_glaze_weight: '',

                            glaze_westage_qty: 0,
                            glaze_westage_weight: '',
                            unit_price: '',
                            overhead_price: '',
                            total_price: '',
                        }
                    ],

                    last_month_overhead_amt: '',
                    last_month_production_kg: '',
                    overhead_per_kg: '',
                },
                errors: {},
            };
        },

        methods:{

            getOverheadCosting(){
                var _this = this;
                _this.$loading(true);
                axios.get(base_url+"raw-material-ratio-setup").then((response) => {
                    _this.$loading(false);
                    if (response.data.recyle_chip) {
                        _this.overhead_kg_price = response.data.recyle_chip.overhead_per_kg;

                        _this.form.last_month_overhead_amt = response.data.recyle_chip.last_month_overhead_amt;
                        _this.form.last_month_production_kg = response.data.recyle_chip.last_month_production_kg;
                        _this.form.overhead_per_kg = response.data.recyle_chip.overhead_per_kg;                        
                    }
                });
            },

            addNewItem(){
                var _this = this;
                _this.form.details.push({
                    product_id: '',
                    remarks: '',

                    dryer_product_weight: '',
                    current_qty: '',
                    receive_qty: '',
                    remain_qty: '',


                    trans_to_glaze_qty: '',
                    trans_to_glaze_weight: '',

                    glaze_westage_qty: 0,
                    glaze_westage_weight: '',
                    unit_price: '',
                    overhead_price: '',
                    total_price: '',
                });
                _this.initSelect2();
            },

            removeItem(index){
                var _this = this;
                if(_this.form.details.length > 1){
                    _this.form.details.splice(index, 1);
                    _this.initSelect2();

                }
                _this.totalWeightCalc();
            },

            store:function(app){
                var _this = this;
                _this.form.approve_status = app;
                axios.post(base_url+'dryer-section/store', this.form).then( (response) => {
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

            totalWeightCalc(){
                var _this = this;
                var total_trans_weight = 0;
                //var total_qty = 0;

                var total_trans_qty = 0 ;
                var total_weight = 0;

                var total_remain_qty = 0;
                var total_receive_qty = 0;

                var total_waste_qty = 0;
                var total_waste_weight = 0;

                var remain_qty = 0;
                var receive_qty = 0;

                var waste_qty = 0;
                var waste_weight = 0;

                var overhead_price =0; 
                var total_amount =0;

                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    total_trans_weight = Number(details[i].dryer_product_weight) * Number(details[i].trans_to_glaze_qty);
                    _this.form.details[i].trans_to_glaze_weight = total_trans_weight.toFixed(2);

                    waste_qty = Number(details[i].glaze_westage_qty);
                    remain_qty = Number(details[i].current_qty) + Number(details[i].receive_qty) - Number(details[i].trans_to_glaze_qty) - waste_qty;
                    _this.form.details[i].remain_qty = remain_qty;


                    receive_qty = Number(details[i].receive_qty);

                    waste_weight = Number(details[i].dryer_product_weight) * Number(details[i].glaze_westage_qty);
                    _this.form.details[i].glaze_westage_weight = waste_weight;

                    // Over head calculation 
                    overhead_price = Number(details[i].dryer_product_weight) * Number(details[i].trans_to_glaze_qty) * _this.overhead_kg_price;

                    _this.form.details[i].overhead_price = overhead_price.toFixed(2);

                    total_trans_qty = Number(details[i].trans_to_glaze_qty) + total_trans_qty;
                    total_weight = total_trans_weight + total_weight;

                    total_receive_qty = receive_qty + total_receive_qty;
                    total_remain_qty = remain_qty + total_remain_qty;

                    total_waste_qty = waste_qty + total_waste_qty;
                    total_waste_weight = waste_weight + total_waste_weight;

                    let total_price = Number(details[i].trans_to_glaze_qty) * details[i].unit_price + overhead_price;
                    details[i].total_price = total_price.toFixed( 2 ); 
                    total_amount = Number(total_price) + total_amount;  

                }

                _this.form.total_trans_to_glaze_qty = total_trans_qty;
                _this.form.total_trans_to_glaze_weight = total_weight.toFixed(2);

                _this.form.total_receive_qty = total_receive_qty;
                _this.form.total_remain_qty = total_remain_qty;


                _this.form.total_waste_qty = total_waste_qty;
                _this.form.total_waste_weight = total_waste_weight;

                _this.form.total_amount = total_amount.toFixed( 2 );                

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

            dryerNoGenerate(){
                var _this = this;
                axios.get(base_url+'dryer-no')
                    .then( (response) => {
                        _this.form.dryer_no = response.data;
                    });
            },

            getShappingLastPrice(product_id,dataIndex) {
                var _this = this;
                axios.get(base_url + 'shapping-section?token_last_price=yes&product_id=' + product_id)
                    .then((response) => {

                        _this.form.details[dataIndex].unit_price = response.data.unit_price;
                        _this.totalWeightCalc();
                    });
            },

            initSelect2() {
                setTimeout(function () {
                    var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                    jQuery(document).ready(function() {Select2.init()});
                }, 250);
            },
        },

        mounted(){
            var _this = this;
            _this.initSelect2();

            $('form').on('focus', 'input[type=number]', function (e) {
                $(this).on('mousewheel.disableScroll', function (e) {
                    e.preventDefault()
                })
            })
            $('form').on('blur', 'input[type=number]', function (e) {
                $(this).off('mousewheel.disableScroll')
            })            

            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                   var selectDryerWeight = $('option:selected', $(e.target)).attr('data-dryer-weight');
                   var openingQty = $('option:selected', $(e.target)).attr('dryer-opening-qty');
                   var receiveQty = $('option:selected', $(e.target)).attr('dryer-receive-qty');

                _this.form.details[dataIndex].dryer_product_weight  = selectDryerWeight ? selectDryerWeight : 0;
                _this.form.details[dataIndex].current_qty  = openingQty ? openingQty : 0;
                _this.form.details[dataIndex].receive_qty  = receiveQty ? receiveQty : 0;

                var isDuplicated = _this.duplicateCheck(selectedItem.val());
                if (isDuplicated) {
                    _this.form.details[dataIndex].product_id = '';
                    _this.initSelect2();                    
                } else {
                    _this.form.details[dataIndex].product_id = selectedItem.val();

                    _this.getShappingLastPrice( selectedItem.val(), dataIndex);
                }
            });


            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.dryer_date = '';

                    }else {
                        _this.form.dryer_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created(){
            var _this = this;
            
            _this.totalWeightCalc();
            _this.dryerNoGenerate();
            setTimeout(function () {
                _this.getOverheadCosting();
            }, 150);            
}
    }
</script>
