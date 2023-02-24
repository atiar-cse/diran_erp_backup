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

                        @view="doAlertView"
                >

                </vdtnet-table>
            </div>
        </div>

        <AccEditMassbodySection
                v-else-if="edit_form"
                :account_lists="account_lists"
                :edit-id="edit_id"
                :permissions="permissions"
        ></AccEditMassbodySection>
        <AccViewMassbodySection
                v-else-if="view_form"
                :edit-id="edit_id"
        ></AccViewMassbodySection>

    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import VdtnetTable from 'vue-datatables-net';
    import AccEditMassbodySection from './AccEditMassbodySection.vue';
    import AccViewMassbodySection from './AccViewMassbodySection.vue';


    export default {
        components: {
            AccEditMassbodySection,
            AccViewMassbodySection,
            VdtnetTable
        },

        props:['account_lists','permissions'],

        data: function(){
            return {
                data_list:true,
                add_form:false,
                edit_form:false,
                edit_id:false,
                view_form:false,

                //VdtnetTable Options
                tableId: 'acc-mass-body-voucher',
                options: {
                    ajax: {
                        url: base_url+"acc-mass-body-voucher",
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
                    massbody_no: { label: 'Massbody NO', sortable: true, searchable: true, defaultOrder: 'asc'  },
                    massbody_date: { label: 'Date', sortable: true, searchable: true  },
                    account_status: { label: 'Account Approval',template:' <div v-if="row.account_status == 0"  class="badge badge-pill badge-danger" >Pending</div><div v-if="row.account_status == 1"  class="badge badge-pill badge-success" >Approved</div>'},
                    'production_massbodies.created_by': { label: 'User', template: '<span v-if="row.ad_name">Added By : {{ row.ad_name }}</span><br><span v-if="row.ed_name">Edited By  : {{ row.ed_name }}</span><br><span v-if="row.account_status==1 && row.acc_ap_name">Approved By  : {{ row.acc_ap_name }} </span>', sortable: false, searchable: false },

                    actions: {
                        isLocal: true,
                        label: 'Actions',
                        render: (data,type,row,meta) => {
                            var htmlButtons = '';
                            if (this.permissions.indexOf('acc-mass-body-voucher.edit') != -1 && row.account_status !=1) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                            }

                            if (this.permissions.indexOf('acc-mass-body-voucher.show') != -1 ) {
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

