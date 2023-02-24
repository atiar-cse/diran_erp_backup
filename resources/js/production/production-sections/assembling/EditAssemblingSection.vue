<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >

            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="forming_no" class="col-lg-2 col-form-label">Assembling No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="forming_no" v-model="form.assembling_no"
                               class="form-control form-control-sm m-input" placeholder="Enter Assembling No" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('assembling_no'))">{{
                            (errors.hasOwnProperty('assembling_no')) ? errors.assembling_no[0] :'' }}
                        </div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Assembling Date <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.assembling_date" data-dateField="assembling_date" readonly
                                   placeholder="Select date from picker" id="m_datepicker_2" autocomplete="off"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('assembling_date'))">{{
                            (errors.hasOwnProperty('assembling_date')) ? errors.assembling_date[0] :'' }}
                        </div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.narration" id="narration"
                                  rows="2"></textarea>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="assembling_types">Assembling Types <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" v-model="form.assembling_types"
                                data-selectField="assembling_types" id="assembling_types">
                            <option value="Production"> Production</option>
                            <!-- <option value="Reject"> Reject</option> -->
                        </select>
                    </div>
                </div>
                <div v-show="form.assembling_types === 'Production'">
                    <div class="form-group m-form__group row">
                        <label class="col-lg-2 col-form-label" for="finishingProduct">Finishing Product <span
                            class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <select class="form-control select2" v-model="form.finishing_product_id"
                                    data-selectField="finishing_product_id" id="finishingProduct" style="width: 100%">
                                <option v-for="(value,index) in finish_product_lists" :value="value.id"
                                        :finish-product-buying-price="value.buying_price"> {{value.product_name}}
                                </option>
                            </select>
                        </div>
                        <label class="col-lg-2 col-form-label" for="trans_to_packing_qty">Trans to Packing qty
                            <span class="requiredField">*</span> (Finished Qty)</label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="trans_to_packing_qty" @input="calcFinishedProductUnitPrice"
                                   v-model="form.trans_to_packing_qty"
                                   class="form-control form-control-sm m-input"
                                   placeholder="Enter Trans to Packing Qty">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('trans_to_packing_qty'))">{{
                                (errors.hasOwnProperty('trans_to_packing_qty')) ? errors.trans_to_packing_qty[0] :'' }}
                            </div>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="unit_price" class="col-lg-2 col-form-label">Unit Price <span
                            class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="unit_price" v-model="form.unit_price"
                                   @change="totalTransQtyPriceCalc()" class="form-control form-control-sm m-input"
                                   placeholder="Enter Unit Price" readonly>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('unit_price'))">{{
                                (errors.hasOwnProperty('unit_price')) ? errors.unit_price[0] :'' }}
                            </div>
                        </div>
                        <label class="col-lg-2 col-form-label" for="total_price">Total Price <span
                            class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="total_price" v-model="form.total_price"
                                   @change="totalTransQtyPriceCalc()" class="form-control form-control-sm m-input"
                                   placeholder="Enter Total Price" readonly>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('total_price'))">{{
                                (errors.hasOwnProperty('total_price')) ? errors.total_price[0] :'' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="pre_mth_ovr_price">Previous month overhead price
                        (Kg)</label>
                    <div class="col-lg-4">
                        <input type="text" id="overhead_kg_price" v-model="overhead_kg_price" class="form-control form-control-sm m-input" placeholder="ex: 1 kg" readonly>
                    </div>
                </div>


                <br><br>
                <!--begin::Portlet-->

                <div class="form-group m-form__group row add-manage-section">
                    <div class="m-section__content col-lg-12">
                        <div class="table-responsive">
                          <table class="table table-sm m-table table-bordered borderless">
                              <thead class="thead-inverse">
                              <tr>
                                  <th></th>
                                  <th>Product <!--<span class="requiredField">*</span>--></th>
                                  <th>Remarks</th>
                                  <th style="width: 160px;">Opening / Current Qty <!--<span class="requiredField">*</span>-->
                                  </th>
                                  <th>Rec.F Finishing Sec Qty <!--<span class="requiredField">*</span>--></th>
                                  <th>Unit Price <!--<span class="requiredField">*</span>--></th>
                                  <th>Used Qty (To Assemble) <!--<span class="requiredField">*</span>--></th>
                                  <th>Overhead Price <!--<span class="requiredField">*</span>--></th>
                                  <th>Total Price <!--<span class="requiredField">*</span>--></th>
                                  <th style="width: 160px;">Balance</th>
                                  <th></th>
                              </tr>
                              </thead>
                              <tbody>
                              <tr v-for="(details, index) in form.details">
                                  <th scope="row">
                                      <a href="javascript:void(0)" @click="addNewItem"
                                         class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                         data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                         title="Add More">
                                          <i class="la la-plus"></i>
                                      </a>
                                  </th>
                                  <td class="tdWidth">
                                      <select class="form-control select2 select-product" v-bind:data-index="index"
                                              v-model="details.product_id" style="width:100%">
                                          <option v-for="(value,index) in product_lists"
                                                  :value="value.id"
                                                  :assembling_current_qty="value.assembling_current_qty"
                                                  :assembling_receive_qty="value.assembling_receive_qty"
                                          > {{value.product_name}}
                                          </option>
                                      </select>
                                      <div class="requiredField" v-if="errors['details.'+index+'.product_id']">{{
                                          errors['details.'+index+'.product_id'][0] }}
                                      </div>
                                  </td>
                                  <td>
                                      <input type="text" class="form-control form-control-sm m-input"
                                             v-model="details.remarks" placeholder="">
                                      <div class="requiredField" v-if="errors['details.'+index+'.remarks']">{{
                                          errors['details.'+index+'.remarks'][0] }}
                                      </div>
                                  </td>
                                  <td style="width: 160px;">
                                      <input style="width: 160px;" type="number" step="any"
                                             class="form-control form-control-sm m-input"
                                             v-model="details.current_qty" @input="totalTransQtyPriceCalc()"
                                             placeholder="">
                                      <div class="requiredField" v-if="errors['details.'+index+'.current_qty']">{{
                                          errors['details.'+index+'.current_qty'][0] }}
                                      </div>
                                  </td>
                                  <td>
                                      <input type="number" step="any" class="form-control form-control-sm m-input"
                                             v-model="details.receive_qty" @input="totalTransQtyPriceCalc()"
                                             placeholder="" readonly>
                                      <div class="requiredField" v-if="errors['details.'+index+'.receive_qty']">{{
                                          errors['details.'+index+'.receive_qty'][0] }}
                                      </div>
                                  </td>

                                  <td>
                                      <input type="number" step="any" class="form-control form-control-sm m-input"
                                             v-model="details.unit_price" @input="totalTransQtyPriceCalc()"
                                             placeholder="">
                                      <div class="requiredField" v-if="errors['details.'+index+'.unit_price']">{{
                                          errors['details.'+index+'.unit_price'][0] }}
                                      </div>
                                  </td>

                                  <td>
                                      <input type="number" step="any" class="form-control form-control-sm m-input"
                                             v-model="details.trans_to_packing_qty" @input="totalTransQtyPriceCalc()"
                                             placeholder="">
                                      <div class="requiredField"
                                           v-if="errors['details.'+index+'.trans_to_packing_qty']">{{
                                          errors['details.'+index+'.trans_to_packing_qty'][0] }}
                                      </div>
                                  </td>
                                  <td>
                                      <input type="text" v-model="details.overhead_price"
                                             class="form-control form-control-sm m-input number" readonly>
                                      <div class="requiredField" v-if="errors['details.'+index+'.overhead_price']">{{
                                          errors['details.'+index+'.overhead_price'][0] }}
                                      </div>
                                  </td>
                                  <td>
                                      <input type="number" step="any" class="form-control form-control-sm m-input"
                                             v-model="details.total_price" placeholder="">
                                      <div class="requiredField" v-if="errors['details.'+index+'.total_price']">{{
                                          errors['details.'+index+'.total_price'][0] }}
                                      </div>
                                  </td>

                                  <td style="width: 160px;">
                                      <input style="width: 160px;" type="number" step="any"
                                             class="form-control form-control-sm m-input" v-model="details.remain_qty"
                                             placeholder="" readonly>
                                  </td>
                                  <td scope="row" class="width-100 text-center">
                                      <a @click="removeItem(index)"
                                         class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                         data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                         title="Delete"><i class="la la-trash-o"></i></a>
                                  </td>
                              </tr>
                              <tr>
                                  <td colspan="4" class="text-right">Total</td>
                                  <td>
                                      <input type="text" v-model="form.total_receive_qty"
                                             class="form-control form-control-sm m-input" placeholder="Total.R Qty"
                                             readonly>
                                  </td>

                                  <td></td>
                                  <td>
                                      <input type="text" v-model="form.total_trans_to_packing_qty"
                                             class="form-control form-control-sm m-input"
                                             placeholder="Final Trans Qty" readonly>
                                  </td>
                                  <td></td>
                                  <td>
                                      <input type="text" v-model="form.total_amount"
                                             class="form-control form-control-sm m-input" placeholder="Total Amount"
                                             readonly>
                                  </td>
                                  <td>
                                      <input type="text" v-model="form.total_remain_qty"
                                             class="form-control form-control-sm m-input" placeholder="Tot. Rem. Qty"
                                             readonly>
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

            <!-- Inventory Materials -->
            <hr>
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label class="col-lg-6 col-form-label" > <strong>Assembling Materials (Inventory)</strong> </label>

                    <label for="warehouse_id" class="col-lg-2 col-form-label"> Warehouse </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" data-selectField="warehouse_id" id="warehouse_id"
                                v-model="form.warehouse_id">
                            <option v-for="(value,index) in warehouse_lists" :value="value.id">
                                {{value.purchase_ware_houses_name}}
                            </option>
                        </select>
                    </div>
                </div>

                <!--begin::Portlet-->
                <div class="form-group m-form__group row add-manage-section">
                    <div class="m-section__content col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse">
                                <tr>
                                    <th></th>
                                    <th class="width-210">Product Name</th>
                                    <th>Remarks</th>
                                    <th>Available Stock Qty</th>
                                    <th>Qty</th>
                                    <th>Unit Price (Inv. Avg)</th>
                                    <th class="width-160">Total Price </th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>

                                <tr v-for="(inv_details, index) in form.inv_details" >
                                    <th scope="row">
                                        <a href="javascript:void(0)" @click="addNewInventoryItem"
                                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </th>
                                    <td class="width-210">
                                        <select class="form-control m-select2 select2 inv-select-product width-210"
                                                v-bind:data-index="index" v-model="inv_details.product_id"
                                                @input="calcInventoryStock()" style="width: 100%">
                                            <option v-for="(value,index) in inv_product_lists" :value="value.id"
                                                    :unit_price="value.unit_price"
                                                    :current_stock_qty="value.current_stock_qty"
                                                > {{value.product_name}} - In Stock {{value.current_stock_qty}}
                                            </option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" v-model="inv_details.remarks"
                                               class="form-control form-control-sm m-input" placeholder="">
                                    </td>
                                    <td> {{ inv_details.current_stock_qty }} </td>
                                    <td>
                                        <input type="text" v-model="inv_details.qty"
                                               class="form-control form-control-sm m-input number" placeholder=""
                                               @input="calcInventoryStock()">
                                    </td>
                                    <td>
                                        <input type="text" v-model="inv_details.unit_price"
                                               class="form-control form-control-sm m-input number"
                                               @input="calcInventoryStock()">
                                    </td>
                                    <td class="width-160">
                                        <input type="text" v-model="inv_details.total_price"
                                               class="form-control form-control-sm m-input number width-160"
                                               @input="calcInventoryStock()" readonly>
                                    </td>

                                    <td>
                                        <a @click="removeInventoryItem(index)"
                                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="4" class="text-right">Total</td>
                                    <td class="">
                                        <input type="text" v-model="form.inv_total_qty"
                                               class="form-control form-control-sm m-input" readonly>
                                    </td>
                                    <td class="text-right">Total Amount</td>
                                    <td class="">
                                        <input type="text" v-model="form.inv_total_amount"
                                               class="form-control form-control-sm m-input" readonly>
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
            <!-- //Inventory Materials -->

            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-md-6">
                            <p v-if="permissions.indexOf('assembling-section.approve') != -1" class="bg-danger text-white"> Please dont't approve back dated data entry! </p>
                        </div>
                        <div class="col-lg-6 m--align-right">
                            <button type="submit" v-if="permissions.indexOf('assembling-section.approve') != -1" class="btn btn-sm btn-primary" @click.prevent="update(form.id,1)" ><i class="la la-check"></i> <span>Approve</span></button>
                            <button type="submit" class="btn btn-sm btn-success"  @click.prevent="update(form.id,0)" ><i class="la la-check"></i> <span>Update and Go List</span></button>
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

        props:['permissions','finish_product_lists', 'product_lists','editId','warehouse_lists'],

        data:function(){
            return{

                data_list:false,
                add_form:false,
                edit_form:true,
                view_form:false,

                overhead_kg_price: 0,

                inv_product_lists: [],

                form: {
                    assembling_no: '',
                    assembling_date: '',
                    warehouse_id: 111,
                    assembling_types: 'Production',
                    finishing_product_id: '',
                    trans_to_packing_qty: 0,
                    unit_price: '',
                    total_price: '',
                    narration: '',
                    total_rm_qty: '',
                    total_rm_price: '',
                    approve_status: '',
                    status: '',
                    details: [
                        {
                            product_id: '',
                            remarks: '',
                            current_qty: '',
                            receive_qty: '',
                            trans_to_packing_qty: '',
                            unit_price: '',
                            overhead_price: '',
                            total_price: '',
                            remain_qty: '',
                        }
                    ],

                    last_month_overhead_amt: '',
                    last_month_production_kg: '',
                    overhead_per_kg: '',

                    inv_details: [
                        {
                            product_id: '',
                            remarks: '',
                            current_stock_qty: '',
                            qty: '',
                            unit_price: 0,
                            total_price: '',
                        }
                    ],
                    inv_total_qty: '',
                    inv_total_amount: '',
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

            addNewItem() {
                var _this = this;
                _this.form.details.push({
                    product_id: '',
                    remarks: '',
                    current_qty: '',
                    receive_qty: '',
                    trans_to_packing_qty: '',
                    unit_price: '',
                    overhead_price: '',
                    total_price: '',
                    remain_qty: '',
                });

                _this.totalTransQtyPriceCalc();
                _this.initSelect2();
            },

            removeItem(index) {
                var _this = this;
                if (_this.form.details.length > 1) {
                    _this.form.details.splice(index, 1);
                }
                _this.totalTransQtyPriceCalc();
            },

            edit(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url+'assembling-section/'+id+'/edit')
                    .then( (response) => {
                        _this.$loading(false);
                        _this.form  = response.data.data;

                        setTimeout(function () {
                            _this.getOverheadCosting();

                                setTimeout(function () {
                                    _this.totalTransQtyPriceCalc();

                                    _this.getInventoryStockInfo();
                                },250);
                        },250);

                        _this.initSelect2();
                    });
            },

            update(id,app){

                var _this = this;
                _this.form.approve_status = app;

                _this.$loading(true);
                axios.put(base_url+'assembling-section/'+id, this.form).then( (response) => {
                    _this.$loading(false);
                    if(response.data.status = 'success') {
                        this.showMassage(response.data);
                        EventBus.$emit('data-changed');
                    } else {
                        this.showMassage(response.data);
                    }
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

            totalTransQtyPriceCalc() {
                var _this = this;
                var total_receive_qty = 0;
                var total_trans_to_packing_qty = 0;
                var overhead_price = 0;
                var totalPrice = 0;
                var total_amount = 0;

                var remain_qty = 0;
                var total_remain_qty = 0;

                var details = _this.form.details;
                var length = details.length;

                for (let i = 0; i < length; i++) {

                    total_receive_qty = total_receive_qty + Number(details[i].receive_qty);
                    total_trans_to_packing_qty = total_trans_to_packing_qty + Number(details[i].trans_to_packing_qty);

                    // Over head calculation
                    overhead_price = Number(details[i].trans_to_packing_qty) * _this.overhead_kg_price;
                    _this.form.details[i].overhead_price = overhead_price.toFixed(2);

                    totalPrice = Number(details[i].unit_price) * Number(details[i].trans_to_packing_qty) + overhead_price;
                    _this.form.details[i].total_price = totalPrice;

                    total_amount = totalPrice + total_amount;

                    remain_qty = Number(details[i].current_qty) + Number(details[i].receive_qty) - Number(details[i].trans_to_packing_qty);
                    _this.form.details[i].remain_qty = remain_qty;
                    total_remain_qty = total_remain_qty + remain_qty;
                }

                _this.form.total_receive_qty = total_receive_qty;
                _this.form.total_trans_to_packing_qty = total_trans_to_packing_qty;
                _this.form.total_amount = total_amount;
                _this.form.total_remain_qty = total_remain_qty;

                _this.form.total_rm_qty = total_trans_to_packing_qty;
                _this.form.total_rm_price = total_amount;

                _this.calcFinishedProductUnitPrice();
            },

            calcFinishedProductUnitPrice() {

                var _this = this;

                _this.form.total_price = _this.form.total_rm_price + Number( _this.form.inv_total_amount );
                if( Number(_this.form.total_rm_price) > 0 && Number(_this.form.trans_to_packing_qty) > 0 ){
                    _this.form.unit_price = Number(_this.form.total_price / _this.form.trans_to_packing_qty).toFixed(2);
                }
                else
                {
                    _this.form.total_price = _this.form.unit_price * _this.form.trans_to_packing_qty;
                }
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

            getInventoryOrSemiFinishedLastPrice(product_id,dataIndex) {
                var _this = this;
                axios.get(base_url + 'finished-stock-section?token_last_price=yes&product_id=' + product_id)
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

            //Inventory Items

            addNewInventoryItem() {
                var _this = this;
                this.form.inv_details.push({
                    product_id: '',
                    remarks: '',
                    current_stock_qty: '',
                    qty: '',
                    unit_price: '',
                    total_price: '',
                });
                _this.initSelect2();
            },

            removeInventoryItem(index) {
                var _this = this;
                if (_this.form.inv_details.length > 1) {
                    _this.form.inv_details.splice(index, 1);
                }
                _this.calcInventoryStock();
            },

            calcInventoryStock() {
                var inv_details = this.form.inv_details;
                var length = inv_details.length;
                var total_prices = 0;
                var totalAmount = 0;
                var totalQty = 0;

                for (let i = 0; i < length; i++) {
                    total_prices = Number(inv_details[i].qty) * inv_details[i].unit_price;
                    inv_details[i].total_price = total_prices.toFixed(2);
                    totalAmount = Number(total_prices) + totalAmount;
                    totalQty = Number(inv_details[i].qty) + totalQty;
                }
                this.form.inv_total_qty = totalQty.toFixed(2);
                this.form.inv_total_amount = totalAmount.toFixed(2);

                this.calcFinishedProductUnitPrice();
            },

            duplicateInventoryCheck(selectedValue) {
                var no_index = this.form.details.length;
                for (let i = 0; i < no_index; i++) {
                    if (this.form.details[i].product_id == selectedValue) {
                        alert("Duplicate Found");
                        return true;
                    }
                }
                return false;
            },

            getInventoryStockInfo() {
                var _this = this;
                var warehouse_id = _this.form.warehouse_id;

                axios.get(base_url + 'stock-open?token_stock_info=stock_info&warehouse_id=' + warehouse_id )
                    .then((response) => {
                        console.log(response.data)
                        _this.inv_product_lists = response.data;
                        //_this.calcInventoryStock();
                    });
            },
        },

        mounted(){
            var _this = this;
            _this.initSelect2();

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if( selectField == 'assembling_types' ){
                    _this.form.assembling_types= selectedItem.val();

                }else if(selectField == 'warehouse_id'){
                    _this.form.warehouse_id = selectedItem.val();

                    //Load Inventory Products
                    _this.getInventoryStockInfo();

                }else if(selectField == 'finishing_product_id'){

                    _this.form.finishing_product_id= selectedItem.val();
                    _this.form.unit_price = finish_product_unit_price ? finish_product_unit_price : 0;
                    _this.form.total_price = finish_product_unit_price ? finish_product_unit_price : 0 * _this.form.trans_to_packing_qty;
                }
            });

            $('#editComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');

                var unit_price = $('option:selected', $(e.target)).attr('unit_price');
                var assembling_current_qty = $('option:selected', $(e.target)).attr('assembling_current_qty');
                var assembling_receive_qty = $('option:selected', $(e.target)).attr('assembling_receive_qty');

                _this.form.details[dataIndex].unit_price = unit_price ? unit_price : 0;
                _this.form.details[dataIndex].current_qty = assembling_current_qty ? assembling_current_qty : 0;
                _this.form.details[dataIndex].receive_qty = assembling_receive_qty ? assembling_receive_qty : 0;

                var isDuplicated = _this.duplicateCheck(selectedItem.val());
                if (isDuplicated) {
                    _this.form.details[dataIndex].product_id = '';
                    _this.initSelect2();
                } else {
                    _this.form.details[dataIndex].product_id = selectedItem.val();

                    _this.getInventoryOrSemiFinishedLastPrice(selectedItem.val(), dataIndex);
                }
            });


            $('#editComponent').on('change', '.inv-select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');

                var unit_price = $('option:selected', $(e.target)).attr('unit_price');
                var current_stock_qty = $('option:selected', $(e.target)).attr('current_stock_qty');

                _this.form.inv_details[dataIndex].unit_price = unit_price ? unit_price : 0;
                _this.form.inv_details[dataIndex].current_stock_qty = current_stock_qty ? current_stock_qty : 0;

                var isDuplicated = _this.duplicateInventoryCheck(selectedItem.val());
                if (isDuplicated) {
                    _this.form.inv_details[dataIndex].product_id = '';
                    _this.initSelect2();
                } else {
                    _this.form.inv_details[dataIndex].product_id = selectedItem.val();

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
                        _this.form.assembling_date = '';
                    }else {
                        _this.form.assembling_date = moment(ev.date).format('DD/MM/YYYY');
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
