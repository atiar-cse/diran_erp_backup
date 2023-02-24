<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="warning_to_employee_id" class="col-lg-3 col-form-label">Warning To <span class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <select class="form-control select2" id="warning_to_employee_id"  v-model="form.warning_to_employee_id" data-selectField="warning_to_employee_id">
                            <option>----Select----</option>
                            <option v-for="(wtvalue,index) in employee_list" :value="wtvalue.id"> {{wtvalue.first_name}} {{wtvalue.last_name}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="warning_types">Warning Type </label>
                    <div class="col-lg-6">
                        <input type="text" id="warning_types" v-model="form.warning_types" class="form-control form-control-sm m-input" placeholder="Enter warning types">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('warning_types'))">{{ (errors.hasOwnProperty('warning_types')) ? errors.warning_types[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="subject">Subject </label>
                    <div class="col-lg-6">
                        <input type="text" id="subject" v-model="form.subject" class="form-control form-control-sm m-input" placeholder="Enter subject">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('subject'))">{{ (errors.hasOwnProperty('subject')) ? errors.subject[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="warning_by_employee_id">Warning By </label>
                    <div class="col-lg-6">
                        <select class="form-control select2" id="warning_by_employee_id"  v-model="form.warning_by_employee_id" data-selectField="warning_by_employee_id">
                            <option>----Select----</option>
                            <option v-for="(wbvalue,index) in employee_list" :value="wbvalue.id"> {{wbvalue.first_name}} {{wbvalue.last_name}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label" for="warning_date">Warning Date </label>
                    <div class="col-lg-6">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.warning_date" data-dateField="warning_date" readonly placeholder="Select date from picker" id="warning_date" />
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('po_receive_date'))">{{ (errors.hasOwnProperty('po_receive_date')) ? errors.po_receive_date[0] :'' }}</div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label for="narration" class="col-lg-3 col-form-label">Narration <span class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <textarea class="form-control form-control-sm" id="narration" v-model="form.narration" placeholder="" rows="2"></textarea>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('narration'))">{{ (errors.hasOwnProperty('narration')) ? errors.narration[0] :'' }}</div>
                    </div>
                </div>
                <div class="form-group m-form__group row" :class="{'has-danger': (errors.hasOwnProperty('status'))}">
                    <label class="col-form-label col-lg-4 col-sm-12">Status <span class="requiredField">*</span></label>
                    <div class="col-lg-8 col-md-8 col-sm-12">
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
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save and Go List</span></button>

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

        props:['employee_list','permissions'],

        data:function(){
            return{

                product_list:false,
                add_form:true,
                edit_form:false,

                form:{
                    warning_to_employee_id: '',
                    warning_types: '',
                    subject: '',
                    warning_by_employee_id: '',
                    warning_date: '',
                    narration: '',
                    status:'1'
                },
                errors: {},
            };
        },

        methods:{
            store:function(){
                var _this = this;
                axios.post(base_url+'warning', this.form).then( (response) => {
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
        },

        mounted(){
            var _this = this;

            var Select2= {
                init:function() {
                    $(".select2").select2( {
                            placeholder: "Please select an option"
                        }
                    )
                }
            };
            jQuery(document).ready(function() {
                    Select2.init()
                }
            );


            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if( selectField == 'warning_to_employee_id' ){
                    _this.form.warning_to_employee_id= selectedItem.val();
                }else if(selectField == 'warning_by_employee_id'){
                    _this.form.warning_by_employee_id= selectedItem.val();
                }
            });

            $(document).on("focus", ".datepicker", function () {

                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.warning_date = '';

                    }else {
                        _this.form.warning_date = moment(ev.date).format('DD-MM-YYYY');

                    }

                });
            });


        },

        created(){

        }

    }
</script>