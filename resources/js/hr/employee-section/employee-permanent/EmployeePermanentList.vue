<style scoped>
    .imageSize {
        width: 70px;
        height: 70px;
    }
</style>

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
                    <th>Photo</th>
                    <th>Employee Name</th>
                    <th>Designation</th>
                    <th>Phone</th>
                   <!-- <th>Probation Period</th>-->
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td><img :src="value.photo" class="imageSize"></td>
                    <td>
                        {{value.first_name}} {{value.last_name}}<br>
                         Role : {{value.emp_roll_id ? value.emp_roll_id.role_name:''}}<br>
                         Supervisor : {{value.supervisor}}

                    </td>

                    <td>
                        {{ value.emp_department ? value.emp_department.department_name:''}}<br>
                        Designation : {{ value.emp_designation ? value.emp_designation.designation_name:''}}<br>
                        Branch : {{ value.emp_branch ? value.emp_branch.branch_name:''}}

                    </td>
                    <td>{{value.phone}}</td>

                    <td scope="row" class="width-100 text-center">
                        <a @click="update_permanent(value.id)"  class="btn btn-sm btn-info" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Permanent">Make Permanent</a>
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

    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import Pagination from  '../../../components/Pagination.vue';

    export default {
        components: {

            Pagination
        },

        data: function(){
            return {
                product_list:true,
                resultData: {},
                perPage: 10
            };
        },

        methods: {
            index(pageNo,perPage){
                if(pageNo){ pageNo = pageNo; }else{pageNo = this.resultData.current_page; }
                if(perPage){ perPage = perPage;}else{ perPage = this.perPage;}

                axios.get(base_url+"employee-permanent/?page="+pageNo+"&perPage="+perPage).then((response) => {
                    this.resultData = response.data;
                });
            },

            update_permanent(id){
                var _this = this;
                swal({
                        title: "Are you sure?",
                        text: "You want to permanent this information!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, Permanent it!",
                        closeOnConfirm: false
                    },
                    function(){
                        axios.post(base_url + 'employee-permanent/update-permanent/'+id).then((response) => {
                            _this.showMassage(response.data);
                            swal("Permanent!", "Your imaginary file has been permanent.", "success");
                            location.reload();
                        }).catch((error)=>{
                            swal('Error:',error,'error');
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

