<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="employee_id" class="col-lg-2 col-form-label">Employee Name<span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" id="employee_id"  v-model="form.employee_id" data-selectField="employee_id">
                            <option v-for="(evalue,eindex) in employee_lists"
                                    :value="evalue.id"
                                    :emp_department="evalue.emp_department ? evalue.emp_department.department_name : ''"
                                    :current_department_id="evalue.emp_department ? evalue.emp_department.id : ''"
                                    :emp_designation="evalue.emp_designation ? evalue.emp_designation.designation_name : ''"
                                    :current_designation_id="evalue.emp_designation ? evalue.emp_designation.id : ''"
                                    :get_pay_grade="evalue.get_pay_grade ? evalue.get_pay_grade.pay_grade_name : ''"
                                    :current_pay_grade_id="evalue.get_pay_grade ? evalue.get_pay_grade.id : ''"
                                    :get_salary="evalue.get_salary ? evalue.get_salary.gross_salary : ''"
                                    :current_salary="evalue.get_salary ? evalue.get_salary.gross_salary : ''"
                            > {{evalue.first_name +' '+ evalue.last_name}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('employee_id'))">{{ (errors.hasOwnProperty('employee_id')) ? errors.employee_id[0] :'' }}</div>
                    </div>
                    <label for="current_department_display" class="col-lg-2 col-form-label">Current Department<span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="current_department_display" v-model="form.current_department_display" class="form-control form-control-sm m-input" placeholder="Department" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('current_department_display'))">{{ (errors.hasOwnProperty('current_department_display')) ? errors.current_department_display[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="current_designation_display">Current Designation <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="current_designation_display" v-model="form.current_designation_display" class="form-control form-control-sm m-input" placeholder="Designation" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('current_designation_display'))">{{ (errors.hasOwnProperty('current_designation_display')) ? errors.current_designation_display[0] :'' }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="current_pay_grade_display">Current Pay Grade <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="current_pay_grade_display" v-model="form.current_pay_grade_display" class="form-control form-control-sm m-input" placeholder="Pay Grade" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('current_pay_grade_display'))">{{ (errors.hasOwnProperty('current_pay_grade_display')) ? errors.current_pay_grade_display[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="current_salary_display" class="col-lg-2 col-form-label">Current Salary <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="current_salary_display" v-model="form.current_salary_display" class="form-control form-control-sm m-input" placeholder="Salary" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('current_salary_display'))">{{ (errors.hasOwnProperty('current_salary_display')) ? errors.current_salary_display[0] :'' }}</div>
                    </div>
                    <label for="promoted_pay_grade" class="col-lg-2 col-form-label">Promoted Pay Grade <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" id="promoted_pay_grade"  v-model="form.promoted_pay_grade" data-selectField="promoted_pay_grade">
                            <option>----Select----</option>
                            <option v-for="(value,index) in paygrade_lists" :value="value.id" :gross_salary="value.gross_salary"> {{value.pay_grade_name}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('promoted_pay_grade'))">{{ (errors.hasOwnProperty('promoted_pay_grade')) ? errors.promoted_pay_grade[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="new_salary" class="col-lg-2 col-form-label">New Salary <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="new_salary" v-model="form.new_salary" class="form-control form-control-sm m-input" readonly placeholder="New salary">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('new_salary'))">{{ (errors.hasOwnProperty('new_salary')) ? errors.new_salary[0] :'' }}</div>
                    </div>
                    <label for="promoted_department" class="col-lg-2 col-form-label">Promoted Department <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" id="promoted_department"  v-model="form.promoted_department" data-selectField="promoted_department">
                            <option>----Select----</option>
                            <option v-for="(value,index) in department_lists" :value="value.id"> {{value.department_name}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('promoted_department'))">{{ (errors.hasOwnProperty('promoted_department')) ? errors.promoted_department[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label for="promoted_designation" class="col-lg-2 col-form-label">Promoted Designation <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" id="promoted_designation"  v-model="form.promoted_designation" data-selectField="promoted_designation">
                            <option>----Select----</option>
                            <option v-for="(value,index) in designation_lists" :value="value.id"> {{value.designation_name}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('promoted_designation'))">{{ (errors.hasOwnProperty('promoted_designation')) ? errors.promoted_designation[0] :'' }}</div>
                    </div>
                    <label for="promotion_date" class="col-lg-2 col-form-label">Promoted Date <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.promotion_date" data-dateField="promotion_date" readonly placeholder="Select date from picker" id="promotion_date" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('promotion_date'))">{{ (errors.hasOwnProperty('promotion_date')) ? errors.promotion_date[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label for="narration" class="col-lg-2 col-form-label">Narration</label>
                    <div class="col-lg-4">
                        <textarea class="form-control form-control-sm" id="narration" v-model="form.narration" placeholder="" rows="2"></textarea>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('narration'))">{{ (errors.hasOwnProperty('narration')) ? errors.narration[0] :'' }}</div>
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
            </div>
        </form>
        <!--end::Form-->
    </div>
</template>

<script>
    import { EventBus } from '../../../vue-assets';

    export default {

        props:['permissions','employee_lists','paygrade_lists','department_lists','designation_lists'],

        data:function(){
            return{

                product_list:false,
                add_form:true,
                edit_form:false,

                form:{
                    employee_id: '',
                    current_department_id: '',
                    current_designation_id: '',
                    current_pay_grade_id: '',
                    current_salary: '',
                    promoted_pay_grade: '',
                    new_salary: '',
                    promoted_department: '',
                    promoted_designation: '',
                    promotion_date: '',
                    narration: '',
                    status:'1',
                    current_department_display: '',
                    current_designation_display: '',
                    current_pay_grade_display: '',
                    current_salary_display: '',
                },
                errors: {},
            };
        },

        methods:{
            store:function(){
                var _this = this;
                axios.post(base_url+'promotion', this.form).then( (response) => {
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

                if( selectField == 'employee_id' ){

                    _this.form.employee_id= selectedItem.val();

                    _this.form.current_department_id  = $('option:selected', this).attr('current_department_id');
                    _this.form.current_designation_id = $('option:selected', this).attr('current_designation_id');
                    _this.form.current_pay_grade_id   = $('option:selected', this).attr('current_pay_grade_id');
                    _this.form.current_salary         = $('option:selected', this).attr('current_salary');


                    _this.form.current_department_display   = $('option:selected', this).attr('emp_department');
                    _this.form.current_designation_display  = $('option:selected', this).attr('emp_designation');
                    _this.form.current_pay_grade_display    = $('option:selected', this).attr('get_pay_grade');
                    _this.form.current_salary_display       = $('option:selected', this).attr('get_salary');

                }else if(selectField == 'promoted_department'){
                    _this.form.promoted_department= selectedItem.val();
                }else if(selectField == 'promoted_designation'){
                    _this.form.promoted_designation= selectedItem.val();
                }else if(selectField == 'promoted_pay_grade'){
                    _this.form.promoted_pay_grade= selectedItem.val();
                     var gross_salary = $('option:selected', this).attr('gross_salary');
                    _this.form.new_salary = gross_salary;
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
                        _this.form.promotion_date = '';

                    }else {
                        _this.form.promotion_date = moment(ev.date).format('DD-MM-YYYY');

                    }

                });
            });


        },

        created(){

        }

    }
</script>