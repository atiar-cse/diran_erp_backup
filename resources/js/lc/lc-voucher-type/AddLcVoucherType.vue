<template>
    <div>
        <!--begin:: Add Modal-->
        <div aria-hidden="true" aria-labelledby="" class="modal fade" id="addModel" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add LC Voucher Type</h5>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data"
                          method="POST"
                          v-on:submit.prevent="store">
                        <div class="modal-body">
                            <div :class="{'has-danger': (errors.hasOwnProperty('voucher_type_name'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Voucher Type Name <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter Voucher Type Name" type="text"
                                           v-model="form.voucher_type_name"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('voucher_type_name'))">{{
                                        (errors.hasOwnProperty('voucher_type_name')) ? errors.voucher_type_name[0] :''
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div :class="{'has-danger': (errors.hasOwnProperty('menu_link_db_tbl'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Menu Link DB Table Name<span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter Menu Link Table Name" type="text"
                                           v-model="form.menu_link_db_tbl"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('menu_link_db_tbl'))">{{
                                        (errors.hasOwnProperty('menu_link_db_tbl')) ? errors.menu_link_db_tbl[0] :''
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div :class="{'has-danger': (errors.hasOwnProperty('transaction_column'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">SL / TransactionNo Column Name<span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Table" type="text"
                                           v-model="form.transaction_column"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('transaction_column'))">{{
                                        (errors.hasOwnProperty('transaction_column')) ? errors.transaction_column[0] :''
                                        }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('narration'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Narration </label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter Narration" type="text"
                                           v-model="form.narration"/>
                                </div>
                            </div>
                            <div :class="{'has-danger': (errors.hasOwnProperty('status'))}"
                                 class="form-group m-form__group row">
                                <label class="col-form-label col-lg-4 col-sm-12">Status <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <span class="m-switch m-switch--icon">
                                        <label>
                                            <input checked="checked" type="checkbox" v-model="form.status">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm btn-secondary" data-dismiss="modal" type="reset">Close</button>
                            <button class="btn btn-sm btn-success" type="submit"><i class="la la-check"></i>
                                <span>Save</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end::Add Modal-->

        <!--begin:: Edit Modal-->
        <div aria-hidden="true" aria-labelledby="" class="modal fade" id="editModel" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit LC Voucher Type</h5>
                        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form class="m-form m-form--fit m-form--label-align-right" enctype="multipart/form-data"
                          method="POST"
                          v-on:submit.prevent="update(form.id)">
                        <div class="modal-body">
                            <div :class="{'has-danger': (errors.hasOwnProperty('voucher_type_name'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Voucher Type Name <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter Voucher Type Name" type="text"
                                           v-model="form.voucher_type_name"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('voucher_type_name'))">{{
                                        (errors.hasOwnProperty('voucher_type_name')) ? errors.voucher_type_name[0] :''
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div :class="{'has-danger': (errors.hasOwnProperty('menu_link_db_tbl'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Menu Link DB Table Name <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter Menu Link Table Name" type="text"
                                           v-model="form.menu_link_db_tbl"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('menu_link_db_tbl'))">{{
                                        (errors.hasOwnProperty('menu_link_db_tbl')) ? errors.menu_link_db_tbl[0] :''
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div :class="{'has-danger': (errors.hasOwnProperty('transaction_column'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">SL / TransactionNo Column Name<span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="" type="text"
                                           v-model="form.transaction_column"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('transaction_column'))">{{
                                        (errors.hasOwnProperty('transaction_column')) ? errors.transaction_column[0] :''
                                        }}
                                    </div>
                                </div>
                            </div>

                            <div :class="{'has-danger': (errors.hasOwnProperty('narration'))}"
                                 class="form-group m-form__group row m--margin-top-20">
                                <label class="col-form-label col-lg-4 col-sm-12">Narration </label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input class="form-control" placeholder="Enter Narration" type="text"
                                           v-model="form.narration"/>
                                </div>
                            </div>
                            <div :class="{'has-danger': (errors.hasOwnProperty('status'))}"
                                 class="form-group m-form__group row">
                                <label class="col-form-label col-lg-4 col-sm-12">Status <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <span class="m-switch m-switch--icon">
                                        <label>
                                            <input checked="checked" type="checkbox" v-model="form.status">
                                            <span></span>
                                        </label>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-sm btn-secondary" data-dismiss="modal" type="button">Close</button>
                            <button class="btn btn-sm btn-success" type="submit"><i class="la la-check"></i> <span>Update</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end::Edit Modal-->
    </div>
</template>

<script>
    import {EventBus} from '../../vue-assets';

    export default {

        data: function () {
            return {
                form: {
                    voucher_type_name: '',
                    menu_link_db_tbl: '',
                    transaction_column: '',
                    narration: '',
                    status: '1'
                },
                errors: {},
            };
        },

        methods: {
            store: function () {
                axios.post(base_url + 'lc-voucher-type', this.form).then((response) => {
                    $('#addModel').modal('hide');
                    this.showMassage(response.data);
                    EventBus.$emit('new-data-created');
                })
                    .catch(error => {
                        if (error.response.status == 422) {
                            this.errors = error.response.data.errors;
                        } else {
                            console.log(456);
                            this.showMassage(error);
                        }
                    });
            },

            update: function (id) {
                axios.put(base_url + 'lc-voucher-type/' + id + '/', this.form)
                    .then((response) => {
                        $('#editModel').modal('hide');
                        this.showMassage(response.data);
                        EventBus.$emit('new-data-created');
                    }).catch(error => {
                    if (error.response.status == 422) {
                        this.errors = error.response.data.errors;
                    } else {
                        this.showMassage(error);
                    }
                });
            },

            showMassage(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, 'Success');
                } else if (data.status == 'error') {
                    toastr.error(data.message, 'Error');
                } else {
                    toastr.error(data.message, 'Error');
                }
            },
        },

        mounted() {
            var _this = this;
            $('#addModel,#editModel').on('hidden.bs.modal', function () {
                _this.errors = {};
                _this.form = {
                        'voucher_type_name': '', 'menu_link_db_tbl': '', 'transaction_column': '', 'narration': '', 'status': '1'
                    };
            });
            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                axios.get(base_url + 'lc-voucher-type/' + id + '/edit').then((response) => {
                    _this.form = response.data;
                    $('#editModel').modal("show");
                });
            });
        },

        created() {

        }
    }
</script>
