<template>
    <div>
        <div class="m-portlet__body" v-if="data_list">
            <div class="row">
                <div class="item-show-limit col-md-8">
                </div>
                <div class="col-md-4">
                    <form class="form-inline d-flex mx-1 justify-content-end" @submit.stop.prevent="doSearch">
                        <div class="input-group">
                            <input  v-model="quickSearch"  id="quickSearch" type="search" placeholder="Quick search" class="form-control">
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

                        @view="doAlertView"
                        @superAdmin="doSuperAdmin"
                >

                </vdtnet-table>
            </div>

        </div>

        <AddContraEntryVoucher
                v-else-if="add_form"
                :bank_wise_account_list="bank_wise_account_list"
                :account_list="account_list"
                :bank_list="bank_list"
                :branch_list="branch_list"
                :permissions="permissions"
        ></AddContraEntryVoucher>
        <EditContraEntryVoucher
                v-else-if="edit_form"
                :bank_wise_account_list="bank_wise_account_list"
                :account_list="account_list"
                :bank_list="bank_list"
                :branch_list="branch_list"
                :edit-id="edit_id"
                :permissions="permissions"
        ></EditContraEntryVoucher>
        <ViewContraEntry
                v-else-if="view_form"
                :edit-id="edit_id"
        ></ViewContraEntry>

    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import VdtnetTable from 'vue-datatables-net';
    import AddContraEntryVoucher from './AddContraEntryVoucher.vue';
    import EditContraEntryVoucher from './EditContraEntryVoucher.vue';
    import ViewContraEntry from './ViewContraEntry.vue';


    export default {
        components: {
            AddContraEntryVoucher,
            EditContraEntryVoucher,
            ViewContraEntry,
            VdtnetTable
        },

        props:['bank_wise_account_list','account_list','bank_list','branch_list','permissions'],

        data: function(){
            return {
                data_list:true,
                add_form:false,
                edit_form:false,
                edit_id:false,

                //VdtnetTable Options
                tableId: 'contra-entry-voucher',
                options: {
                    ajax: {
                        url: base_url+"contra-entry-voucher",
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
                    id: { label: 'ID', },
                    contra_entry_no: { label: 'Contra NO', sortable: true, searchable: true, defaultOrder: 'DESC'  },
                    contra_date: { label: 'Contra Date',sortable: true, searchable: true  },
                    payment_method: { label: 'Payment Method',template: '<span v-if="row.payment_method == 1">Cash</span> <span v-if="row.payment_method == 2">Cheque</span> <span v-if="row.payment_method == 3">Bank Deposit</span> <span v-if="row.payment_method == 4">Card</span> <span v-if="row.payment_method == 5">Account Transfer</span>', sortable: false, searchable: false},
                    amount: { label: 'Amount' },
                    'accounts_contra_entry_vouchers.debit_account_id': { label: 'Chart of Account',template: '<span v-if="row.debit_account_id !=null"> Debit: {{ row.dr_chart_of_accounts_title}}({{row.dr_chart_of_account_code}})</span><br><span v-if="row.credit_account_id !=null">Credit  : {{row.cr_chart_of_accounts_title}}({{row.cr_chart_of_account_code}})</span></span>', sortable: false, searchable: false},
                    approve_status: { label: 'Approve State',template:' <div v-if="row.approve_status == 0"  class="badge badge-pill badge-danger" >Pending</div><div v-if="row.approve_status == 1"  class="badge badge-pill badge-success" >Approved</div>' },
                    'accounts_contra_entry_vouchers.created_by': { label: 'User', template: '<span v-if="row.ad_name">Added By : {{ row.ad_name }}</span><br><span v-if="row.ed_name">Edited By  : {{ row.ed_name }}</span><br><span v-if="row.approve_status==1 && row.ap_name">Approved By  : {{ row.ap_name }} </span><br>', sortable: false, searchable: false },

                    actions: {
                        isLocal: true,
                        label: 'Actions',
                        render: (data, type, row, meta) => {
                            var htmlButtons = '';
                            if (this.permissions.indexOf('contra-entry-voucher.edit') != -1 && row.approve_status !=1 ) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                            }
                            if (this.permissions.indexOf('contra-entry-voucher.destroy') != -1 && row.approve_status !=1) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="delete" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>';
                            }
                            if (this.permissions.indexOf('contra-entry-voucher.show') != -1 && row.approve_status ==1) {
                                htmlButtons = htmlButtons + ' <a class="btn btn-outline-success m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-action="view"  data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="View"><i class="la la-eye"></i></a>';
                            }
                            if ((this.permissions.indexOf('super-admin') != -1 || this.permissions.indexOf('contra-entry-voucher.unapprove') != -1) && row.approve_status ==1 ) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="superAdmin" class="btn btn-outline-warning m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Unapprove"><i class="fa fa-times"></i></a>';
                            }
                            return htmlButtons;
                        }
                    }
                },
                quickSearch: '',
            };
        },

        methods: {

            dateFormate(value){
                if (value) {
                    return moment(String(value)).format('MM/DD/YYYY')
                }
            },

            editData(id){

                var _this = this;
                _this.add_form = false;
                _this.data_list = false;
                _this.edit_form = true;
                _this.edit_id = id;
                $('#addButton').hide();
                $('#listButton').show();
            },

            viewData(id){
                var _this = this;
                _this.add_form = false;
                _this.data_list = false;
                _this.edit_form = false;
                _this.view_form = true;
                _this.edit_id = id;
                $('#addButton').hide();
                $('#listButton').show();
            },


            deleteData(id,tr){
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
                        axios.delete(base_url+'contra-entry-voucher/'+id).then((response) => {

                            _this.showMassage(response.data);
                            tr.remove();
                        }).catch((error)=>{
                            swal('Error:','Something Error Found !, Please try again','error');
                        });
                    });
            },

            doSuperAdmin(data){ //Unapprove
                var _this = this;
                var id = data.id;
                swal({
                        title: "Are you sure?",
                        text: "It will be marked as Unapproved and Amounts will be removed from Accounts!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, unapprove it!",
                        closeOnConfirm: false
                    },
                    function(){
                        axios.get(base_url+'contra-entry-voucher/'+id+'?isSuperAdmin=unapprove').then((response) => {
                            if (response.data.status == 'success') {
                                swal("Unapproved!", "The record has been unapproved!.", "success");
                                _this.showMassage(response.data);
                                _this.doReload();
                            } else {
                                _this.showMassage(response.data);
                            }
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

            //VdtnetTable Methods
            doAlertEdit(data) {
                this.editData(data.id);
            },

            doAlertView(data) {
                this.viewData(data.id);
            },
            doAlertDelete(data, row, tr, target) {

                this.deleteData(data.id,tr);
            },
            doSearch() {
                this.$refs.table.search(this.quickSearch)
            },
            doExport(type) {
                const parms = this.$refs.table.getServerParams()
                parms.export = type
                window.alert('GET /api/v1/export?' + jQuery.param(parms))
            },
            doReload() {
                const table = this.$refs.table
                setTimeout(() => { // simulate extra long running ajax
                    table.reload()
                }, 500)
            }

        },

        created(){

            var _this = this;
            $('body').on('click', '#addButton', function() {
                _this.add_form = true;
                _this.data_list = false;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').hide();
                $('#listButton').show();

            });

            $('body').on('click', '#listButton', function() {
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
            },900);
        },
    }
</script>

