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

        <AddCashReceiptVoucher
                v-else-if="add_form"
                :cost_center_list="cost_center_list"
                :account_list="account_list"
                :subcode2_list="subcode2_list"
                :branch_list="branch_list"
                :permissions="permissions"
        ></AddCashReceiptVoucher>
        <EditCashReceiptVoucher
                v-else-if="edit_form"
                :edit-id="edit_id"
                :cost_center_list="cost_center_list"
                :account_list="account_list"
                :subcode2_list="subcode2_list"
                :branch_list="branch_list"
                :permissions="permissions"
        ></EditCashReceiptVoucher>
        <ViewCashReceipt
                v-else-if="view_form"
                :edit-id="edit_id"
        ></ViewCashReceipt>

    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import VdtnetTable from 'vue-datatables-net';
    import AddCashReceiptVoucher from './AddCashReceiptVoucher.vue';
    import EditCashReceiptVoucher from './EditCashReceiptVoucher.vue';
    import ViewCashReceipt from './ViewCashReceipt.vue';


    export default {
        components: {
            AddCashReceiptVoucher,
            EditCashReceiptVoucher,
            ViewCashReceipt,
            VdtnetTable
        },

        props:['permissions','cost_center_list','account_list','subcode2_list','branch_list'],

        data: function(){
            return {
                data_list:true,
                add_form:false,
                edit_form:false,
                edit_id:false,
                //VdtnetTable Options
                tableId: 'cash-receipt-voucher',
                options: {
                    ajax: {
                        url: base_url+"cash-receipt-voucher",
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
                    id: { label: 'ID' },
                    receipt_transaction_no: { label: 'Receipt NO', sortable: true, searchable: true, defaultOrder: 'DESC'  },
                    receipt_date: { label: 'Receipt Date',sortable: true, searchable: true  },
                    total_debit_amount: { label: 'Total Debit' },
                    total_credit_amount: { label: 'Total Credit' },
                    approve_status: { label: 'Approve State',template:' <div v-if="row.approve_status == 0"  class="badge badge-pill badge-danger" >Pending</div><div v-if="row.approve_status == 1"  class="badge badge-pill badge-success" >Approved</div>' },

                    'accounts_cash_receipt_vouchers.created_by': { label: 'User', template: '<span v-if="row.ad_name">Added By : {{ row.ad_name }}</span><br><span v-if="row.ed_name">Edited By  : {{ row.ed_name }}</span><br><span v-if="row.approve_status==1 && row.ap_name">Approved By  : {{ row.ap_name }} </span><br>', sortable: false, searchable: false },


                    actions: {
                        isLocal: true,
                        label: 'Actions',
                        render: (data, type, row, meta) => {
                            var htmlButtons = '';
                            if (this.permissions.indexOf('cash-receipt-voucher.edit') != -1 && row.approve_status !=1 ) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                            }
                            if (this.permissions.indexOf('cash-receipt-voucher.destroy') != -1 && row.approve_status !=1) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="delete" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>';
                            }
                            if (this.permissions.indexOf('cash-receipt-voucher.show') != -1) {
                                htmlButtons = htmlButtons + ' <a class="btn btn-outline-success m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-action="view"  data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="View"><i class="la la-eye"></i></a>';
                            }
                            if ((this.permissions.indexOf('super-admin') != -1 || this.permissions.indexOf('cash-receipt-voucher.unapprove') != -1) && row.approve_status ==1 ) {
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
                        axios.delete(base_url+'cash-receipt-voucher/'+id).then((response) => {

                            _this.showMassage(response.data);
                            tr.remove()
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
                        axios.get(base_url+'cash-receipt-voucher/'+id+'?isSuperAdmin=unapprove').then((response) => {
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

