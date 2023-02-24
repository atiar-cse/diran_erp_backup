<template>
    <div>

        <div class="m-portlet__body" v-if="data_list">
            <div class="row">
                <div class="item-show-limit col-md-8">
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
                <div class="col-md-4">
                    <div class="m-input-icon m-input-icon--left">
                        <input type="text" class="form-control m-input" placeholder="Search..." id="inputSearch">
                        <span class="m-input-icon__icon m-input-icon__icon--left">
                        <span><i class="la la-search"></i></span>
                    </span>
                    </div>
                </div>
            </div>
            <br><br>
            <!--begin: Datatable -->
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                <tr>
                    <th>S/L</th>
                    <th>Requisition NO</th>
                    <th>Requisition Date</th>
                    <th>Requisition Type</th>
                    <th>Production Qty</th>
                    <th>Total Qty</th>
                    <th>Total Price</th>
                    <th>Approve Status</th>
                    <th class="width-200 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''" id="dataTable">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.rm_requisition_no}}</td>
                    <td>{{ dateFormate(value.requisition_date) }}</td>
                    <td>{{ value.requisition_types }}</td>
                    <td>{{value.estimate_qty_for_production}}</td>
                    <td>{{value.total_qty}}</td>
                    <td>{{value.total_amount}}</td>
                    <td>
                        <span v-if="value.approve_status == 1" class="badge badge-pill badge-success">Approved</span>
                        <span v-if="value.approve_status == 0" class="badge badge-pill badge-danger">Pending</span>
                    </td>
                    <td scope="row" class="width-100 text-center">
                        <a v-if="permissions.indexOf('requisition-for-raw-material.show') != -1"
                           @click="viewData(value.id)"
                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="View"><i
                            class="la la-eye"></i></a>
                        <a v-if="permissions.indexOf('requisition-for-raw-material.edit') != -1 && value.approve_status != 1"
                           @click="editData(value.id)"
                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i
                            class="la la-edit"></i></a>
                        <a v-if="permissions.indexOf('requisition-for-raw-material.destroy') !=-1 && value.approve_status !=1 "
                           @click="deleteData(value.id)"
                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i
                            class="la la-trash-o"></i></a>
                    </td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="9" class="text-center">No Data Available.</td>
                </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="text-center col-md-12">
                    <pagination :resultData="resultData" @clicked="index" :mid-size="9"></pagination>
                </div>
            </div>
        </div>

        <AddRequisitionForRM
            v-else-if="add_form"
            :product_lists="product_lists"
            :warehouse_lists="warehouse_lists"
            :account_lists="account_lists"
            :permissions="permissions"
        ></AddRequisitionForRM>
        <EditRequisitionforRM
            v-else-if="edit_form"
            :product_lists="product_lists"
            :edit-id="edit_id"
            :warehouse_lists="warehouse_lists"
            :account_lists="account_lists"
            :permissions="permissions"
        ></EditRequisitionforRM>
        <ViewRequisitionForRm
            v-else-if="view_form"
            :edit-id="edit_id"
        ></ViewRequisitionForRm>

    </div>
</template>

<script>

    import {EventBus} from '../../../vue-assets';
    import Pagination from '../../../components/Pagination.vue';
    import AddRequisitionForRM from './AddRequisitionForRM.vue';
    import EditRequisitionforRM from './EditRequisitionforRM.vue';
    import ViewRequisitionForRm from './ViewRequisitionForRm.vue';


    export default {
        components: {
            AddRequisitionForRM,
            EditRequisitionforRM,
            ViewRequisitionForRm,
            Pagination
        },

        props: ['product_lists', 'permissions', 'warehouse_lists', 'account_lists'],

        data: function () {
            return {
                data_list: true,
                add_form: false,
                edit_form: false,
                edit_id: false,
                view_form: false,
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
                axios.get(base_url + "requisition-for-raw-material/?page=" + pageNo + "&perPage=" + perPage)
                    .then((response) => {
                        _this.$loading(false);
                        _this.resultData = response.data;
                    });
            },

            dateFormate(value) {
                if (value) {
                    return moment(String(value)).format('MM/DD/YYYY')
                }
            },

            editData(id) {
                var _this = this;
                _this.add_form = false;
                _this.data_list = false;
                _this.edit_form = true;
                _this.edit_id = id;
                $('#addButton').hide();
                $('#listButton').show();
            },

            viewData(id) {
                var _this = this;
                _this.add_form = false;
                _this.data_list = false;
                _this.edit_form = false;
                _this.view_form = true;
                _this.edit_id = id;
                $('#addButton').hide();
                $('#listButton').show();
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
                        axios.delete(base_url + 'requisition-for-raw-material/' + id + '/delete')
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

            datatables() {
                $(document).ready(function () {
                    $("#inputSearch").on("keyup", function () {
                        var value = $(this).val().toLowerCase();
                        $("#dataTable tr").filter(function () {
                            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                        });
                    });
                });
            },
        },

        mounted() {

        },

        created() {
            var _this = this;
            $('body').on('click', '#addButton', function () {
                _this.add_form = true;
                _this.data_list = false;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').hide();
                $('#listButton').show();
                _this.datatables();
            });

            $('body').on('click', '#listButton', function () {
                _this.add_form = false;
                _this.data_list = true;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').show();
                $('#listButton').hide();
            });

            EventBus.$on('data-changed', (id) => {
                _this.add_form = false;
                _this.data_list = true;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').show();
                $('#listButton').hide();
                _this.index(1);
            });

            _this.index(1);
            _this.datatables();
        },
    }
</script>

