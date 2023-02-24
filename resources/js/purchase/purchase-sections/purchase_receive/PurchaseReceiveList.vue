<template>
    <div>
        <div class="m-portlet__body" v-if="data_list">
            <div class="row">
                <div class="item-show-limit col-md-8">
                </div>
                <div class="col-md-4">
                    <form class="form-inline d-flex mx-1 justify-content-end" @submit.stop.prevent="doSearch">
                        <div class="input-group">
                            <input v-model="quickSearch" id="quickSearch" type="search" placeholder="Quick search"
                                   class="form-control">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-magnify"/> Go
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--begin: Datatable -->

            <div class="table-responsive">
                <vdtnet-table :id="tableId" ref="table" :fields="fields" :opts="options" @edit="doAlertEdit"
                              @delete="doAlertDelete" @view="doAlertView">
                </vdtnet-table>
            </div>
        </div>

        <AddPurchaseReceive
            v-else-if="add_form"
            :permissions="permissions"
            :product_lists="product_lists"
            :supplier_lists="supplier_lists"
            :warehouse_lists="warehouse_lists"
            :cost_type_lists="cost_type_lists"
        ></AddPurchaseReceive>
        <EditPurchaseReceive
            v-else-if="edit_form"
            :edit-id="edit_id"
            :permissions="permissions"
            :product_lists="product_lists"
            :supplier_lists="supplier_lists"
            :warehouse_lists="warehouse_lists"
        ></EditPurchaseReceive>
        <ViewPurchaseReceive
            v-else-if="view_form"
            :edit-id="edit_id"
        ></ViewPurchaseReceive>

    </div>
</template>

<script>

    import {EventBus} from '../../../vue-assets';
    import VdtnetTable from 'vue-datatables-net';
    import AddPurchaseReceive from './AddPurchaseReceive.vue';
    import EditPurchaseReceive from './EditPurchaseReceive.vue';
    import ViewPurchaseReceive from './ViewPurchaseReceive.vue';


    export default {
        components: {
            AddPurchaseReceive,
            EditPurchaseReceive,
            ViewPurchaseReceive,
            VdtnetTable
        },

        props: ['permissions', 'product_lists', 'supplier_lists', 'warehouse_lists', 'cost_type_lists'],

        data: function () {
            return {
                data_list: true,
                add_form: false,
                edit_form: false,
                edit_id: false,
                view_form: false,

                //VdtnetTable Options
                tableId: 'po-receive',
                options: {
                    ajax: {
                        url: base_url + "po-receive",
                        dataSrc: (json) => {
                            return json.data
                        }
                    },
                    responsive: false,
                    processing: true,
                    searching: true,
                    searchDelay: 1500,
                    destroy: true,
                    ordering: true,
                    lengthChange: true,
                    serverSide: true,
                    fixedHeader: true,
                    saveState: true,
                    stateSave: true
                },
                fields: {

                    id: {label: 'ID'},
                    po_order_no: {label: 'Goods Receive NO', sortable: true, searchable: true, defaultOrder: 'ASC'},
                    po_receive_date: {label: 'Purchase Date', sortable: true, searchable: true},
                    'purchase_requisitions.purchase_requisition_no': {
                        label: 'Req. No',
                        template: '{{row.purchase_requisition_no}}',
                        sortable: true,
                        searchable: true
                    },
                    'purchase_supplier_entries.purchase_supplier_name': {
                        label: 'Supplier',
                        template: '{{row.purchase_supplier_name}}',
                        sortable: true,
                        searchable: true
                    },
                    po_narration: {
                        label: 'Narration',
                        template: '<span style="white-space: pre-line;">{{ row.po_narration }}</span>'
                    },
                    po_total_qty: {label: 'Total Qty'},
                    po_total_price: {label: 'Total Price'},
                    approve_status: {
                        label: 'Approve State',
                        template: ' <div v-if="row.approve_status == 0"  class="badge badge-pill badge-danger" >Pending</div><div v-if="row.approve_status == 1"  class="badge badge-pill badge-success" >Approved</div>'
                    },
                    'purchase_order_receives.created_by': {
                        label: 'User',
                        template: '<span v-if="row.ad_name">Added By : {{ row.ad_name }}</span><br><span v-if="row.ed_name">Edited By  : {{ row.ed_name }}</span><br><span v-if="row.approve_status==1 && row.ap_name">Approved By  : {{ row.ap_name }} </span><br>',
                        sortable: false,
                        searchable: false
                    },

                    actions: {
                        isLocal: true,
                        label: 'Actions',
                        render: (data, type, row, meta) => {
                            var htmlButtons = '';
                            if (this.permissions.indexOf('po-receive.edit') != -1 && row.approve_status == 0) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                            }
                            if (this.permissions.indexOf('po-receive.destroy') != -1 && row.approve_status == 0) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="delete" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>';
                            }
                            if (this.permissions.indexOf('po-receive.show') != -1) {
                                htmlButtons = htmlButtons + ' <a class="btn btn-outline-success m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-action="view"  data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="View"><i class="la la-eye"></i></a>';
                            }
                            return htmlButtons;
                        }
                    }
                },
                quickSearch: '',
            };
        },

        methods: {

            editData(id) {
                var _this = this;
                _this.add_form = false;
                _this.data_list = false;
                _this.edit_form = true;
                _this.edit_id = id;
                _this.view_form = false;
                $('#addButton').hide();
                $('#listButton').show();
            },

            viewData(id) {
                var _this = this;
                _this.add_form = false;
                _this.data_list = false;
                _this.view_form = true;
                _this.edit_form = false;
                _this.edit_id = id;
                $('#addButton').hide();
                $('#listButton').show();
            },

            deleteData(id, tr) {
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
                        swal("Deleted!", "Your Purchase Oreder has been deleted.", "success");
                        _this.$loading(true);
                        axios.delete(base_url + 'po-receive/' + id + '/delete')
                            .then((response) => {
                                _this.$loading(false);
                                _this.showMassage(response.data);
                                tr.remove();
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

            //VdtnetTable Methods
            doAlertEdit(data) {
                this.editData(data.id);
            },
            doAlertView(data) {
                this.viewData(data.id);
            },
            doAlertDelete(data, row, tr, target) {
                this.deleteData(data.id, tr);
            },
            doSearch() {
                this.$refs.table.search(this.quickSearch)
            },
            doExport(type) {
                const parms = this.$refs.table.getServerParams()
                parms.export = type
                window.alert('GET /api/v1/export?' + jQuery.param(parms))
            }
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

            });

            setTimeout(function () {
                const vdt_parms = _this.$refs.table.getServerParams();
                if (vdt_parms.search.value) {
                    _this.quickSearch = vdt_parms.search.value;
                    $('#quickSearch').focus();
                }
            }, 900);
        },
    }
</script>

