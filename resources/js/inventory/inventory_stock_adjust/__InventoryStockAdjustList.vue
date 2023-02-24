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

        <AddInventoryStockAdjust  v-else-if="add_form" :product_lists="product_lists" :inventory_stock_adjust_no="inventory_stock_adjust_no" :enum_val="enum_val" :warehouse_lists="warehouse_lists"></AddInventoryStockAdjust>
        <EditInventoryStockAdjust  v-else-if="edit_form" :product_lists="product_lists" :edit-id="edit_id"  :enum_val="enum_val" :warehouse_lists="warehouse_lists"></EditInventoryStockAdjust>

    </div>
</template>

<script>

    import { EventBus } from '../../vue-assets';
    import VdtnetTable from 'vue-datatables-net';
    import AddInventoryStockAdjust from './AddInventoryStockAdjust.vue';
    import EditInventoryStockAdjust from './EditInventoryStockAdjust.vue';


    export default {
        components: {
            AddInventoryStockAdjust,
            EditInventoryStockAdjust,
            VdtnetTable
        },

        props:['permissions','inventory_stock_adjust_no','product_lists','enum_val','warehouse_lists'],

        data: function(){
            return {
                product_list:true,
                add_form:false,
                edit_form:false,
                edit_id:false,

                //VdtnetTable Options
                tableId: 'stock-adjust',
                options: {
                    ajax: {
                        url: base_url+"stock-adjust",
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
                    id: { label: 'ID', sortable: false },
                    inventory_stock_adjusts_no: { label: 'Stock Adjust NO', sortable: true, searchable: true, defaultOrder: 'asc'  },
                    inventory_stock_adjusts_date: { label: 'Date',/*render:(data) =>{return this.dateFormat(data)},*/sortable: true, searchable: true  },
                    'purchase_ware_houses.purchase_ware_houses_name': { label: 'Warehouse', template: '{{ row.purchase_ware_houses_name }}',sortable: true, searchable: true },

                    inventory_stock_adjusts_narration: { label: 'Narration',sortable: false, searchable: false },

                    inventory_stock_adjusts_total_qty: { label: 'Total Qty', sortable: false, searchable: false  },
                    inventory_stock_adjusts_total_price: { label: 'Total Price', sortable: false, searchable: false  },

                    //'inventory_stock_adjusts.created_by': { label: 'User', template: '<span v-if="row.created_by">Add By : {{ row.user_name }}</span><br><span v-if="row.updated_by">Edit By  : {{ row.user_name }}</span><br><span v-if="row.approve_status==1">Approved By  : {{row.user_name }} </span><br>', sortable: false, searchable: false },

                    actions: {
                        isLocal: true,
                        label: 'Actions',
                        render: (data) => {
                            var htmlButtons = '';

                            if (this.permissions.indexOf('stock-adjust.edit') != -1 ) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                            }
                            if (this.permissions.indexOf('stock-adjust.destroy') != -1 ) {
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


                editData(id){

                    var _this = this;
                    _this.add_form = false;
                    _this.product_list = false;
                    _this.edit_form = true;
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
                            axios.delete(base_url+'stock-adjust/'+id+'/delete').then((response) => {

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
                $('#addButton').hide();
                $('#listButton').show();
            });

            $('body').on('click', '#listButton', function() {
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

