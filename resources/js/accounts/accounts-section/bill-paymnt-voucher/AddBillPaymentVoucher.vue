<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right"  >
            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="po_order_no" class="col-lg-2 col-form-label">Bill No </label>
                    <div class="col-lg-4">
                        <input type="text"  id="po_order_no"  v-model="form.po_order_no" class="form-control form-control-sm m-input" placeholder="Enter Purchase Order No">
                        <div class="requiredField" v-if="(errors.hasOwnProperty('po_order_no'))">{{ (errors.hasOwnProperty('po_order_no')) ? errors.po_order_no[0] :'' }}</div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Bill Date </label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.po_receive_date" data-dateField="po_receive_date" readonly placeholder="Select date from picker" id="m_datepicker_2" />
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
                    <label for="po_supplier_id" class="col-lg-2 col-form-label">Bill Payment Type </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-supplier" id="po_supplier_ids" data-selectField="po_supplier_id" v-model="form.po_supplier_id" >
                            <option v-for="(svalue,sindex) in supplier_lists" :value="svalue.id" > {{svalue.purchase_supplier_name}}</option>
                            <div class="requiredField" v-if="errors['po_supplier_id']">{{ errors['po_supplier_id'][0] }}</div>
                        </select>
                    </div>
                    <label for="po_supplier_id" class="col-lg-2 col-form-label">Cost Center </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2  select-supplier" id="po_supplier_id" data-selectField="po_supplier_id" v-model="form.po_supplier_id" >
                            <option v-for="(svalue,sindex) in supplier_lists" :value="svalue.id" > {{svalue.purchase_supplier_name}}</option>
                            <div class="requiredField" v-if="errors['po_supplier_id']">{{ errors['po_supplier_id'][0] }}</div>
                        </select>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.po_narration"  id="narration" rows="2"></textarea>
                    </div>
                </div>
                <br><br>
                <!--begin::Portlet-->


                <div class="form-group m-form__group row">
                    <div class="m-section__content col-lg-6">
                        <div><strong>Expence Account List</strong></div>
                        <div class ="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse" >
                                <tr>
                                    <th></th>
                                    <th>Account List</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <td scope="row">
                                        <a href="javascript:void(0)"  @click="addNewItem" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <select class="form-control m-select2 select2  select-product" v-bind:data-index="index" v-model="details.pod_product_id"  >
                                            <option v-for="(value,index) in product_lists" :value="value.id" > {{value.product_name}}</option>
                                            <div class="requiredField" v-if="errors['details.'+index+'.pod_product_id']">{{ errors['details.'+index+'pod_product_id'][0] }}</div>
                                        </select>
                                    </td>

                                    <td>
                                         <div class="input-group date">
                                            <input type="text" class="form-control form-control-sm m-input " v-model="form.po_receive_date" />

                                        </div>

                                    </td>
                                    <td>
                                        <a  @click="removeItem(index)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">Total </td>
                                    <td  class="">
                                        <input type="text" v-model="form.po_total_price" class="form-control form-control-sm m-input">
                                    </td>
                                    <td></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <br><br>
                    </div>



                    <div class="m-section__content col-lg-6">
                        <div><strong>Payment Account List</strong></div>
                        <div class ="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse" >
                                <tr>
                                    <th></th>
                                    <th>Account List</th>
                                    <th>Amount</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <td scope="row">
                                        <a href="javascript:void(0)"  @click="addNewItem" class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <select class="form-control m-select2 select2  select-product" v-bind:data-index="index" v-model="details.pod_product_id"  >
                                            <option v-for="(value,index) in product_lists" :value="value.id" > {{value.product_name}}</option>
                                            <div class="requiredField" v-if="errors['details.'+index+'.pod_product_id']">{{ errors['details.'+index+'pod_product_id'][0] }}</div>
                                        </select>
                                    </td>

                                    <td>
                                        <div class="input-group date">
                                            <input type="text" class="form-control form-control-sm m-input" v-model="form.po_receive_date"   />

                                        </div>

                                    </td>
                                    <td>
                                        <a  @click="removeItem(index)" class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right">Total </td>
                                    <td  class="">
                                        <input type="text" v-model="form.po_total_price" class="form-control form-control-sm m-input">
                                    </td>
                                    <td></td>
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
                            <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save</span></button>
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
        props:['product_lists','po_order_generate','supplier_lists','requisition_lists'],

        data:function(){
            return{
                product_list:false,
                add_form:true,
                edit_form:false,

                form:{
                    po_order_no: this.po_order_generate,
                    po_receive_date: '',
                    po_narration: '',
                    po_supplier_id:'',
                    po_requisition_id:'',
                    po_order_docs:'',
                    po_total_qty: '',

                    po_total_price: '',
                    details: [
                        {
                            pod_product_id:'',
                            pod_remarks:'',
                            pod_unit:'',
                            pod_unit_price:0,
                            pod_qty:0,
                            pod_total_price:0,
                        }
                    ],
                },
                errors: {},
            };
        },

        methods:{
            productQtyCalculation(){
            },
            addNewItem(){
                this.form.details.push({
                    pod_product_id:'',
                    pod_remarks:'',
                    pod_unit:'',
                    pod_unit_price:0,
                    pod_qty:0,
                    pod_total_price:0,
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
                axios.post(base_url+'po-receive/store', this.form).then( (response) => {
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

            loadDetails(){

                var _this = this;
                var id= this.form.po_requisition_id;

                axios.get(base_url+"po-receive/"+id+"/po-requisition-list").then((response) => {

                    if(response.data.length > 0){
                        this.form.details = response.data;
                        console.log(response.data)

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
                    }
                });
            },

            totalQtyAndPrice(){
                var total_qty = 0;
                var total_amount = 0;
                var total_price = 0;
                var details = this.form.details;
                var length = details.length;

                for(let i = 0; i < length; i++) {
                    total_price = Number(details[i].pod_qty) * Number(details[i].pod_unit_price);
                    this.form.details[i].pod_total_price = total_price;
                    total_qty = Number(details[i].pod_qty) + total_qty;
                    total_amount = total_price + total_amount;
                }
                this.form.po_total_qty = total_qty;
                this.form.po_total_price = total_amount;
            },
            duplicateCheck(){

                var no_index = this.form.details.length;
                //alert(no_index);
                for(let i=0; i<no_index; i++){
                    for(let j=i; j<no_index ; j++){

                        if(this.form.details[i].pod_product_id == this.form.details[j].pod_product_id){
                            alert("Duplicate Found");
                            console.log("Duplicate Found");
                        }

                    }
                }

            },

            onFileChange(e) {
                // alert(e.target.files || e.dataTransfer.files);
                let files = e.target.files || e.dataTransfer.files;
                if (!files.length)
                    return;
                this.createImage(files[0]);
            },
            createImage(file) {
                let reader = new FileReader();
                var _this = this;

                reader.onload = (e) => {
                    this.form.po_order_docs =e.target.result;
                };
                reader.readAsDataURL(file);
            },

            processFile() {
                /*alert(this.$refs.file.files[0]);
                 this.file = this.$refs.file.files[0];
                 let reader  = new FileReader();

                 reader.addEventListener("load", function () {

                 this.form.po_order_docs = reader.result;
                 }.bind(this), false);

                 reader.onload = (e) => {
                 this.form.po_order_docs =reader.result;
                 };
                 reader.readAsDataURL(this.file);

                 let formData = new FormData();


                 formData.append('this.form.po_order_docs', this.file);*/



                /* var files = this.$refs.file.files[0];
                 var data = new FormData();
                 data.append('this.form.po_order_docs', files[0]);

                 const files = this.$refs.file.files;
                 alert(files[0]);
                 const data = new FormData();
                 data.append('this.form.po_order_docs', files[0]);*/


                this.file = _this.$refs.file.files[0];
                let form = new FormData();

                form.append('po_order_docs', this.file);
                form.append('_method', 'put');
                //alert();


            },

            handleFileUpload(){

                this.file = this.$refs.file.files[0];
                let reader  = new FileReader();

                reader.addEventListener("load", function () {
                    this.showPreview = true;
                    this.imagePreview = reader.result;
                }.bind(this), false);

                if( this.file ){
                    if ( /\.(jpe?g|png|gif)$/i.test( this.file.name ) ) {
                        reader.readAsDataURL( this.file );
                    }
                }
            }

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
            $('#addComponent').on('change', '.select-product', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');

                _this.form.details[dataIndex].pod_product_id = selectedItem.val();
                //_this.duplicateCheck();
            });

            $('#addComponent').on('change', '.select2,.po_requisition_id', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'po_supplier_id'){
                    _this.form.po_supplier_id = selectedType.val();
                }else if(dataIndex == 'po_requisition_id'){
                    _this.form.po_requisition_id = selectedType.val();
                    _this.loadDetails();
                }
            });

            /*$('#addComponent').on('change', '.select-requisition', function (e) {
             var selectedType = $(this),
             curerntVal = !!selectedType.val(),
             dataIndex = $(e.currentTarget).attr('data-selectId');
             if(dataIndex == 'rindex'){
             _this.form.po_requisition_id = selectedType.val();
             }

             });*/

            /*var BootstrapDatepicker=function() {
             var t;
             t=mUtil.isRTL()? {
             leftArrow: '<i class="la la-angle-right"></i>', rightArrow: '<i class="la la-angle-left"></i>'
             }
             : {
             leftArrow: '<i class="la la-angle-left"></i>', rightArrow: '<i class="la la-angle-right"></i>'
             }
             ;
             return {
             init:function() {
             $("#m_datepicker_2, .datepicker").datepicker( {

             onSelect: function(date,e) {
             var dateField = $(this).attr('data-dateField');
             if(dateField == 'po_receive_date'){
             _this.form.po_receive_date = date;
             }
             },
             todayHighlight: !0,
             orientation: "bottom left",
             templates: t,
             autoclose: true,
             format: 'dd/mm/yyyy'
             });
             }
             }
             }
             ();
             jQuery(document).ready(function() {
             BootstrapDatepicker.init()
             }
             );*/

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

        created() {
            var _this = this;
            _this.totalQtyAndPrice();
        }

    }
</script>
