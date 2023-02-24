<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >
            <div class="m-portlet__body">
                <div class="row">
                    <div class="col-lg-12" >
                        <div class="row justify-content-md-center">
                            <div class="col-lg-3">
                                <label for="" class="col-form-label">Date</label>
                                <div class="input-group date">
                                    <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.po_receive_date" data-dateField="po_receive_date" readonly placeholder="Select date from picker" id="m_datepicker_2" />
                                    <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <label for="" class="col-form-label">Chart Of Account</label>
                                <input type="text" class="form-control form-control-sm m-input" v-model="form.po_receive_date"/>
                            </div>
                            <div class="col-lg-3">
                                <label for="" class="col-form-label">Balance</label>
                                <input type="text" class="form-control form-control-sm m-input" v-model="form.po_receive_date"/>
                            </div>
                          </div>
                         <br>
                        <br>
                        <br>
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
            </div>
        </form>
        <!--end::Form-->
    </div>
</template>

<script>
    import { EventBus } from '../../../vue-assets';

    export default {

        props:['brand_list','category_list','measure_unit_list','country_list'],

        data:function(){
            return{

                product_list:false,
                add_form:true,
                edit_form:false,

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
            store:function(){
                var _this = this;
                axios.post(base_url+'production-entry/store', this.form).then( (response) => {
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
            $('form').on('focus', 'input[type=number]', function (e) {
                $(this).on('mousewheel.disableScroll', function (e) {
                    e.preventDefault()
                })
            });
            $('form').on('blur', 'input[type=number]', function (e) {
                $(this).off('mousewheel.disableScroll')
            });

            var _this = this;
            $('#addModel,#editModel').on('hidden.bs.modal', function(){
                _this.errors = {};
                _this.form = {'measure_unit':'','status':'1'};
            });

            EventBus.$on('update-button-clicked', (id) => {
                _this.errors = {};
                axios.get(base_url+'production-entry/'+id+'/edit').then((response) => {
                    _this.form = response.data;
                    $('#editModel').modal("show");
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


            $('#addComponent').on('change', '.select2', function (e) {
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

            $(document).on("focus", ".datepicker", function () {

                $(this).datepicker({
                    format: 'dd-mm-yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.po_receive_date = '';

                    }else {
                        _this.form.po_receive_date = moment(ev.date).format('DD-MM-YYYY');

                    }

                });
            });


        },

        created(){

        }

    }
</script>
