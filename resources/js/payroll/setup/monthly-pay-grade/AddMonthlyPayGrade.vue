<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="pay_grade_name" class="col-lg-2 col-form-label">Pay Grade Name <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="pay_grade_name" v-model="form.pay_grade_name" class="form-control form-control-sm m-input" placeholder="Enter pay grade name">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('pay_grade_name'))">{{ (errors.hasOwnProperty('pay_grade_name')) ? errors.pay_grade_name[0] :'' }}</div>
                    </div>
                    <label for="gross_salary" class="col-lg-2 col-form-label">Gross Salary <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="gross_salary" v-model="form.gross_salary" class="form-control form-control-sm m-input" placeholder="Enter gross salary">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('gross_salary'))">{{ (errors.hasOwnProperty('gross_salary')) ? errors.gross_salary[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label for="percentage_of_basic" class="col-lg-2 col-form-label">Percentage Of Basic <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="percentage_of_basic" v-model="form.percentage_of_basic" class="form-control form-control-sm m-input" placeholder="10%">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('percentage_of_basic'))">{{ (errors.hasOwnProperty('percentage_of_basic')) ? errors.percentage_of_basic[0] :'' }}</div>
                    </div>
                    <label for="basic_salary" class="col-lg-2 col-form-label">Basic Salary <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="basic_salary" v-model="form.basic_salary" class="form-control form-control-sm m-input" placeholder="Enter basic salary">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('basic_salary'))">{{ (errors.hasOwnProperty('basic_salary')) ? errors.basic_salary[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="over_time_rate" class="col-lg-2 col-form-label">Overtime Rate (Per Hour)</label>
                    <div class="col-lg-4">
                        <input type="text" id="over_time_rate" v-model="form.over_time_rate" class="form-control form-control-sm m-input" placeholder="Enter over time rate">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('over_time_rate'))">{{ (errors.hasOwnProperty('over_time_rate')) ? errors.over_time_rate[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label  class="col-lg-2 col-form-label">Allowance </label>
                    <div class="col-lg-4">
                        <div class="m-checkbox-list">
                            <label class="m-checkbox m-checkbox--success">
                                <input type="checkbox"  id="selectAllowAll" > Select All
                                <span></span>
                            </label>
                            <span v-for="allowance in allowance_lists">
                                <label class="m-checkbox m-checkbox--success">
                                    <input class="alwnsAllChackbox" type="checkbox" :value="allowance.id"  v-model="form.selectedAllowance"> {{allowance.allowance_name}}
                                    <span></span>
                                </label>
                            </span>
                        </div>

                    </div>
                    <label class="col-lg-2 col-form-label">Deduction</label>
                    <div class="col-lg-4">
                        <div class="m-checkbox-list">
                            <label class="m-checkbox m-checkbox--success">
                                <input type="checkbox" id="selectDectAll"> Select All
                                <span></span>
                            </label>
                            <span v-for="deduction in deduction_lists">
                                <label class="m-checkbox m-checkbox--success">
                                    <input class="deductAllCheckbox" type="checkbox" :value="deduction.id" v-model="form.selectedDeduction"> {{deduction.deduction_name}}
                                    <span></span>
                                </label>
                            </span>

                        </div>

                    </div>
                </div>
            </div>
            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="reset" class="btn btn-sm btn-secondary">Cancel</button>
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

        props:['permissions','allowance_lists','deduction_lists'],

        data:function(){
            return{

                product_list:false,
                add_form:true,
                edit_form:false,

                form:{
                    pay_grade_name: '',
                    gross_salary: '',
                    percentage_of_basic: '',
                    basic_salary: '',
                    over_time_rate: '',
                    status:'1',
                    selectedAllowance:[],
                    selectedDeduction:[],
                },
                errors: {},
            };
        },

        methods:{

            store:function(){
                var _this = this;
                axios.post(base_url+'monthly-pay-grade', this.form).then( (response) => {
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

            $(document).on("click", '#selectAllowAll', function (event) {
                if (this.checked) {
                    $('.alwnsAllChackbox').each(function () {
                        this.checked = true;
                    });

                } else {
                    $('.alwnsAllChackbox').each(function () {
                        this.checked = false;
                    });
                }
              });

            $(document).on("click", '#selectDectAll', function (event) {
                if (this.checked) {
                    $('.deductAllCheckbox ').each(function () {
                        this.checked = true;
                    });
                 } else {
                    $('.deductAllCheckbox').each(function () {
                        this.checked = false;
                    });
                }
            });

        },

        created(){

        }

    }
</script>