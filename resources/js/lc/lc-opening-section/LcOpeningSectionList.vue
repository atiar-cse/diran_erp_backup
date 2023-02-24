<template>
    <div>

        <div class="m-portlet__body" v-if="data_list">
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
                    @view="doAlertView"
                >

                </vdtnet-table>
            </div>

            <!--<table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                <tr>
                    <th>S/L</th>
                    <th>LC No</th>
                    <th>Opening Date</th>
                    <th>Supplier</th>
                    <th>PI No</th>
                    <th>LC Category</th>
                    <th>Type</th>
                    <th>Opening Bank</th>
                    <th>Total Value (BDT)</th>
                    &lt;!&ndash;<th>LC Opening Charge</th>&ndash;&gt;
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.lc_no}}</td>
                    <td>{{value.lc_opening_date}}</td>
                    <td>{{value.get_supplier ? value.get_supplier.purchase_supplier_name : ''}}</td>
                    <td>{{value.get_pi_no ? value.get_pi_no.pi_no : ''}}</td>
                    <td>
                        <span v-if="value.lc_category == 1" class="badge badge-pill badge-primary">Export</span>
                        <span v-if="value.lc_category == 2" class="badge badge-pill badge-secondary">Import</span>
                    </td>
                    <td>
                        <span v-if="value.lc_type == 1" class="badge badge-pill badge-primary">Master LC</span>
                        <span v-if="value.lc_type == 2" class="badge badge-pill badge-secondary">Deffard LC</span>
                    </td>
                    <td>{{value.get_opening_bank ? value.get_opening_bank.accounts_bank_names : ''}}</td>
                    <td class="text-right">{{value.lc_total_value}}</td>

                    <td scope="row" class="width-100 text-center">

                        <a v-if="permissions.indexOf('lc-opening-section.show') !=-1" @click="viewData(value.id)" class="btn btn-outline-success m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="View"><i class="la la-eye"></i></a>
                        <span v-if="value.approve_status == 1" title="Approved"><i class="la la-check-circle-o"></i></span>
                        <a v-if="permissions.indexOf('lc-opening-section.edit') !=-1 && value.approve_status != 1"  @click="editData(value.id)" class="btn btn-outline-success m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>
                        <a v-if="permissions.indexOf('lc-opening-section.destroy') !=-1 && value.approve_status != 1" @click="deleteData(value.id)" class="btn btn-outline-danger m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>

                    </td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="11" class="text-center">No Data Available.</td>
                </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="text-center col-md-12" >
                    <pagination :resultData="resultData" @clicked="index" :mid-size="9"></pagination>
                </div>
            </div>-->
        </div>

        <AddLcOpeningSection v-else-if="add_form"
                             :permissions="permissions"
                             :pi_invoice_lists="pi_invoice_lists"
                             :bank_lists="bank_lists"
        ></AddLcOpeningSection>
        <EditLcOpeningSection v-else-if="edit_form"
                              :permissions="permissions"
                              :pi_invoice_lists="pi_invoice_lists"
                              :bank_lists="bank_lists"
                              :edit-id="edit_id"
        ></EditLcOpeningSection>
        <ViewLcOpeningSection
            v-else-if="view_form"
            :edit-id="edit_id"
        ></ViewLcOpeningSection>
    </div>
</template>

<script>

    import {EventBus} from '../../vue-assets';
    import VdtnetTable from 'vue-datatables-net';
    import AddLcOpeningSection from './AddLcOpeningSection.vue';
    import EditLcOpeningSection from './EditLcOpeningSection.vue';
    import ViewLcOpeningSection from './ViewLcOpeningSection.vue';


    export default {
        components: {
            AddLcOpeningSection,
            EditLcOpeningSection,
            ViewLcOpeningSection,
            VdtnetTable
        },

        props: ['permissions', 'pi_invoice_lists', 'bank_lists'],

        data: function () {
            return {
                data_list: true,
                add_form: false,
                edit_form: false,
                edit_id: false,
                view_form: false,


                //VdtnetTable Options
                tableId: 'lc-opening-section',
                options: {
                    ajax: {
                        url: base_url + "lc-opening-section",
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
                    lc_no: {label: 'LC No', sortable: true, searchable: true, defaultOrder: 'DESC'},
                    lc_opening_date: {label: 'Order Date', sortable: true, searchable: true},
                    'lc_proforma_invoice_entries.pi_no': {
                        label: 'PI No',
                        template: '{{ row.pi_no}}',
                        sortable: true,
                        searchable: true
                    },
                    'purchase_supplier_entries.purchase_supplier_name': {
                        label: 'Supplier',
                        template: '{{ row.purchase_supplier_name}}',
                        sortable: true,
                        searchable: true
                    },
                    'accounts_bank_names.accounts_bank_names': {
                        label: 'Opening Bank',
                        template: '{{ row.accounts_bank_names}}',
                        sortable: true,
                        searchable: true
                    },
                    lc_category: {
                        label: 'LC Category',
                        template: '<span v-if="row.lc_category == 1" class="badge badge-pill badge-primary">Export</span> <span v-if="row.lc_category == 2" class="badge badge-pill badge-secondary">Import</span>',
                        sortable: false,
                        searchable: false
                    },
                    lc_type: {
                        label: 'Type',
                        template: '<span v-if="row.lc_type == 1" class="badge badge-pill badge-primary">Master LC</span> <span v-if="row.lc_type == 2" class="badge badge-pill badge-secondary">Deffard LC</span> <span v-if="row.lc_type == 3" class="badge badge-pill badge-info">Others</span>',
                        sortable: false,
                        searchable: false
                    },
                    lc_total_value: {label: 'Total Value (BDT)'},
                    approve_status: {
                        label: 'Approve State',
                        template: ' <div v-if="row.approve_status == 0"   class="badge badge-pill badge-danger" >Processing</div><div v-if="row.approve_status == 1"  class="badge badge-pill badge-success" >Approve</div>'
                    },
                    'lc_opening_sections.created_by': {
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

                            if (this.permissions.indexOf('lc-opening-section.edit') != -1 && row.approve_status != 1) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                            }
                            if (this.permissions.indexOf('lc-opening-section.destroy') != -1 && row.approve_status != 1) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="delete" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>';
                            }
                            if (this.permissions.indexOf('lc-opening-section.show') != -1) {
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

                axios.get(base_url+"lc-opening-section/?page="+pageNo+"&perPage="+perPage).then((response) => {
                    this.resultData = response.data;

                });
            },*/

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
                        swal("Deleted!", "Your LC Opening has been deleted.", "success");
                        _this.$loading(true);
                        axios.delete(base_url + 'lc-opening-section/' + id)
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

