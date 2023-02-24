<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="payroll_salary_month" class="col-lg-2 col-form-label">Month <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="payroll_salary_month"  class="form-control form-control-sm m-input datepicker" v-model="form.payroll_salary_month" data-dateField="payroll_salary_month" readonly style="width: 100%"  placeholder="Select month" autocomplete="off" />

                        <div class="requiredField" v-if="(errors.hasOwnProperty('payroll_salary_month'))">{{ (errors.hasOwnProperty('payroll_salary_month')) ? errors.payroll_salary_month[0] :'' }}</div>
                    </div>
                    <label for="payable_total_salary" class="col-lg-2 col-form-label">Total Salary <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="payable_total_salary" v-model="form.payable_total_salary" class="form-control form-control-sm m-input" placeholder="Total salary" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('payable_total_salary'))">{{ (errors.hasOwnProperty('payable_total_salary')) ? errors.payable_total_salary[0] :'' }}</div>
                    </div>
                </div>

                <div class="m-form__heading">
                    <h3 class="m-form__heading-title">Accounts <i data-toggle="m-tooltip" data-width="auto" class="m-form__heading-help-icon flaticon-info" title="" data-original-title="If different than the corresponding address"></i> <small><mark><i> <!--Journal will hit once LC approved-->xyz. </i></mark></small> </h3>

                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="debit_account_id">Debit Account <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2"  id="debit_account_id" v-model="form.debit_account_id" data-selectField="debit_account_id">
                            <option v-for="(value,index) in account_list" :value="value.id"> {{ value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('debit_account_id'))">{{ (errors.hasOwnProperty('debit_account_id')) ? errors.debit_account_id[0] :'' }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="credit_account_id">Credit Account <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2"  id="credit_account_id" v-model="form.credit_account_id" data-selectField="credit_account_id">
                            <option v-for="(value,index) in account_list" :value="value.id"> {{ value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('credit_account_id'))">{{ (errors.hasOwnProperty('credit_account_id')) ? errors.credit_account_id[0] :'' }}</div>
                    </div>
                </div>
            </div>
            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="submit"  class="btn btn-sm btn-success" @click.prevent="store(0)" ><i class="la la-check"></i> <span>Save and Go List</span></button>
                            <button  type="submit" v-if="permissions.indexOf('payroll-salary-summery.approve') !=-1" class="btn btn-sm btn-success" @click.prevent="store(1)"><i class="la la-check"></i> <span>Approve</span></button>
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

        props:['permissions','account_list'],

        data:function(){
            return{

                product_list:false,
                add_form:true,
                edit_form:false,

                form:{
                    payroll_salary_month: '',
                    payable_total_salary: '',
                    approve:0,

                    amount:'',
                    debit_account_id: '',
                    credit_account_id: '',


                },
                errors: {},
            };
        },

        methods:{

            calculateTotalSalary(){
                var _this = this;
                axios.get(base_url+'calculate-total-salary')
                    .then( (response) => {
                        _this.form.payable_total_salary = response.data;
                        _this.form.amount = response.data;
                    });
            },

            store:function(approval){
                var _this = this;
                _this.form.approve = approval;

                //var result = confirm("Are you sure?");

                axios.post(base_url+'payroll-salary-summery', this.form).then( (response) => {
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

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if(selectField == 'debit_account_id'){
                    _this.form.debit_account_id= selectedItem.val();
                }else if(selectField == 'credit_account_id'){
                    _this.form.credit_account_id= selectedItem.val();
                }
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: "yyyy-mm-dd",
                    viewMode: "months",
                    minViewMode: 1,
                    clearBtn: true,

                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    var selectField = $(ev.currentTarget).attr('data-dateField');

                    if(ev.date == undefined){
                        if (selectField == 'payroll_salary_month') {
                            _this.form.payroll_salary_month = '';

                        }

                    }else if(selectField == 'payroll_salary_month'){
                        _this.form.payroll_salary_month = moment(ev.date).format('Y-MM-01');
                       // _this.calculateTotalSalary();


                    }

                });
            });



        },

        created(){

            var _this = this;
           _this.calculateTotalSalary();
            _this.form.payroll_salary_month = moment().format('Y-MM-01');

        }

    }
</script>