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

        <AddPurchaseRequisition
            v-else-if="add_form"
                :permissions="permissions"
                :product_lists="product_lists"
                :enum_val="enum_val"
        ></AddPurchaseRequisition>
        <EditPurchaseRequisition
            v-else-if="edit_form"
                :permissions="permissions"
                :edit-id="edit_id"
                :product_lists="product_lists"
                :enum_val="enum_val"
        ></EditPurchaseRequisition>
        <ViewPurchaseRequisition
            v-else-if="view_form"
            :edit-id="edit_id"
        ></ViewPurchaseRequisition>


    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import VdtnetTable from 'vue-datatables-net';
    import AddPurchaseRequisition from './AddPurchaseRequisition.vue';
    import EditPurchaseRequisition from './EditPurchaseRequisition.vue';
    import ViewPurchaseRequisition from './ViewPurchaseRequisition.vue';


    export default {
        components: {
            AddPurchaseRequisition,
            EditPurchaseRequisition,
            ViewPurchaseRequisition,
            VdtnetTable
        },

        props:['permissions','product_lists','enum_val'],

        data: function(){
            return {
                data_list:true,
                add_form:false,
                edit_form:false,
                view_form:false,
                edit_id:false,


                //VdtnetTable Options
                tableId: 'pr-requisition',
                options: {
                    ajax: {
                        url: base_url+"pr-requisition",
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
                    purchase_requisition_no: { label: 'Requisition NO', sortable: true, searchable: true, defaultOrder: 'DESC'  },
                    purchase_requisition_date: { label: 'Requisition Date', sortable: true, searchable: true  },
                    purchase_requisition_total_qty: { label: 'Total Qty' },
                    purchase_requisition_total_price: { label: 'Total Price' },
                    purchase_requisition_narration: {
                        label: 'Narration',
                        template: '<span style="white-space: pre-line;">{{ row.purchase_requisition_narration }}</span>'
                    },
                    purchase_requisition_supervisor_narration: {
                        label: 'Supp. Narration',
                        template: '<span style="white-space: pre-line;">{{ row.purchase_requisition_supervisor_narration }}</span>'
                    },
                    approve_status: { label: 'Approve State',template:' <div v-if="row.approve_status == 0"  class="badge badge-pill badge-danger" >Pending</div><div v-if="row.approve_status == 1"  class="badge badge-pill badge-success" >Approved</div>' },
                    'purchase_requisitions.created_by': { label: 'User', template: '<span v-if="row.ad_name">Added By : {{ row.ad_name }}</span><br><span v-if="row.ed_name">Edited By  : {{ row.ed_name }}</span><br><span v-if="row.approve_status==1 && row.ap_name">Approved By  : {{ row.ap_name }} </span><br>', sortable: false, searchable: false },

                    actions: {
                        isLocal: true,
                        label: 'Actions',
                        render: (data,type,row,meta) => {
                            var htmlButtons = '';
                            if (this.permissions.indexOf('pr-requisition.edit') != -1 && row.approve_status !=1) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                            }
                            if (this.permissions.indexOf('pr-requisition.destroy') != -1 && row.approve_status !=1) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="delete" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>';
                            }
                            if (this.permissions.indexOf('pr-requisition.show') != -1 ) {
                                htmlButtons = htmlButtons + ' <a class="btn btn-outline-success m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-action="view"  data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="View"><i class="la la-eye"></i></a>';
                            }
                            if ((this.permissions.indexOf('super-admin') != -1 || this.permissions.indexOf('pr-requisition.unapprove') != -1) && row.approve_status ==1 ) {
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

            editData(id){
                var _this = this;
                _this.add_form = false;
                _this.edit_form = true;
                _this.data_list = false;
                _this.view_form = true;
                _this.edit_id = id;
                $('#addButton').hide();
                $('#listButton').show();
            },

            viewData(id){
                var _this = this;
                _this.add_form = false;
                _this.data_list = false;
                _this.view_form = true;
                _this.edit_form = false;
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
                        swal("Deleted!", "Your Purchase Requisition No has been deleted.", "success");
                        axios.delete(base_url+'pr-requisition/'+id+'/delete').then((response) => {

                            _this.showMassage(response.data);
                            tr.remove();
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

            doSuperAdmin(data){ //Unapprove                
                var _this = this;
                var id = data.id;
                swal({
                        title: "Are you sure? It's risky operation!",
                        text: "It will be marked as Unapproved and Amounts will be removed from Accounts! The Stock Entry Quantity will be decreased. And Regarding 'PO Receive & Stock Entry' & 'Purchase Invoice Voucher' information will be removed!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, unapprove it!",
                        closeOnConfirm: false
                    },
                    function(){                        
                        axios.get(base_url+'pr-requisition/'+id+'?isSuperAdmin=unapprove').then((response) => {
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

