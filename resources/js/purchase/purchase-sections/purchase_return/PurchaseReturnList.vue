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

                        @view="doAlertView"
                >

                </vdtnet-table>
            </div>

        </div>

        <AddPurchaseReturn
            v-else-if="add_form"
            :permissions="permissions"
            :supplier_lists="supplier_lists"
            :warehouse_lists="warehouse_lists"
            :issue_type_lists="issue_type_lists"
            :product_lists="product_lists"
        ></AddPurchaseReturn>
        <EditPurchaseReturn
            v-else-if="edit_form"
                :edit-id="edit_id"
                :permissions="permissions"
                :supplier_lists="supplier_lists"
                :warehouse_lists="warehouse_lists"
                :issue_type_lists="issue_type_lists"
                :product_lists="product_lists"
        ></EditPurchaseReturn>
        <ViewPurchaseReturn
            v-else-if="view_form"
            :edit-id="edit_id"
        ></ViewPurchaseReturn>

    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import VdtnetTable from 'vue-datatables-net';
    import AddPurchaseReturn from './AddPurchaseReturn.vue';
    import EditPurchaseReturn from './EditPurchaseReturn.vue';
    import ViewPurchaseReturn from './ViewPurchaseReturn.vue';


    export default {
        components: {
            AddPurchaseReturn,
            EditPurchaseReturn,
            ViewPurchaseReturn,
            VdtnetTable
        },

        props:['permissions','supplier_lists','warehouse_lists','issue_type_lists','product_lists'],

        data: function(){
            return {
                product_list:true,
                add_form:false,
                edit_form:false,
                edit_id:false,

                //VdtnetTable Options
                tableId: 'po-return',
                options: {
                    ajax: {
                        url: base_url+"po-return",
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
                    po_return_no: { label: 'Return NO', sortable: true, searchable: true, defaultOrder: 'asc'  },
                    po_return_date: { label: 'Return Date',/*render:(data) =>{return this.dateFormat(data)},*/sortable: true, searchable: true  },
                    'purchase_supplier_entries.purchase_supplier_name': { label: 'Supplier', template: '{{ row.purchase_supplier_name }}',sortable: true, searchable: true  },
                    'purchase_ware_houses.purchase_ware_houses_name': { label: 'Warehouse', template: '{{ row.purchase_ware_houses_name }}',sortable: true, searchable: true  },
                    'purchase_return_types.purchase_return_types_name': { label: 'Rtn Issue Type', template: '{{ row.purchase_return_types_name }}',sortable: true, searchable: true  },
                    po_rtn_narration: { label: 'Narration' },
                    po_rtn_total_qty: { label: 'Total Qty' },
                    po_rtn_total_price: { label: 'Total Price' },
                    approve_status: { label: 'Approve State',template:' <div v-if="row.approve_status == 0"  class="badge badge-pill badge-danger" >Pending</div><div v-if="row.approve_status == 1"  class="badge badge-pill badge-success" >Approved</div>' },
                    'purchase_returns.created_by': { label: 'User', template: '<span v-if="row.ad_name">Added By : {{ row.ad_name }}</span><br><span v-if="row.ed_name">Edited By  : {{ row.ed_name }}</span><br><span v-if="row.approve_status==1 && row.ap_name">Approved By  : {{ row.ap_name }} </span><br>', sortable: false, searchable: false },

                    actions: {
                        isLocal: true,
                        label: 'Actions',
                        render: (data,type,row,meta) => {
                            var htmlButtons = '';
                            if (this.permissions.indexOf('po-return.edit') != -1 && row.approve_status == 0) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                            }
                            if (this.permissions.indexOf('po-return.destroy') != -1 && row.approve_status == 0) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="delete" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>';
                            }

                            if (this.permissions.indexOf('po-return.show') != -1 ) {
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
            /*index(pageNo,perPage){
                if(pageNo){ pageNo = pageNo; }else{pageNo = this.resultData.current_page; }
                if(perPage){ perPage = perPage;}else{ perPage = this.perPage;}

                axios.get(base_url+"po-return/?page="+pageNo+"&perPage="+perPage).then((response) => {
                   this.resultData = response.data;

                });
            },*/

            editData(id){
                var _this = this;
                _this.add_form = false;
                _this.product_list = false;
                _this.edit_form = true;
                _this.edit_id = id;
                _this.view_form = false;
                $('#addButton').hide();
                $('#listButton').show();
            },


            viewData(id){
                var _this = this;
                _this.add_form = false;
                _this.product_list = false;
                _this.edit_form = false;
                _this.edit_id = id;
                _this.view_form = true;
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
                        axios.delete(base_url+'po-return/'+id+'/delete').then((response) => {

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

