<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="forming_no" class="col-lg-2 col-form-label">Forming No <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="forming_no" v-model="form.forming_no" class="form-control form-control-sm m-input" placeholder="Enter forming no" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('forming_no'))">{{ (errors.hasOwnProperty('forming_no')) ? errors.forming_no[0] :'' }}</div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Forming Date <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.forming_date" data-dateField="forming_date" readonly placeholder="Select date from picker" id="m_datepicker_2" autocomplete="off"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('forming_date'))">{{ (errors.hasOwnProperty('forming_date')) ? errors.forming_date[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="forming_type">Forming Type</label>
                    <div class="col-lg-4">
                        <select class="form-control select2" v-model="form.forming_type" data-selectField="forming_type" id="forming_type">
                            <option> Local</option>
                            <option> Govt</option>
                        </select>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.narration"  id="narration" rows="2"></textarea>
                    </div>
                </div>
                <div class="form-group m-form__group row">
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
                                    <th class="width-210">Product<span class="requiredField">*</span></th>
                                    <th>Remarks</th>
                                    <th>Roll Weight</th>
                                    <th>Opening / Current qty</th>
                                    <th>Roll Production Qty<span class="requiredField">*</span></th>
                                    <th>Trans to shapping<span class="requiredField">*</span></th>
                                    <th>Trans Weight<span class="requiredField">*</span></th>
                                    <th>Waste Qty<span class="requiredField">*</span></th>
                                    <th>Waste Weight<span class="requiredField">*</span></th>
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
                                        <select class="form-control select2 select-product width-210" v-bind:data-index="index" v-model="details.product_id" style="width:100%">
                                            <option v-for="(value,index) in product_lists" 
                                                :value="value.id" 
                                                :data-roll-weight="value.forming_weight"
                                                :forming-opening-qty="value.forming_current_qty"
                                            > {{value.product_name}} ({{value.product_category_code}})</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{ errors['details.'+index+'.product_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm m-input width-80" v-model="details.remarks" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.remarks']">{{ errors['details.'+index+'.remarks'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.roll_weight" @input="calcRollWeightWiseUnitPrice(index)" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.roll_weight']">{{ errors['details.'+index+'.roll_weight'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.current_qty" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.current_qty']">{{ errors['details.'+index+'.current_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.receive_qty" @input="totalTransQtyWeightCalc()" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.receive_qty']">{{ errors['details.'+index+'.receive_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.trans_to_shapping_qty" @input="totalTransQtyWeightCalc()" id=""  placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.trans_to_shapping_qty']">{{ errors['details.'+index+'.trans_to_shapping_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.trans_to_shapping_weight" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.trans_to_shapping_weight']">{{ errors['details.'+index+'.trans_to_shapping_weight'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.shapping_westage_qty" placeholder="" @input="totalTransQtyWeightCalc()">
                                        <div class="requiredField" v-if="errors['details.'+index+'.shapping_westage_qty']">{{ errors['details.'+index+'.shapping_westage_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.shapping_westage_weight" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.shapping_westage_weight']">{{ errors['details.'+index+'.shapping_westage_weight'][0] }}</div>
                                    </td>
                                    <td class="width-160">
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-160" v-model="details.forming_remain_qty" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.forming_remain_qty']">{{ errors['details.'+index+'.forming_remain_qty'][0] }}</div>
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
                                        <a  @click="removeItem(index,details.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right">Total</td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_receive_qty" class="form-control form-control-sm m-input" placeholder="Qty" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_trans_to_shapping_qty" class="form-control form-control-sm m-input" placeholder="Final Weight" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_trans_to_shap_weight" class="form-control form-control-sm m-input" placeholder="Final Weight" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_waste_qty" class="form-control form-control-sm m-input" placeholder="Waste Qty" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_waste_weight" class="form-control form-control-sm m-input" placeholder="Waste Weight" readonly>
                                    </td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_remain_qty" class="form-control form-control-sm m-input" placeholder="Balance Qty" readonly>
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_amount" class="form-control form-control-sm m-input" placeholder="total amount" readonly>
                                    </td>
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
                            <p v-if="permissions.indexOf('forming-section.approve') != -1" class="bg-info text-white"> On Approve : Total Wastage Weight will be added to Recycle Chip! </p>
                        </div>                        
                        <div class="col-lg-6 m--align-right">
                            <button type="submit" v-if="permissions.indexOf('forming-section.approve') != -1" class="btn btn-sm btn-primary" @click.prevent="update(form.id,1)" ><i class="la la-check"></i> <span>Approve</span></button>
                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="update(form.id,0)" ><i class="la la-check"></i> <span>Update and Go List</span></button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- @ View Latest Approved Massbody -->
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
                                                    <h4 class="print-head-title">Latest Massbody(Approved) Info - </h4>
                                                </div>

                                                <div class="m-invoice__items">
                                                    <div class="m-invoice__item ">
                                                        <span class="m-invoice__subtitle">Massbody No</span>
                                                        <span class="m-invoice__text">{{ massbody.massbody_no }}</span>
                                                    </div>
                                                    <div class="m-invoice__item text-center">
                                                        <span class="m-invoice__subtitle">Massbody Date</span>
                                                        <span class="m-invoice__text">{{ massbody.massbody_date }}</span>
                                                    </div>
                                                    <div class="m-invoice__item text-center">
                                                        <span class="m-invoice__subtitle">Requisition No</span>
                                                        <span class="m-invoice__text">{{ massbody.requisition.rm_requisition_no }}</span>
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
                                                        <td>{{ massbody.green_chip_name }}</td>
                                                        <td class="text-right">{{ massbody.green_chip_percentage }}</td>
                                                        <td class="text-right">{{ massbody.green_chip_weight }}</td>
                                                        <td>{{massbody.green_chip_unit_price}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ massbody.recycle_chip_name }}</td>
                                                        <td class="text-right">{{ massbody.recycle_chip_percentage }}</td>
                                                        <td class="text-right">{{ massbody.recycle_chip_weight }}</td>
                                                        <td>{{massbody.recycle_chip_unit_price}}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right m--font-danger" colspan="1">Total</td>
                                                        <td class="text-right m--font-danger">{{ massbody.total_percentage }}</td>
                                                        <td class="m--font-danger">{{ massbody.total_weight_qty }}</td>
                                                        <td>
                                                            {{ mb_unit_price }} Per Kg
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="description-section">
                                                <p>
                                                    <span style="font-weight: 600">Narration :</span>
                                                    <span>
                                                        {{ massbody.narration }}
                                                    </span>
                                                </p>
                                            </div>

                                            <div style="clear:both;width:100%;height:10px;"></div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>           
            <!-- // View Latest Approved Massbody -->            
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

                mb_unit_price: 0, //Massbody Actual Unit Price
                massbody:{
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

                overhead_kg_price : 0,

                form:{
                    forming_no: '',
                    forming_date: '',
                    forming_type: '',
                    narration: '',
                    total_receive_qty: '',
                    total_trans_to_shapping_qty: '',
                    total_trans_to_shap_weight: '',
                    total_remain_qty: '',
                    total_waste_qty: '',
                    total_waste_weight: '',
                    process_loss_percentage: '',
                    process_loss_weight: '',
                    weight_after_process_loss: '',

                    approve_status: '',
                    details: [
                        {
                            product_id: '',
                            remarks: '',
                            roll_weight: '',
                            current_qty: '',
                            trans_to_shapping_qty: '',
                            trans_to_shapping_weight: '',
                            forming_remain_qty: '',
                            shapping_westage_qty: 0,
                            shapping_westage_weight: '',
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
                    roll_weight: '',
                    current_qty: '',
                    trans_to_shapping_qty: '',
                    trans_to_shapping_weight: '',
                    forming_remain_qty: '',
                    shapping_westage_qty: 0,
                    shapping_westage_weight: '',
                    unit_price: '',
                    overhead_price: '',
                    total_price: '',                    
                });

                _this.initSelect2();
            },

            removeItem(index,deletedId){
                var _this = this;
                if(_this.form.details.length > 1){
                    _this.form.details.splice(index, 1);
                    if (deletedId) {
                     _this.form.deleteID.push(deletedId);
                     }
                    _this.totalTransQtyWeightCalc();
                }
            },

            edit(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url+'forming-section/'+id+'/edit')
                    
                    .then( (response) => {
                        _this.$loading(false);
                        _this.form  = response.data.data;
                        _this.initSelect2();

                        setTimeout(function () {
                            _this.getOverheadCosting();
                        }, 150);                        
                    });
            },

            update(id,app){
                var _this = this;
                _this.form.approve_status = app;
                _this.$loading(true);
                axios.put(base_url+'forming-section/'+id, this.form).then( (response) => {
                    _this.$loading(false);
                    this.showMassage(response.data);
                    EventBus.$emit('data-changed');
                })
                    .catch(error => {
                        _this.$loading(false);
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

                var total_waste_qty = 0;
                var total_waste_weight = 0;
                var total_weight = 0;

                var remain_qty = 0;
                var waste_qty = 0;
                var waste_weight = 0;
                var processLossWeight = 0;
                var weightAfterProcessLoss =0;
                var totalReceiveQty =0; 
                var overhead_price =0;

                var total_amount =0;

                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    total_trans_weight = Number(details[i].roll_weight) * Number(details[i].trans_to_shapping_qty);
                    _this.form.details[i].trans_to_shapping_weight = total_trans_weight.toFixed(2);

                    waste_qty = Number(details[i].shapping_westage_qty);
                    remain_qty = Number(details[i].current_qty) + Number(details[i].receive_qty) - Number(details[i].trans_to_shapping_qty) - waste_qty;
                    _this.form.details[i].forming_remain_qty = remain_qty;



                    waste_weight = Number(details[i].roll_weight) * Number(details[i].shapping_westage_qty);
                    _this.form.details[i].shapping_westage_weight = waste_weight;

                     // Over head calculation
                    overhead_price = Number(details[i].roll_weight) * Number(details[i].trans_to_shapping_qty)*_this.overhead_kg_price;

                    _this.form.details[i].overhead_price = overhead_price.toFixed(2);

                    total_trans_qty = Number(details[i].trans_to_shapping_qty) + total_trans_qty;
                    total_weight = total_trans_weight + total_weight;

                    total_remain_qty = remain_qty + total_remain_qty;

                    total_waste_qty = waste_qty + total_waste_qty;
                    total_waste_weight = waste_weight + total_waste_weight;
                    totalReceiveQty = Number(details[i].receive_qty) + totalReceiveQty;

                    let total_price = Number(details[i].trans_to_shapping_qty) * details[i].unit_price + overhead_price;
                    details[i].total_price = total_price.toFixed( 2 ); 
                    total_amount = Number(total_price) + total_amount;                      
                }

                _this.form.total_trans_to_shapping_qty = total_trans_qty;
                _this.form.total_trans_to_shap_weight = total_weight;

                _this.form.total_remain_qty = total_remain_qty;

                _this.form.total_waste_qty = total_waste_qty;
                _this.form.total_waste_weight = total_waste_weight;
                _this.form.total_receive_qty = totalReceiveQty;

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

            loadLatestMassbodyView() {
                var _this = this;
                axios.get(base_url+'massbody-section?get_latest_massbody=yes')
                    .then( (response) => {
                        _this.massbody  = response.data;
                        _this.calcMassbodyUnitPrice();
                    });
            },  

            calcMassbodyUnitPrice(){
                var _this = this;
                var mb_unit_price = Number(_this.massbody.green_chip_unit_price) + Number(_this.massbody.recycle_chip_unit_price);
                    mb_unit_price = (mb_unit_price/2).toFixed(2);
                _this.mb_unit_price = mb_unit_price; 
            },

            calcRollWeightWiseUnitPrice(index){
                var _this = this;

                let unit_price = _this.form.details[index].roll_weight * _this.mb_unit_price;
                _this.form.details[index].unit_price = unit_price.toFixed( 2 ); 

                _this.totalTransQtyWeightCalc();
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

            $('form').on('focus', 'input[type=number]', function (e) {
                $(this).on('mousewheel.disableScroll', function (e) {
                    e.preventDefault()
                })
            })
            $('form').on('blur', 'input[type=number]', function (e) {
                $(this).off('mousewheel.disableScroll')
            })

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if( selectField == 'forming_type' ){
                    _this.form.forming_type= selectedItem.val();
                }
            });

            $('#editComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                var selectrollWeight = $('option:selected', $(e.target)).attr('data-roll-weight');
                var openingQty = $('option:selected', $(e.target)).attr('forming-opening-qty');

                _this.form.details[dataIndex].roll_weight  = selectrollWeight ? selectrollWeight : 1;
                _this.form.details[dataIndex].current_qty  = openingQty ? openingQty : 0;

                var isDuplicated = _this.duplicateCheck(selectedItem.val());
                if (isDuplicated) {
                    _this.form.details[dataIndex].product_id = '';
                    _this.initSelect2();                    
                } else {
                    _this.form.details[dataIndex].product_id = selectedItem.val();
                    _this.calcRollWeightWiseUnitPrice(dataIndex);
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
                        _this.form.forming_date = '';
                    }else {
                        _this.form.forming_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created(){
            var _this = this;
            _this.edit(_this.editId);

            _this.totalTransQtyWeightCalc();
            _this.loadLatestMassbodyView();
        }
    }
</script>
