<template>
    <div>
        <!--begin:: Add Modal-->
        <div class="modal fade" id="addModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" >Add Public Holiday</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="store" class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">

                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('product_category_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Holiday Name <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" v-model="form.holiday_name" data-selectField="holiday_name" style="width:100% " />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('holiday_name'))">{{ (errors.hasOwnProperty('holiday_name')) ? errors.holiday_name[0] :'' }}</div>
                                </div>
                            </div>

                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('from_date'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">From Date<span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <div class="input-group date">
                                        <input type="text" id="from_date" class="form-control form-control-sm m-input datepicker" v-model="form.from_date" data-dateField="from_date" autocomplete="off" placeholder="Select date from picker"  />
                                        <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar-check-o"></i>
                                        </span>
                                        </div>
                                    </div>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('from_date'))">{{ (errors.hasOwnProperty('from_date')) ? errors.from_date[0] :'' }}</div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('to_date'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">To Date<span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <div class="input-group date">
                                        <input type="text" id="to_date" class="form-control form-control-sm m-input datepickers" v-model="form.to_date" data-dateField="to_date" autocomplete="off" placeholder="Select date from picker"  />
                                        <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar-check-o"></i>
                                        </span>
                                        </div>
                                    </div>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('to_date'))">{{ (errors.hasOwnProperty('to_date')) ? errors.to_date[0] :'' }}</div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('narration'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Narration<span class="requiredField"></span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
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
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end::Modal-->

        <!--begin:: Add Modal-->
        <div class="modal fade" id="editModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Public Holiday</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">
                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('holiday_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12" for="holiday_names">Holiday Name <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" id="holiday_names" v-model="form.holiday_name" data-selectField="holiday_name" style="width:100% " />
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('holiday_name'))">{{ (errors.hasOwnProperty('holiday_name')) ? errors.holiday_name[0] :'' }}</div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('from_date'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">From Date  <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                        <div class="input-group date">
                                            <input type="text" id="from_dates" class="form-control form-control-sm m-input datepicker" v-model="form.from_date" data-dateField="from_date"  autocomplete="off" placeholder="Select date from picker"  />
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="la la-calendar-check-o"></i>
                                                </span>
                                            </div>
                                        </div>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('from_date'))">{{ (errors.hasOwnProperty('from_date')) ? errors.from_date[0] :'' }}</div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('to_date'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">To Date  <span class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <div class="input-group date">
                                        <input type="text" id="to_dates" class="form-control form-control-sm m-input datepickers" v-model="form.to_date" data-dateField="to_date" autocomplete="off" placeholder="Select date from picker"  />
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('to_date'))">{{ (errors.hasOwnProperty('to_date')) ? errors.to_date[0] :'' }}</div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row m--margin-top-20" :class="{'has-danger': (errors.hasOwnProperty('narration'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Narration  <span class="requiredField"></span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <textarea class="form-control form-control-sm" id="na" v-model="form.narration" placeholder="" rows="2"></textarea>
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

       props:['holiday_list'],

        data:function(){
            return{
                form:{
                    holiday_name: '',
                    from_date: '',
                    to_date: '',
                    narration: '',
                    status:'1'
                },
                errors: {},
            };
        },

        methods:{
            store:function(){
                axios.post(base_url+'public-holiday', this.form).then( (response) => {
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
                axios.put(base_url+'public-holiday/'+id, this.form).then( (response) => {
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
        },

        mounted(){
            var _this = this;
            $('#addModel,#editModel').on('hidden.bs.modal', function(){
                _this.errors = {};
                _this.form = {
                    holiday_name: '',
                    from_date: '',
                    to_date: '',
                    narration: '',
                    status:'1'};
            });

            $('#addModel,#editModel').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'holiday_name'){
                    _this.form.holiday_name = selectedType.val();
                }
            });

            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                axios.get(base_url+'public-holiday/'+id+'/edit').then((response) => {
                    _this.form = response.data;
                    $('#editModel').modal("show");
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
                });
            });

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
                        _this.form.from_date = '';

                    }else {
                        _this.form.from_date = moment(ev.date).format('DD/MM/YYYY');

                    }

                });
            });

            $(document).on("focus", ".datepickers", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                    //startDate: new Date(),
                    //startDate: '-2d',
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.to_date = '';

                    }else {
                        _this.form.to_date = moment(ev.date).format('DD/MM/YYYY');

                    }

                });
            });

        },

        created(){

        }

    }
</script>
