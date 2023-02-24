<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="forming_no" class="col-lg-2 col-form-label">Testing/HV No <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="forming_no" v-model="form.testing_no" class="form-control form-control-sm m-input" placeholder="Enter Testing No" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('testing_no'))">{{ (errors.hasOwnProperty('testing_no')) ? errors.testing_no[0] :'' }}</div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Testing/HV Date <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.testing_date" data-dateField="testing_date" readonly placeholder="Select date from picker" id="m_datepicker_2" autocomplete="off"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('testing_date'))">{{ (errors.hasOwnProperty('testing_date')) ? errors.testing_date[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">

                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.narration"  id="narration" rows="2"></textarea>
                    </div>
                    <label class="col-lg-2 col-form-label" for="pre_mth_ovr_price">Previous month overhead price (Kg)</label>
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
                                    <th class="width-210">Product <span class="requiredField">*</span></th>
                                    <th>Remarks</th>
                                    <th>Opening / Current Qty <span class="requiredField">*</span></th>
                                    <th>Rec.F Kiln Qty <span class="requiredField">*</span></th>
                                    <th>Adj. Qty</th>
                                    <th>Unit Price <span class="requiredField">*</span></th>
                                    <th>Trans to Finishing Qty <span class="requiredField">*</span></th>
                                    <th>Overhead Price <span class="requiredField">*</span></th>
                                    <th>Total Price <span class="requiredField">*</span></th>
                                    <th>Reject Qty <span class="requiredField">*</span> </th>
                                    <th class="width-160">Balance</th>
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
                                                :kiln_weight="value.kiln_weight"
                                                :testing-opening-qty="value.testing_current_qty"
                                                :testing-receive-qty="value.testing_receive_qty"
                                            > {{value.product_name}}</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{ errors['details.'+index+'.product_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm m-input width-80" v-model="details.remarks" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.remarks']">{{ errors['details.'+index+'.remarks'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.current_qty" @input="totalTransQtyPriceCalc()" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.current_qty']">{{ errors['details.'+index+'.current_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.receive_qty" @input="totalTransQtyPriceCalc()" placeholder="" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.receive_qty']">{{ errors['details.'+index+'.receive_qty'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.adj_qty" @input="totalTransQtyPriceCalc()" placeholder="">
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.unit_price" @input="totalTransQtyPriceCalc()" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{ errors['details.'+index+'.unit_price'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.trans_to_finishing" @input="totalTransQtyPriceCalc()" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.trans_to_finishing']">{{ errors['details.'+index+'.trans_to_finishing'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.overhead_price" class="form-control form-control-sm m-input number" readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.overhead_price']">{{ errors['details.'+index+'.overhead_price'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.total_price" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.total_price']">{{ errors['details.'+index+'.total_price'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-80" v-model="details.reject_qty" @input="totalTransQtyPriceCalc()" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.reject_qty']">{{ errors['details.'+index+'.reject_qty'][0] }}</div>
                                    </td>
                                    <td class="width-160">
                                        <input type="number" step="any" class="form-control form-control-sm m-input width-160" v-model="details.remain_qty" placeholder="" readonly>
                                    </td>
                                    <td scope="row" class="width-100 text-center">
                                        <a  @click="removeItem(index,details.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right">Total</td>
                                    <td>
                                        <input type="text" v-model="form.total_receive_qty" class="form-control form-control-sm m-input" placeholder="Total.R Qty" readonly>
                                    </td>
                                    <td>
                                        <input type="text" v-model="form.total_adj_qty" class="form-control form-control-sm m-input" placeholder="Tot. Adj. Qty" readonly>
                                    </td>
                                    <td></td>
                                    <td>
                                        <input type="text" v-model="form.total_trans_to_store_qty" class="form-control form-control-sm m-input" placeholder="Final Trans Qty" readonly>
                                    </td>
                                    <td></td>
                                    <td>
                                        <input type="text" v-model="form.total_amount" class="form-control form-control-sm m-input" placeholder="Total Amount" readonly>
                                    </td>
                                    <td>
                                        <input type="text" v-model="form.total_reject_qty" class="form-control form-control-sm m-input" placeholder="Total Reject Qty" readonly>
                                    </td>
                                    <td>
                                        <input type="text" v-model="form.total_remain_qty" class="form-control form-control-sm m-input" placeholder="Tot. Rem. Qty" readonly>
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
                            <button type="submit" v-if="permissions.indexOf('testing-section.approve') != -1" class="btn btn-sm btn-primary" @click.prevent="update(form.id,1)" ><i class="la la-check"></i> <span>Approve</span></button>
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

        props:['permissions','product_lists','editId','warehouse_lists'],

        data:function(){
            return{

                data_list:false,
                add_form:false,
                edit_form:true,
                view_form:false,

                overhead_kg_price: 0,

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
                            product_id: '',
                            remarks: '',
                            current_qty: '',
                            receive_qty: '',
                            adj_qty: 0,
                            trans_to_finishing: '',
                            unit_price: '',
                            overhead_price: '',
                            total_price: '',
                            remain_qty: '',
                            reject_qty: 0,

                            kiln_weight: 0,
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
                    current_qty: '',
                    receive_qty: '',
                    adj_qty: 0,
                    trans_to_finishing: '',
                    unit_price: '',
                    overhead_price: '',
                    total_price: '',
                    remain_qty: '',
                    reject_qty: 0,
                });
                _this.initSelect2();
            },

            removeItem(index,deletedId){
                var _this = this;
                if(_this.form.details.length > 1){
                    _this.form.details.splice(index, 1);
                    _this.initSelect2();

                    if (deletedId) {
                     _this.form.deleteID.push(deletedId);
                     }
                    _this.totalTransQtyPriceCalc();
                }
            },

            edit(id) {
                var _this = this;
                 _this.$loading(true);
                axios.get(base_url+'testing-section/'+id+'/edit')
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

                axios.put(base_url+'testing-section/'+id, this.form).then( (response) => {
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

            totalTransQtyPriceCalc(){
                var _this = this;
                var total_receive_qty = 0;
                var total_adj_qty = 0;
                var total_trans_to_store_qty = 0;
                var overhead_price =0;
                var totalPrice = 0;
                var total_amount = 0;

                var remain_qty = 0;
                var total_remain_qty = 0;
                var total_reject_qty = 0;

                var details = _this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {

                    total_receive_qty = total_receive_qty + Number(details[i].receive_qty);
                    total_adj_qty = total_adj_qty + Number(details[i].adj_qty);
                    total_trans_to_store_qty = total_trans_to_store_qty + Number(details[i].trans_to_finishing);

                    // Over head calculation
                    overhead_price = Number(details[i].trans_to_finishing)*_this.overhead_kg_price;
                    _this.form.details[i].overhead_price = overhead_price.toFixed(2);

                    totalPrice = Number(details[i].unit_price) * Number(details[i].trans_to_finishing) + overhead_price;
                    _this.form.details[i].total_price = totalPrice;

                    total_amount = totalPrice + total_amount;

                    remain_qty = Number(details[i].current_qty) + Number(details[i].receive_qty) + Number(details[i].adj_qty) - Number(details[i].trans_to_finishing) - Number(details[i].reject_qty);
                    _this.form.details[i].remain_qty = remain_qty;
                    total_remain_qty = total_remain_qty + remain_qty;

                    total_reject_qty = total_reject_qty + Number(details[i].reject_qty);
                }

                _this.form.total_receive_qty = total_receive_qty;
                _this.form.total_adj_qty = total_adj_qty;
                _this.form.total_trans_to_store_qty = total_trans_to_store_qty;
                _this.form.total_amount = total_amount;
                _this.form.total_remain_qty = total_remain_qty;
                _this.form.total_reject_qty = total_reject_qty;

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

            getKilnLastPrice(product_id,dataIndex) {
                var _this = this;
                axios.get(base_url + 'kiln-section?token_last_price=yes&product_id=' + product_id)
                    .then((response) => {

                        _this.form.details[dataIndex].unit_price = response.data.unit_price;
                        _this.totalTransQtyPriceCalc();
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

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if( selectField == 'warehouse_id' ){
                    _this.form.warehouse_id= selectedItem.val();
                }
            });

            $('#editComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                    var kiln_weight = $('option:selected', $(e.target)).attr('kiln_weight');
                    var openingQty = $('option:selected', $(e.target)).attr('testing-opening-qty');
                    var receiveQty = $('option:selected', $(e.target)).attr('testing-receive-qty');

                if (kiln_weight > 0) {

                    _this.form.details[dataIndex].kiln_weight  = kiln_weight ? kiln_weight : 0;
                    _this.form.details[dataIndex].current_qty  = openingQty ? openingQty : 0;
                    _this.form.details[dataIndex].receive_qty  = receiveQty ? receiveQty : 0;

                    _this.totalTransQtyPriceCalc();

                    var isDuplicated = _this.duplicateCheck(selectedItem.val());
                    if (isDuplicated) {
                        _this.form.details[dataIndex].product_id = '';
                        _this.initSelect2();
                    } else {
                        _this.form.details[dataIndex].product_id = selectedItem.val();

                        _this.getKilnLastPrice( selectedItem.val(), dataIndex);
                    }
                } else {
                    alert('Please update kiln weight first in product entry list!');
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
                        _this.form.testing_date = '';
                    }else {
                        _this.form.testing_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created(){
            var _this = this;
            _this.edit(_this.editId);
            _this.totalTransQtyPriceCalc();
        }
    }
</script>
