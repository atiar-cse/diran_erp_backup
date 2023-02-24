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
                    <th>Employee Name</th>
                    <th>Current Department</th>
                    <th>Current Designation</th>
                    <th>Current Pay Grade</th>
                    <th>Current Salary </th>
                    <th>Promoted Pay Grade</th>
                    <th>New Salary</th>
                    <th>Promoted Department</th>
                    <th>Promoted Designation</th>
                    <th>Promoted Date</th>
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.get_employee ? value.get_employee.first_name +' '+ value.get_employee.last_name:''}}</td>
                    <td>{{value.get_department ? value.get_department.department_name :''}}</td>
                    <td>{{value.get_designation ? value.get_designation.designation_name :''}}</td>
                    <td>{{value.get_current_paygrade ? value.get_current_paygrade.pay_grade_name :''}}</td>
                    <td>{{value.current_salary }}</td>
                    <td>{{value.get_promoted_paygrade ? value.get_promoted_paygrade.pay_grade_name :''}}</td>
                    <td>{{value.new_salary}}</td>
                    <td>{{value.get_promoted_department ? value.get_promoted_department.department_name :''}}</td>
                    <td>{{value.get_promoted_designation ? value.get_promoted_designation.designation_name :''}}</td>
                    <td>{{value.increament_date}}</td>
                    <td scope="row" class="width-100 text-center">
                        <a  @click="editData(value.id)" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la 	la-edit"></i></a>
                        <a  @click="deleteData(value.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
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

        <AddIncreament  v-else-if="add_form"
                       :employee_lists="employee_lists"
                       :paygrade_lists="paygrade_lists"
                       :department_lists="department_lists"
                       :designation_lists="designation_lists"
        ></AddIncreament>
        <EditIncreament  v-else-if="edit_form"
                        :employee_lists="employee_lists"
                        :paygrade_lists="paygrade_lists"
                        :department_lists="department_lists"
                        :designation_lists="designation_lists"
                        :edit-id="edit_id"
        ></EditIncreament>

    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import Pagination from  '../../../components/Pagination.vue';
    import AddIncreament from './AddIncreament.vue';
    import EditIncreament from './EditIncreament.vue';


    export default {
        components: {
            AddIncreament,
            EditIncreament,
            Pagination
        },

        props:['permissions','employee_lists','paygrade_lists','department_lists','designation_lists'],

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

                axios.get(base_url+"increament/?page="+pageNo+"&perPage="+perPage).then((response) => {
                    this.resultData = response.data;
                });
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


            deleteData(id){
                var _this = this;
                swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this information!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false
                    },
                    function(){
                        swal("Deleted!", "Your imaginary file has been deleted.", "success");
                        axios.delete(base_url+'increament/'+id).then((response) => {
                            _this.index(1);
                            _this.showMassage(response.data);
                        }).catch((error)=>{
                            swal('Error:','Something Error Found !, Please try again','error');
                        });
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

