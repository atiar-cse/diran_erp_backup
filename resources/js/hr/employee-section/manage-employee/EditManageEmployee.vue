<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right"  >
            <div class="m-portlet__body">
                <div class="m-wizard__form-step m-wizard__form-step--current" id="m_wizard_form_step_1">
                    <div class="mb-3 form-group m-form__group row heading">
                        <span class="font-weight-bold text-uppercase">Employee Account :</span>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="role_id" class="col-lg-2 col-form-label">Role <span class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="role_id"  v-model="form.role_id" data-selectField="role_id">
                                <option>----Select----</option>
                                <option v-for="(value,index) in role_list" :value="value.id"> {{value.role_name}}</option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('role_id'))">{{ (errors.hasOwnProperty('role_id')) ? errors.role_id[0] :'' }}</div>
                        </div>
                        <label for="user_name" class="col-lg-2 col-form-label">User Name <span class="requiredField">*</span> </label>
                        <div class="col-lg-4">
                            <input type="text" id="user_name" v-model="form.user_name" class="form-control form-control-sm m-input" placeholder="Enter user name">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('user_name'))">{{ (errors.hasOwnProperty('user_name')) ? errors.user_name[0] :'' }}</div>
                        </div>
                    </div>
                    <!--<div class="form-group m-form__group row">
                        <label for="password" class="col-lg-2 col-form-label">Password </label>
                        <div class="col-lg-4">
                            <input type="text" id="password" v-model="form.password" class="form-control form-control-sm m-input" placeholder="Enter password">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('password'))">{{ (errors.hasOwnProperty('password')) ? errors.password[0] :'' }}</div>
                        </div>

                        <label for="confirm_password" class="col-lg-2 col-form-label">Password Confirmation </label>
                        <div class="col-lg-4">
                            <input type="text" id="confirm_password" v-model="form.confirm_password" class="form-control form-control-sm m-input" placeholder="Enter confirm password">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('confirm_password'))">{{ (errors.hasOwnProperty('confirm_password')) ? errors.confirm_password[0] :'' }}</div>
                        </div>
                    </div>-->
                    <div class="mt-5"></div>

                    <div class="mb-3 form-group m-form__group row heading">
                        <span class="font-weight-bold text-uppercase">Personal Information :</span>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="first_name" class="col-lg-2 col-form-label">First Name<span class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <input type="text" id="first_name" v-model="form.first_name" class="form-control form-control-sm m-input" placeholder="Enter first name">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('first_name'))">{{ (errors.hasOwnProperty('first_name')) ? errors.first_name[0] :'' }}</div>
                        </div>
                        <label for="last_name" class="col-lg-2 col-form-label">Last Name </label>
                        <div class="col-lg-4">
                            <input type="text" id="last_name" v-model="form.last_name" class="form-control form-control-sm m-input" placeholder="Enter last name">
                        </div>
                    </div>

                    <div class="form-group m-form__group row">
                        <label for="fingerprint_no" class="col-lg-2 col-form-label">Fingerprint No </label>
                        <div class="col-lg-4">
                            <input type="text" id="fingerprint_no" v-model="form.fingerprint_no" class="form-control form-control-sm m-input" placeholder="Enter fingerprint no">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('fingerprint_no'))">{{ (errors.hasOwnProperty('fingerprint_no')) ? errors.fingerprint_no[0] :'' }}</div>
                        </div>

                        <label for="emp_id" class="col-lg-2 col-form-label">Employee ID <span class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <input type="text" id="emp_id" v-model="form.emp_id" class="form-control form-control-sm m-input" placeholder="Enter Employee ID">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('emp_id'))">{{ (errors.hasOwnProperty('emp_id')) ? errors.emp_id[0] :'' }}</div>
                        </div>
                    </div>

                    <div class="form-group m-form__group row">
                        <label for="department_id" class="col-lg-2 col-form-label">Department Name </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="department_id"  v-model="form.department_id" data-selectField="department_id">
                                <option v-for="(value,index) in department_list" :value="value.id"> {{value.department_name}}</option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('department_id'))">{{ (errors.hasOwnProperty('department_id')) ? errors.department_id[0] :'' }}</div>
                        </div>

                        <label for="designation_id" class="col-lg-2 col-form-label">Designation Name <span class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="designation_id"  v-model="form.designation_id" data-selectField="designation_id">
                                <option v-for="(value,index) in designation_list" :value="value.id"> {{value.designation_name}}</option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('designation_id'))">{{ (errors.hasOwnProperty('designation_id')) ? errors.designation_id[0] :'' }}</div>
                        </div>
                    </div>

                    <div class="form-group m-form__group row">
                        <label for="branch_id" class="col-lg-2 col-form-label">Unit Name </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="branch_id"  v-model="form.branch_id" data-selectField="branch_id">
                                <option v-for="(value,index) in branch_list" :value="value.id"> {{value.branch_name}}</option>
                            </select>
                        </div>

                        <label for="work_shift_id" class="col-lg-2 col-form-label">Work Shift </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="work_shift_id"  v-model="form.work_shift_id" data-selectField="work_shift_id">
                                <option v-for="(value,index) in shift_list" :value="value.id"> {{value.shift_name}}</option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('work_shift_id'))">{{ (errors.hasOwnProperty('work_shift_id')) ? errors.work_shift_id[0] :'' }}</div>
                        </div>
                    </div>



                    <div class="form-group m-form__group row">
                        <label for="emp_email" class="col-lg-2 col-form-label">Email <span class="requiredField">*</span> </label>
                        <div class="col-lg-4">
                            <input type="text" id="emp_email" v-model="form.emp_email" class="form-control form-control-sm m-input" placeholder="example@gmail.com">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('emp_email'))">{{ (errors.hasOwnProperty('emp_email')) ? errors.emp_email[0] :'' }}</div>
                        </div>
                        <label for="phone" class="col-lg-2 col-form-label">Phone <span class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <input type="text" id="phone" v-model="form.phone" class="form-control form-control-sm m-input" placeholder="Enter phone">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('phone'))">{{ (errors.hasOwnProperty('phone')) ? errors.phone[0] :'' }}</div>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="gender" class="col-lg-2 col-form-label">Gender </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="gender"  v-model="form.gender" data-selectField="gender">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Others">Others</option>

                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('gender'))">{{ (errors.hasOwnProperty('gender')) ? errors.gender[0] :'' }}</div>
                        </div>
                        <label for="religion_id" class="col-lg-2 col-form-label"> Religion</label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="religion_id"  v-model="form.religion_id" data-selectField="religion_id">
                                <option v-for="(value,index) in religion_list" :value="value.id"> {{value.name}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group m-form__group row">
                        <label for="date_of_birth" class="col-lg-2 col-form-label">Date Of Birth </label>
                        <div class="col-lg-4">
                            <div class="input-group date">
                                <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.date_of_birth" data-dateField="date_of_birth" readonly placeholder="Select date from picker" id="date_of_birth" />
                                <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                                </div>
                            </div>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('date_of_birth'))">{{ (errors.hasOwnProperty('date_of_birth')) ? errors.date_of_birth[0] :'' }}</div>
                        </div>
                        <label for="date_of_joining" class="col-lg-2 col-form-label"> Date Of Joining </label>
                        <div class="col-lg-4">
                            <div class="input-group date">
                                <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.date_of_joining" data-dateField="date_of_joining" readonly placeholder="Select date from picker" id="date_of_joining" />
                                <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                                </div>
                            </div>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('date_of_joining'))">{{ (errors.hasOwnProperty('date_of_joining')) ? errors.date_of_joining[0] :'' }}</div>
                        </div>
                    </div>

                    <div class="form-group m-form__group row">

                        <label for="marital_status" class="col-lg-2 col-form-label"> Marital Status </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="marital_status"  v-model="form.marital_status" data-selectField="marital_status">
                                <option value="Married">Married</option>
                                <option value="Unmarried">Unmarried</option>
                                <option value="Single">Single</option>

                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('marital_status'))">{{ (errors.hasOwnProperty('marital_status')) ? errors.marital_status[0] :'' }}</div>
                        </div>

                        <label for="employee_type_id" class="col-lg-2 col-form-label">Emp Type </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="employee_type_id"  v-model="form.employee_type_id" data-selectField="employee_type_id">
                                <option v-for="(value,index) in type_list" :value="value.id"> {{value.name}}</option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('employee_type_id'))">{{ (errors.hasOwnProperty('employee_type_id')) ? errors.employee_type_id[0] :'' }}</div>
                        </div>
                    </div>

                    <div class="form-group m-form__group row">

                        <label for="team_id" class="col-lg-2 col-form-label"> Team<span class="requiredField"></span></label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="team_id"  v-model="form.team_id" data-selectField="team_id">
                                <option v-for="(value,index) in team_list" :value="value.id"> {{value.name}}</option>
                            </select>
                        </div>

                        <label for="salary_grade_id" class="col-lg-2 col-form-label">Salary Grade</label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="salary_grade_id"  v-model="form.salary_grade_id" data-selectField="salary_grade_id">
                                <option v-for="(value,index) in grade_list" :value="value.id"> {{value.name}}</option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('salary_grade_id'))">{{ (errors.hasOwnProperty('salary_grade_id')) ? errors.salary_grade_id[0] :'' }}</div>
                        </div>
                    </div>

                    <div class="mb-3 form-group m-form__group row heading">
                        <span class="font-weight-bold text-uppercase">Gross Salary Info :</span>
                    </div>

                    <div class="form-group m-form__group row">
                        <label for="gross_salary" class="col-lg-2 col-form-label">Gross Salary</label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="gross_salary" v-model="form.gross_salary" @input="grossCalculation()" class="form-control form-control-sm m-input" placeholder="Gross Salary">
                        </div>
                        <label for="basic_salary" class="col-lg-2 col-form-label">Basic</label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="basic_salary" v-model="form.basic_salary" class="form-control form-control-sm m-input" placeholder="Basic" readonly>
                        </div>
                    </div>

                    <div class="form-group m-form__group row">

                        <label for="house_rent" class="col-lg-2 col-form-label">House Rent </label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="house_rent" v-model="form.house_rent" class="form-control form-control-sm m-input" placeholder="House Rent" readonly>
                        </div>

                        <label for="medical" class="col-lg-2 col-form-label">Medical</label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="medical" v-model="form.medical" class="form-control form-control-sm m-input" placeholder="Medical" readonly>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">

                        <label for="lunch" class="col-lg-2 col-form-label">Food </label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="lunch" v-model="form.lunch" class="form-control form-control-sm m-input" placeholder="Food" >
                        </div>

                        <label for="transport" class="col-lg-2 col-form-label">Transport</label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="transport" v-model="form.transport" class="form-control form-control-sm m-input" placeholder="Transport" readonly>
                        </div>
                    </div>

                    <div class="mb-3 form-group m-form__group row heading">
                        <span class="font-weight-bold text-uppercase">Compliance Salary Info :</span>
                    </div>

                    <div class="form-group m-form__group row">
                        <label for="compliance_salary" class="col-lg-2 col-form-label">Compliance Salary</label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="compliance_salary" v-model="form.compliance_salary" @input="complianceCalculation()" class="form-control form-control-sm m-input" placeholder="Compliance Salary">
                        </div>
                        <label for="comp_basic_salary" class="col-lg-2 col-form-label">Comp Basic </label>
                        <div class="col-lg-4">
                            <input type="number" step="any"  id="comp_basic_salary" v-model="form.comp_basic_salary" class="form-control form-control-sm m-input" placeholder="Comp Basic" readonly>
                        </div>
                    </div>

                    <div class="form-group m-form__group row">
                        <label for="comp_house_rent" class="col-lg-2 col-form-label">Comp House Rent</label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="comp_house_rent" v-model="form.comp_house_rent" class="form-control form-control-sm m-input" placeholder="Comp House Rent" readonly>
                        </div>
                        <label for="comp_medical" class="col-lg-2 col-form-label">Comp Medical </label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="comp_medical" v-model="form.comp_medical" class="form-control form-control-sm m-input" placeholder="Comp Medical" readonly>
                        </div>
                    </div>

                    <div class="form-group m-form__group row">
                        <label for="comp_lunch" class="col-lg-2 col-form-label">Comp Food</label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="comp_lunch" v-model="form.comp_lunch" class="form-control form-control-sm m-input" placeholder="Comp Food">
                        </div>
                        <label for="comp_transport" class="col-lg-2 col-form-label">Comp Transport </label>
                        <div class="col-lg-4">
                            <input type="number" step="any" id="comp_transport" v-model="form.comp_transport" class="form-control form-control-sm m-input" placeholder="Comp Transport" readonly>
                        </div>
                    </div>


                    <div class="form-group m-form__group row">
                        <label for="emergency_contact" class="col-lg-2 col-form-label">Emergency Contact</label>
                        <div class="col-lg-4">
                            <input type="text" id="emergency_contact" v-model="form.emergency_contact" class="form-control form-control-sm m-input" placeholder="Enter emergency contact">
                        </div>
                        <label for="em_address" class="col-lg-2 col-form-label">Address </label>
                        <div class="col-lg-4">
                            <textarea class="form-control form-control-sm" id="em_address" v-model="form.em_address" placeholder="" rows="2"></textarea>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('em_address'))">{{ (errors.hasOwnProperty('em_address')) ? errors.em_address[0] :'' }}</div>
                        </div>
                    </div>

                    <div class="form-group m-form__group row">

                        <label for="photo" class="col-lg-2 col-form-label">Photo </label>
                        <div class="col-lg-4">
                            <div class="input-group">
                                <input type="file" class="custom-file-input m-input Image" id="photo"  autocomplete="off" @change="onEditFileChange">
                                <label class="custom-file-label" for="photo">Choose file</label>
                            </div>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('photo'))">{{ (errors.hasOwnProperty('photo')) ? errors.photo[0] :'' }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="m-portlet__foot m-portlet__foot--fit m--margin-top-40">
                <div class="m-form__actions m-form__actions">
                    <div class="row">
                        <div class="col-lg-2"></div>
                        <div class="col-lg-8 m--align-right">
                            <button type="submit" class="btn btn-primary m-btn m-btn--custom m-btn--icon" data-wizard-action="submit">
                                <span>
                                    <i class="la la-check"></i>&nbsp;&nbsp;
                                    <span>Update</span>
                                </span>
                            </button>

                        </div>
                        <div class="col-lg-2"></div>
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

        props:['role_list','department_list','designation_list','branch_list','monthly_pay_grade_list','hourly_pay_grade_list','editId','shift_list','type_list','team_list','religion_list','grade_list','salary_configuration_list'],

        data:function(){
            return{

                data_list:false,
                add_form:true,
                edit_form:true,

                form:{
                    role_id: '',
                    user_name: '',
                    //password: '',
                    //confirm_password: '',
                    first_name: '',
                    last_name: '',
                    emp_id: '',
                    fingerprint_no: '',
                    supervisor: '',
                    department_id: '',
                    designation_id: '',
                    branch_id: '',
                    work_shift_id: '',
                    employee_type_id: '',
                    team_id: '',
                    salary_grade_id: '',
                    gross_salary: '',
                    basic_salary: '',
                    house_rent: '',
                    medical: '',
                    transport: '',
                    lunch: '',
                    compliance_salary: '',
                    comp_basic_salary: '',
                    comp_house_rent: '',
                    comp_medical: '',
                    comp_transport: '',
                    comp_lunch: '',
                    emp_email: '',
                    phone: '',
                    gender: '',
                    religion_id: '',
                    report_two_id: '',
                    date_of_birth: '',
                    date_of_joining: '',
                    date_of_leaving: '',
                    emergency_contact: '',
                    em_address: '',
                    marital_status: '',
                    photo: '',
                    status:'1'
                },
                errors: {},
            };
        },

        methods:{

            edit(id) {
                var _this = this;
                axios.get(base_url+'manage-employee/'+id+'/edit')
                    .then( (response) => {
                        _this.form  = response.data.data;
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
            update(id){
                axios.put(base_url+'manage-employee/'+id, this.form).then( (response) => {
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



            onEditFileChange(e) {
                //alert(e.target.files || e.dataTransfer.files);
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0]);
            },
            createImage(file) {
                let reader = new FileReader();
                var _this = this;
                reader.onload = (e) => {
                    this.form.photo = e.target.result;
                };
                reader.readAsDataURL(file);
            },


            grossCalculation(){
                var _this =this;
                var salary_config = _this.salary_configuration_list;
                //console.log(salary_config.gross_salary);
                var gross = _this.form.gross_salary;
                _this.form.basic_salary = gross*(salary_config.basic_salary/100); //tbl key_name:basic_salary:'50'
                _this.form.house_rent   = gross*(salary_config.house_rent/100);   //tbl key_name:house_rent:'25'
                _this.form.medical      = gross*(salary_config.medical/100);     //tbl key_name:medical:'12.5'
                _this.form.transport    = gross*(salary_config.transport/100);   //tbl key_name:transport:'12.5'

            },

            complianceCalculation(){
                var _this =this;
                var salary_config = _this.salary_configuration_list;
                var com_salary = _this.form.compliance_salary;
                _this.form.comp_basic_salary = com_salary*(salary_config.basic_salary/100); //tbl key_name:basic_salary:'50'
                _this.form.comp_house_rent   = com_salary*(salary_config.house_rent/100);   //tbl key_name:house_rent:'25'
                _this.form.comp_medical      = com_salary*(salary_config.medical/100);     //tbl key_name:medical:'12.5'
                _this.form.comp_transport    = com_salary*(salary_config.transport/100);   //tbl key_name:transport:'12.5'

            }
        },



        mounted(){

            var _this = this;

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if( selectField == 'role_id' ){
                    _this.form.role_id= selectedItem.val();
                }else if(selectField == 'department_id'){
                    _this.form.department_id= selectedItem.val();
                }else if(selectField == 'designation_id'){
                    _this.form.designation_id= selectedItem.val();
                }else if(selectField == 'branch_id'){
                    _this.form.branch_id= selectedItem.val();
                }else if(selectField == 'work_shift_id'){
                    _this.form.work_shift_id= selectedItem.val();
                }else if(selectField == 'employee_type_id') {
                    _this.form.employee_type_id = selectedItem.val();
                }else if(selectField == 'team_id') {
                    _this.form.team_id = selectedItem.val();
                }else if(selectField == 'salary_grade_id') {
                    _this.form.salary_grade_id = selectedItem.val();
                }else if(selectField == 'religion_id') {
                    _this.form.religion_id = selectedItem.val();
                }else if(selectField == 'supervisor'){
                    _this.form.supervisor= selectedItem.val();
                }else if(selectField == 'gender'){
                    _this.form.gender= selectedItem.val();
                }else if(selectField == 'report_two_id'){
                    _this.form.report_two_id= selectedItem.val();
                }else if(selectField == 'marital_status'){
                    _this.form.marital_status= selectedItem.val();
                }
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    var selectField = $(ev.currentTarget).attr('data-dateField');

                    if(ev.date == undefined){
                        if (selectField == 'date_of_birth') {
                            _this.form.date_of_birth = '';
                        } else if(selectField == 'date_of_joining'){
                            _this.form.date_of_joining = '';
                        } else if(selectField == 'date_of_leaving'){
                            _this.form.date_of_leaving = '';
                        }
                    }else if(selectField == 'date_of_birth'){
                        _this.form.date_of_birth = moment(ev.date).format('DD-MM-YYYY');
                    }else if(selectField == 'date_of_joining'){
                        _this.form.date_of_joining = moment(ev.date).format('DD-MM-YYYY');
                    }else if(selectField == 'date_of_leaving'){
                        _this.form.date_of_leaving = moment(ev.date).format('DD-MM-YYYY');
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
            _this.grossCalculation();
            _this.complianceCalculation();
        }

    }
</script>
