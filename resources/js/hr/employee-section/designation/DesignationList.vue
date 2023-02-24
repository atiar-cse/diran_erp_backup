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
                    <th>Designation Name</th>
                    <th>Employee Level</th>
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.designation_name}}</td>
                    <td>{{value.get_employee_level ? value.get_employee_level.employee_level_name:''}}</td>
                    <td scope="row" class="width-100 text-center">
                        <a @click="openUpdateModal(value.id)"
                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-offset="-20px -20px"
                           data-placement="top" data-toggle="m-tooltip" title="Edit"
                           v-if="permissions.indexOf('designation.edit') != -1"><i
                            class="la 	la-edit"></i></a>
                        <a @click="deleteData(value.id)"
                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-offset="-20px -20px"
                           data-placement="top" data-toggle="m-tooltip" title="Delete"
                           v-if="permissions.indexOf('designation.destroy') != -1"><i
                            class="la la-trash-o"></i></a>
                    </td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="5">No Data Available.</td>
                </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="text-center col-md-12">
                    <pagination :resultData="resultData" @clicked="index" :mid-size="9"></pagination>
                </div>
            </div>
        </div>

        <AddDesignation
            :employee_level_list="employee_level_list"
            :permissions="permissions"
        ></AddDesignation>
    </div>
</template>

<script>

    import {EventBus} from '../../../vue-assets';
    import AddDesignation from './AddDesignation.vue';
    import Pagination from '../../../components/Pagination.vue';
    import VdtnetTable from 'vue-datatables-net';

    export default {
        components: {
            AddDesignation,
            Pagination,
            VdtnetTable
        },

        props: ['permissions', 'employee_level_list'],

        data: function () {
            return {
                resultData: {},
                perPage: 10
            };
        },

        methods: {
            index(pageNo, perPage) {
                var _this = this;

                if (pageNo) {
                    pageNo = pageNo;
                } else {
                    pageNo = _this.resultData.current_page;
                }
                if (perPage) {
                    perPage = perPage;
                } else {
                    perPage = _this.perPage;
                }

                _this.$loading(true);
                axios.get(base_url + "designation/?page=" + pageNo + "&perPage=" + perPage)
                    .then((response) => {
                        _this.$loading(false);
                        this.resultData = response.data;
                    });
            },

            deleteData(id) {
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
                    function () {
                        swal("Deleted!", "Your imaginary file has been deleted.", "success");
                        _this.$loading(true);
                        axios.delete(base_url + 'designation/' + id)
                            .then((response) => {
                                _this.$loading(false);
                                _this.index(1);
                                _this.showMassage(response.data);
                            })
                            .catch((error) => {
                                _this.$loading(false);
                                swal('Error:', 'Something Error Found !, Please try again', 'error');
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

            openUpdateModal(id) {
                EventBus.$emit('update-button-clicked', id);
            },

            changePerPage() {
                var vm = this;
                vm.index(1, vm.perPage);
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

