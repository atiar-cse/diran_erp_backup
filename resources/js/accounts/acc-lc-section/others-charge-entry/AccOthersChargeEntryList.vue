<template>
    <div>
        <div class="m-portlet__body" v-if="acc_data_list">
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
                <vdtnet-table :id="tableId" ref="table" :fields="fields" :opts="options" @edit="doAlertEdit" @view="doAlertView"></vdtnet-table>
            </div>

        </div>

        <AccEditOthersChargeEntry v-else-if="edit_form"
            :permissions="permissions" 
            :lc_lists="lc_lists" 
            :lc_cost_name_category_list="lc_cost_name_category_list"
            :account_list="account_list"
            :edit-id="edit_id" 
        ></AccEditOthersChargeEntry>
        <AccViewOthersChargeEntry
            v-else-if="view_form"
            :edit-id="edit_id"
        ></AccViewOthersChargeEntry>
    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import VdtnetTable from 'vue-datatables-net';

    import AccEditOthersChargeEntry from './AccEditOthersChargeEntry.vue';
    import AccViewOthersChargeEntry from './AccViewOthersChargeEntry.vue';


    export default {
        components: {
            AccEditOthersChargeEntry,
            AccViewOthersChargeEntry,
            VdtnetTable
        },

        props:['permissions','lc_lists','lc_cost_name_category_list','account_list'],

        data: function(){
            return {
                acc_data_list:true,
                add_form:false,
                edit_form:false,
                edit_id:false,
                view_form:false,

                //VdtnetTable Options
                tableId: 'acc-others-charge-entry',
                options: {
                    ajax: {
                        url: base_url+"acc-others-charge-entry",
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
                    id: { label: 'ID'  },
                    'lc_opening_sections.lc_no': { label: 'LC No',template:'{{ row.lc_no }}', sortable: true, searchable: true},
                    'lc_commercial_invoice_entries.ci_no': { label: 'CI No',template:'{{ row.ci_no }}', sortable: true, searchable: true},
                    others_charge_date: { label: 'Date', sortable: true, searchable: true },
                    'lc_cost_name_categories.cost_category_name': { label: 'Cost Category',template:'{{ row.cost_category_name }}', sortable: true, searchable: true},
                    total_cost: { label: 'Total Cost',template:'<span class="text-center">{{ row.total_cost}}</span>'  },

                    approve_status: { label: 'Approve State',template:' <div v-if="row.account_status == 0"  class="badge badge-pill badge-danger" >Pending</div><div v-if="row.account_status == 1"  class="badge badge-pill badge-success" >Approved</div>'  },
                    'lc_others_charge_entries.created_by': { label: 'User', template: '<span v-if="row.ad_name">Added By : {{ row.ad_name }}</span><br><span v-if="row.ac_ed_name">Edited By  : {{ row.ac_ed_name }}</span><br><span v-if="row.account_status==1 && row.ac_ap_name">Approved By  : {{ row.ac_ap_name }} </span><br>', sortable: false, searchable: false },

                    actions: {
                        isLocal: true,
                        label: 'Actions',
                        render: (data,type,row,meta) => {
                            var htmlButtons = '';

                            if (this.permissions.indexOf('acc-others-charge-entry.edit') != -1 && row.account_status !=1 ) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                            }
                            if (this.permissions.indexOf('acc-others-charge-entry.show') != -1 ) {
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
                _this.acc_data_list = false;
                _this.edit_form = true;
                _this.edit_id = id;
                _this.view_form = false;
                $('#addButton').hide();
                $('#listButton').show();
            },

            viewData(id){
                var _this = this;
                _this.acc_data_list = false;
                _this.edit_form = false;
                _this.view_form = true;
                _this.edit_id = id;
                $('#addButton').hide();
                $('#listButton').show();
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

            doSearch() {
                this.$refs.table.search(this.quickSearch)
            },

        },

        created(){

            var _this = this;


            $('body').on('click', '#listButton', function() {
                _this.acc_data_list = true;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').show();
                $('#listButton').hide();
            });

            EventBus.$on('data-changed', (id) => {
                _this.add_form = false;
                _this.acc_data_list = true;
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

