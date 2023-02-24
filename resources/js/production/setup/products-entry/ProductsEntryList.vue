<template>
    <div>

        <div class="m-portlet__body" v-if="product_list">
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
                >

                </vdtnet-table>
            </div>
        </div>

        <AddProductsEntry v-else-if="add_form" :brand_list="brand_list" :category_list="category_list"
                          :measure_unit_list="measure_unit_list" :country_list="country_list"></AddProductsEntry>
        <EditProductsEntry v-else-if="edit_form" :brand_list="brand_list" :category_list="category_list"
                           :measure_unit_list="measure_unit_list" :country_list="country_list"
                           :edit-id="edit_id"></EditProductsEntry>

    </div>
</template>

<script>

    import {EventBus} from '../../../vue-assets';

    import AddProductsEntry from './AddProductsEntry.vue';
    import EditProductsEntry from './EditProductsEntry.vue';

    import VdtnetTable from 'vue-datatables-net';

    export default {
        components: {
            AddProductsEntry,
            EditProductsEntry,
            VdtnetTable
        },

        props: ['brand_list', 'category_list', 'measure_unit_list', 'country_list', 'permissions'],

        data: function () {
            return {
                product_list: true,
                add_form: false,
                edit_form: false,
                edit_id: false,

                //VdtnetTable Options
                tableId: 'production-entry',
                options: {
                    ajax: {
                        url: base_url + "production-entry",
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
                    product_name: {label: 'Product Name', sortable: true, searchable: true, defaultOrder: 'DESC'},
                    product_code: {label: 'Code', sortable: true, searchable: true},
                    'production_brands.product_brand_name': {
                        label: 'Brand',
                        template: '{{ row.product_brand_name }}',
                        sortable: true,
                        searchable: true
                    },
                    'production_categories.product_category_name': {
                        label: 'Category',
                        template: '{{ row.product_category_name }}',
                        sortable: true,
                        searchable: true
                    },
                    'production_measure_units.measure_unit': {
                        label: 'Measure Unit',
                        template: '{{ row.measure_unit }}',
                        sortable: true,
                        searchable: true
                    },
                    buying_price: {label: 'Buying Price'},
                    selling_price: {label: 'Selling Price'},

                    is_raw_material_status: {
                        label: 'RM Status',
                        render: (data) => {
                            if (data == 1) {
                                return '<span class="badge badge-pill badge-success">Raw Material</span>';
                            } else {
                                return '<span class="badge badge-pill badge-primary">Finish Goods</span>';
                            }
                        }
                    },

                    status: {
                        label: 'Status',
                        render: (data) => {
                            if (data == 1) {
                                return '<div class="m-demo__preview m-demo__preview--badge"> <span class="m-badge m-badge--success m-badge--wide m-badge--rounded"> <i class="la la-check-circle"></i> Active </span></div>';
                            } else {
                                return '<div class="m-demo__preview m-demo__preview--badge"> <span class="m-badge m-badge--danger m-badge--wide m-badge--rounded"> <i class="la la-ban"></i> Inactive</span></div>';
                            }
                        }
                    },
                    'production_products.created_by': {
                        label: 'User',
                        template: '<span v-if="row.ad_name">Added By : {{ row.ad_name }}</span><br><span v-if="row.ed_name">Edited By  : {{ row.ed_name }}</span>',
                        sortable: false,
                        searchable: false
                    },
                    actions: {
                        isLocal: true,
                        label: 'Actions',
                        render: (data) => {
                            var htmlButtons = '';
                            if (this.permissions.indexOf('production-entry.edit') != -1) {
                                htmlButtons = htmlButtons + ' <a href="javascript:void(0);" data-action="edit" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la   la-edit"></i></a>';
                            }
                            if (this.permissions.indexOf('production-entry.destroy') != -1) {
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

            editData(id) {

                var _this = this;
                _this.add_form = false;
                _this.product_list = false;
                _this.edit_form = true;
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
                        swal("Deleted!", "Your imaginary file has been deleted.", "success");
                        _this.$loading(true);
                        axios.delete(base_url + 'production-entry/' + id + '/delete')
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
                _this.product_list = false;
                _this.edit_form = false;
                $('#addButton').hide();
                $('#listButton').show();
            });

            $('body').on('click', '#listButton', function () {
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
            }, 900);

        },
    }
</script>

