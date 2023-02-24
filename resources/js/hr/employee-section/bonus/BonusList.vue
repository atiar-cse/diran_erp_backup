<template>
    <div>
        <div class="m-portlet__body">
            <div class="item-show-limit">
                <span>Show</span>
                <select @change="changePerPage" class="per_page" name="per_page" v-model="perPage">
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
                    <th>Bonus Name</th>
                    <th>Unit Name</th>
                    <th>Employee Type</th>
                    <th>Start Month</th>
                    <th>End Month</th>
                    <th>Salary Type</th>
                    <th>Amount Percent</th>
                    <th>Bonus Percent</th>
                    <th>Effective Date</th>
                    <th>Status</th>
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.title}}</td>
                    <td>{{value.get_unit ? value.get_unit.unit_name:''}}</td>
                    <td>{{value.get_employee_type ? value.get_employee_type.title:''}}</td>
                    <td>{{value.start_month}}</td>
                    <td>{{value.end_month}}</td>
                    <td>{{value.salary_type}}</td>
                    <td>{{value.amount_percent}}</td>
                    <td>{{value.bonus_percent}}</td>
                    <td>{{value.effective_date}}</td>
                    <td>
                        <div class="m-demo__preview m-demo__preview--badge" v-if="value.status==1"><span
                            class="m-badge m-badge--success m-badge--wide m-badge--rounded"> <i
                            class="la la-check-circle"></i> Active </span></div>
                        <div class="m-demo__preview m-demo__preview--badge" v-if="value.status==0"><span
                            class="m-badge m-badge--danger m-badge--wide m-badge--rounded"> <i class="la la-ban"></i> Inactive</span>
                        </div>
                    </td>
                    <td class="width-100 text-center" scope="row">
                        <a @click="openUpdateModal(value.id)"
                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-offset="-20px -20px"
                           data-placement="top" data-toggle="m-tooltip" title="Edit"
                           v-if="permissions.indexOf('bonus.edit') != -1"><i
                            class="la 	la-edit"></i></a>
                        <a @click="deleteData(value.id)"
                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-offset="-20px -20px"
                           data-placement="top" data-toggle="m-tooltip" title="Delete"
                           v-if="permissions.indexOf('bonus.destroy') != -1"><i
                            class="la la-trash-o"></i></a>
                    </td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="12">No Data Available.</td>
                </tr>
                </tbody>
            </table>

            <div class="row">
                <div class="text-center col-md-12">
                    <pagination :mid-size="9" :resultData="resultData" @clicked="index"></pagination>
                </div>
            </div>
        </div>

        <AddBonus
            :employee_type_list="employee_type_list"
            :unit_list="unit_list"
        ></AddBonus>
    </div>
</template>

<script>

    import {EventBus} from '../../../vue-assets';
    import AddBonus from './AddBonus.vue';
    import Pagination from '../../../components/Pagination.vue';

    export default {
        components: {
            AddBonus,
            Pagination
        },

        props: ['permissions', 'unit_list', 'employee_type_list'],

        data: function () {
            return {
                resultData: {},
                perPage: 10
            };
        },

        methods: {
            index(pageNo, perPage) {
                if (pageNo) {
                    pageNo = pageNo;
                } else {
                    pageNo = this.resultData.current_page;
                }
                if (perPage) {
                    perPage = perPage;
                } else {
                    perPage = this.perPage;
                }

                axios.get(base_url + "bonus/?page=" + pageNo + "&perPage=" + perPage).then((response) => {
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
                        axios.delete(base_url + 'bonus/' + id).then((response) => {
                            _this.index(1);
                            _this.showMassage(response.data);
                        }).catch((error) => {
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

