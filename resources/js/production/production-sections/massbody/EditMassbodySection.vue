<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id,0)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >
                <div class="m-portlet__body">
                    <div class="form-group m-form__group row">
                        <label for="massbody_no" class="col-lg-2 col-form-label">Massbody No </label>
                        <div class="col-lg-4">
                            <input type="text" id="massbody_no" v-model="form.massbody_no" class="form-control form-control-sm m-input" placeholder="Enter massbody no" readonly>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('massbody_no'))">{{ (errors.hasOwnProperty('massbody_no')) ? errors.massbody_no[0] :'' }}</div>
                        </div>
                        <label for="m_datepicker_2" class="col-lg-2 col-form-label">Massbody Date </label>
                        <div class="col-lg-4">
                            <div class="input-group date">
                                <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.massbody_date" readonly placeholder="Select date from picker" id="m_datepicker_2" autocomplete="off"/>
                                <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                                </div>
                            </div>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('massbody_date'))">{{ (errors.hasOwnProperty('massbody_date')) ? errors.massbody_date[0] :'' }}</div>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label class="col-lg-2 col-form-label" for="requisition_id">Requisition No </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="requisition_id" v-model="form.requisition_for_raw_material_id" data-selectField="requisition_for_raw_material_id" @change="massbodyQtyCalc()" disabled>
                                <option v-for="(value,index) in rm_requisition_list" 
                                    :value="value.id" 
                                    :data-Weight="value.total_qty"
                                    :green_chip_amount="value.total_amount"
                                    > {{value.rm_requisition_no}}</option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('requisition_for_raw_material_id'))">{{ (errors.hasOwnProperty('requisition_for_raw_material_id')) ? errors.requisition_for_raw_material_id[0] :'' }}</div>
                        </div>
                        <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                        <div class="col-lg-4">
                            <textarea class="form-control m-input" v-model="form.narration" id="narration" rows="2"></textarea>
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
                                        <th>Product</th>
                                        <th>Percentage</th>
                                        <th>Weight</th>
                                        <th>Avg. Unit Price</th>
                                        <th>Total Price</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" v-model="form.green_chip_name" class="form-control form-control-sm m-input">
                                            <div class="requiredField" v-if="(errors.hasOwnProperty('green_chip_name'))">{{ (errors.hasOwnProperty('green_chip_name')) ? errors.green_chip_name[0] :'' }}</div>
                                        </td>
                                        <td>
                                            <input type="number" step="any" v-model="form.green_chip_percentage " @input="massbodyQtyCalc()" class="form-control form-control-sm m-input" placeholder="">
                                            <div class="requiredField" v-if="(errors.hasOwnProperty('green_chip_percentage'))">{{ (errors.hasOwnProperty('green_chip_percentage')) ? errors.green_chip_percentage[0] :'' }}</div>
                                        </td>
                                        <td>
                                            <input type="number" step="any" v-model="form.green_chip_weight" class="form-control form-control-sm m-input" readonly>
                                        </td>
                                        <td>{{form.green_chip_unit_price}}</td>
                                        <td>
                                            {{ (form.green_chip_unit_price * form.green_chip_weight).toFixed(2) }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                            <input type="text" v-model="form.recycle_chip_name" class="form-control form-control-sm m-input">
                                            <div class="requiredField" v-if="(errors.hasOwnProperty('recycle_chip_name'))">{{ (errors.hasOwnProperty('recycle_chip_name')) ? errors.recycle_chip_name[0] :'' }}</div>
                                        </td>
                                        <td>
                                            <input type="number" step="any" v-model="form.recycle_chip_percentage" class="form-control form-control-sm m-input" readonly>
                                            <div class="requiredField" v-if="(errors.hasOwnProperty('green_chip_percentage'))">{{ (errors.hasOwnProperty('green_chip_percentage')) ? errors.green_chip_percentage[0] :'' }}</div>
                                        </td>
                                        <td>
                                            <input type="number" step="any" v-model="form.recycle_chip_weight" class="form-control form-control-sm m-input" readonly>
                                        </td>
                                        <td>{{form.recycle_chip_unit_price}}</td>
                                        <td>
                                            {{ (form.recycle_chip_unit_price * form.recycle_chip_weight).toFixed(2) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-right"><b>Total</b></td>
                                        <td class="">
                                            <input type="text" v-model="form.total_percentage"  class="form-control form-control-sm m-input" readonly>
                                        </td>
                                        <td colspan="">
                                            <input type="text" v-model="form.total_weight_qty" class="form-control form-control-sm m-input" readonly>
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    </tbody>
                                </table>

                                <br>
                                <hr>
                                <div class ="table-responsive col-md-8">
                                    <table class="table table-sm m-table table-bordered borderless text-center">
                                        <thead class="thead-inverse" >
                                            <tr>
                                                <th>Available Recycle Chip Weight (kg)</th>
                                                <th>Unit Price</th>
                                                <th>Total Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    {{recycle_chip.recycle_chip_current_weight}}
                                                </td>
                                                <td>
                                                    {{recycle_chip.recycle_chip_unit_price}}
                                                </td>
                                                <td>
                                                    {{recycle_chip.recycle_chip_total_amt}}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>                                
                            </div>
                            <br><br>
                        </div>
                    </div>
                </div>


                <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                    <div class="m-form__actions m-form__actions--solid">
                        <div class="row">
                            <div class="col-lg-12 m--align-right">
                                <button type="submit" v-if="permissions.indexOf('massbody-section.approve') != -1" class="btn btn-sm btn-primary" @click.prevent="update(form.id,1)" ><i class="la la-check"></i> <span>Approve</span></button>
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

        props:['permissions','rm_requisition_list', 'recycle_chip','editId'],

        data:function(){
            return{
                data_list:false,
                add_form:false,
                edit_form:true,
                view_form:false,
                form:{
                    massbody_no: '',
                    massbody_date: '',
                    requisition_for_raw_material_id: '',
                    narration: '',
                    green_chip_name: 'Green Chip',
                    green_chip_percentage: 0,
                    green_chip_weight: 0,
                    green_chip_amount: 0,

                    recycle_chip_name: 'Recycle Chip',
                    recycle_chip_percentage: 0,
                    recycle_chip_weight: 0,
                    recycle_chip_amount: 0,

                    recycle_chip_unit_price: 0,

                    total_percentage: 100,
                    total_weight_qty: 0,
                    approve_status: '',
                    available_recycle_chip: '',
                },
                errors: {},
            };
        },

        methods:{

                massbodyQtyCalc(){
                    var greenChipWeight = this.form.green_chip_weight;
                    var recycleChipWeight = 0;
                    var totalWeight = 0;

                    var totalPercentage = this.form.total_percentage;
                    var greenChipPercenage = this.form.green_chip_percentage;
                    var recycleChipPercentage = parseInt(totalPercentage) - parseInt(greenChipPercenage);


                    if(parseInt(greenChipPercenage) == 0){
                        recycleChipWeight = parseInt(greenChipWeight) * parseInt(recycleChipPercentage) ;
                    }else{
                        recycleChipWeight = parseInt(greenChipWeight) * parseInt(recycleChipPercentage) / parseInt(greenChipPercenage);
                    }
                    this.form.recycle_chip_weight = recycleChipWeight.toFixed( 3 );;
                    totalWeight = parseInt(greenChipWeight) + parseInt(recycleChipWeight);
                    this.form.total_weight_qty = totalWeight.toFixed( 3 );
                    this.form.recycle_chip_percentage = recycleChipPercentage;

                    let grand_total = recycleChipWeight * _this.recycle_chip.recycle_chip_unit_price;
                    _this.form.recycle_chip_amount = grand_total.toFixed( 2 );

                    _this.form.recycle_chip_unit_price = _this.recycle_chip.recycle_chip_unit_price;                    
                },

                edit(id) {
                    var _this = this;
                    axios.get(base_url+'massbody-section/'+id+'/edit')
                        .then( (response) => {
                            _this.form  = response.data;

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
                    axios.put(base_url+'massbody-section/'+id, _this.form)
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
            },

            mounted(){
                var _this = this;

                $('#editComponent').on('change', '.select2', function (e) {
                    var selectedItem = $(this);
                    var selectField = $(e.currentTarget).attr('data-selectField');
                    var selectweight = $('option:selected', $(e.target)).attr('data-weight');

                    if( selectField == 'requisition_for_raw_material_id' ){
                        _this.form.requisition_for_raw_material_id= selectedItem.val();
                        _this.form.green_chip_weight  = selectweight;
                        _this.massbodyQtyCalc();
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
                            _this.form.massbody_date = '';

                        }else {
                            _this.form.massbody_date = moment(ev.date).format('DD/MM/YYYY');

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

            created(){
                var _this = this;
                _this.edit(_this.editId);
            }

    }
</script>
