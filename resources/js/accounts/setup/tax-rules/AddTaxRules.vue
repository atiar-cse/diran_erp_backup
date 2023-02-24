<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">

                <div class="col-md-12 row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tax Name <span class="requiredField">*</span></label>
                            <input type="text" class="form-control form-control-sm m-input"
                                   v-model="form.sales_customer_name" placeholder="Enter Customer Name"/>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_name'))">{{
                                (errors.hasOwnProperty('sales_customer_name')) ? errors.sales_customer_name[0] :'' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tax Number <span class="requiredField">*</span></label>
                            <input type="text" class="form-control form-control-sm"
                                   v-model="form.sales_customer_contact_person_name"
                                   placeholder="Enter sales Contact Person name"/>
                            <div class="requiredField"
                                 v-if="(errors.hasOwnProperty('sales_customer_contact_person_name'))">{{
                                (errors.hasOwnProperty('sales_customer_contact_person_name')) ?
                                errors.sales_customer_contact_person_name[0] :'' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Rate <span class="requiredField">*</span></label>
                            <input type="text" class="form-control form-control-sm"
                                   v-model="form.sales_customer_contact_person_name"
                                   placeholder="Enter sales Contact Person name"/>
                            <div class="requiredField"
                                 v-if="(errors.hasOwnProperty('sales_customer_contact_person_name'))">{{
                                (errors.hasOwnProperty('sales_customer_contact_person_name')) ?
                                errors.sales_customer_contact_person_name[0] :'' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-2 col-form-label">Status ?</label>
                    <div class="col-lg-4">
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
                            <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i> <span>Save and Go List</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
</template>

<script>
    import {EventBus} from '../../../vue-assets';

    export default {

        props: ['brand_list', 'category_list', 'measure_unit_list', 'country_list'],

        data: function () {
            return {

                product_list: false,
                add_form: true,
                edit_form: false,

                form: {
                    product_name: '',
                    product_code: '',
                    category_id: '',
                    brand_id: '',
                    measure_unit_id: '',
                    country_id: '',
                    credit_limit: '',
                    buying_price: '',
                    selling_price: '',
                    complete_product_weight: '',
                    forming_weight: '',
                    shapping_weight: '',
                    dryer_weight: '',
                    glaze_weight: '',
                    kiln_weight: '',
                    product_description: '',
                    is_raw_material_status: '',
                    status: '1'
                },
                errors: {},
            };
        },

        methods: {

            store: function () {
                var _this = this;
                _this.$loading(true);
                axios.post(base_url + 'production-entry/store', _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        _this.showMassage(response.data);
                        EventBus.$emit('data-changed');
                    })
                    .catch(error => {
                        _this.$loading(false);
                        if (error.response.status == 422) {
                            this.errors = error.response.data.errors;
                        } else {
                            this.showMassage(error);
                        }
                    });
            },

            showMassage(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, 'Success Alert');
                } else if (data.status == 'error') {
                    toastr.error(data.message, 'Error Alert');
                } else {
                    toastr.error(data.message, 'Error Alert');
                }
            },
        },

        mounted() {
            var _this = this;
            $('#addModel,#editModel').on('hidden.bs.modal', function () {
                _this.errors = {};
                _this.form = {'measure_unit': '', 'status': '1'};
            });

            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                _this.$loading(true);
                axios.get(base_url + 'production-entry/' + id + '/edit')
                    .then((response) => {
                        _this.$loading(false);
                        _this.form = response.data;
                        $('#editModel').modal("show");
                    });
            });

            var Select2 = {
                init: function () {
                    $(".select2").select2({
                            placeholder: "Please select an option"
                        }
                    )
                }
            };
            jQuery(document).ready(function () {
                    Select2.init()
                }
            );

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if (selectField == 'measure_unit_id') {
                    _this.form.measure_unit_id = selectedItem.val();
                } else if (selectField == 'category_id') {
                    _this.form.category_id = selectedItem.val();
                } else if (selectField == 'brand_id') {
                    _this.form.brand_id = selectedItem.val();
                } else if (selectField == 'country_id') {
                    _this.form.country_id = selectedItem.val();
                }
            });
        },

        created() {

        }
    }
</script>
