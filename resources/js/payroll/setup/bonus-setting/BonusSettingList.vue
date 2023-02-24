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
                    <th>Festival Name</th>
                    <th>Bonus Type</th>
                    <th>Percentage Of Basic</th>
                    <th>Status</th>
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.festival_name}}</td>
                    <td>{{value.bonus_type}}</td>
                    <td>{{value.percentage_of_bonus}}</td>
                    <td>{{value.bonus_type}}</td>
                    <td scope="row" class="width-100 text-center">
                        <a  @click="openUpdateModal(value.id)"  class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la 	la-edit"></i></a>
                        <a  @click="deleteData(value.id)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                        <!--<a v-if="permissions.indexOf('businessTypeRegistrationDestroy') !=-1"  @click="deleteData(value.id)" class="btn btn-danger btn-sm"><i aria-hidden="true" class="fa fa-trash-o btnColor"></i></a>
                        <a v-if="permissions.indexOf('businessTypeRegistrationEdit') !=-1" @click="openUpdateModal(value.id)" class="btn btn-primary btn-sm"><i aria-hidden="true" class="fa fa-pencil-square-o btnColor"></i></a>-->
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



        <AddBonusSetting></AddBonusSetting>
    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import AddBonusSetting from './AddBonusSetting.vue';
    import Pagination from  '../../../components/Pagination.vue';

    export default {
        components: {
            AddBonusSetting,
            Pagination
        },

        props:['permissions'],

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

                axios.get(base_url+"bonus-setting/?page="+pageNo+"&perPage="+perPage).then((response) => {
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
                        swal("Deleted!", "Your imaginary file has been deleted.", "success");
                        axios.delete(base_url+'bonus-setting/'+id).then((response) => {
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

