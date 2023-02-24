<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="product_name" class="col-lg-2 col-form-label">Product Name <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="product_name" v-model="form.product_name" class="form-control form-control-sm m-input" placeholder="Enter product name">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('product_name'))">{{ (errors.hasOwnProperty('product_name')) ? errors.product_name[0] :'' }}</div>
                    </div>
                    <label for="product_code" class="col-lg-2 col-form-label">Product Code </label>
                    <div class="col-lg-4">
                        <input type="text" id="product_code" v-model="form.product_code" class="form-control form-control-sm m-input" placeholder="Enter product code">
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="category">Category <span class="requiredField">*</span> </label>
                    <div class="col-lg-4">
                        <select class="form-control select2" id="category" data-selectField="category_id" v-model="form.category_id">
                            <option>----Select Category----</option>
                            <option v-for="(value,index) in category_list" :value="value.id"> {{value.product_category_name}}</option>
                        </select>
                    </div>
                    <label class="col-lg-2 col-form-label" for="brand">Brand </label>
                    <div class="col-lg-4">
                        <select class="form-control select2" id="brand" data-selectField="brand_id" v-model="form.brand_id">
                            <option >----Select Brand----</option>
                            <option v-for="(value,index) in brand_list" :value="value.id"> {{value.product_brand_name}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="measure_unit">Measure Unit <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2"  id="measure_unit" data-selectField="measure_unit_id" v-model="form.measure_unit_id">
                            <option >----Select Measure----</option>
                            <option v-for="(value,index) in measure_unit_list" :value="value.id"> {{value.measure_unit}}</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('product_name'))">{{ (errors.hasOwnProperty('product_name')) ? errors.product_name[0] :'' }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="country_of_origin">Country of Origin </label>
                    <div class="col-lg-4">
                        <select class="form-control select2" data-selectField="country_id" id="country_of_origin" v-model="form.country_id">
                            <option>----Select Country----</option>
                            <option v-for="(value,index) in country_list" :value="value.id"> {{value.country_name}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="credit_limit">Credit Limit </label>
                    <div class="col-lg-4">
                        <input type="number" step="any" id="credit_limit" v-model="form.credit_limit" class="form-control form-control-sm m-input" placeholder="Enter credit limit">
                    </div>
                    <label class="col-lg-2 col-form-label" for="weight">Complete Weight </label>
                    <div class="col-lg-4">
                        <input type="number" step="any" id="weight" v-model="form.complete_product_weight" class="form-control form-control-sm m-input" placeholder="Enter weight">
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="buying_price">Buying Price </label>
                    <div class="col-lg-4">
                        <input type="number" step="any" id="buying_price" v-model="form.buying_price" class="form-control form-control-sm m-input" placeholder="Enter buying price">
                    </div>
                    <label class="col-lg-2 col-form-label" for="selling_price">Selling Price </label>
                    <div class="col-lg-4">
                        <input type="number" step="any" id="selling_price" v-model="form.selling_price" class="form-control form-control-sm m-input" placeholder="Enter selling price">
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="forming_weight">Forming Weight </label>
                    <div class="col-lg-4">
                        <input type="number" step="any" id="forming_weight" v-model="form.forming_weight" class="form-control form-control-sm m-input" placeholder="Enter forming weight">
                    </div>
                    <label class="col-lg-2 col-form-label" for="shapping_weight">Shapping Weight </label>
                    <div class="col-lg-4">
                        <input type="number" step="any" id="shapping_weight" v-model="form.shapping_weight" class="form-control form-control-sm m-input" placeholder="Enter shapping weight">
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="dryer_weight">Dryer Weight </label>
                    <div class="col-lg-4">
                        <input type="number" step="any" id="dryer_weight" v-model="form.dryer_weight" class="form-control form-control-sm m-input" placeholder="Enter dryer weight">
                    </div>
                    <label class="col-lg-2 col-form-label" for="glaze_weight">Glaze Weight </label>
                    <div class="col-lg-4">
                        <input type="number" step="any" id="glaze_weight" v-model="form.glaze_weight" class="form-control form-control-sm m-input" placeholder="Enter glaze weight">
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="firirng_weight">Kiln Weight </label>
                    <div class="col-lg-4">
                        <input type="number" step="any" id="firirng_weight" v-model="form.kiln_weight" class="form-control form-control-sm m-input" placeholder="Enter kiln weight">
                    </div>
                    <label class="col-lg-2 col-form-label" for="description">Description </label>
                    <div class="col-lg-4">
                        <textarea class="form-control form-control-sm" id="description" v-model="form.product_description" placeholder="" rows="2"></textarea>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label"></label>
                    <div class="col-lg-4">
                        <div class="m-checkbox-list">
                            <label class="m-checkbox">
                                <input type="checkbox" v-model="form.is_raw_material_status"> is row material ?
                                <span></span>
                            </label>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('is_raw_material_status'))">{{ (errors.hasOwnProperty('is_raw_material_status')) ? errors.is_raw_material_status[0] :'' }}</div>
                    </div>
                    <label class="col-2 col-form-label">Active Status ?</label>
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
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Update and Go List</span></button>
                            <!--<div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success">Save</button>
                                <button type="button" class="btn btn-sm btn-success dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(119px, 31px, 0px);">
                                    <a class="dropdown-item" href="#">Save</a>
                                    <a class="dropdown-item" href="#">Save and Add</a>
                                    <a class="dropdown-item" href="#">Save and Go List</a>
                                </div>
                            </div>-->
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
                        _this.initSelect2();
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

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if( selectField == 'measure_unit_id' ){
                    _this.form.measure_unit_id= selectedItem.val();
                }else if(selectField == 'category_id'){
                    _this.form.category_id= selectedItem.val();
                }else if(selectField == 'brand_id'){
                    _this.form.brand_id= selectedItem.val();
                }else if(selectField == 'country_id'){
                    _this.form.country_id= selectedItem.val();
                }
            });

        },

        created(){
            var _this = this;
            _this.edit(_this.editId);
        }

    }
</script>
