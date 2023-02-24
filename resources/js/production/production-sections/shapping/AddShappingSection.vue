<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >

            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="shapping_no" class="col-lg-2 col-form-label">Shaping No <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="shapping_no" v-model="form.shapping_no" class="form-control form-control-sm m-input" placeholder="Enter Shapping No" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('shapping_no'))">{{ (errors.hasOwnProperty('shapping_no')) ? errors.shapping_no[0] :'' }}</div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Shapping Date <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.shapping_date" data-dateField="shapping_date" readonly placeholder="Select date from picker" id="m_datepicker_2" autocomplete="off"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('shapping_date'))">{{ (errors.hasOwnProperty('shapping_date')) ? errors.shapping_date[0] :'' }}</div>
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
                                    <th>Product <span class="requiredField">*</span></th>
                                    <th>Remarks</th>
                                    <th>Weight <span class="requiredField">*</span></th>
                                    <th style="width: 160px;">Opening / Current Qty <span class="requiredField">*</span></th>
                                    <th>Rec.F Forming Qty <span class="requiredField">*</span></th>
                                    <th>Trans to Dry Qty <span class="requiredField">*</span></th>
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
                                                :data-shapping-weight="value.shapping_weight" 
                                                :shapping-opening-qty="value.shapping_current_qty"
                                                :shapping-rececive-qty="value.shapping_rececive_qty"
                                            > {{value.product_name}} ({{value.product_category_code}})</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{ errors['details.'+index+'.product_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm m-input" v-model="details.remarks" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.remarks']">{{ errors['details.'+index+'.remarks'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input" v-model="details.shapping_product_weight" @input="totalTransQtyWeightCalc()" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.shapping_product_weight']">{{ errors['details.'+index+'.shapping_product_weight'][0] }}</div>
                                    </td>
                                    <td style="width: 160px;">
                                        <input style="width: 160px;" type="number" step="any" class="form-control form-control-sm m-input" v-model="details.current_qty" @input="totalTransQtyWeightCalc()" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.current_qty']">{{ errors['details.'+index+'.current_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input" v-model="details.receive_qty" @input="totalTransQtyWeightCalc()" placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.receive_qty']">{{ errors['details.'+index+'.receive_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input" v-model="details.trans_to_dry_qty" @input="totalTransQtyWeightCalc()" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.trans_to_dry_qty']">{{ errors['details.'+index+'.trans_to_dry_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input" v-model="details.trans_to_dry_weight" @input="totalTransQtyWeightCalc()" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.trans_to_dry_weight']">{{ errors['details.'+index+'.trans_to_dry_weight'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input" v-model="details.dry_westage_qty" @input="totalTransQtyWeightCalc()" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.dry_westage_qty']">{{ errors['details.'+index+'.dry_westage_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input" v-model="details.dry_westage_weight" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.dry_westage_weight']">{{ errors['details.'+index+'.dry_westage_weight'][0] }}</div>
                                    </td>
                                    <td style="width: 160px;">
                                        <input style="width: 160px;" type="number" step="any" class="form-control form-control-sm m-input" v-model="details.remain_qty" @input="totalTransQtyWeightCalc()" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.remain_qty']">{{ errors['details.'+index+'.remain_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.unit_price" class="form-control form-control-sm m-input number" @input="totalTransQtyWeightCalc()">
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
                                        <a  @click="removeItem(index)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Total</td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_receive_qty" class="form-control form-control-sm m-input" placeholder="Recive Qty" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_trans_to_dryer_qty" class="form-control form-control-sm m-input" placeholder="Final Weight" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_trans_to_dryer_weight" class="form-control form-control-sm m-input" placeholder="Final Weight" readonly>
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
                                        <input type="text" v-model="form.weight_after_process_loss" class="form-control form-control-sm m-input" placeholder="Total Waste">
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
                            <p v-if="permissions.indexOf('shapping-section.approve') != -1" class="bg-info text-white"> On Approve : Total Wastage Weight will be added to Recycle Chip! </p>
                        </div>                         
                        <div class="col-lg-6 m--align-right">
                            <button type="submit" v-if="permissions.indexOf('shapping-section.approve') != -1" class="btn btn-sm btn-primary" @click.prevent="store(1)" ><i class="la la-check"></i> <span>Approve</span></button>
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
                    shapping_no: '',
                    shapping_date: '',
                    narration: '',
                    total_trans_to_dryer_qty: '',
                    total_trans_to_dryer_weight: '',
                    total_receive_qty: '',
                    total_remain_qty: '',
                    total_waste_qty: '',
                    total_waste_weight: '',
                    process_loss_percentage: '',
                    process_loss_weight: '',
                    weight_after_process_loss: '',
                    total_amount: '',
                    status: '',
                    approve_status: '',

                    details: [
                        {
                            product_id: '',
                            remarks: '',
                            shapping_product_weight: '',
                            current_qty: '',
                            receive_qty: '',
                            trans_to_dry_qty: '',
                            trans_to_dry_weight: '',
                            remain_qty: '',
                            dry_westage_qty: 0,
                            dry_westage_weight: '',
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
                    shapping_product_weight: '',
                    current_qty: '',
                    receive_qty: '',
                    trans_to_dry_qty: '',
                    trans_to_dry_weight: '',
                    remain_qty: '',
                    dry_westage_qty: 0,
                    dry_westage_weight: '',
                    overhead_price: '',
                    unit_price: '',
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
                _this.totalTransQtyWeightCalc();
            },

            store:function(app){
                var _this = this;
                _this.form.approve_status = app;

                axios.post(base_url+'shapping-section/store', this.form)
                    .then( (response) => {
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

                var waste_qty = 0;
                var waste_weight = 0;
                var processLossWeight = 0;
                var weightAfterProcessLoss =0;
                var overhead_price =0;
                var total_amount =0;

                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    total_trans_weight = Number(details[i].shapping_product_weight) * Number(details[i].trans_to_dry_qty);
                    _this.form.details[i].trans_to_dry_weight = total_trans_weight.toFixed(2);

                    waste_qty = Number(details[i].dry_westage_qty);
                    remain_qty = Number(details[i].current_qty) + Number(details[i].receive_qty) - Number(details[i].trans_to_dry_qty) - waste_qty;
                    _this.form.details[i].remain_qty = remain_qty;
                    
                    receive_qty = Number(details[i].receive_qty);

                    waste_weight = Number(details[i].shapping_product_weight) * Number(details[i].dry_westage_qty);
                    _this.form.details[i].dry_westage_weight = waste_weight;

                    // Over head calculation 
                    overhead_price = Number(details[i].shapping_product_weight) * Number(details[i].trans_to_dry_qty) *_this.overhead_kg_price;

                    _this.form.details[i].overhead_price = overhead_price.toFixed(2);

                    total_trans_qty = Number(details[i].trans_to_dry_qty) + total_trans_qty;
                    total_weight = total_trans_weight + total_weight;

                    total_receive_qty = receive_qty + total_receive_qty;
                    total_remain_qty = remain_qty + total_remain_qty;

                    total_waste_qty = waste_qty + total_waste_qty;
                    total_waste_weight = waste_weight + total_waste_weight;

                    let total_price = Number(details[i].trans_to_dry_qty) * details[i].unit_price + overhead_price;
                    details[i].total_price = total_price.toFixed( 2 ); 
                    total_amount = Number(total_price) + total_amount;                     
                }

                _this.form.total_trans_to_dryer_qty = total_trans_qty;
                _this.form.total_trans_to_dryer_weight = total_weight.toFixed(2);

                _this.form.total_receive_qty = total_receive_qty;
                _this.form.total_remain_qty = total_remain_qty;

                _this.form.total_waste_qty = total_waste_qty;
                _this.form.total_waste_weight = total_waste_weight;

                processLossWeight =  Number(_this.form.process_loss_percentage)/100 * total_waste_weight;
                _this.form.process_loss_weight = processLossWeight.toFixed( 2 );
                weightAfterProcessLoss = total_waste_weight - processLossWeight;
                _this.form.weight_after_process_loss = weightAfterProcessLoss.toFixed( 2 );

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

            shappingNoGenerate(){
                var _this = this;
                axios.get(base_url+'shapping-no')
                    .then( (response) => {
                        _this.form.shapping_no = response.data;
                    });
            },

            getFormingLastPrice(product_id,dataIndex) {
                var _this = this;
                axios.get(base_url + 'forming-section?token_last_price=yes&product_id=' + product_id)
                    .then((response) => {

                        _this.form.details[dataIndex].unit_price = response.data.unit_price;
                        _this.totalTransQtyWeightCalc();
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

            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                    var selectShappingWeight = $('option:selected', $(e.target)).attr('data-shapping-weight');
                    var openingQty = $('option:selected', $(e.target)).attr('shapping-opening-qty');
                    var receiveQty = $('option:selected', $(e.target)).attr('shapping-rececive-qty');

                _this.form.details[dataIndex].shapping_product_weight  = selectShappingWeight ? selectShappingWeight : 0;
                _this.form.details[dataIndex].current_qty  = openingQty ? openingQty : 0;
                _this.form.details[dataIndex].receive_qty  = receiveQty ? receiveQty : 0;

                var isDuplicated = _this.duplicateCheck(selectedItem.val());
                if (isDuplicated) {
                    _this.form.details[dataIndex].product_id = '';
                    _this.initSelect2();                    
                } else {
                    _this.form.details[dataIndex].product_id = selectedItem.val();

                    _this.getFormingLastPrice( selectedItem.val(), dataIndex);
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
                        _this.form.shapping_date = '';

                    }else {
                        _this.form.shapping_date = moment(ev.date).format('DD/MM/YYYY');

                    }
                });
            });
        },

        created(){
            var _this = this;

            _this.totalTransQtyWeightCalc();
            _this.shappingNoGenerate();
            setTimeout(function () {
                _this.getOverheadCosting();
            }, 150);            
        }
    }
</script>
