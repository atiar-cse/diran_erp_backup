<template>
    <div>
        <div class="m-portlet__body" v-if="product_list">
            <br><br>
            <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent"
                  class="m-form m-form--fit m-form--label-align-right">
                <div class="m-portlet__body">
                    <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                        <div class="row align-items-center">
                            <div class="col-xl-12 order-2 order-xl-1">
                                <div class="form-group m-form__group row align-items-center">
                                    <div class="col-md-4">
                                        <div class="m-form__group m-form__group">
                                            <label class="m-label m-label--single">Department<span class="requiredField">*</span></label>
                                            <div class="m-form__control">
                                                <select class="form-control m-select2 select2" required
                                                        data-selectField="employee_attendance_department_id"
                                                        id="employee_attendance_department_id"
                                                        v-model="form.employee_attendance_department_id">
                                                    <option v-for="(dvalue,dindex) in department_lists"
                                                            :value="dvalue.id"> {{dvalue.department_name}}
                                                    </option>
                                                </select>
                                                <div class="requiredField"
                                                     v-if="(errors.hasOwnProperty('inventory_current_stocks_warehouse_id'))">
                                                    {{ (errors.hasOwnProperty('employee_attendance_department_id')) ? errors.employee_attendance_department_id[0] : ''
                                                    }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-md-none m--margin-bottom-10"></div>
                                    </div>


                                    <div class="col-md-4">
                                        <div class="m-form__group m-form__group">
                                            <label class="m-label m-label--single">Shift <span class="requiredField">*</span></label>
                                            <select class="form-control m-select2 select2" required
                                                    data-selectField="employee_shift_id"
                                                    id="employee_shift_id"
                                                    v-model="form.employee_shift_id">
                                                <option v-for="(svalue,sindex) in work_shift_list"
                                                        :value="svalue.shift_name">
                                                       <span v-if="svalue.shift_name == 1">Day</span>
                                                       <span v-if="svalue.shift_name == 2">Evening</span>
                                                </option>
                                            </select>
                                            <div class="requiredField"
                                                 v-if="(errors.hasOwnProperty('employee_shift_id'))">
                                                {{ (errors.hasOwnProperty('employee_shift_id')) ? errors.employee_shift_id[0] : ''
                                                }}
                                            </div>
                                        </div>
                                        <div class="d-md-none m--margin-bottom-10"></div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="m-form__group m-form__group">
                                            <label class="m-label m-label--single">Date <span class="requiredField">*</span></label>
                                            <div class="input-group date">
                                                <input type="text" id="date" required
                                                       class="form-control form-control-sm m-input datepicker"
                                                       v-model="form.emp_attendance_date"
                                                       data-dateField="emp_attendance_date"
                                                       placeholder="Select date from picker" autocomplete="off"/>
                                                <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar-check-o"></i>
                                        </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-md-none m--margin-bottom-10"></div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="m-form__group m-form__group">
                                            <div class="m-input-icon m-input-icon--left">
                                                <button type="button" class="btn btn-sm btn-success" @click="loadEmployee()">Filter</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br><br>
                    <!--begin::Portlet-->
                    <div class="form-group m-form__group row">
                        <div class="m-section__content col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-sm m-table table-bordered borderless">
                                    <thead class="thead-inverse">
                                    <tr>
                                        <th>Sl</th>
                                        <th>Employee Name </th>
                                        <th>In Time</th>
                                        <th>Out Time</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="form.employee_attendance_department_id  != '' ">

                                    <tr v-for="(employee, index) in form.employee">
                                        <td scope="row">
                                            {{ index + 1 }}
                                            <input type="hidden" class="small" v-model="employee.id">
                                        </td>

                                        <td>
                                            {{ employee.first_name + ' ' + employee.last_name}}
                                        </td>
                                        <td>
                                            <div class="input-group timepicker">
                                            <input type="text" id="start_time" v-model="employee.start_time" v-bind:data-index="index" class="form-control form-control-sm m-input start_time" readonly placeholder="">
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-clock-o"></i>
                                                </span>
                                            </div>
                                            </div>
                                            <div class="requiredField" v-if="errors['employee.'+index+'.start_time']">
                                                {{ errors['employee.' + index + '.start_time'][0] }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group timepicker">
                                            <input type="text" id="end_time" v-model="employee.end_time" v-bind:data-index="index" class="form-control form-control-sm m-input end_time" readonly placeholder="">
                                                <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-clock-o"></i>
                                                </span>
                                                </div>
                                            </div>
                                            <div class="requiredField" v-if="errors['employee.'+index+'.end_time']">
                                                {{ errors['employee.' + index + '.end_time'][0] }}
                                            </div>
                                        </td>
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
                                <button type="reset" class="btn btn-sm btn-secondary">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i> <span>Save</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>

    import {EventBus} from '../../../vue-assets';

    export default {
        props: ['department_lists', 'employee_lists','work_shift_list'],

        data: function () {
            return {
                product_list: true,
                add_form: true,
                edit_form: false,

                form: {
                    employee_attendance_department_id: '',
                    employee_shift_id: '',
                    emp_attendance_date: '',
                    employee: [
                        {
                            employee_attendance_employee_id: '',
                            employee_attendance_department_id: '',
                            employee_shift_id: '',
                            emp_attendance_date: '',
                            first_name: '',
                            last_name: '',
                            fingerprint_no: '',
                            start_time: '',
                            end_time: '',
                        }
                    ],
                },
                errors: {},
            };
        },


        methods: {
            loadEmployee() {

                var _this = this;
                var department_id = this.form.employee_attendance_department_id;
                var work_shift_id = this.form.employee_shift_id;
                var date          = this.form.emp_attendance_date;

                if(department_id ==''){
                    toastr.error('Please Enter Department!', {timeOut: 5000});
                }else if(work_shift_id==''){
                    toastr.error('Please Enter Shift!', {timeOut: 5000});
                }else if(date ==''){
                    toastr.error('Please Enter Date!', {timeOut: 5000});
                }

                axios.get(base_url + 'employee-wise-attendance-load/'+department_id +'/'+work_shift_id+'/'+date).then((response) => {
                    this.form.employee = response.data;
                });
            },

            index(pageNo, perPage){
                if (pageNo) {
                    pageNo = pageNo;
                } else {
                    pageNo = this.resultData.current_page;
                }
                if (perPage) {
                    perPage = perPage;
                } else {
                    perPage = this.perPage;
                }

                axios.get(base_url + "manual-attendance-entry/?page=" + pageNo + "&perPage=" + perPage).then((response) => {
                    this.resultData = response.data;

                });
            },

            store: function () {
                var _this = this;
                axios.post(base_url + 'manual-attendance-entry', this.form).then((response) => {
                    this.showMassage(response.data);
                    EventBus.$emit('data-changed');
                })
                    .catch(error => {
                        if (error.response.status == 422) {
                            this.errors = error.response.data.errors;
                        } else {
                            this.showMassage(error);
                        }
                    });
            },


            showMassage(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, 'Success Alert', {timeOut: 5000});
                } else {
                    toastr.error(data.message, 'Error Alert', {timeOut: 5000});
                }
            },

            changePerPage(){
                var vm = this;
                vm.index(1, vm.perPage);
            },

        },

        mounted(){
            var _this = this;

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if (dataIndex == 'employee_attendance_department_id') {
                    _this.form.employee_attendance_department_id = selectedType.val();

                }else if (dataIndex == 'employee_shift_id') {
                    _this.form.employee_shift_id = selectedType.val();

                }

            });

            $(document).on("focus", ".start_time", function (e) {
                var selectedItem = $(this);
                var dataIndex = $(e.currentTarget).attr('data-index');
                $(this).timepicker({
                    format: 'LT',
                    scrollDefault: 'now',
                }).on('click change', function () {
                    _this.form.employee[dataIndex].start_time = selectedItem.val();
                })
            });

            $(document).on("focus", ".end_time", function (e) {
                var selectedItem = $(this);
                var dataIndex = $(e.currentTarget).attr('data-index');
                $(this).timepicker({
                    format: 'LT',
                    scrollDefault: 'now',
                }).on('click change', function () {
                    _this.form.employee[dataIndex].end_time = selectedItem.val();

                })
            });

            var Select2 = {
                init: function () {
                    $(".select2").select2({
                            placeholder: "Please select an option"
                        }
                    )
                }
            };
            jQuery(document).ready(function () {
                    Select2.init()
                }
            );


            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                    //startDate: new Date(),
                    startDate: '-2d',
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.emp_attendance_date = '';

                    } else {
                        _this.form.emp_attendance_date = moment(ev.date).format('YYYY-MM-DD');

                    }

                });
            });
        },
        created(){

            var _this = this;

            /*$('body').on('click', '#addButton', function() {
             _this.add_form = true;
             _this.product_list = false;
             _this.edit_form = false;
             $('#addButton').hide();
             $('#listButton').show();
             });*/

            $('body').on('click', '#listButton', function () {
                _this.add_form = false;
                _this.product_list = true;
                _this.edit_form = false;
                $('#addButton').show();
                $('#listButton').hide();
            });

            EventBus.$on('data-changed', (id) => {
                _this.add_form = false;
                _this.product_list = true;
                _this.edit_form = false;
                $('#addButton').show();
                $('#listButton').hide();
                _this.index(1);
            });

            _this.index(1);
        },
    }
</script>

