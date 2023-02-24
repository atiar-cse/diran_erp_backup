<template>
    <div>
        <div class="m-portlet__body">
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
                    <th>Division</th>
                    <th>Department Name</th>
                    <th>Head Person</th>
                    <th>Status</th>
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.get_div? value.get_div.title : ''}} {{value.get_div? ' - '+value.get_div.get_unit.unit_name : ''}}</td>
                    <td>{{value.department_name}}</td>
                    <td>{{value.get_person ? value.get_person.first_name:''}} {{ value.get_person ? value.get_person.last_name:''}}</td>
                    <td>
                        <div class="m-demo__preview m-demo__preview--badge" v-if="value.status==1"> <span class="m-badge m-badge--success m-badge--wide m-badge--rounded"> <i class="la la-check-circle"></i> Active </span></div>
                        <div class="m-demo__preview m-demo__preview--badge" v-if="value.status==0"> <span class="m-badge m-badge--danger m-badge--wide m-badge--rounded"> <i class="la la-ban"></i> Inactive</span></div>
                    </td>
                    <td scope="row" class="width-100 text-center">
                        <a v-if="permissions.indexOf('department.edit') != -1" @click="openUpdateModal(value.id)"  class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la 	la-edit"></i></a>
                        <a v-if="permissions.indexOf('department.destroy') != -1" @click="deleteData(value.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                    </td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="6">No Data Available.</td>
                </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="text-center col-md-12" >
                    <pagination :resultData="resultData" @clicked="index" :mid-size="9"></pagination>
                </div>
            </div>
        </div>


        <AddDepartmant
                :permissions="permissions"
                :head_person_lists="head_person_lists"
                :div_lists="div_lists"
        ></AddDepartmant>
    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import AddDepartmant from './AddDepartmant.vue';
    import Pagination from  '../../../components/Pagination.vue';

    export default {
        components: {
            AddDepartmant,
            Pagination
        },

        props:['permissions','head_person_lists','div_lists'],

        data: function(){
            return {
                resultData: {},
                perPage: 10
            };
        },

        methods: {
            index(pageNo,perPage){
                if(pageNo){ pageNo = pageNo; }else{pageNo = this.resultData.current_page; }
                if(perPage){ perPage = perPage;}else{ perPage = this.perPage;}

                axios.get(base_url+"department/?page="+pageNo+"&perPage="+perPage).then((response) => {
                    this.resultData = response.data;

                });
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
                        swal("Deleted!", "Your Department has been deleted.", "success");
                        axios.delete(base_url+'department/'+id).then((response) => {
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

            openUpdateModal(id){
                EventBus.$emit('update-button-clicked', id);
            },

            changePerPage(){
                var vm = this;
                vm.index(1,vm.perPage);
            },

        },

        created() {
            var _this = this;
            _this.index(1);
            EventBus.$on('new-data-created', function () {
                _this.index(1);
            });
        }

    }
</script>

