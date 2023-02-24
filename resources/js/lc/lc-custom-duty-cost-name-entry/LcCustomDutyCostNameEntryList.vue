<template>
    <div>

        <div class="m-portlet__body">
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
                >

                </vdtnet-table>
            </div>
            <!--<table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                <tr>
                    <th>S/L</th>
                    <th>Custom Duty Cost Name</th>
                    <th>Chart Of Accounts</th>
                    <th>Status</th>
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.duty_cost_name}}</td>
                    <td>
                        {{value.get_chart_acc ? value.get_chart_acc.chart_of_accounts_title : ''}} ({{value.get_chart_acc ? value.get_chart_acc.chart_of_account_code : ''}})
                    </td>
                    <td>
                        <div v-if="value.status == 1" class="m-demo__preview m-demo__preview&#45;&#45;badge">
                            <span class="m-badge m-badge&#45;&#45;success m-badge&#45;&#45;wide m-badge&#45;&#45;rounded"> <i class="la la-check-circle"></i> Active </span>
                        </div>
                        <div v-if="value.status == 0" class="m-demo__preview m-demo__preview&#45;&#45;badge">
                            <span class="m-badge m-badge&#45;&#45;danger m-badge&#45;&#45;wide m-badge&#45;&#45;rounded"> <i class="la la-ban"></i> Inactive</span>
                        </div>

                    </td>
                    <td scope="row" class="width-100 text-center">
                        <a v-if="permissions.indexOf('lc-custom-duty-cost-name-entry.edit') != -1" @click="openUpdateModal(value.id)"  class="btn btn-outline-success m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la 	la-edit"></i></a>
                        <a v-if="permissions.indexOf('lc-custom-duty-cost-name-entry.destroy') != -1" @click="deleteData(value.id)" class="btn btn-outline-danger m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                    </td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="5">No Data Available.</td>
                </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="text-center col-md-12" >
                    <pagination :resultData="resultData" @clicked="index" :mid-size="9"></pagination>
                </div>
            </div>-->
        </div>



        <AddLcCustomDutyCostNameEntry
            :account_list="account_list"
        ></AddLcCustomDutyCostNameEntry>
    </div>
</template>

<script>

    import { EventBus } from '../../vue-assets';
    import AddLcCustomDutyCostNameEntry from './AddLcCustomDutyCostNameEntry.vue';
    import VdtnetTable from 'vue-datatables-net';

    export default {
        components: {
            AddLcCustomDutyCostNameEntry,
            VdtnetTable
        },

        props:['permissions','account_list'],

        data: function(){
            return {
                //VdtnetTable Options
                tableId: 'lc-custom-duty-cost-name-entry',
                options: {
                    ajax: {
                        url: base_url+"lc-custom-duty-cost-name-entry",
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
                    duty_cost_name: { label: 'Custom Duty Cost Name', sortable: true, searchable: true },/*defaultOrder: 'asc'*/
                    'accounts_chartof_accounts.chart_of_accounts_title': { label: 'Chart Of Accounts',template:'{{ row.chart_of_accounts_title }} ({{ row.chart_of_account_code }})', sortable: true, searchable: true  },
                    //'lc_cost_name_entries.created_by': { label: 'User', template: '<span v-if="row.created_by">Add By : {{ row.user_name }}</span><br><span v-if="row.updated_by">Edit By  : {{ row.user_name }}</span><br><span v-if="row.approve_status==1">Approved By  : {{row.user_name }} </span><br>', sortable: false, searchable: false },

                    status: {
                        label: 'Status',sortable: false, searchable: false,
                        render: (data) => {
                            if (data==1) {
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
                            if (this.permissions.indexOf('lc-custom-duty-cost-name-entry.edit') != -1) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                            }
                            if (this.permissions.indexOf('lc-custom-duty-cost-name-entry.destroy') != -1) {
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
            /*index(pageNo,perPage){
                if(pageNo){ pageNo = pageNo; }else{pageNo = this.resultData.current_page; }
                if(perPage){ perPage = perPage;}else{ perPage = this.perPage;}

                axios.get(base_url+"lc-custom-duty-cost-name-entry/?page="+pageNo+"&perPage="+perPage).then((response) => {
                    this.resultData = response.data;

                });
            },*/

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
                        axios.delete(base_url+'lc-custom-duty-cost-name-entry/'+id).then((response) => {

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

            openUpdateModal(id){
                EventBus.$emit('update-button-clicked', id);
            },

            //VdtnetTable Methods
            doAlertEdit(data) {
                this.openUpdateModal(data.id);
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
                setTimeout(() => {
                    // simulate extra long running ajax
                    table.reload()
                }, 500)
            }

        },

        created() {
            var _this = this;

            EventBus.$on('new-data-created', function () {
                _this.doReload();
            });

            setTimeout(function () {
                const vdt_parms = _this.$refs.table.getServerParams();
                if (vdt_parms.search.value) {
                    _this.quickSearch = vdt_parms.search.value;
                    $('#quickSearch').focus();
                }
            },900);

        }

    }
</script>
