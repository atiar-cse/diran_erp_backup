<template>
    <div>
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="search" id="listComponent" class="m-form m-form--fit m-form--label-align-right hide_print" >
            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-12 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">

                            <div class="col-md-4">
                                <div class="">
                                    <label id="work_order_id" class="m-label m-label--single">Work Order No :</label>
                                    <div class="m-form__control">
                                        <select id="work_order_id" class="form-control m-select2 select2" data-selectField="work_order_id"  v-model="form.work_order_id" >
                                            <option :value="0">---Please Select---</option>
                                            <option v-for="(svalue,sindex) in work_order_lists"
                                                :value="svalue.id"
                                            > {{ svalue.work_order_no }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="">
                                    <label id="supplier_id" class="m-label m-label--single">Supplier :</label>
                                    <div class="m-form__control">
                                        <select id="supplier_id" class="form-control m-select2 select2" data-selectField="supplier_id"  v-model="form.supplier_id" >
                                            <option :value="0">---Please Select---</option>
                                            <option v-for="(svalue,sindex) in supplier_lists"
                                                :value="svalue.id"
                                            > {{ svalue.purchase_supplier_name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div class="col-md-4">
                                <div class="">
                                    <label id="product_id" class="m-label m-label--single">Product:</label>
                                    <div class="m-form__control">
                                        <select id="product_id" class="form-control m-select2 select2" data-selectField="product_id"  v-model="form.product_id" >
                                            <option :value="0">---Please Select---</option>
                                            <option v-for="(svalue,sindex) in product_lists"
                                                :value="svalue.id"
                                            > {{ svalue.product_name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div class="col-md-4">
                                <div class="">
                                    <label id="from_date" class="m-label m-label--single">From:</label>
                                    <div class="m-form__control">
                                        <div class="input-group date">
                                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.from_date" data-dateField="from_date" readonly placeholder="Select date from picker" id="from_date" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar-check-o"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="">
                                    <label id="to_date" class="m-label m-label--single">To:</label>
                                    <div class="m-form__control">
                                        <div class="input-group date">
                                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.to_date" data-dateField="to_date" readonly placeholder="Select date from picker" id="to_date" />
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar-check-o"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div  class="col-md-4">
                                <br/>
                                <div class="">
                                    <div class="m-input-icon m-input-icon--left">
                                        <button type="submit" class="btn btn-sm btn-success" ><i class="la la-search"></i> <span>Search</span></button>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>

                </div>
            </div>
        </form>
        <hr>
        <div class="m-portlet__body" v-if="product_list">
            <div class="item-show-limit">
                <span>Show</span>
                <select name="per_page" class="per_page" @change="changePerPage" v-model="perPage">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="30">30</option>
                    <option value="40">40</option>
                    <option value="50">50</option>
                </select>
                <span>Entries</span>
            </div>
            <br>
            <!--begin: Datatable -->
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                <tr>
                    <th>S/L</th>
                    <th>Work Order  No</th>
                    <th>LC No</th>
                    <th>CI No</th>
                    <th>Supplier</th>
                    <th>Date</th>
                    <th>Qty</th>
                    <th>Narration</th>
                    <th>Total Amount (BDT)</th>
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.get_lc_no.get_pi_no.get_work_order.work_order_no}}</td>
                    <td>{{value.get_lc_no.lc_no}}</td>
                    
                    <td>{{value.ci_no}}</td>
                    <!-- <td>{{value.get_supplier ? value.get_supplier.purchase_supplier_name : ''}}</td> -->
                    <td>{{value.get_lc_no.get_supplier.purchase_supplier_name}}</td>
                    <td>{{value.ci_bill_entry_date}}</td>
                    <td>{{value.total_qty}}</td>
                    <td>{{value.narration}}</td>
                    <td class="text-right">{{value.total_amount}}</td>
                    <td scope="row" class="width-100 text-center">
                        <a v-if="permissions.indexOf('lc-report.show') !=-1" @click="viewData(value.id)" class="btn btn-outline-success m-btn m-btn--icon  m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="View"><i class="la la-eye"></i> Landed Cost</a>
                    </td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="10" class="text-center">No Data Available.</td>
                </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="text-center col-md-12" >
                    <pagination :resultData="resultData" @clicked="index" :mid-size="9"></pagination>
                </div>
            </div>
        </div>

        <ViewLcReport
            v-else-if="view_form"
            :edit-id="edit_id"
        ></ViewLcReport>
    </div>
</template>

<script>

    import { EventBus } from '../../vue-assets';
    import Pagination from  '../../components/Pagination.vue';
    import ViewLcReport from './ViewLcReport.vue';


    export default {
        components: {
            ViewLcReport,
            Pagination
        },

        props:['permissions','supplier_lists','product_lists','work_order_lists'],

        data: function(){
            return {
                product_list:true,
                add_form:false,
                edit_form:false,
                edit_id:false,
                view_form:false,
                resultData: {},
                form:{
                    work_order_id:'',
                    supplier_id: '',
                    product_id: '',
                    from_date: '',
                    to_date: '',
                },
                errors: {},
                perPage: 10
            };
        },

        methods: {
            search(){
                var _this = this;
                _this.index(1);
            },

            index(pageNo,perPage){

                if(pageNo){ pageNo = pageNo; }else{pageNo = this.resultData.current_page; }
                if(perPage){ perPage = perPage;}else{ perPage = this.perPage;}

                var work_order_id = this.form.work_order_id;
                var supplier_id = this.form.supplier_id;
                var product_id = this.form.product_id;
                var from_date = this.form.from_date;
                var to_date = this.form.to_date;

                axios.get(base_url+"lc-report/?page="+pageNo+"&perPage="+perPage+"&work_order_id="+work_order_id+"&supplier_id="+supplier_id+"&product_id="+product_id+"&from_date="+from_date+"&to_date="+to_date).then((response) => {
                    this.resultData = response.data;

                });
            },

            viewData(id){
                var _this = this;
                _this.add_form = false;
                _this.product_list = false;
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

            changePerPage(){
                var vm = this;
                vm.index(1,vm.perPage);
            },
        },

        mounted(){
            var _this =this;
            $('#listComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    dataIndex = $(e.currentTarget).attr('data-selectField');

                    console.log( selectedType.val() );
                if(dataIndex == 'work_order_id'){
                    _this.form.work_order_id = selectedType.val();
                    if (selectedType.val()==0) { _this.form.work_order_id = ''; }
                }else if(dataIndex == 'supplier_id'){
                    _this.form.supplier_id = selectedType.val();
                    if (selectedType.val()==0) { _this.form.supplier_id = ''; }
                }else if(dataIndex == 'product_id'){
                    _this.form.product_id = selectedType.val();
                    if (selectedType.val()==0) { _this.form.product_id = ''; }
                }
            });

            var Select2= {
                init:function() {
                    $(".select2").select2( {
                        placeholder: "Please select an option"
                    })
                }
            };
            jQuery(document).ready(function() {
                Select2.init()
            });

            $(document).on("focus", ".datepicker", function () {

                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    var selectField = $(ev.currentTarget).attr('data-dateField');

                    if(ev.date == undefined){
                        if (selectField == 'from_date') {
                            _this.form.from_date = '';
                        } else if(selectField == 'to_date'){
                            _this.form.to_date = '';
                        }
                    }else if(selectField == 'from_date'){
                        _this.form.from_date = moment(ev.date).format('YYYY-MM-DD');
                    }else if(selectField == 'to_date'){
                        _this.form.to_date = moment(ev.date).format('YYYY-MM-DD');
                    }
                });
            });
        },

        created(){

            var _this = this;
            $('body').on('click', '#addButton', function() {
                _this.add_form = true;
                _this.product_list = false;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').hide();
                $('#listButton').show();
            });

            $('body').on('click', '#listButton', function() {
                _this.add_form = false;
                _this.product_list = true;
                _this.edit_form = false;
                _this.view_form = false;
                // $('#addButton').show();
                // $('#listButton').hide();
            });

            EventBus.$on('data-changed', (id) => {
                _this.add_form = false;
                _this.product_list = true;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').show();
                $('#listButton').hide();
                _this.index(1);
            });

            // _this.index(1);
        },
    }
</script>
