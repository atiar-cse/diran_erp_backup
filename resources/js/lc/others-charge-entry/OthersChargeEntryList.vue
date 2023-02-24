<template>
    <div>
        <div class="m-portlet__body" v-if="data_list">
            <div class="row">
                <div class="item-show-limit col-md-8">
                </div>
                <div class="col-md-4">
                    <form class="form-inline d-flex mx-1 justify-content-end" @submit.stop.prevent="doSearch">
                        <div class="input-group">
                            <input class="form-control" id="quickSearch" placeholder="Quick search" type="search"
                                   v-model="quickSearch">
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
                <vdtnet-table :fields="fields" :id="tableId" :opts="options" @delete="doAlertDelete" @edit="doAlertEdit"
                              @view="doAlertView" ref="table">
                </vdtnet-table>
            </div>

        </div>

        <AddOthersChargeEntry :lc_cost_name_category_list="lc_cost_name_category_list"
                              :lc_lists="lc_lists"
                              :permissions="permissions"
                              v-else-if="add_form"
        ></AddOthersChargeEntry>
        <EditOthersChargeEntry :edit-id="edit_id"
                               :lc_cost_name_category_list="lc_cost_name_category_list"
                               :lc_lists="lc_lists"
                               :permissions="permissions"
                               v-else-if="edit_form"
        ></EditOthersChargeEntry>
        <ViewOthersChargeEntry
            v-else-if="view_form"
            :edit-id="edit_id"
        ></ViewOthersChargeEntry>
    </div>
</template>

<script>

    import {EventBus} from '../../vue-assets';
    import VdtnetTable from 'vue-datatables-net';
    import AddOthersChargeEntry from './AddOthersChargeEntry.vue';
    import EditOthersChargeEntry from './EditOthersChargeEntry.vue';
    import ViewOthersChargeEntry from './ViewOthersChargeEntry.vue';


    export default {
        components: {
            AddOthersChargeEntry,
            EditOthersChargeEntry,
            ViewOthersChargeEntry,
            VdtnetTable
        },

        props: ['permissions', 'lc_lists', 'lc_cost_name_category_list'],

        data: function () {
            return {
                data_list: true,
                add_form: false,
                edit_form: false,
                edit_id: false,
                view_form: false,

                //VdtnetTable Options
                tableId: 'others-charge-entry',
                options: {
                    ajax: {
                        url: base_url + "others-charge-entry",
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
                    lc_others_charge_no: {label: 'Lc Others Charge No.', sortable: true, searchable: true},
                    'lc_opening_sections.lc_no': {
                        label: 'LC No',
                        template: '{{ row.lc_no }}',
                        sortable: true,
                        searchable: true,
                        defaultOrder: 'DESC'
                    },
                    'lc_commercial_invoice_entries.ci_no': {
                        label: 'CI No',
                        template: '{{ row.ci_no }}',
                        sortable: true,
                        searchable: true
                    },
                    total_amount: {
                        label: 'Total Amount',
                        template: '<span class="text-center">{{ row.total_amount}}</span>'
                    },

                    approve_status: {
                        label: 'Approve State',
                        template: ' <div v-if="row.approve_status == 0"  class="badge badge-pill badge-danger" >Pending</div><div v-if="row.approve_status == 1"  class="badge badge-pill badge-success" >Approved</div>'
                    },
                    'lc_others_charge_entries.created_by': {
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
                            if (this.permissions.indexOf('others-charge-entry.edit') != -1 && row.approve_status != 1) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                            }
                            if (this.permissions.indexOf('others-charge-entry.destroy') != -1 && row.approve_status != 1) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="delete" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>';
                            }
                            if (this.permissions.indexOf('others-charge-entry.show') != -1) {
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
                _this.edit_form = false;
                _this.view_form = true;
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
                        swal("Deleted!", "Your Other Charge Entry has been deleted.", "success");
                        _this.$loading(true);
                        axios.delete(base_url + 'others-charge-entry/' + id)
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

