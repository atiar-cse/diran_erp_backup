<template>
    <div>

        <div class="m-portlet__body" v-if="product_list">
            <div class="item-show-limit">
                <span>Show</span>
                <select name="per_page" class="per_page" @change="changePerPage" v-model="perPage">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="40">40</option>
                    <option value="50">50</option>
                </select>
                <span>Entries</span>
            </div>
            <br><br>
            <!--begin: Datatable -->
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                <tr>
                    <th>S/L</th>
                    <th>Month</th>
                    <th>Total Salary</th>
                    <th>Debit </th>
                    <th>Credit </th>
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{formate_date(value.payroll_salary_month)}}</td>
                    <td>{{value.payable_total_salary}}</td>
                    <td>{{value.get_debit? value.get_debit.chart_of_accounts_title :''}}</td>
                    <td>{{value.get_credit? value.get_credit.chart_of_accounts_title :''}}</td>


                    <td scope="row" class="width-100 text-center">
                        <span v-if="value.approve_status == 1" title="Approved"><i class="la la-check-circle-o text-danger"></i></span>
                        <a v-if="permissions.indexOf('payroll-salary-summery.edit') !=-1 && value.approve_status != 1 "  @click="editData(value.id)" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la 	la-edit"></i></a>
                    </td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="12" class="text-center">No Data Available.</td>
                </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="text-center col-md-12" >
                    <pagination :resultData="resultData" @clicked="index" :mid-size="9"></pagination>
                </div>
            </div>
        </div>

        <AddPayrollSalaryProcessMonthly  v-else-if="add_form"
                      :account_list ="account_list"
                      :permissions ="permissions"
        ></AddPayrollSalaryProcessMonthly>

        <EditPayrollSalaryProcessMonthly  v-else-if="edit_form"
                      :account_list ="account_list"
                      :permissions ="permissions"
                      :edit-id="edit_id"
        ></EditPayrollSalaryProcessMonthly>




    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import Pagination from  '../../../components/Pagination.vue';
    import AddPayrollSalaryProcessMonthly from './AddPayrollSalaryProcessMonthly.vue';
    import EditPayrollSalaryProcessMonthly from './EditPayrollSalaryProcessMonthly.vue';


    export default {
        components: {
            AddPayrollSalaryProcessMonthly,
            EditPayrollSalaryProcessMonthly,
            Pagination
        },

        props:['permissions','account_list'],

        data: function(){
            return {
                product_list:true,
                add_form:false,
                edit_form:false,
                edit_id:false,
                resultData: {},
                perPage: 10
            };
        },

        methods: {
            index(pageNo,perPage){
                if(pageNo){ pageNo = pageNo; }else{pageNo = this.resultData.current_page; }
                if(perPage){ perPage = perPage;}else{ perPage = this.perPage;}

                axios.get(base_url+"payroll-salary-summery/?page="+pageNo+"&perPage="+perPage).then((response) => {
                    this.resultData = response.data;

                });
            },

            formate_date(value){
                if(value){
                    return moment(String(value)).format('Y-MMM-DD')
                }

            },

            editData(id){

                var _this = this;
                _this.add_form = false;
                _this.product_list = false;
                _this.edit_form = true;
                _this.edit_id = id;
                $('#addButton').hide();
                $('#listButton').show();
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
                vm.index(1,vm.perPage);
            },

        },

        created(){
            var _this = this;

            $('body').on('click', '#addButton', function() {
                _this.add_form = true;
                _this.product_list = false;
                _this.edit_form = false;
                $('#addButton').hide();
                $('#listButton').show();
            });

            $('body').on('click', '#listButton', function() {
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

