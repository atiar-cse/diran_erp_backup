<template>
    <div>
        <!--begin:: Add Modal-->
        <div class="modal fade" id="addModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add Customer</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="store" class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">
                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Customer Name <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm m-input" v-model="form.sales_customer_name" placeholder="Enter Customer Name" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_name'))">{{ (errors.hasOwnProperty('sales_customer_name')) ? errors.sales_customer_name[0] :'' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact Person Name <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.sales_customer_contact_person_name" placeholder="Enter sales Contact Person name" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_contact_person_name'))">{{ (errors.hasOwnProperty('sales_customer_contact_person_name')) ? errors.sales_customer_contact_person_name[0] :'' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Chart of Account Head <span class="requiredField">*</span></label>
                                        <select class="form-control m-select2 select2" data-selectField="chart_ac_id" id="chart_ac_id" v-model="form.chart_ac_id" style="width: 100%"  >
                                            <option v-for="(value,index) in chat_lists" :value="value.id"> {{value.chart_of_accounts_title}}({{value.chart_of_account_code}})</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['chart_ac_id']">{{ errors['chart_ac_id'][0] }}</div>

                                        <!--<span class="requiredField"><b> Please,Collect Code from Account Dept. </b></span>
                                        <label>Customer Code <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.sales_customer_id" placeholder="Enter sales suplier Id" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_id'))">{{ (errors.hasOwnProperty('sales_customer_id')) ? errors.sales_customer_id[0] :'' }}</div>-->
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>Join Date <span class="requiredField">*</span></label>
                                    <div class="input-group date">
                                        <input type="text" id="date" class="form-control form-control-sm m-input datepicker" v-model="form.sales_customer_join_date" data-dateField="sales_customer_join_date" placeholder="Select date from picker"  />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_join_date'))">{{ (errors.hasOwnProperty('sales_customer_join_date')) ? errors.sales_customer_join_date[0] :'' }}</div>
                                </div>
                            </div>
                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact Person Job Title </label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.sales_customer_contact_person_job_title" placeholder="Enter contact person job title" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Business Phone  <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm ph" v-model="form.sales_customer_business_phone" placeholder="Enter sales business phone" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_business_phone'))">{{ (errors.hasOwnProperty('sales_customer_business_phone')) ? errors.sales_customer_business_phone[0] :'' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Mobile <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm ph" v-model="form.sales_customer_mobile" placeholder="Enter mobile no" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_mobile'))">{{ (errors.hasOwnProperty('sales_customer_mobile')) ? errors.sales_customer_mobile[0] :'' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.sales_customer_email" placeholder="Enter email address" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Credit Limit</label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.sales_customer_credit_limit" placeholder="How much a customer get due limit this here" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Web Address</label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.sales_customer_web_address" placeholder="Enter web address" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 row" >
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Narration</label>
                                    <textarea v-model="form.sales_customer_narration" class="form-control form-control-sm" placeholder="If any write about customer write here"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Office Address</label>
                                    <textarea v-model="form.sales_customer_office_address" class="form-control form-control-sm" placeholder="Enter Sales Customer Office Address"></textarea>
                                </div>
                            </div>
                        </div>
                            <div class="col-md-12 row" :class="{'has-danger': (errors.hasOwnProperty('status'))}" >
                            <label class="col-form-label col-lg-4 col-sm-12">Status <span class="requiredField">*</span></label>
                            <div class="col-md-6">
                                <span class="m-switch m-switch--icon  pull-left">
                                    <label>
                                        <input type="checkbox" checked="checked" v-model="form.status">
                                        <span></span>
                                    </label>
                                </span>
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>

                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end::Modal-->

        <!--begin:: Edit Modal-->
        <div class="modal fade" id="editModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Customer</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">

                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Customer Name <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.sales_customer_name" placeholder="Enter sales suplier name" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_name'))">{{ (errors.hasOwnProperty('sales_customer_name')) ? errors.sales_customer_name[0] :'' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact Person Name <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.sales_customer_contact_person_name" placeholder="Enter sales Contact Person name" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_contact_person_name'))">{{ (errors.hasOwnProperty('sales_customer_contact_person_name')) ? errors.sales_customer_contact_person_name[0] :'' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">

                                        <label>Chart of Account Head <span class="requiredField">*</span></label>
                                        <select class="form-control m-select2 select2" data-selectField="chart_ac_id" id="chart_ac_id2" v-model="form.chart_ac_id" style="width: 100%" >
                                            <option v-for="(value,index) in chat_lists" :value="value.id"> {{value.chart_of_accounts_title}}({{value.chart_of_account_code}})</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['chart_ac_id']">{{ errors['chart_ac_id'][0] }}</div>

                                        <!--<span class="requiredField"><b> Please,Collect Code from Account Dept. </b></span>
                                        <label>Customer Code <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.sales_customer_id" placeholder="Enter Customer Id" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_id'))">{{ (errors.hasOwnProperty('sales_customer_id')) ? errors.sales_customer_id[0] :'' }}</div>-->
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label>Join Date <span class="requiredField">*</span></label>
                                    <div class="input-group date">
                                        <input type="text" id="date2" class="form-control form-control-sm m-input datepicker" v-model="form.sales_customer_join_date" data-dateField="sales_customer_join_date" placeholder="Select date from picker"  />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_join_date'))">{{ (errors.hasOwnProperty('sales_customer_join_date')) ? errors.sales_customer_join_date[0] :'' }}</div>
                                </div>
                            </div>

                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact Person Job Title </label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.sales_customer_contact_person_job_title" placeholder="Enter contact person job title" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Business Phone  <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm ph" v-model="form.sales_customer_business_phone" placeholder="Enter business phone" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_business_phone'))">{{ (errors.hasOwnProperty('sales_customer_business_phone')) ? errors.sales_customer_business_phone[0] :'' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Mobile <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm ph" v-model="form.sales_customer_mobile" placeholder="Enter mobile no" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_mobile'))">{{ (errors.hasOwnProperty('sales_customer_mobile')) ? errors.sales_customer_mobile[0] :'' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.sales_customer_email" placeholder="Enter email address" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Credit Limit</label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.sales_customer_credit_limit" placeholder="How much a customer get due limit this here" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Web Address</label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.sales_customer_web_address" placeholder="Enter web address" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Narration</label>
                                        <textarea v-model="form.sales_customer_narration" class="form-control form-control-sm" placeholder="If any write about customer write here"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Office Address</label>
                                        <textarea v-model="form.sales_customer_office_address" class="form-control form-control-sm" placeholder="Enter Customer Office Address"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 row" :class="{'has-danger': (errors.hasOwnProperty('status'))}" >
                                <label class="col-form-label col-lg-4 col-sm-12">Status <span class="requiredField">*</span></label>
                                <div class="col-md-6">
                                <span class="m-switch m-switch--icon  pull-left">
                                    <label>
                                        <input type="checkbox" checked="checked" v-model="form.status">
                                        <span></span>
                                    </label>
                                </span>
                                </div>
                                <div class="col-md-6">
                                </div>
                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Update</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end::Modal-->
    </div>
</template>

<script>
    import { EventBus } from '../../../vue-assets';

    export default {

        props:['permissions','chat_lists'],

        data:function(){
            return{
                form:{
                    sales_customer_name: '',
                    chart_ac_id: '',
                    sales_customer_join_date: '',
                    sales_customer_contact_person_name: '',
                    sales_customer_contact_person_job_title:'',
                    sales_customer_business_phone:'',
                    sales_customer_mobile:'',
                    sales_customer_email:'',
                    sales_customer_web_address:'',
                    sales_customer_credit_limit:'',
                    sales_customer_office_address:'',
                    sales_customer_narration:'',
                    status:'1'
                },
                errors: {},
            };
        },

        methods:{

            store:function(){
                axios.post(base_url+'customer/store', this.form).then( (response) => {
                    $('#addModel').modal('hide');
                    this.showMassage(response.data);
                    EventBus.$emit('new-data-created');
                })
                    .catch(error => {
                        if(error.response.status == 422){
                            this.errors = error.response.data.errors;
                        }else{
                            this.showMassage(error);
                        }
                    });
            },

            update:function (id) {
                axios.put(base_url+'customer/'+id+'/update', this.form).then( (response) => {
                    $('#editModel').modal('hide');
                    this.showMassage(response.data);
                    EventBus.$emit('new-data-created');
                })
                    .catch(error => {
                        if(error.response.status == 422){
                            this.errors = error.response.data.errors;
                        }else{
                            this.showMassage(error);
                        }
                    });
            },

           /* phoneCheck(fieldName){
                alert(fieldName);
                var _this = this;
                var number = this.form.fieldName;
                alert(number);
                console.log(number);

            },*/

            showMassage(data){
                if(data.status == 'success'){
                    toastr.success(data.message, 'Success');
                }else if(data.status == 'error'){
                    toastr.error(data.message, 'Error');
                }else{
                    toastr.error(data.message, 'Error');
                }
            },

            initSelect2(){
                setTimeout(function () {
                    var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                    jQuery(document).ready(function() {Select2.init();});
                },250);
            },
        },

        mounted(){
            var _this = this;
            $('#addModel,#editModel').on('hidden.bs.modal', function(){
                _this.errors = {};
                _this.form = {
                    'sales_customer_name': '',
                    'chart_ac_id': '',
                    'sales_customer_join_date': '',
                    'sales_customer_contact_person_name': '',
                    'sales_customer_contact_person_job_title':'',
                    'sales_customer_business_phone':'',
                    'sales_customer_mobile':'',
                    'sales_customer_email':'',
                    'sales_customer_web_address':'',
                    'sales_customer_credit_limit':'',
                    'sales_customer_office_address':'',
                    'sales_customer_narration':'',
                    'status':'1'

                };
            });

            $('#addModel,#editModel').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'chart_ac_id'){
                    _this.form.chart_ac_id = selectedType.val();
                }
            });


            _this.initSelect2();

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                    //startDate: new Date(),
                    //startDate: '-2d',
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.sales_customer_join_date = '';

                    }else {
                        _this.form.sales_customer_join_date = moment(ev.date).format('DD/MM/YYYY');

                    }

                });
            });

            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                axios.get(base_url+'customer/'+id+'/edit').then((response) => {
                    _this.form = response.data;
                    $('#editModel').modal("show");

                    _this.initSelect2();
                });
            });

            /*$('.ph').keypress(function(event) {
                //alert(event.target.value+'22222');
                var num=event.target.value;
                var regex = /^[+]*[(]{0,1}[0-9]{1,3}[)]{0,1}[-\s\./0-9]*$/g;

                if(regex.test(num)){
                    console.log("true");
                    //event.preventDefault();
                }else{
                    console.log("false");
                    //event.preventDefault();
                }
            }).on('paste', function(event) {
                event.preventDefault();
            });*/

            $('.ph').keypress(function (event) {
                if (((event.which != 46 || (event.which == 46 && $(this).val() == '')) ||
                    $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            }).on('paste', function(event) {
                event.preventDefault();
            });

        },

        created(){

        }

    }
</script>
