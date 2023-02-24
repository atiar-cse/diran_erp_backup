<template>
    <div class="add-manage-section">
            <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="current_salary" id="listComponent" class="m-form m-form--fit m-form--label-align-right" >
                <div class="m-portlet__body">
                    <div class="form-group m-form__group row">
                        <label for="employee_id" class="col-lg-2 col-form-label">Employee</label>
                        <div class="col-lg-3">
                            <select class="form-control m-select2 select2 width-210" data-selectField="employee_id" id="employee_id" v-model="form.employee_id"  @change="loadproduct()">
                                <option v-for="(evalue,eindex) in employee_lists" :value="evalue.id" > {{evalue.first_name +' '+ evalue.last_name +' ('+ evalue.fingerprint_no +')'}}</option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('employee_id'))">{{ (errors.hasOwnProperty('employee_id')) ? errors.employee_id[0] :'' }}</div>
                        </div>
                        <label for="salary_month" class="col-lg-2 col-form-label">Month</label>
                        <div class="col-lg-3">
                            <input type="text" id="salary_month" class="form-control form-control-sm m-input datepicker month" v-model="form.salary_month"  placeholder="Select date from picker" autocomplete="off" required/>
                        </div>
                        <div class="col-lg-2">
                            <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i> Filter</button>
                        </div>
                    </div>

                </div>
            </form>
                    <br><br>
                    <!--begin::Portlet-->
                <div class="m-portlet__body" v-if="salary_list">
                    <div class="form-group m-form__group row add-manage-section">
                        <div class="bg-success p-3 mb-2 col-lg-12">
                            <span><strong style="color: #fff; font-size: 16px">Salary Sheet/Final Balance</strong></span>
                        </div>
                        <div class="m-section__content col-lg-12">
                            <div class="sheet-header">
                                <h3>DIRAN ENTERPRISE LTD.</h3>
                                <h6>Monthly Salary Sheet</h6>
                                <h6><strong>Head Office</strong></h6>
                                <h6>For the Month of ....</h6>
                            </div>
                            <div class ="table-responsive">
                                <table class="table table-sm m-table table-bordered borderless">
                                    <thead class="thead-inverse" >
                                        <tr>
                                            <th rowspan="2">Emp ID</th>
                                            <th rowspan="2">Dept.</th>
                                            <th rowspan="2">Employee Name</th>
                                            <th rowspan="2">Basic Salary</th>
                                            <th rowspan="2">House Rent</th>
                                            <th>Others</th>
                                            <th rowspan="2">Gross Salary</th>
                                            <th rowspan="2">Days Works</th>
                                            <th colspan="3">Deduction</th>
                                            <th colspan="2">Providend Fund</th>
                                            <th rowspan="2">Total Deduction</th>
                                            <th rowspan="2">Net Salary</th>
                                        </tr>
                                        <tr>
                                            <th>Med + Conv</th>
                                            <th>Lunch</th>
                                            <th>Tax</th>
                                            <th>Adv.</th>
                                            <th>Own CONT</th>
                                            <th>CO'S CONT</th>
                                        </tr>
                                    </thead>
                                    <tbody v-if="resultData !=''">
                                        <tr v-for="(value, index) in resultData">
                                            <td class="width-35">{{value.employee_id}}</td>
                                            <td>{{value.get_emp_name.emp_department ? value.get_emp_name.emp_department.department_name :''}}</td>
                                            <td>{{ value.get_emp_name ? value.get_emp_name.user_name:'' }}</td>

                                            <td>{{ value.basic_salary }}</td>
                                            <td>{{ value.get_emp_name.house_rent }}</td>
                                            <td>{{ value.get_emp_name.medical }}</td>
                                            <td>{{ value.gross_salary }}</td>
                                            <td>{{ value.total_working_days }}</td>
                                            <td>{{ value.get_emp_name.lunch }}</td>
                                            <td></td>
                                            <td></td>
                                            <td>{{ value.basic_salary*5/100 }}</td>
                                            <td>{{ value.basic_salary*5/100 }}</td>
                                            <td>{{ value.get_emp_name.lunch + value.basic_salary*5/100 }}</td>
                                            <td>{{ value.gross_salary - value.get_emp_name.lunch - value.basic_salary*5/100 }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">Total</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                    <tbody  v-else>
                                    <tr>
                                        <td colspan="15">No Data Available</td>

                                    </tr>
                                    </tbody>

                                </table>
                                <div class="sheet-sammury table-responsive">
                                    <div class="sheet-sammuery__column">
                                        <table class="table-bordered">
                                            <tr>
                                                <td>Payment by bank account </td>
                                                <td class="width-35">=</td>
                                                <td>85,050.00</td>
                                            </tr>
                                            <tr>
                                                <td>Payment by cash </td>
                                                <td class="width-35">=</td>
                                                <td>34,650.00</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="sheet-sammuery__column">
                                        <table class="table-bordered">
                                            <tr>
                                                <td>Net Salary </td>
                                                <td>1,19,700.00</td>
                                            </tr>
                                            <tr>
                                                <td>Deposite P.F </td>
                                                <td>6,300.00</td>
                                            </tr>
                                            <tr>
                                                <td>Total</td>
                                                <td>1,26,000.00</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <br><br>
                    </div>
                </div>
                <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                    <div class="m-form__actions m-form__actions--solid">
                        <div class="row">
                            <div class="col-lg-12 m--align-right">
                                <button type="reset" class="btn btn-sm btn-secondary">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Approve</span></button>
                                <!--<button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save and Go List</span></button>-->
                            </div>
                        </div>
                    </div>
                </div>
        </div>
</template>


<script>

    import { EventBus } from '../../../vue-assets';

    export default {
        props:['employee_lists'],

        data:function(){

            return{
                salary_list:true,
                resultData: {},

                form:{
                    employee_id: '',
                    salary_month: '',
                    first_name:'',

                },
                errors: {},
            };
        },



        methods: {
           /* index() {
                axios.get(base_url+"generate-salary-sheet",this.form)
                    .then((response) => {
                        console.log(response.data);
                        this.resultData = response.data;

                    });

            },*/

            current_salary() {
                let employee_id = this.form.employee_id;
                let month = this.form.salary_month;

                axios.get(base_url+"salary-sheet/?employee_id="+employee_id+"&month="+month)
                    .then((response) => {

                        console.log(response.data);

                        this.resultData = response.data;
                    });

            },

            format_joining_date(value){
                if (value) {
                    return moment(String(value)).format('DD-MMM-YYYY')
                }
            },

            format_date(value){
                if (value) {
                    return moment(String(value)).format('MMMM YYYY')
                }
            },

            formatNumber(value) {
                let val = (value/1).toFixed(2).replace(',', '.')
                return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
            }
        },

        mounted(){
            var _this = this;

            $('#listComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'employee_id'){
                    _this.form.employee_id = selectedType.val();
                }
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


            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: "mm-yyyy",
                    viewMode: "months",
                    minViewMode: 1,
                    clearBtn: true,
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.salary_month = '';

                    }else {
                        _this.form.salary_month = moment(ev.date).format('Y-MM-01');

                    }

                });
            });
        },
        created(){

            var _this = this;

            $('body').on('click', '#listButton', function() {
                _this.salary_list = true;
                $('#listButton').hide();
            });

            EventBus.$on('data-changed', (id) => {
                _this.salary_list = true;
                $('#listButton').hide();
            });



        },
    }
</script>

