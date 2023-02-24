<template>
    <div>
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="stocks" id="listComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="row print_links">
                <div class="col-md-6"></div>
                <div class="col-md-6 text-right" style="margin-top:3px; ">
                    <a href="#" @click="doExportPdf()" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i
                        class="fa fa-file-pdf"></i><span>PDF</span></span></a>
                    <a href="#" @click="doExportExcel()" class="btn btn-accent btn-sm m-btn  m-btn m-btn--icon"><span><i
                        class="fa fa-file-excel"></i><span>Excel</span></span></a>
                </div>
            </div>

            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-12 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">

                            <div class="col-md-4">
                                <div class="m-form__group m-form__group">
                                    <label class="m-label m-label--single">Warehouse:</label>
                                    <div class="m-form__control">
                                        <select class="form-control m-select2 select2" data-selectField="warehouse_id"
                                                v-model="form.warehouse_id">
                                            <option v-for="(wvalue,windex) in warehouse_lists" :value="wvalue.id">
                                                {{wvalue.purchase_ware_houses_name}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div class="col-md-4">
                                <div class="m-form__group m-form__group">
                                    <label class="m-label m-label--single">Product:</label>
                                    <div class="m-form__control">
                                        <select class="form-control m-select2 select2" data-selectField="product_id"
                                                v-model="form.product_id">
                                            <option v-for="(value,index) in product_lists" :value="value.id">
                                                {{value.product_name}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div class="col-md-4">
                                <br/>
                                <div class="m-form__group m-form__group">
                                    <div class="m-input-icon m-input-icon--left">
                                        <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i>
                                            <span>Search</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>

        <div class="m-portlet__body" v-if="stock_list">
            <!--begin: Datatable -->
            <table class="table-stock-report table table-bordered">
                <thead>
                <tr>
                    <th>S/L</th>
                    <th>Warehouse</th>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Open qty</th>
                    <th>Current Qty</th>
                </tr>
                </thead>
                <tbody v-if="resultData !=''">
                <tr v-for="(value,index) in resultData">
                    <td>{{++index}}</td>
                    <td>{{value.purchase_ware_houses_name}}</td>
                    <td>{{value.inventory_current_stocks_product_id}}</td>
                    <td>{{value.product_name}}</td>
                    <td>{{value.inventory_stocks_open_qty}}</td>
                    <td>{{value.inventory_stocks_current_qty}}</td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="5" class="text-center">No Data Available.</td>
                </tr>
                </tbody>
            </table>

        </div>
    </div>
</template>

<script>

    import {EventBus} from '../../vue-assets';

    export default {

        props: ['product_lists', 'warehouse_lists'],

        data: function () {
            return {
                stock_list: true,
                resultData: {},
                form: {
                    warehouse_id: '',
                    product_id: '',
                },
                errors: {},
            };
        },

        methods: {

            stocks() {
                var _this = this;
                _this.$loading(true);
                axios.post(base_url + "stock-report/stocks", _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        console.log(response);
                        _this.resultData = response.data;
                    });
            },

            showMassage(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, 'Success Alert', {timeOut: 5000});
                } else {
                    toastr.error(data.message, 'Error Alert', {timeOut: 5000});
                }
            },

            doExportPdf() {
                var _this = this;
                //if (_this.form.return_id) {
                window.open(base_url + 'stock-report/stocks?export=pdf&warehouse_id=' + _this.form.warehouse_id + '&product_id=' + _this.form.product_id + '&brand_id=' + _this.form.brand_id + '&type_id=' + _this.form.type_id + '', "_blank");
                /*}else {
                    $('#return_id').focus()
                    var data = {message: 'Please select Return No'};
                    this.showMassage(data);

                }*/
            },

            doExportExcel() {
                var _this = this;
                //if (_this.form.return_id) {
                window.open(base_url + 'stock-report/stocks?export=excel&warehouse_id=' + _this.form.warehouse_id + '&product_id=' + _this.form.product_id + '&brand_id=' + _this.form.brand_id + '&type_id=' + _this.form.type_id + '', "_blank");
                /*}else {
                    $('#return_id').focus()
                    var data = {message: 'Please select Return No'};
                    this.showMassage(data);

                }*/
            },
        },

        mounted() {
            var _this = this;
            $('#listComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if (dataIndex == 'warehouse_id') {
                    _this.form.warehouse_id = selectedType.val();
                } else if (dataIndex == 'product_id') {
                    _this.form.product_id = selectedType.val();
                }
            });

            var Select2 = {
                init: function () {
                    $(".select2").select2({
                        placeholder: "Please select an option"
                    })
                }
            };
            jQuery(document).ready(function () {
                Select2.init()
            });
        },

        created() {
            var _this = this;

            $('body').on('click', '#listButton', function () {
                _this.stock_list = true;
                $('#listButton').hide();
            });

            EventBus.$on('data-changed', (id) => {
                _this.stock_list = true;
                $('#listButton').hide();
            });
        },
    }
</script>

