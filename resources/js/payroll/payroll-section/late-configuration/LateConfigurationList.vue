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
                    <th>Order NO</th>
                    <th>Purchase Date</th>
                    <th>Supplier Id</th>
                    <th>Requition Id</th>
                    <th>Order Docs</th>
                    <th>Narration</th>
                    <th>Total Qty</th>
                    <th>Total Price</th>
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.po_order_no}}</td>
                    <td>{{value.po_receive_date}}</td>
                    <td>{{value.purchase_supplier_name}}</td>
                    <td>{{value.purchase_requisition_no}}</td>
                    <td>{{value.po_order_docs}}</td>
                    <td>{{value.po_narration}}</td>
                    <td>{{value.po_total_qty}}</td>
                    <td>{{value.po_total_price}}</td>
                    <td scope="row" class="width-100 text-center">
                        <a @click="editData(value.id)"
                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i
                            class="la 	la-edit"></i></a>
                        <a @click="deleteData(value.id)"
                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i
                            class="la la-trash-o"></i></a>
                    </td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="10" class="text-center">No Data Available.</td>
                </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="text-center col-md-12">
                    <pagination :resultData="resultData" @clicked="index" :mid-size="9"></pagination>
                </div>
            </div>
        </div>

        <AddPurchaseReceive v-else-if="add_form" :product_lists="product_lists" :po_order_generate="po_order_generate"
                            :supplier_lists="supplier_lists"
                            :requisition_lists="requisition_lists"></AddPurchaseReceive>
        <EditPurchaseReceive v-else-if="edit_form" :product_lists="product_lists" :edit-id="edit_id"
                             :supplier_lists="supplier_lists"
                             :requisition_lists="requisition_lists"></EditPurchaseReceive>

    </div>
</template>

<script>

    import {EventBus} from '../../../vue-assets';
    import Pagination from '../../../components/Pagination.vue';
    import AddPurchaseReceive from './AddPurchaseReceive.vue';
    import EditPurchaseReceive from './EditPurchaseReceive.vue';


    export default {
        components: {
            AddPurchaseReceive,
            EditPurchaseReceive,
            Pagination
        },

        props: ['product_lists', 'po_order_generate', 'supplier_lists', 'requisition_lists'],

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
                axios.get(base_url + "po-receive/?page=" + pageNo + "&perPage=" + perPage)
                    .then((response) => {
                        _this.$loading(false);
                        _this.resultData = response.data;
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
                        _this.$loading(true);
                        axios.delete(base_url + 'po-receive/' + id + '/delete')
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

