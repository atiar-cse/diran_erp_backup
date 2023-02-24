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
                    <th>Leave Type</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Number Of Day</th>
                    <th>Purpose</th> 
                    <th>Approve</th>
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.first_name +' '+ value.last_name}}</td>
                    <td>{{value.leave_title }}</td>
                    <td>{{format_date(value.application_from_date)}}</td>
                    <td>{{format_date(value.application_to_date)}}</td>
                    <td>{{value.number_of_day}}</td>
                    <td>{{value.purpose}}</td>
                    <td>
                        <a  @click="approvedEmpLeave(value.id)" v-if="permissions.indexOf('apply-for-leave.leave-approved') != -1 && value.status!=2" class="btn btn-sm btn-info" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Approved">Approve</a>
                        <div v-if="value.status == 2" class="badge badge-pill badge-danger" style="padding:6px;">Approved</div>
                    </td>
                    <td scope="row" class="width-100 text-center">
                        <a  @click="editData(value.id)" v-if="value.status!=2" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la 	la-edit"></i></a>
                        <a  @click="deleteData(value.id)" v-if="value.status!=2" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
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

        <AddApplyForLeave  v-else-if="add_form"
                           :permissions="permissions"
                           :employee_lists="employee_lists"
                           :leave_type_lists="leave_type_lists"
        ></AddApplyForLeave>
        <EditApplyForLeave  v-else-if="edit_form"
                            :permissions="permissions"
                            :employee_lists="employee_lists"
                            :leave_type_lists="leave_type_lists"
                            :edit-id="edit_id"
        ></EditApplyForLeave>

    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import Pagination from  '../../../components/Pagination.vue';
    import AddApplyForLeave from './AddApplyForLeave.vue';
    import EditApplyForLeave from './EditApplyForLeave.vue';


    export default {
        components: {
            AddApplyForLeave,
            EditApplyForLeave,
            Pagination
        },

        props:['employee_lists','leave_type_lists','permissions'],

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

                axios.get(base_url+"apply-for-leave/?page="+pageNo+"&perPage="+perPage).then((response) => {
                    this.resultData = response.data;

                });
            },

            format_date(value){
                if (value) {
                    return moment(String(value)).format('DD-MM-YYYY')
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
                        axios.delete(base_url+'apply-for-leave/'+id).then((response) => {
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

            approvedEmpLeave(id){
                var _this = this;
                swal({
                        title: "Are you sure?",
                        text: "You want to Approve this information!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, Approve it!",
                        closeOnConfirm: false
                    },
                    function(){
                        axios.post(base_url +'apply-for-leave/leave-approved/'+id).then((response) => {
                            _this.showMassage(response.data);
                            swal("Approve!", "Your confirmation has been Approved.", "success");
                            location.reload();
                        }).catch((error)=>{
                            swal('Error:',error,'error');
                        });
                    });
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

