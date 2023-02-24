<template>
    <div>
        <!--begin:: Add Modal-->
        <div class="modal fade" id="addModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add Purchase Supplier</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="store" class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">
                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Supplier Name <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm m-input" v-model="form.purchase_supplier_name" placeholder="Enter purchase suplier name" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('purchase_supplier_name'))">{{ (errors.hasOwnProperty('purchase_supplier_name')) ? errors.purchase_supplier_name[0] :'' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact Person Name <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.purchase_supplier_contact_person_name" placeholder="Enter purchase Contact Person name" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('purchase_supplier_contact_person_name'))">{{ (errors.hasOwnProperty('purchase_supplier_contact_person_name')) ? errors.purchase_supplier_contact_person_name[0] :'' }}</div>
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


                                        <!--<span class="requiredField"><b> Please,Collect Code from Account Dept.</b> </span>
                                        <label>Supplier Code <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.purchase_supplier_id" placeholder="Enter purchase Supplier Id" />
                                        -->
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="purchase_supplier_join_date" >Supplier Join Date</label>
                                        <div class="input-group date">
                                            <input type="text" readonly id="purchase_supplier_join_date" class="form-control form-control-sm m-input datepicker" v-model="form.purchase_supplier_join_date" data-dateField="purchase_supplier_join_date" placeholder="Select date from picker"  />
                                            <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-calendar-check-o"></i>
                                                    </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact Person Job Title </label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.purchase_supplier_contact_person_job_title" placeholder="Enter contact person job title" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Business Phone  <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm ph" v-model="form.purchase_supplier_business_phone" placeholder="Enter purchase business phone" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('purchase_supplier_business_phone'))">{{ (errors.hasOwnProperty('purchase_supplier_business_phone')) ? errors.purchase_supplier_business_phone[0] :'' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Mobile <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm ph" v-model="form.purchase_supplier_mobile" placeholder="Enter mobile no" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('purchase_supplier_mobile'))">{{ (errors.hasOwnProperty('purchase_supplier_mobile')) ? errors.purchase_supplier_mobile[0] :'' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.purchase_supplier_email" placeholder="Enter email address" />
                                    </div>
                                </div>
                            </div>
                        <div class="col-md-12 row" >
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Credit Limit</label>
                                    <input type="text" class="form-control form-control-sm" v-model="form.purchase_supplier_credit_limit" placeholder="How much a customer get due limit this here" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label> Web Address</label>
                                    <input type="text" class="form-control form-control-sm" v-model="form.purchase_supplier_web_address" placeholder="Enter web address" />
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 row" >
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Narration</label>
                                    <textarea v-model="form.purchase_supplier_narration" class="form-control form-control-sm" placeholder="If any write about supplier write here"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Office Address<span class="requiredField">*</span></label>
                                    <textarea v-model="form.purchase_supplier_office_address" class="form-control form-control-sm" placeholder="Enter Purchase Supplier Office Address"></textarea>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('purchase_supplier_office_address'))">{{ (errors.hasOwnProperty('purchase_supplier_office_address')) ? errors.purchase_supplier_office_address[0] :'' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Is Importer?  <span class="requiredField">*</span></label>
                                    <select class="form-control select2" id="is_importer1"  v-model="form.is_importer" data-selectField="is_importer" style="width:100%">
                                        <option value="0"> No</option>
                                        <option value="1"> Yes</option>
                                    </select>
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
                        <h5 class="modal-title">Edit Purchase Supplier</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">

                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Purchase Supplier Name <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.purchase_supplier_name" placeholder="Enter purchase suplier name" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('purchase_supplier_name'))">{{ (errors.hasOwnProperty('purchase_supplier_name')) ? errors.purchase_supplier_name[0] :'' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Purchase Contact Person Name <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.purchase_supplier_contact_person_name" placeholder="Enter purchase Contact Person name" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('purchase_supplier_contact_person_name'))">{{ (errors.hasOwnProperty('purchase_supplier_contact_person_name')) ? errors.purchase_supplier_contact_person_name[0] :'' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Chart of Account Head <span class="requiredField">*</span></label>
                                        <select class="form-control m-select2 select2" data-selectField="chart_ac_id" id="chart_ac_id2" v-model="form.chart_ac_id" style="width: 100%" >
                                            <option v-for="(value,index) in chat_lists" :value="value.id"> {{value.chart_of_accounts_title}}({{value.chart_of_account_code}})</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['chart_ac_id']">{{ errors['chart_ac_id'][0] }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="purchase_supplier_date">Supplier Join Date</label>
                                        <div class="input-group date">
                                            <input type="text" readonly id="purchase_supplier_date"class="form-control form-control-sm m-input datepicker" v-model="form.purchase_supplier_join_date" data-dateField="purchase_supplier_join_date" placeholder="Select date from picker"/>
                                            <div class="input-group-append">
                                                    <span class="input-group-text">
                                                        <i class="la la-calendar-check-o"></i>
                                                    </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact Person Job Title </label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.purchase_supplier_contact_person_job_title" placeholder="Enter contact person job title"/>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Business Phone <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm ph" v-model="form.purchase_supplier_business_phone" placeholder="Enter purchase business phone"/>
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('purchase_supplier_business_phone'))">{{(errors.hasOwnProperty('purchase_supplier_business_phone')) ? errors.purchase_supplier_business_phone[0] :'' }}</div>
                                    </div>
                                </div>
                            </div>



                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Mobile <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm ph" v-model="form.purchase_supplier_mobile" placeholder="Enter mobile no" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('purchase_supplier_mobile'))">{{ (errors.hasOwnProperty('purchase_supplier_mobile')) ? errors.purchase_supplier_mobile[0] :'' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.purchase_supplier_email" placeholder="Enter email address" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Credit Limit</label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.purchase_supplier_credit_limit" placeholder="How much a customer get due limit this here" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label> Web Address</label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.purchase_supplier_web_address" placeholder="Enter web address" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Narration</label>
                                        <textarea v-model="form.purchase_supplier_narration" class="form-control form-control-sm" placeholder="If any write about supplier write here"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Office Address</label>
                                        <textarea v-model="form.purchase_supplier_office_address" class="form-control form-control-sm" placeholder="Enter Purchase Supplier Office Address"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Is Importer?  <span class="requiredField">*</span></label>
                                        <select class="form-control select2" id="is_importer"  v-model="form.is_importer" data-selectField="is_importer" style="width:100%">
                                            <option value="0"> No</option>
                                            <option value="1"> Yes</option>
                                        </select>
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
                    purchase_supplier_name: '',
                    chart_ac_id: '',
                    purchase_supplier_join_date: '',
                    purchase_supplier_contact_person_name: '',
                    purchase_supplier_contact_person_job_title:'',
                    purchase_supplier_business_phone:'',
                    purchase_supplier_mobile:'',
                    purchase_supplier_email:'',
                    purchase_supplier_web_address:'',
                    purchase_supplier_credit_limit:'',
                    purchase_supplier_office_address:'',
                    purchase_supplier_narration:'',
                    is_importer:'',
                    status:'1'
                },
                errors: {},
            };
        },

        methods:{
            store:function(){
                axios.post(base_url+'supplier-entry/store', this.form).then( (response) => {
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
                axios.put(base_url+'supplier-entry/'+id+'/update', this.form).then( (response) => {
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

            _this.initSelect2();

            $('#addModel,#editModel').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if( selectField == 'is_importer' ){
                    _this.form.is_importer= selectedItem.val();
                }else if(selectField == 'chart_ac_id'){
                    _this.form.chart_ac_id = selectedItem.val();
                }
            });

            $('#addModel,#editModel').on('hidden.bs.modal', function(){
                _this.errors = {};
                _this.form = {
                    'purchase_supplier_name': '',
                    'chart_ac_id': '',
                    'purchase_supplier_join_date': '',
                    'purchase_supplier_contact_person_name': '',
                    'purchase_supplier_contact_person_job_title':'',
                    'purchase_supplier_business_phone':'',
                    'purchase_supplier_mobile':'',
                    'purchase_supplier_email':'',
                    'purchase_supplier_web_address':'',
                    'purchase_supplier_credit_limit':'',
                    'purchase_supplier_office_address':'',
                    'purchase_supplier_narration':'',

                    'is_importer':'',
                    'status':'1'

                };
            });


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
                        _this.form.purchase_supplier_join_date = '';

                    }else {
                        _this.form.purchase_supplier_join_date = moment(ev.date).format('DD/MM/YYYY');

                    }
                });
            });

            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                axios.get(base_url+'supplier-entry/'+id+'/edit').then((response) => {
                    _this.form = response.data;
                    $('#editModel').modal("show");
                    _this.initSelect2();
                });
            });

            $('.ph').keypress(function(event) {
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
