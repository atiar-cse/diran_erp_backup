<template>
    <div>
        <div class="m-portlet__body" v-if="product_list">
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
                    <th>Title</th>
                    <th>In Start</th>
                    <th>In time</th>
                    <th>Late Start</th>
                    <th>In End</th>
                    <th>Out Start</th>
                    <th>Out End</th>
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.title}}</td>
                    <td>{{value.in_start}}</td>
                    <td>{{value.in_time}}</td>
                    <td>{{value.late_start}}</td>
                    <td>{{value.in_end}}</td>
                    <td>{{value.out_start}}</td>
                    <td>{{value.out_end}}</td>
                    <td class="width-100 text-center" scope="row">
                        <a @click="editData(value.id)"
                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-offset="-20px -20px" data-placement="top" data-toggle="m-tooltip" title="Edit"><i
                            class="la 	la-edit"></i></a>
                        <a @click="deleteData(value.id)"
                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-offset="-20px -20px" data-placement="top" data-toggle="m-tooltip" title="Delete"><i
                            class="la la-trash-o"></i></a>
                    </td>

                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td class="text-center" colspan="12">No Data Available.</td>
                </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="text-center col-md-12">
                    <pagination :mid-size="9" :resultData="resultData" @clicked="index"></pagination>
                </div>
            </div>
        </div>

        <AddShiftSchedule v-else-if="add_form"></AddShiftSchedule>

        <EditShiftSchedule :edit-id="edit_id" v-else-if="edit_form"></EditShiftSchedule>
    </div>
</template>

<script>

    import {EventBus} from '../../../vue-assets';
    import Pagination from '../../../components/Pagination.vue';
    import AddShiftSchedule from './AddShiftSchedule.vue';
    import EditShiftSchedule from './EditShiftSchedule.vue';


    export default {
        components: {
            AddShiftSchedule,
            EditShiftSchedule,
            Pagination
        },

        props: ['permissions'],

        data: function () {
            return {
                product_list: true,
                add_form: false,
                edit_form: false,
                edit_id: false,
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

                axios.get(base_url + "shift-schedule/?page=" + pageNo + "&perPage=" + perPage).then((response) => {
                    this.resultData = response.data;
                });
            },

            editData(id) {
                var _this = this;
                _this.add_form = false;
                _this.product_list = false;
                _this.edit_form = true;
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
                        axios.delete(base_url + 'shift-schedule/' + id).then((response) => {
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

            changePerPage() {
                var vm = this;
                vm.index(1, vm.perPage);
            },
        },

        created() {
            var _this = this;
            $('body').on('click', '#addButton', function () {
                _this.add_form = true;
                _this.product_list = false;
                _this.edit_form = false;
                $('#addButton').hide();
                $('#listButton').show();
            });

            $('body').on('click', '#listButton', function () {
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

