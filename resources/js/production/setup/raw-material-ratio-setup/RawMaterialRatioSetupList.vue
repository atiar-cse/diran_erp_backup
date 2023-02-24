<template>
    <div>
        <form method="POST" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >
        <div class="m-portlet__body">
            <!--begin: Datatable -->
            <div class="form-group m-form__group row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-sm m-table table-bordered">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Product Name <span class="requiredField">*</span></th>
                                <th>1000 KG Ratio <span class="requiredField">*</span></th>
                                <th>1 KG Ratio <span class="requiredField">*</span></th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody>
                            <tr v-for="(details, index) in form.details">
                                <td>
                                    <a href="javascript:void(0)"  @click="addNewItem" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Add More">
                                        <i class="la la-plus"></i>
                                    </a>
                                </td>
                                <td>
                                    <select class="form-control select2 select-product" v-bind:data-index="index" v-model="details.product_id" style="width:100%;">
                                        <option v-for="(value,index) in product_list" :value="value.id"> {{value.product_name}} ({{value.category.product_category_code}}) </option>
                                    </select>
                                    <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{ errors['details.'+index+'.product_id'][0] }}</div>
                                </td>
                                <td>
                                    <input type="number" id="1000kg"  class="form-control form-control-sm m-input" v-model="details.thousand_kgs_weight"  @keyup="totalKg()" placeholder="Enter 1000 kg ratio">
                                    <div class="requiredField" v-if="errors['details.'+index+'.thousand_kgs_weight']">{{ errors['details.'+index+'.thousand_kgs_weight'][0] }}</div>
                                </td>
                                <td>
                                    <input type="text" id="1kg" readonly class="form-control form-control-sm m-input" v-model="details.one_kg_weight = details.thousand_kgs_weight/1000" placeholder="Enter 1 kg ratio">
                                </td>
                                <td>
                                    <a  @click="removeItem(index)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                </td>                                
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td>
                                    <input type="number" class="form-control form-control-sm m-input"  v-model="form.total_thousand_kgs" placeholder="1000" readonly>
                                </td>
                                <td>
                                    <input type="number" class="form-control form-control-sm m-input"  v-model="form.total_one_kg" placeholder="1" readonly>
                                </td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <br>
            <hr>
            <div class="form-group m-form__group row">
                <div class="m-section__content col-md-8">
                    <h4>Recycle Chip (Wastage)</h4>
                    <div class="table-responsive">
                        <table id="recyle_chip" class="table table-sm m-table table-bordered borderless">
                            <thead class="thead-inverse" >
                                <tr>
                                    <th>Current/Open Weight (kg)<span class="requiredField">*</span></th>
                                    <th>Unit Price</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="form.recycle_chip_current_weight" @input="calcRecycleChip()" class="form-control form-control-sm m-input">
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('recycle_chip_current_weight'))">{{ (errors.hasOwnProperty('recycle_chip_current_weight')) ? errors.recycle_chip_current_weight[0] :'' }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="form.recycle_chip_unit_price" @input="calcRecycleChip()" class="form-control form-control-sm m-input">
                                    </td>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="form.recycle_chip_total_amt" class="form-control form-control-sm m-input" readonly>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <br><br>
                </div>
            </div> 


            <hr>
            <div class="form-group m-form__group row">
                <div class="m-section__content col-md-4">
                    <h4>Glaze Material Pricing</h4>
                    <div class="table-responsive">
                        <table id="glaze_price" class="table table-sm m-table table-bordered borderless">
                            <thead class="thead-inverse" >
                                <tr>
                                    <th>Glaze Material Price Per Kg<span class="requiredField">*</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <input type="number" step="any" min="0" v-model="form.glaze_material_price_per_kg"  class="form-control form-control-sm m-input">
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('glaze_material_price_per_kg'))">{{ (errors.hasOwnProperty('glaze_material_price_per_kg')) ? errors.glaze_material_price_per_kg[0] :'' }}</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <br><br>
                </div>
            </div> 

            <hr>
            <div class="form-group m-form__group row">
                <div class="m-section__content col-md-9">
                    <h4>Overhead Costing</h4>
                    <div class="table-responsive">
                        <table id="recyle_chip" class="table table-sm m-table table-bordered borderless">
                            <thead class="thead-inverse" >
                            <tr>
                                <th>Last Month Total Factory Expenses <span class="requiredField">*</span></th>
                                <th>Total Finished Production Weight in KG <span class="requiredField">*</span></th>
                                <th>Unit Price per KG</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <input type="number" step="any" min="0" v-model="form.last_month_overhead_amt" class="form-control form-control-sm m-input" @input="calcOverheadPrice()">
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('last_month_overhead_amt'))">{{ (errors.hasOwnProperty('last_month_overhead_amt')) ? errors.last_month_overhead_amt[0] :'' }}</div>
                                </td>
                                <td>
                                    <input type="number" step="any" min="0" v-model="form.last_month_production_kg" class="form-control form-control-sm m-input" @input="calcOverheadPrice()">
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('last_month_production_kg'))">{{ (errors.hasOwnProperty('last_month_production_kg')) ? errors.last_month_production_kg[0] :'' }}</div>
                                </td>
                                <td class="text-right">
                                    {{form.overhead_per_kg}}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <h4>Previous Month : Factory Expenses - COA Subcode Head (2107)</h4>
                    <p> <strong>From</strong> {{ start_date }} <strong>To</strong> {{ end_date }} </p>
                    <p>
                        <strong>Total Debit : </strong> {{ overhead_info.total_dr }} <br>
                        <strong>Total Credit : </strong> {{ overhead_info.total_cr }} <br>
                        <strong>Total Expense : </strong> {{ overhead_info.total_dr - overhead_info.total_cr }} 
                    </p>
                    <br><br>
                </div>
            </div> 

            <hr>
            <div class="form-group m-form__group row">
                <div class="m-section__content col-md-9">
                    <h4>Wastage Overhead Amount</h4>
                    <br>

                    <h5>Previous Month : </h5>
                    <p> <strong>From</strong> {{ start_date }} <strong>To</strong> {{ end_date }} </p>
                    <p>
                        <strong>Total Forming : </strong> {{ wastage_oh.forming_prev.amount }} <br>
                        <strong>Total Shapping : </strong> {{ wastage_oh.shapping_prev.amount }} <br>
                        <strong>Total Dryer : </strong> {{ wastage_oh.dryer_prev.amount }} <br>
                        <strong>Total Glaze : </strong> {{ wastage_oh.glaze_prev.amount }} <br><br>

                        <strong>Grand Total : </strong> {{ wastage_oh_prev_grand_total }} <br><br>
                    </p>
                    <br>

                    <h5>All Months : </h5>
                    <p>
                        <strong>Total Forming : </strong> {{ wastage_oh.forming_all.amount }} <br>
                        <strong>Total Shapping : </strong> {{ wastage_oh.shapping_all.amount }} <br>
                        <strong>Total Dryer : </strong> {{ wastage_oh.dryer_all.amount }} <br>
                        <strong>Total Glaze : </strong> {{ wastage_oh.glaze_all.amount }} <br><br>

                        <strong>All Grand Total : </strong> {{ wastage_oh_all_grand_total }} <br><br>
                    </p>
                    <br>
                </div>
            </div> 

            <hr>
            <div class="form-group m-form__group row">
                <div class="m-section__content col-md-9">
                    <h4>Reject Amount</h4>
                    <br>

                    <h5>Previous Month : </h5>
                    <p> <strong>From</strong> {{ start_date }} <strong>To</strong> {{ end_date }} </p>

                    <div class="table-responsive">
                        <table id="reject_prev_month" class="table table-sm m-table table-bordered borderless">
                            <thead class="thead-inverse" >
                                <tr class="text-center">
                                    <th colspan="2">Particular</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td> Kiln (Reject) : {{ reject.kilns_prev.reject_amt }} </td>
                                <td> Kiln (Overhead) : {{ reject.kilns_prev.reject_oh_amt }} </td>
                                <td class="text-right">
                                    {{ reject_kiln_prev_total }}
                                </td>
                            </tr>
                            <tr>
                                <td> Testing Section (Reject) : {{ reject.testing_prev.reject_amt }} </td>
                                <td> Testing Section (Overhead) : {{ reject.testing_prev.reject_oh_amt }} </td>
                                <td class="text-right">
                                    {{ reject_testing_prev_total }}
                                </td>
                            </tr>
                            <tr>
                                <td> Finished Section (Reject) : {{ reject.finished_prev.reject_amt }} </td>
                                <td> Finished Section (Overhead) : {{ reject.finished_prev.reject_oh_amt }} </td>
                                <td class="text-right">
                                    {{ reject_finished_prev_total }}
                                </td>
                            </tr>
                            <tr>
                                <td> Packing Section (Reject) : {{ reject.packing_prev.reject_amt }} </td>
                                <td> Packing Section (Overhead) : {{ reject.packing_prev.reject_oh_amt }} </td>
                                <td class="text-right">
                                    {{ reject_packing_prev_total }}
                                </td>
                            </tr>
                            <tr> 
                                <td colspan="2"> Grand Total </td> 
                                <td class="text-right">
                                    {{ reject_prev_total }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <br>

                    <h5>All Months : </h5>
                    
                    <div class="table-responsive">
                        <table id="reject_all_month" class="table table-sm m-table table-bordered borderless">
                            <thead class="thead-inverse" >
                                <tr class="text-center">
                                    <th colspan="2">Particular</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td> Kiln (Reject) : {{ reject.kilns_all.reject_amt }} </td>
                                <td> Kiln (Overhead) : {{ reject.kilns_all.reject_oh_amt }} </td>
                                <td class="text-right">
                                    {{ reject_kiln_all_total }}
                                </td>
                            </tr>
                            <tr>
                                <td> Testing Section (Reject) : {{ reject.testing_all.reject_amt }} </td>
                                <td> Testing Section (Overhead) : {{ reject.testing_all.reject_oh_amt }} </td>
                                <td class="text-right">
                                    {{ reject_testing_all_total }}
                                </td>
                            </tr>
                            <tr>
                                <td> Finished Section (Reject) : {{ reject.finished_all.reject_amt }} </td>
                                <td> Finished Section (Overhead) : {{ reject.finished_all.reject_oh_amt }} </td>
                                <td class="text-right">
                                    {{ reject_finished_all_total }}
                                </td>
                            </tr>
                            <tr>
                                <td> Packing Section (Reject) : {{ reject.packing_all.reject_amt }} </td>
                                <td> Packing Section (Overhead) : {{ reject.packing_all.reject_oh_amt }} </td>
                                <td class="text-right">
                                    {{ reject_packing_all_total }}
                                </td>
                            </tr>
                            <tr> 
                                <td colspan="2"> Grand Total </td> 
                                <td class="text-right">
                                    {{ reject_all_total }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <br>
                </div>
            </div>            
        </div>

        <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
            <div class="m-form__actions m-form__actions--solid">
                <div class="row">
                    <div class="col-lg-12 m--align-right">
                        <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save</span></button>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    export default {

        props:['product_list', 'overhead_info', 'wastage_oh', 'reject'],

        data:function(){
            return{
                start_date: '',
                end_date: '',

                form:{
                    total_thousand_kgs: 0,
                    total_one_kg: 0,
                    details: [
                        {
                            product_id: '',
                            thousand_kgs_weight: '',
                            one_kg_weight: ''
                        }
                    ],
                    recycle_chip_current_weight: 0,
                    recycle_chip_unit_price: 0,
                    recycle_chip_total_amt: 0,

                    glaze_material_price_per_kg: 0,

                    last_month_overhead_amt: '',
                    last_month_production_kg: '',
                    overhead_per_kg: '',
                },
                errors: {},
            };
        },

        methods: {

            getData(){
                var _this = this;
                axios.get(base_url+"raw-material-ratio-setup").then((response) => {
                    
                    if (response.data.recyle_chip) {
                        _this.form.recycle_chip_current_weight = response.data.recyle_chip.recycle_chip_current_weight;
                        _this.form.recycle_chip_unit_price = response.data.recyle_chip.recycle_chip_unit_price;
                        _this.form.recycle_chip_total_amt = response.data.recyle_chip.recycle_chip_total_amt;
                        
                        _this.form.glaze_material_price_per_kg = response.data.recyle_chip.glaze_material_price_per_kg;

                        _this.form.last_month_overhead_amt = response.data.recyle_chip.last_month_overhead_amt;
                        _this.form.last_month_production_kg = response.data.recyle_chip.last_month_production_kg;
                        _this.form.overhead_per_kg = response.data.recyle_chip.overhead_per_kg;
                    }

                    if(response.data.rm_ratio_items.length > 0){
                        _this.form.details = response.data.rm_ratio_items;
                         setTimeout(() => {
                            _this.totalKg();
                            _this.initSelect2();
                         },100);                            
                    }
                });
            },

            addNewItem(){
                var _this = this;
                _this.form.details.push({
                    product_id: '',
                    thousand_kgs_weight: '',
                    one_kg_weight: '',
                });

                _this.initSelect2();
            },

            removeItem(index){
                var _this = this;
                if(_this.form.details.length > 1){
                    _this.form.details.splice(index, 1);

                    _this.totalKg();
                    _this.initSelect2();
                }
            },

            totalKg(){
                var total_thousand_kgs = 0;
                var total_one_kgs = 0;
                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    total_thousand_kgs = Number(details[i].thousand_kgs_weight) + total_thousand_kgs;
                    total_one_kgs =  Number(details[i].one_kg_weight) + total_one_kgs ;

                }
                this.form.total_thousand_kgs = total_thousand_kgs;
                this.form.total_one_kg = total_one_kgs.toFixed(2);
            },

            calcRecycleChip(){
                var _this = this;
                _this.form.recycle_chip_total_amt = Number(_this.form.recycle_chip_current_weight * _this.form.recycle_chip_unit_price).toFixed(2);
            },

            calcOverheadPrice(){
                var _this = this;
                _this.form.overhead_per_kg = Number(_this.form.last_month_overhead_amt / _this.form.last_month_production_kg).toFixed(2);
            },

            store:function(){
                var _this = this;
                axios.post(base_url+'raw-material-ratio-setup/store', this.form).then( (response) => {
                   _this.showMassage(response.data);
                }).catch(error => {
                        if(error.response.status == 422){
                            _this.errors = error.response.data.errors;
                        }else{
                            _this.showMassage(error);
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

            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.details[dataIndex].product_id = selectedItem.val();
            });
        },

        created() {
           var _this = this;
           _this.getData();

           _this.start_date = moment().subtract(1,'months').startOf('month').format('YYYY-MM-DD');
           _this.end_date = moment().subtract(1,'months').endOf('month').format('YYYY-MM-DD');
        },

        computed: {

            wastage_oh_prev_grand_total: function () {
                var _this = this;
                let grand_total = 0;

                grand_total = Number( _this.wastage_oh.forming_prev.amount ) + Number( _this.wastage_oh.shapping_prev.amount ) + Number( _this.wastage_oh.dryer_prev.amount)  + Number( _this.wastage_oh.glaze_prev.amount );

              return grand_total.toFixed(2);
            },

            wastage_oh_all_grand_total: function () {
                var _this = this;
                let grand_total = 0;

                grand_total = Number( _this.wastage_oh.forming_all.amount ) + Number( _this.wastage_oh.shapping_all.amount ) + Number( _this.wastage_oh.dryer_all.amount)  + Number( _this.wastage_oh.glaze_all.amount );

              return grand_total.toFixed(2);
            },

            reject_kiln_prev_total: function () {
                var _this = this;
                let grand_total = 0;

                grand_total = Number( _this.reject.kilns_prev.reject_amt ) + Number( _this.reject.kilns_prev.reject_oh_amt );

              return grand_total.toFixed(2);
            },
            reject_testing_prev_total: function () {
                var _this = this;
                let grand_total = 0;

                grand_total = Number( _this.reject.testing_prev.reject_amt ) + Number( _this.reject.testing_prev.reject_oh_amt );

              return grand_total.toFixed(2);
            },
            reject_finished_prev_total: function () {
                var _this = this;
                let grand_total = 0;

                grand_total = Number( _this.reject.finished_prev.reject_amt ) + Number( _this.reject.finished_prev.reject_oh_amt );

              return grand_total.toFixed(2);
            },
            reject_packing_prev_total: function () {
                var _this = this;
                let grand_total = 0;

                grand_total = Number( _this.reject.packing_prev.reject_amt ) + Number( _this.reject.packing_prev.reject_oh_amt );

              return grand_total.toFixed(2);
            },
            reject_prev_total: function () {
                var _this = this;
                let grand_total = 0;

                grand_total = Number( _this.reject_kiln_prev_total ) + Number( _this.reject_testing_prev_total + Number( _this.reject_finished_prev_total ) + Number( _this.reject_packing_prev_total ) );

              return grand_total.toFixed(2);
            },

            reject_kiln_all_total: function () {
                var _this = this;
                let grand_total = 0;

                grand_total = Number( _this.reject.kilns_all.reject_amt ) + Number( _this.reject.kilns_all.reject_oh_amt );

              return grand_total.toFixed(2);
            },
            reject_testing_all_total: function () {
                var _this = this;
                let grand_total = 0;

                grand_total = Number( _this.reject.testing_all.reject_amt ) + Number( _this.reject.testing_all.reject_oh_amt );

              return grand_total.toFixed(2);
            },
            reject_finished_all_total: function () {
                var _this = this;
                let grand_total = 0;

                grand_total = Number( _this.reject.finished_all.reject_amt ) + Number( _this.reject.finished_all.reject_oh_amt );

              return grand_total.toFixed(2);
            },
            reject_packing_all_total: function () {
                var _this = this;
                let grand_total = 0;

                grand_total = Number( _this.reject.packing_all.reject_amt ) + Number( _this.reject.packing_all.reject_oh_amt );

              return grand_total.toFixed(2);
            },
            reject_all_total: function () {
                var _this = this;
                let grand_total = 0;

                grand_total = Number( _this.reject_kiln_all_total ) + Number( _this.reject_testing_all_total + Number( _this.reject_finished_all_total ) + Number( _this.reject_packing_all_total ) );

              return grand_total.toFixed(2);
            },
        }
    }
</script>

