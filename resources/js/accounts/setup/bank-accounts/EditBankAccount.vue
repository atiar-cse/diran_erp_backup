<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="accounts_bank_id" class="col-lg-3 col-form-label">Bank <span class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <select class="form-control select2" id="accounts_bank_id"  v-model="form.accounts_bank_id" data-selectField="accounts_bank_id" @change="loadBranch()">
                            <option v-for="(value,index) in bank_lists" :value="value.id"> {{value.accounts_bank_names}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['accounts_bank_id']">{{ errors['accounts_bank_id'][0] }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="accounts_branch_id">Bank Branch </label>
                    <div class="col-lg-6">
                        <select class="form-control select2" id="accounts_branch_id"  v-model="form.accounts_branch_id" data-selectField="accounts_branch_id">
                            <option v-for="(bvalue,bindex) in branch_list" :value="bvalue.id"> {{bvalue.bank_branch_names}}</option>
                        </select>
                        <div class="requiredField" v-if="errors['accounts_branch_id']">{{ errors['accounts_branch_id'][0] }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="accounts_bank_accounts_name">Account Name <span class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <input type="text" id="accounts_bank_accounts_name" v-model="form.accounts_bank_accounts_name" class="form-control form-control-sm m-input" placeholder="Enter Account Name">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('accounts_bank_accounts_name'))">{{ (errors.hasOwnProperty('accounts_bank_accounts_name')) ? errors.accounts_bank_accounts_name[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="accounts_bank_accounts_number">Account Number </label>
                    <div class="col-lg-6">
                        <input type="text" id="accounts_bank_accounts_number" v-model="form.accounts_bank_accounts_number" class="form-control form-control-sm m-input" placeholder="Enter Account Number">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('accounts_bank_accounts_number'))">{{ (errors.hasOwnProperty('accounts_bank_accounts_number')) ? errors.accounts_bank_accounts_number[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="accounts_branch_id">Chart of Account Head </label>
                    <div class="col-lg-6">
                        <select class="form-control select2" id="chart_ac_id"  v-model="form.chart_ac_id" data-selectField="chart_ac_id">
                            <option v-for="(cvalue,cindex) in chart_lists" :value="cvalue.id"> {{cvalue.chart_of_accounts_title}}({{cvalue.chart_of_account_code}})</option>
                        </select>
                        <div class="requiredField" v-if="errors['chart_ac_id']">{{ errors['chart_ac_id'][0] }}</div>
                    </div>
                </div>



                <div class="form-group m-form__group row">
                    <label for="m_datepicker_2" class="col-lg-3 col-form-label">Opening Date </label>
                    <div class="col-lg-6">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.accounts_bank_accounts_date" data-dateField="accounts_bank_accounts_date" readonly placeholder="Select date from picker" id="m_datepicker_2" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('accounts_bank_accounts_date'))">{{ (errors.hasOwnProperty('accounts_bank_accounts_date')) ? errors.accounts_bank_accounts_date[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="accounts_bank_accounts_contact_person">Contact Person </label>
                    <div class="col-lg-6">
                        <input type="text" id="accounts_bank_accounts_contact_person" v-model="form.accounts_bank_accounts_contact_person" class="form-control form-control-sm m-input" placeholder="Enter Contact Person Name">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('accounts_bank_accounts_contact_person'))">{{ (errors.hasOwnProperty('accounts_bank_accounts_contact_person')) ? errors.accounts_bank_accounts_contact_person[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="accounts_bank_accounts_contact_number">Contact Number </label>
                    <div class="col-lg-6">
                        <input type="text" id="accounts_bank_accounts_contact_number" v-model="form.accounts_bank_accounts_contact_number" class="form-control form-control-sm m-input" placeholder="Enter Contact Number">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('accounts_bank_accounts_contact_number'))">{{ (errors.hasOwnProperty('accounts_bank_accounts_contact_number')) ? errors.accounts_bank_accounts_contact_number[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-3 col-form-label">Status ?</label>
                    <div class="col-lg-6">
                        <span class="m-switch m-switch--icon">
                            <label>
                                <input type="checkbox" checked="checked" v-model="form.status">
                                <span></span>
                            </label>
                        </span>
                    </div>
                </div>
            </div>
            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-12 m--align-right">
                            <button type="reset" class="btn btn-sm btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Update and Go List</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
</template>

<script>
    import { EventBus } from '../../../vue-assets';

    export default {

        props:['bank_lists','chart_lists','editId'],

        data:function(){
            return{
                branch_list: [],
                account_list:false,
                add_form:false,
                edit_form:true,
                form:{
                    accounts_bank_id: '',
                    accounts_branch_id: '',
                    accounts_bank_accounts_name: '',
                    accounts_bank_accounts_number: '',
                    accounts_bank_accounts_date: '',
                    accounts_bank_accounts_contact_person: '',
                    accounts_bank_accounts_contact_number: '',
                    chart_ac_id: '',
                    status:'1'
                },
                errors: {},
            };
        },

        methods:{
            edit(id) {
                var _this = this;
                axios.get(base_url+'bank-account/'+id+'/edit')
                    .then( (response) => {
                        _this.form  = response.data.data;
                        _this.branch_list  = response.data.branch;
                        setTimeout(function () {
                            var Select2= {init:function() {$(".select2").select2( {placeholder: "Please select an option"})}};
                            jQuery(document).ready(function() {Select2.init()});
                        },100);
                    });
            },

            update(id){
                axios.put(base_url+'bank-account/'+id+'/update', this.form).then( (response) => {
                    this.showMassage(response.data);
                    EventBus.$emit('data-changed');
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
                    toastr.success(data.message, 'Success Alert');
                }else if(data.status == 'error'){
                    toastr.error(data.message, 'Error Alert');
                }else{
                    toastr.error(data.message, 'Error Alert');
                }
            },

            loadBranch(){
                var _this = this;
                axios.get(base_url + 'bank-wise-branch?bank_id=' + this.form.accounts_bank_id)
                    .then((response) => {
                        _this.branch_list = response.data;
                    });
            },
        },

        mounted(){
            var _this = this;
            $('#editComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');
                if( selectField == 'accounts_bank_id' ){
                    _this.form.accounts_bank_id= selectedItem.val();
                    _this.loadBranch();
                }else if(selectField == 'accounts_branch_id'){
                    _this.form.accounts_branch_id= selectedItem.val();
                }else if(selectField == 'chart_ac_id'){
                    _this.form.chart_ac_id= selectedItem.val();
                }
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                    //startDate: '-2d',
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.accounts_bank_accounts_date = '';
                    }else {
                        _this.form.accounts_bank_accounts_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
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

        },
        created(){
            var _this = this;
            _this.edit(_this.editId);
        }

    }
</script>
