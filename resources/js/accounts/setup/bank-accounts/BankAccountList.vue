<template>
    <div>

        <div class="m-portlet__body" v-if="account_list">
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
                <vdtnet-table
                    :id="tableId"
                    ref="table"
                    :fields="fields"
                    :opts="options"
                    @edit="doAlertEdit"
                    @delete="doAlertDelete"

                >

                </vdtnet-table>
            </div>

        </div>

        <AddBankAccount
            v-else-if="add_form"
            :bank_lists="bank_lists"
            :chart_lists="chart_lists"
        ></AddBankAccount>
        <EditBankAccount
            v-else-if="edit_form"
            :bank_lists="bank_lists"
            :chart_lists="chart_lists"
            :edit-id="edit_id"
        ></EditBankAccount>

    </div>
</template>

<script>

    import {EventBus} from '../../../vue-assets';
    import VdtnetTable from 'vue-datatables-net';
    import AddBankAccount from './AddBankAccount.vue';
    import EditBankAccount from './EditBankAccount.vue';


    export default {
        components: {
            AddBankAccount,
            EditBankAccount,
            VdtnetTable
        },

        props: ['bank_lists', 'chart_lists', 'permissions'],

        data: function () {
            return {
                account_list: true,
                add_form: false,
                edit_form: false,
                edit_id: false,

                //VdtnetTable Options
                tableId: 'bank-account',
                options: {
                    ajax: {
                        url: base_url + "bank-account",
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
                    accounts_bank_accounts_date: {
                        label: 'Opening Date',
                        sortable: true,
                        searchable: true,
                        defaultOrder: 'DESC'
                    },
                    'accounts_bank_names.accounts_bank_names': {
                        label: 'Bank Name',
                        template: '{{row.accounts_bank_names}}',
                        sortable: true,
                        searchable: true
                    },
                    'accounts_bank_branches.bank_branch_names': {
                        label: 'Branch Name',
                        template: '{{row.bank_branch_names}}',
                        sortable: true,
                        searchable: true
                    },
                    'accounts_chartof_accounts.chart_of_accounts_title': {
                        label: 'COA',
                        template: '<span v-if="row.chart_of_accounts_title">{{row.chart_of_accounts_title}} ({{ row.chart_of_account_code }})</span>',
                        sortable: true,
                        searchable: true
                    },
                    accounts_bank_accounts_name: {label: 'Account Name', sortable: true, searchable: true},
                    accounts_bank_accounts_number: {label: 'Account Number', sortable: true, searchable: true},
                    accounts_bank_accounts_contact_person: {label: 'Contact Person'},
                    accounts_bank_accounts_contact_number: {label: 'Contact Number'},
                    'accounts_bank_accounts.created_by': {
                        label: 'User',
                        template: '<span v-if="row.ad_name">Added By : {{ row.ad_name }}</span><br><span v-if="row.ed_name">Edited By  : {{ row.ed_name }}</span>',
                        sortable: false,
                        searchable: false
                    },
                    status: {
                        label: 'Status',
                        render: (data) => {
                            if (data == 1) {
                                return '<div class="m-demo__preview m-demo__preview--badge"> <span class="m-badge m-badge--success m-badge--wide m-badge--rounded"> <i class="la la-check-circle"></i> Active </span></div>';
                            } else {
                                return '<div class="m-demo__preview m-demo__preview--badge"> <span class="m-badge m-badge--danger m-badge--wide m-badge--rounded"> <i class="la la-ban"></i> Inactive</span></div>';
                            }
                        }
                    },

                    actions: {
                        isLocal: true,
                        label: 'Actions',
                        render: (data) => {
                            var htmlButtons = '';
                            if (this.permissions.indexOf('bank-account.edit') != -1) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                            }
                            if (this.permissions.indexOf('bank-account.destroy') != -1) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="delete" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>';
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
                _this.account_list = false;
                _this.edit_form = true;
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
                        swal("Deleted!", "Your imaginary file has been deleted.", "success");
                        _this.$loading(true);
                        axios.delete(base_url + 'bank-account/' + id + '/delete')
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
                _this.account_list = false;
                _this.edit_form = false;
                $('#addButton').hide();
                $('#listButton').show();

            });

            $('body').on('click', '#listButton', function () {
                _this.add_form = false;
                _this.account_list = true;
                _this.edit_form = false;
                $('#addButton').show();
                $('#listButton').hide();
            });

            EventBus.$on('data-changed', (id) => {
                _this.add_form = false;
                _this.account_list = true;
                _this.edit_form = false;
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

