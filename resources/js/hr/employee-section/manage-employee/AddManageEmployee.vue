<template>
    <div>
        <!--begin::Form-->

        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >

            <div class="m-portlet__body">
                <!--begin: Form Wizard Step 1-->
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
                    <div class="form-group m-form__group row">
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
                    </div>
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
                        <label for="fingerprint_no" class="col-lg-2 col-form-label">Fingerprint No <span class="requiredField"></span></label>
                        <div class="col-lg-4">
                            <input type="text" id="fingerprint_no" v-model="form.fingerprint_no" class="form-control form-control-sm m-input" placeholder="Enter fingerprint no">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('fingerprint_no'))">{{ (errors.hasOwnProperty('fingerprint_no')) ? errors.fingerprint_no[0] :'' }}</div>
                        </div>

                        <label for="emp_id" class="col-lg-2 col-form-label">Employee ID <span class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <input type="text" id="emp_id" v-model="form.emp_id" class="form-control form-control-sm m-input" placeholder="Enter Employee ID">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('emp_id'))">{{ (errors.hasOwnProperty('emp_id')) ? errors.emp_id[0] :'' }}</div>
                        </div>

                        <!--<label for="supervisor" class="col-lg-2 col-form-label">Supervisor </label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="supervisor"  v-model="form.supervisor" data-selectField="supervisor">
                                <option>&#45;&#45;&#45;&#45;Select&#45;&#45;&#45;&#45;</option>

                            </select>
                        </div>-->
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

                        <label for="work_shift_id" class="col-lg-2 col-form-label">Work Shift</label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id="work_shift_id"  v-model="form.work_shift_id" data-selectField="work_shift_id">
                                <option v-for="(value,index) in shift_list" :value="value.id"> {{value.shift_name}}</option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('work_shift_id'))">{{ (errors.hasOwnProperty('work_shift_id')) ? errors.work_shift_id[0] :'' }}</div>
                        </div>
                    </div>



                    <div class="form-group m-form__group row">
                        <label for="emp_email" class="col-lg-2 col-form-label">Email <span class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <input type="text" id="emp_email" v-model="form.emp_email" class="form-control form-control-sm m-input" placeholder="example@gmail.com">
                            <div class="requiredField" v-if="(errors.hasOwnProperty('emp_email'))">{{ (errors.hasOwnProperty('emp_email')) ? errors.emp_email[0] :'' }}</div>
                        </div>
                        <label for="phone" class="col-lg-2 col-form-label">Phone </label>
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
                        <label for="date_of_birth" class="col-lg-2 col-form-label">Date Of Birth</label>
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

                        <label for="team_id" class="col-lg-2 col-form-label"> Team</label>
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
                                <input type="file" class="custom-file-input m-input Image" id="photo"  autocomplete="off" @change="onFileChange">
                                <label class="custom-file-label" for="photo">Choose file</label>
                            </div>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('photo'))">{{ (errors.hasOwnProperty('photo')) ? errors.photo[0] :'' }}</div>
                        </div>
                    </div>
                </div>
                <!--end: Form Wizard Step 1-->


                <!--begin: Form Wizard Step 2-->
                <!--<div class="m-wizard__form-step" id="m_wizard_form_step_2">
                    <div class="mb-3 form-group m-form__group row heading">
                        <span class="font-weight-bold text-uppercase">Education Experience</span>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="" class="col-lg-2 col-form-label">Institute<span class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id=""  v-model="form.category_id" data-selectField="category_id">
                                <option>&#45;&#45;&#45;&#45;Select Category&#45;&#45;&#45;&#45;</option>
                                <option v-for="(value,index) in category_list" :value="value.id"> {{value.product_category_name}}</option>
                            </select>
                        </div>
                        <label for="product_code" class="col-lg-2 col-form-label">Board/University Name </label>
                        <div class="col-lg-4">
                            <input type="text" id="product_code" v-model="form.product_code" class="form-control form-control-sm m-input" placeholder="Enter product code">
                        </div>
                    </div>

                    <div class="form-group m-form__group row">
                        <label for="" class="col-lg-2 col-form-label">Degree<span class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <input type="text" id="product_code" v-model="form.product_code" class="form-control form-control-sm m-input" placeholder="Enter product code">
                        </div>
                        <label for="product_code" class="col-lg-2 col-form-label">Passing Year </label>
                        <div class="col-lg-4">
                            <input type="text" id="product_code" v-model="form.product_code" class="form-control form-control-sm m-input" placeholder="Enter product code">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="" class="col-lg-2 col-form-label">Result<span class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <select class="form-control select2" id=""  v-model="form.category_id" data-selectField="category_id">
                                <option>&#45;&#45;&#45;&#45;Select Category&#45;&#45;&#45;&#45;</option>
                                <option v-for="(value,index) in category_list" :value="value.id"> {{value.product_category_name}}</option>
                            </select>
                        </div>
                        <label for="product_code" class="col-lg-2 col-form-label">GPA/CGPA</label>
                        <div class="col-lg-4">
                            <input type="text" id="product_code" v-model="form.product_code" class="form-control form-control-sm m-input" placeholder="Enter product code">
                        </div>
                    </div>
                    <div class="form-group m-form__group row pull-right">
                        <div class="col-lg-12">
                            <a href="javascript:void(0)"  @click="addNewItem" class="btn btn-outline-success m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Add More">
                                <i class="la la-plus"></i>
                            </a>
                            <a  @click="removeItem(index)" class="btn btn-outline-danger m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                        </div>
                    </div>
                </div>-->
                <!--end: Form Wizard Step 2-->
                <div class="mt-5"></div>

                <!--begin: Form Wizard Step 3-->
               <!-- <div class="m-wizard__form-step" id="m_wizard_form_step_3">
                    <div class="mb-3 form-group m-form__group row heading">
                        <span class="font-weight-bold text-uppercase">Professional Experience</span>
                    </div>

                    <div class="form-group m-form__group row">
                        <label for="" class="col-lg-2 col-form-label">Organization Name<span class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <input type="text" id="product_code" v-model="form.product_code" class="form-control form-control-sm m-input" placeholder="Enter product code">
                        </div>
                        <label for="product_code" class="col-lg-2 col-form-label">Designation</label>
                        <div class="col-lg-4">
                            <input type="text" id="product_code" v-model="form.product_code" class="form-control form-control-sm m-input" placeholder="Enter product code">
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="" class="col-lg-2 col-form-label">From Date<span class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <div class="input-group date">
                                <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.po_receive_date" data-dateField="po_receive_date" readonly placeholder="Select date from picker" id="m_datepicker_2" />
                                <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                                </div>
                            </div>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('po_receive_date'))">{{ (errors.hasOwnProperty('po_receive_date')) ? errors.po_receive_date[0] :'' }}</div>
                        </div>
                        <label for="product_code" class="col-lg-2 col-form-label">To Date</label>
                        <div class="col-lg-4">
                            <div class="input-group date">
                                <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.po_receive_date" data-dateField="po_receive_date" readonly placeholder="Select date from picker" id="m_datepicker_2" />
                                <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                                </div>
                            </div>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('po_receive_date'))">{{ (errors.hasOwnProperty('po_receive_date')) ? errors.po_receive_date[0] :'' }}</div>
                        </div>
                    </div>
                    <div class="form-group m-form__group row">
                        <label for="" class="col-lg-2 col-form-label">Responsibility<span class="requiredField">*</span></label>
                        <div class="col-lg-4">
                            <input type="text" id="product_code" v-model="form.product_code" class="form-control form-control-sm m-input" placeholder="Enter product code">
                        </div>
                        <label for="product_code" class="col-lg-2 col-form-label">Skill</label>
                        <div class="col-lg-4">
                            <textarea class="form-control form-control-sm" id="na" v-model="form.product_description" placeholder="" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="form-group m-form__group row pull-right">
                        <div class="col-lg-12">
                            <a href="javascript:void(0)"  @click="addNewItem" class="btn btn-outline-success m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Add More">
                                <i class="la la-plus"></i>
                            </a>
                            <a  @click="removeItem(index)" class="btn btn-outline-danger m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                        </div>
                    </div>
                </div>-->
                <!--end: Form Wizard Step 3-->
                <div class="mt-5"></div>

                <!--begin: Form Wizard Step 4-->
                <!--<div class="m-wizard__form-step" id="m_wizard_form_step_4">

                        <div class="mb-3 form-group m-form__group row heading">
                            <span class="font-weight-bold text-uppercase">Nominee Information</span>
                        </div>

                        <div class="form-group m-form__group row">
                            <label for="" class="col-lg-2 col-form-label">Nominee Name<span class="requiredField">*</span></label>
                            <div class="col-lg-4">
                                <input type="text" id="product_code" v-model="form.product_code" class="form-control form-control-sm m-input" placeholder="Enter product code">
                            </div>
                            <label for="product_code" class="col-lg-2 col-form-label">Email</label>
                            <div class="col-lg-4">
                                <input type="text" id="product_code" v-model="form.product_code" class="form-control form-control-sm m-input" placeholder="Enter product code">
                            </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label for="" class="col-lg-2 col-form-label">Mobile<span class="requiredField">*</span></label>
                            <div class="col-lg-4">
                                <input type="text" id="product_code" v-model="form.product_code" class="form-control form-control-sm m-input" placeholder="Enter product code">
                            </div>
                            <label for="product_code" class="col-lg-2 col-form-label">Phone</label>
                            <div class="col-lg-4">
                                <input type="text" id="product_code" v-model="form.product_code" class="form-control form-control-sm m-input" placeholder="Enter product code">
                            </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label for="" class="col-lg-2 col-form-label">Date Of Birth<span class="requiredField">*</span></label>
                            <div class="col-lg-4">
                                <div class="input-group date">
                                    <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.po_receive_date" data-dateField="po_receive_date" readonly placeholder="Select date from picker" id="m_datepicker_2" />
                                    <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                                    </div>
                                </div>
                                <div class="requiredField" v-if="(errors.hasOwnProperty('po_receive_date'))">{{ (errors.hasOwnProperty('po_receive_date')) ? errors.po_receive_date[0] :'' }}</div>
                            </div>
                            <label for="product_code" class="col-lg-2 col-form-label">Occupation</label>
                            <div class="col-lg-4">
                                <input type="text" id="product_code" v-model="form.product_code" class="form-control form-control-sm m-input" placeholder="Enter product code">
                            </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label for="" class="col-lg-2 col-form-label">Ownership<span class="requiredField">*</span></label>
                            <div class="col-lg-4">
                                <input type="text" id="product_code" v-model="form.product_code" class="form-control form-control-sm m-input" placeholder="Enter product code">
                            </div>
                            <label for="product_code" class="col-lg-2 col-form-label">Relation</label>
                            <div class="col-lg-4">
                                <select class="form-control select2" id=""  v-model="form.category_id" data-selectField="category_id">
                                    <option>&#45;&#45;&#45;&#45;Select Category&#45;&#45;&#45;&#45;</option>
                                    <option v-for="(value,index) in category_list" :value="value.id"> {{value.product_category_name}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group m-form__group row">
                            <label for="" class="col-lg-2 col-form-label">Signature<span class="requiredField">*</span></label>
                            <div class="col-lg-4">
                                <div class="input-group">
                                    <input type="file" class="custom-file-input m-input" id="customFile" autocomplete="off" @change="onFileChange">
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                </div>
                            </div>
                            <label for="product_code" class="col-lg-2 col-form-label">Photo</label>
                            <div class="col-lg-4">
                                <div class="input-group">
                                    <input type="file" class="custom-file-input m-input" id="customFile" autocomplete="off" @change="onFileChange">
                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                </div>
                            </div>
                      </div>
                </div>-->
                <!--end: Form Wizard Step 4-->
                <div class="mt-5"></div>
            </div>

            <!--begin: Form Actions -->
            <div class="m-portlet__foot m-portlet__foot--fit m--margin-top-40">
                <div class="m-form__actions m-form__actions">
                    <div class="row">
                        <div class="col-lg-2"></div>
                        <!--<div class="col-lg-4 m&#45;&#45;align-left">
                            <button class="btn btn-secondary m-btn m-btn&#45;&#45;custom m-btn&#45;&#45;icon" data-wizard-action="prev">
                                <span>
                                    <i class="la la-arrow-left"></i>&nbsp;&nbsp;
                                    <span>Back</span>
                                </span>
                            </button>
                        </div>-->
                        <div class="col-lg-8 m--align-right">
                            <button type="submit" class="btn btn-primary m-btn m-btn--custom m-btn--icon" data-wizard-action="submit">
                                <span>
                                    <i class="la la-check"></i>&nbsp;&nbsp;
                                    <span>Submit</span>
                                </span>
                            </button>
                            <!--<button class="btn btn-warning m-btn m-btn&#45;&#45;custom m-btn&#45;&#45;icon" data-wizard-action="next">
                                <span>
                                    <span>Save & Continue</span>&nbsp;&nbsp;
                                    <i class="la la-arrow-right"></i>
                                </span>
                            </button>-->
                        </div>
                        <div class="col-lg-2"></div>
                    </div>
                </div>
            </div>

            <!--end: Form Actions -->

            <!--<div class="m-portlet__foot m-portlet__no-border m-portlet__foot&#45;&#45;fit">
                <div class="m-form__actions m-form__actions&#45;&#45;solid">
                    <div class="row">
                        <div class="col-lg-12 m&#45;&#45;align-right">
                            <button type="reset" class="btn btn-sm btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save and Go List</span></button>

                        </div>
                    </div>
                </div>
            </div>-->
        </form>
        <!--end::Form-->
    </div>
</template>

<script>
    import { EventBus } from '../../../vue-assets';

    export default {

        props:['role_list','department_list','designation_list','branch_list','monthly_pay_grade_list',
            'hourly_pay_grade_list','shift_list','type_list','team_list','religion_list','grade_list','salary_configuration_list'],

        data:function(){
            return{

                product_list:false,
                add_form:true,
                edit_form:false,



                form:{
                    role_id: '',
                    user_name: '',
                    password: '',
                    confirm_password: '',
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
                    status:''
                },
                errors: {},
            };
        },

        methods:{
            store:function(){
                var _this = this;
                axios.post(base_url+'manage-employee', this.form).then( (response) => {
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

            onFileChange(e) {
                let files = e.target.files || e.dataTransfer.files;

                if (!files.length) {
                    return;
                }

                this.createImage(files[0]);
            },

            createImage(file) {
                let reader = new FileReader();
                var _this = this;

                reader.onload = (e) => {
                    _this.form.photo = e.target.result;
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

            $('.Image').val('');

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


        },

        created(){
           var _this =this;
           _this.grossCalculation();
           _this.complianceCalculation();
        }

    }
</script>
