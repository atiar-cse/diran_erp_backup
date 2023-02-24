<template>
    <div>

        <div class="m-portlet__body">
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
        <AddAccountSubCode2
                :sub_code_list="sub_code_list"
        ></AddAccountSubCode2>
    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import AddAccountSubCode2 from './AddAccountSubCode2.vue';

    import VdtnetTable from 'vue-datatables-net';

    export default {
        components: {
            AddAccountSubCode2,
            VdtnetTable
        },

        props:['permissions','sub_code_list'],

        data: function(){
            return {

                //VdtnetTable Options
                tableId: 'account-sub-code2',
                options: {
                    ajax: {
                      url: base_url+"account-sub-code2",
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
                    'accounts_sub_codes.sub_code_title': { label: 'Account Sub Code', template: '{{ row.sub_code_title }}', sortable: true, searchable: true },
                    sub_code_title2: { label: 'Sub Code2 Title',sortable: true, searchable: true },
                    sub_code2: { label: 'Sub Code2', sortable: true, searchable: true },
                    'accounts_sub_codes.created_by': { label: 'User', template: '<span v-if="row.ad_name">Added By : {{ row.ad_name }}</span><br><span v-if="row.ed_name">Edited By  : {{ row.ed_name }}</span>', sortable: false, searchable: false },
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
                        if (this.permissions.indexOf('account-sub-code2.edit') != -1) {
                            htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                        }
                        return htmlButtons;
                      }
                    }                    
                },
                quickSearch: '',
            };
        },

        methods: {

            /*deleteData(id){
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
                        axios.delete(base_url+'account-sub-code2/'+id).then((response) => {
                            _this.index(1);
                            _this.showMassage(response.data);
                        }).catch((error)=>{
                            swal('Error:','Something Error Found !, Please try again','error');
                        });
                    });
            },*/

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
        }

    }
</script>

