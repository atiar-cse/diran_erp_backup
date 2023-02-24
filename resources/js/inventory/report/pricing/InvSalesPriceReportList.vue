<template>
    <div>
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="handleFormSubmit" id="listComponent"
              class="m-form m-form--fit m-form--label-align-right hide_print">

            <div class="row print_links">
                <div class="col-md-6"></div>
                <div class="col-md-6 text-right" style="margin-top:3px;">
                    <!-- <a href="#" onclick="window.print();return false"
                       class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i
                        class="fa flaticon-technology"></i><span>Print</span></span></a> -->
                </div>
            </div>

            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-12 order-2 order-xl-1">

                        <div class="form-group m-form__group row align-items-center">

                            <div class="col-md-3">
                                <div class="">
                                    <label class="m-label m-label--single">Warehouse: <span class="requiredField">*</span></label>
                                    <div class="m-form__control">
                                        <select required class="form-control m-select2 select2" data-selectField="warehouse_id"
                                                v-model="form.warehouse_id">
                                            <option v-for="(wvalue,windex) in warehouse_lists" :value="wvalue.id">
                                                {{wvalue.purchase_ware_houses_name}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>
                            <!--<div class="col-md-3">
                                <div class="">
                                    <label class="m-label m-label&#45;&#45;single">Brand:</label>
                                    <div class="m-form__control">
                                        <select class="form-control m-select2 select2" data-selectField="brand_id"
                                                v-model="form.brand_id">
                                            <option value="clear"> -&#45;&#45;Unselect/ Clear-&#45;&#45; </option>
                                            <option v-for="(value,index) in product_brands" :value="value.id">
                                                {{value.product_brand_name}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m&#45;&#45;margin-bottom-10"></div>
                            </div>-->

                            <div class="col-md-3">
                                <div class="">
                                    <label class="m-label m-label--single">Category:</label>
                                    <div class="m-form__control">
                                        <select class="form-control m-select2 select2" data-selectField="type_id"
                                                v-model="form.type_id">
                                            <option value="clear"> ---Unselect/ Clear--- </option>
                                            <option v-for="(value,index) in product_types" :value="value.id">
                                                {{value.product_category_name}}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <label class="m-label m-label--single">Product: <span class="requiredField">*</span></label>
                                    <div class="m-form__control">
                                        <select required class="form-control m-select2 select2" data-selectField="product_id"
                                                v-model="form.product_id">
                                            <option value="clear"> ---Unselect/ Clear--- </option>
                                            <option v-for="(value,index) in product_lists" :value="value.id">
                                                {{value.product_name}} <!--({{value.product_code}})-->
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>
                            <div class="col-md-3">
                                <div class="">
                                    <label class="m-label m-label--single"></label>
                                    <div class="m-input-icon m-input-icon--left">
                                        <button type="submit" class="btn btn-sm btn-success"><i class="la la-search"
                                                                                                id="icon_submit"></i>
                                            <span>Search</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group m-form__group row align-items-center">
                            <!--<div class="col-md-3">
                                <div class="">
                                    <label class="m-label m-label&#45;&#45;single">From Date: <span class="requiredField">*</span></label>
                                    <div class="input-group date">
                                        <input type="text"
                                               class="form-control form-control-sm m-input datepicker from_date"
                                               v-model="form.from_date" id="from_date" readonly
                                               placeholder="Select date from picker" required/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-md-none m&#45;&#45;margin-bottom-10"></div>
                            </div>-->
                            <!--<div class="col-md-3">
                                <div class="">
                                    <label class="m-label m-label&#45;&#45;single">To Date : <span class="requiredField">*</span></label>
                                    <div class="input-group date">
                                        <input type="text"
                                               class="form-control form-control-sm m-input datepicker to_date"
                                               v-model="form.to_date" id="to_date" readonly
                                               placeholder="Select date from picker" required/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-md-none m&#45;&#45;margin-bottom-10"></div>
                            </div>-->
                        </div>

                    </div>
                </div>
            </div>

        </form>

        <div id="m-portlet" class="m-portlet__body">

            <div class="table-responsive">
                <!--begin: Datatable -->
                <table class="table table-bordered table-hover text-center" id="m_table_1">
                    <thead>
                        <tr valign="middle">
                            <th rowspan="2">S/L</th>
                            <th colspan="3"> Particulars </th>
                            <th colspan="1"> Opening Balance </th>
                            <th colspan="1"> Inward/Received </th>
                            <th colspan="1"> Outward/Issue </th>
                            <th colspan="1"> Closing Balance </th>
                        </tr>
                        <tr valign="middle">

                            <th> Category </th>
                            <th> Product Name </th>
                            <th> Entry Date </th>

                            <th> Qty </th>
                            <!--<th> Rate </th>
                            <th> Value </th>-->

                            <th> Qty </th>
                            <!--<th> Rate </th>
                            <th> Value </th>-->

                            <th> Qty </th>
                            <!--<th> Rate </th>
                            <th> Value </th>-->

                            <th> Qty </th>
                            <!--<th> Rate </th>
                            <th> Value </th>-->
                        </tr>
                    </thead>
                    <tbody v-if="resultData.length > 0">
                        <tr v-for="(value,index) in resultData">
                            <td class="text-center"> {{++index}} </td>
                            <td class="text-center"> {{value.product_category_name}} </td>
                            <td class="text-center"> {{value.product_name}} </td>
                            <td class="text-center"> {{dateFormate(value.entry_date)}} </td>
                            <td class="text-center">
                                <input type="text" v-model="value.inventory_stocks_open_qty" readonly style="width: 100px;text-align:center;border: 1px solid white">
                            </td>
                            <td class="text-center">
                                <span v-if="value.log_table_name == 'inventory_current_stock_details' || value.log_table_name == 'inventory_stock_adjust_details' || value.log_table_name == 'inventory_stock_transfer_details' || value.log_table_name == 'purchase_order_receive_details' || value.log_table_name == 'sales_delivery_return_details' || value.log_table_name == 'production_packing_details' || value.log_table_name == 'inventory_current_stocks' "> {{value.log_entry_qty}}</span>
                            </td>
                            <td class="text-center">
                                <span v-if="value.log_table_name == 'sales_delivery_challan_details' || value.log_table_name == 'purchase_return_details' || value.log_table_name == 'production_requisition_for_rm_details' || value.log_table_name == 'production_inventory_materials' "> {{value.log_entry_qty}}</span>
                            </td>
                            <td class="text-center"> <input align="center" type="text" v-model="value.log_current_qty"  readonly style="width: 100px;text-align:center;border: 1px solid white"> </td>
                        </tr>
                        <tr style="font-weight: bold">
                            <td colspan="4"> Total</td>

                            <td> {{total_opening}}</td>
                            <!--<td></td>
                            <td>  </td>-->

                            <td> {{total_entry_inward}} </td>
                            <!--<td></td>
                            <td>  </td>-->

                            <td> {{total_entry_outward}}</td>
                            <!--<td></td>
                            <td>  </td>-->

                            <td> {{total_closing}}</td>
                            <!--<td></td>
                            <td>  </td>-->
                        </tr>
                    </tbody>
                    <tbody v-else >
                        <tr>
                            <td colspan="8" class="text-center">No Data Available.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from 'moment';
    import VueLoading from 'vuejs-loading-plugin';

    Vue.use(VueLoading);

    export default {

        props: ['product_lists','warehouse_lists','product_brands','product_types'],

        data: function () {
            return {
                resultData: {},
                cinfo: {},

                total_opening: '',
                total_entry_inward: '',
                total_entry_outward: '',
                total_closing: '',

                form: {
                    warehouse_id: '',
                    brand_id: '',
                    type_id: '',
                    product_id: '',
                    from_date: '',
                    to_date: '',
                },
                errors: {},
            };
        },

        methods: {

            dateFormate(value){
                if (value) {
                    return moment(String(value)).format('DD-MM-YYYY')
                }
            },

            company_info() {
                var _this = this;
                var id = 1;
                axios.get(base_url + 'Company-Info/' + id)
                    .then((response) => {
                        console.log(response.data);
                        _this.cinfo = response.data;
                    });
            },

            handleFormSubmit: function () {
                var _this = this;

                if (_this.form.warehouse_id) {
                    $('#icon_submit').addClass('la-spin');
                    _this.$loading(true);
                    axios.post(base_url + 'inventory-report/sales-price', this.form)
                        .then((response) => {
                            _this.$loading(false);
                            _this.resultData = response.data;

                            var opening = 0;
                            var entry_inward = 0;
                            var entry_outward = 0;
                            var closing = 0;

                            for (var l=0;l<response.data.length;l++)
                            {
                                if(l==0)
                                    opening = (parseFloat(response.data[l].inventory_stocks_open_qty));

                                response.data[l].inventory_stocks_open_qty = opening.toFixed(2);

                                if(response.data[l].log_table_name == 'inventory_current_stock_details' || response.data[l].log_table_name == 'inventory_stock_adjust_details' || response.data[l].log_table_name == 'inventory_stock_transfer_details' || response.data[l].log_table_name == 'purchase_order_receive_details' || response.data[l].log_table_name == 'sales_delivery_return_details' || response.data[l].log_table_name == 'production_packing_details' || response.data[l].log_table_name == 'inventory_current_stocks')
                                {
                                    entry_inward = entry_inward + parseFloat(response.data[l].log_entry_qty);

                                    closing = opening + parseFloat(response.data[l].log_entry_qty);
                                }
                                else if(response.data[l].log_table_name == 'sales_delivery_challan_details' || response.data[l].log_table_name == 'purchase_return_details' || response.data[l].log_table_name == 'production_requisition_for_rm_details' || response.data[l].log_table_name == 'production_inventory_materials')
                                {
                                    entry_outward = entry_outward + parseFloat(response.data[l].log_entry_qty);

                                    closing = opening - parseFloat(response.data[l].log_entry_qty);
                                }

                                response.data[l].log_current_qty = closing.toFixed(2);

                                opening = closing;


                            }

                            //_this.total_opening = opening;
                            _this.total_entry_inward = entry_inward.toFixed(2);
                            _this.total_entry_outward = entry_outward.toFixed(2);
                            //_this.total_closing = closing;

                            $('#icon_submit').removeClass('la-spin');
                        })
                        .catch(error => {
                            _this.$loading(false);
                            if (error.response.status == 422) {
                                this.errors = error.response.data.errors;
                            } else {
                                this.showMassage(error);
                            }
                            $('#icon_submit').removeClass('la-spin');
                        });
                } else {
                    $('#account_id').focus()
                    var data = {message: 'Please select Warehouse first'};
                    this.showMassage(data);
                }
            },

            format_date(value) {
                if (value) {
                    return moment(String(value)).format('MM/DD/YYYY')
                }
            },

            showMassage(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, 'Success Alert', {timeOut: 5000});
                } else {
                    toastr.error(data.message, 'Error Alert', {timeOut: 5000});
                }
            },

            initSelect2(){
                setTimeout(function () {
                    var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                    jQuery(document).ready(function() {Select2.init();});
                },250);
            },

            loadCatWiseProduct(){

                var _this = this;
                var type_id= _this.form.type_id;

                axios.get(base_url+"get_cat_wise_product_list/"+type_id).then((response) => {

                    _this.product_lists = response.data.product_list;

                    _this.initSelect2();
                });
            },
        },

        mounted() {

            var _this = this;
            _this.initSelect2();

            $('#listComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');

                if (dataIndex == 'warehouse_id') {
                    _this.form.warehouse_id = selectedType.val();
                }
                if (dataIndex == 'brand_id') {
                    _this.form.brand_id = selectedType.val();
                    if(curerntVal=='clear') _this.form.brand_id = '';
                }
                if (dataIndex == 'type_id') {
                    _this.form.type_id = selectedType.val();
                    if(curerntVal=='clear')
                        _this.form.type_id = '';
                    else
                        _this.loadCatWiseProduct();
                }
                if (dataIndex == 'product_id') {
                    _this.form.product_id = selectedType.val();
                    if(curerntVal=='clear') _this.form.product_id = '';
                }
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    var dateField = $(this).attr('id');

                    if (ev.date == undefined) {
                        if (dateField == 'from_date') {
                            _this.form.from_date = '';
                        } else if (dateField == 'to_date') {
                            _this.form.to_date = '';
                        }
                    } else {
                        if (dateField == 'from_date') {
                            _this.form.from_date = moment(ev.date).format('DD/MM/YYYY');
                        } else if (dateField == 'to_date') {
                            _this.form.to_date = moment(ev.date).format('DD/MM/YYYY');
                        }
                    }
                });
            });
        },

        created() {
            var _this = this;
            //_this.company_info();
        },
    }
</script>
