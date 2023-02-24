<template>
    <div>
        <!--begin:: Add Modal-->
        <div class="modal fade" id="addModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add Cost Centre</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store"
                          class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">
                            <div class="form-group m-form__group row m--margin-top-20"
                                 :class="{'has-danger': (errors.hasOwnProperty('cost_center_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Cost Centre Name <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.cost_center_name"
                                           placeholder="Enter Cost Centre Name"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('cost_center_name'))">{{
                                        (errors.hasOwnProperty('cost_center_name')) ? errors.cost_center_name[0] :'' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row"
                                 :class="{'has-danger': (errors.hasOwnProperty('status'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Status <span
                                    class="requiredField">*</span></label>
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
                            <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i>
                                <span>Save</span></button>
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
                        <h5 class="modal-title">Edit Cost Centre</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)"
                          class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">
                            <div class="form-group m-form__group row m--margin-top-20"
                                 :class="{'has-danger': (errors.hasOwnProperty('cost_center_name'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Cost Centre Name <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-8 col-md-8 col-sm-12">
                                    <input type="text" class="form-control" v-model="form.cost_center_name"
                                           placeholder="Enter Cost Centre Name"/>
                                    <div class="requiredField" v-if="(errors.hasOwnProperty('cost_center_name'))">{{
                                        (errors.hasOwnProperty('cost_center_name')) ? errors.cost_center_name[0] :'' }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-group m-form__group row"
                                 :class="{'has-danger': (errors.hasOwnProperty('status'))}">
                                <label class="col-form-label col-lg-4 col-sm-12">Status <span
                                    class="requiredField">*</span></label>
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
                            <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i> <span>Update</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end::Modal-->
    </div>
</template>

<script>
    import {EventBus} from '../../../vue-assets';

    export default {

        data: function () {
            return {
                form: {
                    cost_center_name: '',
                    status: '1'
                },
                errors: {},
            };
        },

        methods: {
            store: function () {
                var _this = this;

                _this.$loading(true);
                axios.post(base_url + 'cost-centre/store', _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        $('#addModel').modal('hide');
                        _this.showMassage(response.data);
                        EventBus.$emit('new-data-created');
                    })
                    .catch(error => {
                        _this.$loading(false);
                        if (error.response.status == 422) {
                            _this.errors = error.response.data.errors;
                        } else {
                            _this.showMassage(error);
                        }
                    });
            },

            update: function (id) {
                var _this = this;

                _this.$loading(true);
                axios.put(base_url + 'cost-centre/' + id + '/update', _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        $('#editModel').modal('hide');
                        _this.showMassage(response.data);
                        EventBus.$emit('new-data-created');
                    })
                    .catch(error => {
                        _this.$loading(false);
                        if (error.response.status == 422) {
                            _this.errors = error.response.data.errors;
                        } else {
                            _this.showMassage(error);
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
                _this.form = {'cost_center_name': '', 'status': '1'};
            });

            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                axios.get(base_url + 'cost-centre/' + id + '/edit').then((response) => {
                    _this.form = response.data;
                    $('#editModel').modal("show");
                });
            });
        },

        created() {

        }
    }
</script>
