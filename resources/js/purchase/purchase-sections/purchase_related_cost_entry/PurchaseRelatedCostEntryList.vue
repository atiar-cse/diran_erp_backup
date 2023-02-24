<template>
    <div>
        <div class="m-portlet__body" v-if="product_list">
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
                        @view="doAlertView">
                </vdtnet-table>
            </div>

        </div>

        <AddPurchaseRelatedCostEntry
            v-else-if="add_form"
                :invoice_lists="invoice_lists"
                :cost_type_lists="cost_type_lists"
                :chart_acc_lists="chart_acc_lists"
        ></AddPurchaseRelatedCostEntry>
        <EditPurchaseRelatedCostEntry
            v-else-if="edit_form"
                :edit-id="edit_id"
                :invoice_lists="invoice_lists"
                :cost_type_lists="cost_type_lists"
                :chart_acc_lists="chart_acc_lists"
        ></EditPurchaseRelatedCostEntry>
        <ViewPurchaseRelatedCostEntry
                v-else-if="view_form"
                :edit-id="edit_id"
        ></ViewPurchaseRelatedCostEntry>

    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import VdtnetTable from 'vue-datatables-net';
    import AddPurchaseRelatedCostEntry from './AddPurchaseRelatedCostEntry.vue';
    import EditPurchaseRelatedCostEntry from './EditPurchaseRelatedCostEntry.vue';
    import ViewPurchaseRelatedCostEntry from './ViewPurchaseRelatedCostEntry.vue';

    export default {
        components: {
            AddPurchaseRelatedCostEntry,
            EditPurchaseRelatedCostEntry,
            ViewPurchaseRelatedCostEntry,
            VdtnetTable
        },

        props:['permissions','invoice_lists','cost_type_lists','chart_acc_lists'],

        data: function(){
            return {
                product_list:true,
                add_form:false,
                edit_form:false,
                view_form:false,
                edit_id:false,

                //VdtnetTable Options
                tableId: 'po-cost-entry',
                options: {
                    ajax: {
                        url: base_url+"po-cost-entry",
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
                    'purchase_order_receives.po_order_no': { label: 'Order No.',template:'{{ row.po_order_no }}', sortable: true, searchable: true, defaultOrder: 'DESC'  },
                    po_cost_date: { label: 'Cost Date', sortable: true, searchable: true  },
                    po_cost_narration: { label: 'Narration' },
                    po_total_cost: { label: 'Total Price' },
                    approve_status: { label: 'Approve State',template:' <div v-if="row.approve_status == 0"  class="badge badge-pill badge-danger" >Pending</div><div v-if="row.approve_status == 1"  class="badge badge-pill badge-success" >Approved</div>' },
                    'purchase_related_cost_entries.created_by': { label: 'User', template: '<span v-if="row.ad_name">Added By : {{ row.ad_name }}</span><br><span v-if="row.ed_name">Edited By  : {{ row.ed_name }}</span><br><span v-if="row.approve_status==1 && row.ap_name">Approved By  : {{ row.ap_name }} </span><br>', sortable: false, searchable: false },

                    actions: {
                        isLocal: true,
                        label: 'Actions',
                        render: (data,type,row,meta) => {
                            var htmlButtons = '';
                            if (this.permissions.indexOf('po-cost-entry.edit') != -1 && row.approve_status !=1) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                            }
                            if (this.permissions.indexOf('po-cost-entry.destroy') != -1 && row.approve_status !=1) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="delete" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>';
                            }
                            if (this.permissions.indexOf('po-cost-entry.show') != -1 && row.approve_status ==1)
                            {
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

                editData(id){

                    var _this = this;
                    _this.add_form = false;
                    _this.edit_form = true;
                    _this.product_list = false;
                    _this.view_form = true;
                    _this.edit_id = id;
                    $('#addButton').hide();
                    $('#listButton').show();
                },

                viewData(id){
                    var _this = this;
                    _this.add_form = false;
                    _this.product_list = false;
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
                            swal("Deleted!", "Your imaginary file has been deleted.", "success");
                            axios.delete(base_url+'po-cost-entry/'+id+'/delete').then((response) => {

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
            }

         },

        created(){

            var _this = this;
            $('body').on('click', '#addButton', function() {
                _this.add_form = true;
                _this.product_list = false;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').hide();
                $('#listButton').show();
            });

            $('body').on('click', '#listButton', function() {
                _this.add_form = false;
                _this.product_list = true;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').show();
                $('#listButton').hide();
            });

            EventBus.$on('data-changed', (id) => {
                _this.add_form = false;
                _this.product_list = true;
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

