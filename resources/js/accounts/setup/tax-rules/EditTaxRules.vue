<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="col-md-12 row" >
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tax Name <span class="requiredField">*</span></label>
                            <input type="text" class="form-control form-control-sm m-input" v-model="form.sales_customer_name" placeholder="Enter Customer Name" />
                            <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_name'))">{{ (errors.hasOwnProperty('sales_customer_name')) ? errors.sales_customer_name[0] :'' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tax Number <span class="requiredField">*</span></label>
                            <input type="text" class="form-control form-control-sm" v-model="form.sales_customer_contact_person_name" placeholder="Enter sales Contact Person name" />
                            <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_contact_person_name'))">{{ (errors.hasOwnProperty('sales_customer_contact_person_name')) ? errors.sales_customer_contact_person_name[0] :'' }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Rate <span class="requiredField">*</span></label>
                            <input type="text" class="form-control form-control-sm" v-model="form.sales_customer_contact_person_name" placeholder="Enter sales Contact Person name" />
                            <div class="requiredField" v-if="(errors.hasOwnProperty('sales_customer_contact_person_name'))">{{ (errors.hasOwnProperty('sales_customer_contact_person_name')) ? errors.sales_customer_contact_person_name[0] :'' }}</div>
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-2 col-form-label"> Status ?</label>
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

        props:['brand_list','category_list','measure_unit_list','country_list','editId'],

        data:function(){
            return{
                product_list:false,
                add_form:true,
                edit_form:true,

                form:{
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
                    status:'1'
                },
                errors: {},
            };
        },

        methods:{
            edit(id) {
                var _this = this;
                axios.get(base_url+'production-entry/'+id+'/edit')
                    .then( (response) => {
                        _this.form  = response.data;

                        setTimeout(function () {
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
                        },100);
                    });
            },

            update(id){
                axios.put(base_url+'production-entry/'+id+'/update', this.form).then( (response) => {
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

        },

        created(){
            var _this = this;
            _this.edit(_this.editId);
        }

    }
</script>