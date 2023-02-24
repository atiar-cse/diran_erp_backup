<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="employee_id" class="col-lg-2 col-form-label">Employee Name <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" id="employee_id"  v-model="form.employee_id" data-selectField="employee_id" @change ="currentBalance()">
                            <option v-for="(value,index) in employee_lists" :value="value.id"> {{value.first_name + ' '+ value.last_name + ' ('+ value.fingerprint_no +' )'}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('employee_id'))">{{ (errors.hasOwnProperty('employee_id')) ? errors.employee_id[0] :'' }}</div>
                    </div>
                    <label for="leave_type_id" class="col-lg-2 col-form-label">Leave Type <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" id="leave_type_id"  v-model="form.leave_type_id"  data-selectField="leave_type_id">
                            <option v-for="(value,index) in leave_type_lists" :value="value.id"> {{value.leave_title}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('leave_type_id'))">{{ (errors.hasOwnProperty('leave_type_id')) ? errors.leave_type_id[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="application_from_date">From Date <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" id="application_from_date"  class="form-control form-control-sm m-input datepicker application_from_date" @input ="calculate()" v-model="form.application_from_date" data-dateField="application_from_date" autocomplete="off" placeholder="Select date from picker"  />
                            <div class="input-group-append">
								<span class="input-group-text">
									<i class="la la-calendar-check-o"></i>
								</span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('application_from_date'))">{{ (errors.hasOwnProperty('application_from_date')) ? errors.application_from_date[0] :'' }}</div>
                    </div>

                    <label class="col-lg-2 col-form-label" for="application_to_date">To Date <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" id="application_to_date"  class="form-control form-control-sm m-input datepicker application_to_date" @input ="calculate()" v-model="form.application_to_date" data-dateField="application_to_date" autocomplete="off" placeholder="Select date from picker"  />
                            <div class="input-group-append">
								<span class="input-group-text">
									<i class="la la-calendar-check-o"></i>
								</span>
                            </div>
                        </div>
                        <div id="errorDate" style="color:red;"></div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('application_to_date'))">{{ (errors.hasOwnProperty('application_to_date')) ? errors.application_to_date[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="number_of_day">Number Of Day  <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="number_of_day" v-model="form.number_of_day" class="form-control form-control-sm m-input number_of_day" readonly placeholder="0">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('number_of_day'))">{{ (errors.hasOwnProperty('number_of_day')) ? errors.number_of_day[0] :'' }}</div>
                    </div>

                    <label for="purpose" class="col-lg-2 col-form-label">Purpose </label>
                    <div class="col-lg-4">
                        <textarea class="form-control form-control-sm" id="purpose" v-model="form.purpose" placeholder="" rows="2"></textarea>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('purpose'))">{{ (errors.hasOwnProperty('purpose')) ? errors.purpose[0] :'' }}</div>
                    </div>

                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-8">
                        <div class="m-checkbox-list" >
                            <label class="m-checkbox">
                                Half Leave ? <input type="checkbox" id="check_status" v-model="form.check_status" @click="half_leave()">
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <div class="leave-balance-check">
                        <fieldset>
                            <legend>Leave Balance Check</legend>
                            <div class="leave-balance-check__column">
                                <div class="column__row">
                                    <div>
                                        <label>Leave Title</label>
                                    </div>
                                    <div>
                                        <h5>Leave Entitle</h5>
                                    </div>
                                </div>

                                <div class="column__row" v-for="(value, index) in leaveTypeData">
                                    <div>
                                        <label>{{value.title}}</label>
                                    </div>
                                    <div>
                                        <span>{{value.balance}}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="leave-balance-check__column">
                                <div class="column__row">
                                    <div>
                                        <label>MD. Abu Sayed</label>
                                    </div>
                                    <div>
                                        <h5>Balance</h5>
                                    </div>
                                </div>
                                <div class="column__row" v-for="(value, index) in leaveYearData" >
                                    <div>
                                        <label>{{value.title}}</label>
                                    </div>
                                    <div>
                                        <span>{{value.balance}}</span>
                                    </div>
                                </div>

                            </div>
                            <div class="leave-balance-check__column">
                                <div class="profile_image">
                                    <img src="">
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="reset" class="btn btn-sm btn-secondary">Cancel</button>
                              <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Update and Go List</span></button>

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

        props:['employee_lists','leave_type_lists','permissions','editId'],

        data:function(){
            return{

                product_list:false,
                add_form:true,
                edit_form:true,

                leaveTypeData:{
                    title:'',
                    balance:'',
                },
                leaveYearData:{
                    title:'',
                    balance:'',
                },

                form:{
                    employee_id: '',
                    leave_type_id: '',
                    application_from_date: '',
                    application_to_date: '',
                    number_of_day: '',
                    purpose: '',
                    check_status: '',
                    status:'1',

                },
                errors: {},
            };
        },

        methods:{

            edit(id) {
                var _this = this;
                axios.get(base_url+'apply-for-leave/'+id+'/edit')
                    .then( (response) => {
                        _this.form  = response.data.data;
                        _this.checkLeaveBalance();
                        _this.calculate_year_balance();

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
                axios.put(base_url+'apply-for-leave/'+id, this.form).then( (response) => {
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

            calculate_year_balance()
            {
                var _this = this;
                var employee_id = _this.form.employee_id;
                var date = _this.form.application_from_date;

                if (employee_id != '' && date != '') {
                    axios.get(base_url+"check-year-leave-balance/?employee_id="+employee_id+'&date='+date)
                        .then( (response) => {
                            _this.leaveYearData  = response.data;
                        });
                }

            },

            checkLeaveBalance() {
                var _this = this;
                var employee_id = _this.form.employee_id;

                if (employee_id) {
                    axios.get(base_url+"check-leave-balance/?employee_id="+employee_id)
                        .then( (response) => {

                            _this.leaveTypeData  = response.data;

                            /*_this.leaveTypeData  = response.data.leaveTypeData;
                             _this.leaveYearData  = response.data.leaveYearData;*/
                        });
                }
                _this.calculate_year_balance();
            },


            half_leave(){

                var _this = this;
                var check =_this.form.check_status;
                var requested_leave_date =_this.form.number_of_day;
                var point_val = .5;

                if (check == true) {
                    _this.form.number_of_day = Number(requested_leave_date) + Number(point_val);
                }else{
                    _this.form.number_of_day = Number(requested_leave_date) - Number(point_val);
                }
            },



            calculate(){
                var _this = this;

                var f1 = _this.form.application_from_date;

                var f1_date = f1.split("-").reverse().join("-");

                var f2 = _this.form.application_to_date;

                var f2_date = f2.split("-").reverse().join("-");

                var timeDiff = (Date.parse(f2_date) - Date.parse(f1_date));

                var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

                if (diffDays < 0) {
                    _this.form.application_to_date = '';
                    _this.form.number_of_day = '';
                    $('#errorDate').html("End Date can not be less than Start Date");
                }else{

                    var check =_this.form.check_status;
                    //var requested_leave_date =_this.form.number_of_day;
                    var point_val = .5;

                    if(check==true){
                        _this.form.number_of_day = diffDays+point_val ;
                    }else{
                        _this.form.number_of_day = diffDays+1 ;
                    }

                    $('#errorDate').html('');
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

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if (dataIndex == 'employee_id') {
                    _this.form.employee_id = selectedType.val();
                    _this.checkLeaveBalance();

                }else if (dataIndex == 'leave_type_id') {
                    _this.form.leave_type_id = selectedType.val();


                }

            });





            $(document).on("focus", ".datepicker", function () {

                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                    changeMonth: true,
                    changeYear: true,
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    var selectField = $(ev.currentTarget).attr('data-dateField');

                    if(ev.date == undefined){
                        if (selectField == 'application_from_date') {
                            _this.form.application_from_date = '';
                        } else if(selectField == 'application_to_date'){
                            _this.form.application_to_date = '';
                        }
                    }else if(selectField == 'application_from_date'){
                        _this.form.application_from_date = moment(ev.date).format('DD-MM-YYYY');
                        _this.calculate();
                        _this.calculate_year_balance();
                    }else if(selectField == 'application_to_date'){
                        _this.form.application_to_date = moment(ev.date).format('DD-MM-YYYY');
                        _this.calculate();


                    }

                });
            });


        },

        created(){
            var _this = this;
            _this.edit(_this.editId);

        }

    }
</script>
