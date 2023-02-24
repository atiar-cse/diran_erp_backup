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
                    <th>Pay Grade Name</th>
                    <th>Gross Salary</th>
                    <th>Percentage Of Basic</th>
                    <th>Basic Salary</th>
                    <th>Over Time Rate</th>
                    <th>Status</th>
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.pay_grade_name}}</td>
                    <td>{{value.gross_salary}}</td>
                    <td>{{value.percentage_of_basic}}</td>
                    <td>{{value.basic_salary}}</td>
                    <td>{{value.over_time_rate}}</td>
                    <td>
                        <div v-if="value.status == 1" class="m-demo__preview m-demo__preview--badge">
                            <span class="m-badge m-badge--success m-badge--wide m-badge--rounded"> <i class="la la-check-circle"></i> Active </span>
                        </div>
                        <div v-if="value.status == 0" class="m-demo__preview m-demo__preview--badge">
                            <span class="m-badge m-badge--danger m-badge--wide m-badge--rounded"> <i class="la la-ban"></i> Inactive</span>
                        </div>

                    </td>
                    <td scope="row" class="width-100 text-center">
                        <a v-if="permissions.indexOf('monthly-pay-grade.edit') != -1" @click="editData(value.id)"  class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la 	la-edit"></i></a>
                        <a v-if="permissions.indexOf('monthly-pay-grade.destroy') != -1" @click="deleteData(value.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
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

        <AddMonthlyPayGrade  v-else-if="add_form" :allowance_lists="allowance_lists" :deduction_lists="deduction_lists"></AddMonthlyPayGrade>
        <EditMonthlyPayGrade  v-else-if="edit_form" :allowance_lists="allowance_lists" :deduction_lists="deduction_lists" :edit-id="edit_id"></EditMonthlyPayGrade>


    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import Pagination from  '../../../components/Pagination.vue';
    import AddMonthlyPayGrade from './AddMonthlyPayGrade.vue';
    import EditMonthlyPayGrade from './EditMonthlyPayGrade.vue';


    export default {
        components: {
            AddMonthlyPayGrade,
            EditMonthlyPayGrade,
            Pagination
        },

        props:['permissions','allowance_lists','deduction_lists'],

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

                axios.get(base_url+"monthly-pay-grade/?page="+pageNo+"&perPage="+perPage).then((response) => {
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
                        axios.delete(base_url+'monthly-pay-grade/'+id).then((response) => {
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
            console.log(_this.category_list);
        },
    }
</script>

