<template>
    <div>

        <div class="m-portlet__body" v-if="data_list">
            <div class="row">
                <div class="item-show-limit col-md-8">

                </div>
                <div class="col-md-4">
                    <form
                      class="form-inline d-flex mx-1 justify-content-end"
                      @submit.stop.prevent="doSearch"
                    >
                      <div class="input-group">
                        <input
                          v-model="quickSearch"
                          type="search"
                          placeholder="Quick search"
                          class="form-control"
                        >
                        <div class="input-group-append">
                          <button
                            type="submit"
                            class="btn btn-outline-secondary"
                          >
                            <i class="mdi mdi-magnify" /> Go
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
        </div>

        <AddChartOfAccount
            v-else-if="add_form"
                :sub_code2_list="sub_code2_list"
        ></AddChartOfAccount>
        <EditChartOfAccount
            v-else-if="edit_form"
                :sub_code2_list="sub_code2_list"
                :edit-id="edit_id"
        ></EditChartOfAccount>
        <PrintChartOfAccountList
                v-else-if="print_form"
        ></PrintChartOfAccountList>

    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';

    import AddChartOfAccount from './AddChartOfAccount.vue';
    import EditChartOfAccount from './EditChartOfAccount.vue';
    import PrintChartOfAccountList from './PrintChartOfAccountList.vue';

    import VdtnetTable from 'vue-datatables-net';

    export default {
        components: {
            AddChartOfAccount,
            EditChartOfAccount,
            PrintChartOfAccountList,
            VdtnetTable
        },

        props:['permissions','sub_code2_list'],

        data: function(){
            return {
                data_list:true,
                add_form:false,
                edit_form:false,
                edit_id:false,
                print_form:false,

                //VdtnetTable Options
                tableId: 'chart-of-accounts',
                options: {
                    ajax: {
                      url: base_url+"chart-of-accounts",
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
                    'accounts_main_codes.main_code_title': { label: 'Main Code',template:'<span v-html="row.main_code_title"></span> ({{ row.main_code}})', sortable: true, searchable: true },
                    'accounts_sub_codes.sub_code_title': { label: 'Sub Code',template:'<span v-html="row.sub_code_title"></span> ({{ row.sub_code}})', sortable: true, searchable: true },
                    'accounts_sub_code2s.sub_code_title2': { label: 'Sub Code2',template:'<span v-html="row.sub_code_title2"></span> ({{ row.sub_code2}})', sortable: true, searchable: true },
                    chart_of_accounts_title: { label: 'COA Title', sortable: true, searchable: true },
                    chart_of_account_code: { label: 'COA Code', sortable: true, searchable: true, defaultOrder: 'ASC'  },
                    opening_balance: { label: 'Opening Balance' },
                    current_balance: { label: 'Current Balance' },
                    opening_date: { label: 'Opening Date' },
                    'accounts_chartof_accounts.created_by': { label: 'User', template: '<span v-if="row.ad_name">Added By : {{ row.ad_name }}</span><br><span v-if="row.ed_name">Edited By  : {{ row.ed_name }}</span>', sortable: false, searchable: false },
                    status: {
                      label: 'Status',
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
                        if (this.permissions.indexOf('chart-of-accounts.edit') != -1) {
                            htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                        }
                        if (this.permissions.indexOf('chart-of-accounts.destroy') != -1) {
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
                _this.data_list = false;
                _this.print_form =false;
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
                        axios.delete(base_url+'chart-of-accounts/'+id).then((response) => {

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
                _this.data_list = false;
                _this.edit_form = false;
                $('#addButton').hide();
                $('#printButton').hide();
                $('#listButton').show();
            });

            $('body').on('click', '#listButton', function() {
                _this.add_form = false;
                _this.data_list = true;
                _this.edit_form = false;
                $('#addButton').show();
                $('#printButton').show();
                $('#listButton').hide();
            });

            $('body').on('click', '#printButton', function() {
                _this.print_form = true;
                _this.add_form = false;
                _this.data_list = false;
                _this.edit_form = false;
                $('#printButton').hide();
                $('#addButton').hide();
                $('#listButton').show();
            });

            EventBus.$on('data-changed', (id) => {

                _this.add_form = false;
                _this.data_list = true;
                _this.edit_form = false;
                $('#addButton').show();
                $('#listButton').hide();
                $('#printButton').show();
            });
        },
    }
</script>

