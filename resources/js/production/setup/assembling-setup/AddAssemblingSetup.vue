<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >

            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="product" class="col-lg-2 col-form-label">Product <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <select class="form-control select2" id="product"  v-model="form.finishing_product_id" data-selectField="finishing_product_id">
                            <option v-for="(value,index) in finishing_product_list" :value="value.id">{{value.product_name}} ({{value.category ? value.category.product_category_code : ''}})</option>
                        </select>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('finishing_product_id'))">{{ (errors.hasOwnProperty('finishing_product_id')) ? errors.finishing_product_id[0] :'' }}</div>
                    </div>
                    <label for="qty" class="col-lg-2 col-form-label">Qty <span class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="number" id="qty" v-model="form.finishing_product_qty" class="form-control form-control-sm m-input" readonly />
                            <div class="requiredField" v-if="(errors.hasOwnProperty('finishing_product_qty'))">{{ (errors.hasOwnProperty('finishing_product_qty')) ? errors.finishing_product_qty[0] :'' }}</div>
                        </div>
                    </div>
                </div>
                <br><br>
                <!--begin::Portlet-->

                <div class="form-group m-form__group row">
                    <div class="m-section__content col-lg-12">
                        <div class ="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse" >
                                <tr>
                                    <th></th>
                                    <th>Fitting Product List <span class="requiredField">*</span></th>
                                    <th>Qty <span class="requiredField">*</span></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <th scope="row">
                                        <a href="javascript:void(0)"  @click="addNewItem" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </th>
                                    <td class="tdWidth">
                                        <select class="form-control select2 select--fitting-product" v-bind:data-index="index" v-model="details.fitting_product_id" style="width:100%">
                                            <option v-for="(value,index) in fitting_product_list" :value="value.id"> {{value.product_name}} ({{value.category ? value.category.product_category_code : ''}})</option>
                                        </select>
                                        <div class="requiredField" v-if="errors['details.'+index+'.fitting_product_id']">{{ errors['details.'+index+'.fitting_product_id'][0] }}</div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm m-input" v-model="details.fitting_product_qty" placeholder="Enter qty">
                                        <div class="requiredField" v-if="errors['details.'+index+'.fitting_product_qty']">{{ errors['details.'+index+'.fitting_product_qty'][0] }}</div>
                                    </td>
                                    <td scope="row" class="width-100 text-center">
                                        <a  @click="removeItem(index)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <br><br>
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

        props:['permissions','finishing_product_list','fitting_product_list'],

        data:function(){
            return{
                data_list:false,
                add_form:true,
                edit_form:false,

                form:{

                    finishing_product_id: '',
                    finishing_product_qty: 1,

                    details: [
                        {
                            fitting_product_id: '',
                            fitting_product_qty: '',
                        }
                    ],
                },
                errors: {},
            };
        },


        methods:{

            addNewItem(){
                this.form.details.push({
                    fitting_product_id: '',
                    fitting_product_qty: ''
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

            removeItem(index){
                var _this = this;
                if(_this.form.details.length > 1){
                    _this.form.details.splice(index, 1);
                }
            },

            store:function(){
                var _this = this;
                axios.post(base_url+'assembling-setup/store', _this.form).then( (response) => {
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

            duplicateCheck(){
                var no_index = this.form.details.length;
                var details = this.form.details;
                if (no_index > 1) {
                    for (let i = 0; i < no_index; i++) {
                        for (let j = i+1; j < no_index; j++) {
                            if(details[i].fitting_product_id == details[j].fitting_product_id) {
                                alert("Duplicate Found");
                                details[j].fitting_product_id = '';
                                var Select2 = {
                                    init: function () {
                                        $(".select2").select2({
                                            placeholder: "Please select an option"
                                        })
                                    }
                                };
                                jQuery(document).ready(function () {
                                    Select2.init()
                                });
                            }
                        }
                    }
                }
            },
        },

        mounted(){
            var _this = this;

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if(selectField == 'finishing_product_id' ){
                    _this.form.finishing_product_id= selectedItem.val();
                }
            });

            $('#addComponent').on('change', '.select--fitting-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');
                _this.form.details[dataIndex].fitting_product_id = selectedItem.val();

                _this.duplicateCheck();
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
