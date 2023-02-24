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
                    <th>Warning To</th>
                    <th>Warning Type</th>
                    <th>Subject</th>
                    <th>Warning By</th>
                    <th>Warning Date</th>
                    <th>Narration</th>
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.warning_to_employee ? value.warning_to_employee.first_name  +' '+  value.warning_to_employee.last_name : ''}}</td>
                    <td>{{value.warning_types}}</td>
                    <td>{{value.subject}}</td>
                    <td>{{value.warning_by_employee ? value.warning_by_employee.first_name  +' '+  value.warning_by_employee.last_name : ''}}</td>
                    <td>{{dateFormate(value.warning_date)}}</td>
                    <td>{{value.narration}}</td>
                    <td scope="row" class="width-100 text-center">
                        <a v-if="permissions.indexOf('warning.edit') != -1" @click="editData(value.id)" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la 	la-edit"></i></a>
                        <a v-if="permissions.indexOf('warning.destroy') != -1" @click="deleteData(value.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
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

        <AddWarning  v-else-if="add_form"
                     :employee_list="employee_list"
        ></AddWarning>
        <EditWarning  v-else-if="edit_form"
                      :employee_list="employee_list"
                      :edit-id="edit_id"
        ></EditWarning>


    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import Pagination from  '../../../components/Pagination.vue';
    import AddWarning from './AddWarning.vue';
    import EditWarning from './EditWarning.vue';


    export default {
        components: {
            AddWarning,
            EditWarning,
            Pagination
        },
        props:['employee_list','permissions'],

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

                axios.get(base_url+"warning/?page="+pageNo+"&perPage="+perPage).then((response) => {
                    this.resultData = response.data;
                });
            },

            dateFormate(value){
                if (value) {
                    return moment(String(value)).format('MM/DD/YYYY')
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
                        axios.delete(base_url+'warning/'+id).then((response) => {
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

