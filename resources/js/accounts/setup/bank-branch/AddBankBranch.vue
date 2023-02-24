<template>
    <div>
        <!--begin:: Add Modal-->
        <div class="modal fade" id="addModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add Bank Branch</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="store" class="m-form m-form--fit m-form--label-align-right">

                        <div class="modal-body">
                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Branch Name <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm m-input" v-model="form.bank_branch_names" placeholder="Enter Branch Name" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('bank_branch_names'))">{{ (errors.hasOwnProperty('bank_branch_names')) ? errors.bank_branch_names[0] :'' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Branch Code <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.bank_branch_codes" placeholder="Enter Branch Code" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('bank_branch_codes'))">{{ (errors.hasOwnProperty('bank_branch_codes')) ? errors.bank_branch_codes[0] :'' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <label for="bank_id2">Bank <span class="requiredField">*</span></label>
                                    <div class="form-group">
                                        <select class="form-control  m-select2 select2" data-selectField="bank_id" id="bank_id2"  v-model="form.bank_id"  style="width: 100%" >
                                            <option v-for="(bvalue,bindex) in bank_lists" :value="bvalue.id" > {{bvalue.accounts_bank_names}}</option>
                                        </select>
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('bank_id'))">{{ (errors.hasOwnProperty('bank_id')) ? errors.bank_id[0] :'' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact No <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.bank_branch_contact_no" placeholder="Enter sales Contact No" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('bank_branch_contact_no'))">{{ (errors.hasOwnProperty('bank_branch_contact_no')) ? errors.bank_branch_contact_no[0] :'' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address <span class="requiredField">*</span></label>
                                        <textarea class="form-control form-control-sm"  v-model="form.bank_branch_address" placeholder="" rows="2"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6 row" :class="{'has-danger': (errors.hasOwnProperty('status'))}" >
                                    <label class="col-form-label col-lg-4 col-sm-12">Status <span class="requiredField">*</span></label>
                                    <div class="col-md-6">
                                    <span class="m-switch m-switch--icon  pull-left">
                                    <label>
                                        <input type="checkbox" checked="checked" v-model="form.status">
                                        <span></span>
                                    </label>
                                   </span>
                                    </div>
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
                        <h5 class="modal-title">Edit Bank Branch</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST"  enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">
                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Branch Name <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm m-input" v-model="form.bank_branch_names" placeholder="Enter Branch Name" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('bank_branch_names'))">{{ (errors.hasOwnProperty('bank_branch_names')) ? errors.bank_branch_names[0] :'' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Branch Code <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.bank_branch_codes" placeholder="Enter Branch Code" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('bank_branch_codes'))">{{ (errors.hasOwnProperty('bank_branch_codes')) ? errors.bank_branch_codes[0] :'' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <label for="bank_id2">Bank <span class="requiredField">*</span></label>
                                    <div class="form-group">
                                        <select class="form-control m-select2 select2" data-selectField="bank_id" id="bank_id" v-model="form.bank_id"  style="width: 100%" >
                                            <option v-for="(bvalue,bindex) in bank_lists" :value="bvalue.id" > {{bvalue.accounts_bank_names}}</option>
                                        </select>
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('bank_id'))">{{ (errors.hasOwnProperty('bank_id')) ? errors.bank_id[0] :'' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Contact No <span class="requiredField">*</span></label>
                                        <input type="text" class="form-control form-control-sm" v-model="form.bank_branch_contact_no" placeholder="Enter sales Contact No" />
                                        <div class="requiredField" v-if="(errors.hasOwnProperty('bank_branch_contact_no'))">{{ (errors.hasOwnProperty('bank_branch_contact_no')) ? errors.bank_branch_contact_no[0] :'' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 row" >
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Address <span class="requiredField">*</span></label>
                                        <textarea class="form-control form-control-sm" id="bank_branch_address" v-model="form.bank_branch_address" placeholder="" rows="2"></textarea>
                                    </div>
                                </div>

                                <div class="col-md-6 row" :class="{'has-danger': (errors.hasOwnProperty('status'))}" >
                                    <label class="col-form-label col-lg-4 col-sm-12">Status <span class="requiredField">*</span></label>
                                    <div class="col-md-6">
                                    <span class="m-switch m-switch--icon  pull-left">
                                    <label>
                                        <input type="checkbox" checked="checked" v-model="form.status">
                                        <span></span>
                                    </label>
                                   </span>
                                    </div>
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
    </div>
</template>

<script>
    import { EventBus } from '../../../vue-assets';

    export default {
        props:['bank_lists'],

        data:function(){
            return{
                form:{
                    bank_branch_names:'',
                    bank_branch_codes: '',
                    bank_id: '',
                    bank_branch_contact_no: '',
                    bank_branch_address:'',
                    status:'1'
                },
                errors: {},
            };
        },

        methods:{
            store:function(){
                axios.post(base_url+'bank-branch/store', this.form).then( (response) => {
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
                axios.put(base_url+'bank-branch/'+id+'/update', this.form).then( (response) => {
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
                    'bank_branch_names':'',
                    'bank_branch_codes': '',
                    'bank_id': '',
                    'bank_branch_contact_no': '',
                    'bank_branch_address':'',
                    'status':'1' };
            });



            $('#addModel,#editModel').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'bank_id'){
                    _this.form.bank_id = selectedType.val();
                }
            });
            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                axios.get(base_url+'bank-branch/'+id+'/edit').then((response) => {
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
        },

        created(){

        }

    }
</script>
