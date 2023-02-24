<template>
    <div>
        <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="search" id="listComponent" class="m-form m-form--fit m-form--label-align-right hide_print" >

            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2">
                    <a href="#" onclick="window.print();return false" class="btn btn-primary btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa flaticon-technology"></i><span>Print</span></span></a>
                    <a href="#" onclick="window.print();return false" class="btn btn-accent btn-sm m-btn  m-btn m-btn--icon"><span><i class="fa flaticon-doc"></i><span>PDF</span></span></a>
                </div>
            </div>

            <div class="m-form m-form--label-align-right m--margin-top-20 m--margin-bottom-30">
                <div class="row align-items-center">
                    <div class="col-xl-12 order-2 order-xl-1">
                        <div class="form-group m-form__group row align-items-center">

                            <div class="col-md-3">
                                <div class="">
                                    <label id="supplier_id" class="m-label m-label--single">Supplier :</label>
                                    <div class="m-form__control">
                                        <select id="supplier_id" class="form-control m-select2 select2" data-selectField="supplier_id"  v-model="form.supplier_id" >
                                            <option v-for="(cvalue,cindex) in supplier_lists" :value="cvalue.id" > {{cvalue.purchase_supplier_name}}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="d-md-none m--margin-bottom-10"></div>
                            </div>

                            <div class="col-md-3">
                                <div class="">
                                    <label id="from_date" class="m-label m-label--single">From:</label>
                                    <div class="m-form__control">
                                        <div class="input-group date">
                                            <input type="text" class="form-control form-control-sm m-input datepicker from_date" v-model="form.from_date" data-dateField="from_date" readonly placeholder="Select date from picker" id="from_date" />
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
                            <div class="col-md-3">
                                <div class="">
                                    <label id="end_date" class="m-label m-label--single">To:</label>
                                    <div class="m-form__control">
                                        <div class="input-group date">
                                            <input type="text" class="form-control form-control-sm m-input datepicker end_date" v-model="form.end_date" data-dateField="end_date" readonly placeholder="Select date from picker" id="end_date" />
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

                            <div  class="col-md-3">
                                <br/>
                                <div class="">
                                    <div class="m-input-icon m-input-icon--left">
                                        <button type="submit" class="btn btn-sm btn-success" ><i class="la la-search" id="icon_submit"></i> <span>Search</span></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </form>        

        <div class="m-portlet__body" v-if="customer_ledeger_report_list">

            <div class="m-invoice__head print_only">
                <div class="m-invoice__container m-invoice__container--centered">
                    <div class="row invoice-header"  style="padding-top: 0px;padding-bottom:15px;">
                        <div class="col-md-4">
                            <div class="db-data">
                                <p><span style="font-weight: 600">Supplier :</span> <span>{{supplierName}}</span></p>
                                <p><span style="font-weight: 600">From Date :</span> <span>{{form.from_date}}</span></p>
                                <p><span style="font-weight: 600">To Date : </span> <span>{{form.end_date}}</span></p>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                        <span class="m-invoice__desc">
                            <p><span style="font-weight: 600">Supplier Ledeger Report</span></p>
                            <span>I-Venture Limited</span><br>
                            <span>Level 7, 339/B, Tejgaon I/A</span><br>
                            <span>Dhaka 1208, 1212</span>
                        </span>
                      </div>

                        <div class="col-md-4">
                        <p style="margin-bottom: 62px;">
                            <img src="img/1200px-Channel-i.svg.png" style="width: 150px;float: right">
                            <!--<img src="{{asset('assets/uploads/company_logo/' .$companyInfo.logo)}}" style="width: 150px; height: 50px;float: right">-->
                        </p>
                      </div>
                    </div>
                </div>
            </div>

            <!--begin: Datatable -->
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                    <tr>
                        <th>S/L</th>
                        <th>Jrnl. Date</th>
                        <th>Jrnl. Ref. No</th>
                        <th>Jrnl. Desc</th>

                        <th>Debit Amount</th>
                        <th>Credit Amount</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody v-if="resultData !=''">
                    <tr v-for="(value,index) in resultData">
                        <td>{{++index}}</td>
                        <td>{{value.transaction_date}}</td>
                        <td>{{value.transaction_reference_no}}</td>
                        <td>{{value.transaction_type_name}}</td>

                        <td class="text-right">{{value.total_debit}}</td>
                        <td class="text-right">{{value.total_credit}}</td>
                        <td>{{value.narration}}</td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr>
                        <td colspan="11" class="text-center">No Data Available.</td>
                    </tr>
                </tbody>
                <tfoot v-if="resultData !=''">
                    <tr>
                        <td colspan="4" align="right"><strong>Transaction Total : </strong></td>

                        <td class="text-right"><strong> {{totalDebit.toFixed(2)}} </strong></td>
                        <td class="text-right"><strong> {{totalCredit.toFixed(2)}} </strong></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="4" align="right"><strong> Amount Balance : </strong></td>

                        <td colspan="2" align="middle"><strong> {{totalBalance}} </strong></td>
                        <td></td>
                    </tr>                    
                </tfoot>
            </table>

        </div>

    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import moment from 'moment';

    export default {

        props:['permissions','supplier_lists'],

        data: function(){
            return {
                customer_ledeger_report_list:true,

                totalDebit:0,
                totalCredit:0,
                totalBalance:0,
                supplierName:'',

                resultData: {},
                form:{
                    supplier_id:'',
                    from_date: '',
                    end_date: '',
                },
                errors: {},
            };
        },

        methods: {
            search:function(){
                var _this = this;

                $('#icon_submit').addClass('la-spin');
                axios.post(base_url+'purchase-report/supplier', this.form)
                    .then( (response) => {
                        _this.resultData  = response.data;
                        //EventBus.$emit('data-changed');
                         _this.calcTotal();
                         $('#icon_submit').removeClass('la-spin');
                    }).catch(error => {
                        if(error.response.status == 422){
                            this.errors = error.response.data.errors;
                        }else if(error.response.data.status=='failed'){
                            this.showMassage(error.response.data);
                        }else{
                            this.showMassage(error);
                        }
                        $('#icon_submit').removeClass('la-spin');
                    });
            },

            format_date(value){
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

            calcTotal(){
                var _this = this;

                var total_debit = 0;
                var total_credit = 0;
                _this.resultData.forEach(function(item){

                    total_debit = total_debit + parseFloat(item.total_debit); 
                    total_credit = total_credit + parseFloat(item.total_debit); 
                });

                _this.totalDebit = total_debit;
                _this.totalCredit = total_credit;
                _this.totalBalance = parseFloat(_this.totalDebit - _this.totalCredit).toFixed(2);
            }            
        },

        mounted(){
            var _this =this;
            $('#listComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'supplier_id'){
                    _this.form.supplier_id = selectedType.val();
                    _this.supplierName = $('option:selected', $(e.target)).text();
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
                    clearBtn: true,
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');

                    var dateField = $(this).attr('id');

                    if(ev.date == undefined){
                        if(dateField == 'from_date'){
                            _this.form.from_date = '';
                        }else if(dateField == 'end_date'){
                            _this.form.end_date = '';
                        }
                    }else {
                        if(dateField == 'from_date'){
                            _this.form.from_date = moment(ev.date).format('YYYY-MM-DD');
                        }else if(dateField == 'end_date'){
                            _this.form.end_date = moment(ev.date).format('YYYY-MM-DD');
                        }
                    }

                });
            });
        },

        created(){

            var _this = this;

            $('body').on('click', '#listButton', function() {
                _this.add_form = false;
                _this.customer_ledeger_report_list = true;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').show();
                $('#listButton').hide();
            });

            EventBus.$on('data-changed', (id) => {
                _this.add_form = false;
                _this.customer_ledeger_report_list = true;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').show();
                $('#listButton').hide();
                _this.search(1);
            });

            _this.search(1);
        },       
    }
</script>

