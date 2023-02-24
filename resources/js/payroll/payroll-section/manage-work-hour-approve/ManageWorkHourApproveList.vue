<template>
    <div>
        <div class="m-portlet__body" v-if="product_list">
            <br><br>
            <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >
                <div class="m-portlet__body">
                    <div class="form-group m-form__group row">
                        <label for="inventory_current_stocks_warehouse_id" class="col-lg-2 col-form-label">Department List</label>
                        <div class="col-lg-3">
                            <select class="form-control m-select2 select2" data-selectField="inventory_current_stocks_warehouse_id" id="inventory_current_stocks_warehouse_id" v-model="form.inventory_current_stocks_warehouse_id"  @change="loadproduct()">
                                <option v-for="(wvalue,windex) in warehouse_lists" :value="wvalue.id" > {{wvalue.purchase_ware_houses_name}}</option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('inventory_current_stocks_warehouse_id'))">{{ (errors.hasOwnProperty('inventory_current_stocks_warehouse_id')) ? errors.inventory_current_stocks_warehouse_id[0] :'' }}</div>
                        </div>
                        <label for="inventory_current_stocks_warehouse_id" class="col-lg-2 col-form-label">Date</label>
                        <div class="col-lg-3">
                            <input type="text" id="date" class="form-control form-control-sm m-input" v-model="form.sales_customer_join_date"  placeholder="Select date from picker" autocomplete="off" />

                        </div>
                        <div class="col-lg-2">
                            <button type="submit" class="btn btn-sm btn-success">Filter</button>
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
                                        <th>Sl</th>
                                        <th>Fingerprint No </th>
                                        <th>Employee Name</th>
                                        <th>In Time</th>
                                        <th>Out Time</th>
                                        <th>Total Working Hour</th>
                                        <th>Approve Hour</th>
                                        <th>Approve Minutes</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="form.inventory_current_stocks_warehouse_id  != '' ">

                                    <tr v-for="(product, index) in form.product">
                                        <td scope="row">
                                            {{ index +1 }}
                                            <input type="hidden" class="small" v-model="product.inventory_current_stocks_product_id"   >
                                        </td>
                                        <td>
                                            {{ product.product_name }}
                                        </td>
                                        <td>
                                            {{ product.product_name }}
                                        </td>
                                        <td>
                                            <input type="number" v-model="product.inventory_stocks_open_qty" class="form-control form-control-sm m-input" placeholder="" @input="copyQty({index})">
                                            <div class="requiredField" v-if="errors['product.'+index+'.inventory_stocks_open_qty']">{{ errors['product.'+index+'.inventory_stocks_open_qty'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="number" v-model="product.inventory_stocks_open_qty" class="form-control form-control-sm m-input" placeholder="" @input="copyQty({index})">
                                            <div class="requiredField" v-if="errors['product.'+index+'.inventory_stocks_open_qty']">{{ errors['product.'+index+'.inventory_stocks_open_qty'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="number" v-model="product.inventory_stocks_open_qty" class="form-control form-control-sm m-input" placeholder="" @input="copyQty({index})">
                                            <div class="requiredField" v-if="errors['product.'+index+'.inventory_stocks_open_qty']">{{ errors['product.'+index+'.inventory_stocks_open_qty'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="number" v-model="product.inventory_stocks_open_qty" class="form-control form-control-sm m-input" placeholder="" @input="copyQty({index})">
                                            <div class="requiredField" v-if="errors['product.'+index+'.inventory_stocks_open_qty']">{{ errors['product.'+index+'.inventory_stocks_open_qty'][0] }}</div>
                                        </td>
                                        <td>
                                            <input type="number" v-model="product.inventory_stocks_open_qty" class="form-control form-control-sm m-input" placeholder="" @input="copyQty({index})">
                                            <div class="requiredField" v-if="errors['product.'+index+'.inventory_stocks_open_qty']">{{ errors['product.'+index+'.inventory_stocks_open_qty'][0] }}</div>
                                        </td>
                                        <td>
                                            <div v-if="" class="m-demo__preview m-demo__preview--badge">
                                                <span class="m-badge m-badge--success m-badge--wide m-badge--rounded">Pending </span>
                                            </div>
                                           <!-- <div v-if="value.status == 0" class="m-demo__preview m-demo__preview&#45;&#45;badge">
                                                <span class="m-badge m-badge&#45;&#45;danger m-badge&#45;&#45;wide m-badge&#45;&#45;rounded"> <i class="la la-ban"></i> Inactive</span>
                                            </div>-->
                                        </td>

                                    </tr>
                                    </tbody>
                                    <tbody  v-if="form.inventory_current_stocks_warehouse_id  == '' ">
                                    <tr v-for="(product, index) in product_lists">
                                        <td scope="row">
                                            {{ index +1 }}
                                        </td>
                                        <td>
                                            {{ product.product_name }}
                                        </td>
                                        <td>
                                            {{ product.product_name }}
                                        </td>
                                        <td>
                                            <input type="number" v-model="product.inventory_stocks_open_qty" class="form-control form-control-sm m-input" placeholder="">
                                        </td>
                                        <td>
                                            <input type="number" v-model="product.inventory_stocks_open_qty" class="form-control form-control-sm m-input" placeholder="">
                                        </td>
                                        <td>
                                            <input type="number" v-model="product.inventory_stocks_open_qty" class="form-control form-control-sm m-input" placeholder="">
                                        </td>
                                        <td>
                                            <input type="number" v-model="product.inventory_stocks_open_qty" class="form-control form-control-sm m-input" placeholder="">
                                        </td>
                                        <td>
                                            <input type="number" v-model="product.inventory_stocks_open_qty" class="form-control form-control-sm m-input" placeholder="">
                                        </td>
                                        <td>
                                            <div v-if="" class="m-demo__preview m-demo__preview--badge">
                                                <span class="m-badge m-badge--success m-badge--wide m-badge--rounded"> Pending </span>
                                            </div>
                                           <!-- <div v-if="value.status == 0" class="m-demo__preview m-demo__preview&#45;&#45;badge">
                                                <span class="m-badge m-badge&#45;&#45;danger m-badge&#45;&#45;wide m-badge&#45;&#45;rounded"> <i class="la la-ban"></i> Inactive</span>
                                            </div>-->
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
                                <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Approve</span></button>
                                <!--<button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save and Go List</span></button>-->
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';

    export default {
        props:['product_lists','warehouse_lists'],

        data:function(){
            return{
                product_list:true,
                add_form:true,
                edit_form:false,

                form:{
                    inventory_current_stocks_warehouse_id: '',
                    product: [
                        {
                            product_name:'',
                            inventory_current_stocks_product_id:'',
                            inventory_stocks_open_qty:'',
                            inventory_stocks_current_qty:'',
                        }
                    ],
                },
                errors: {},
            };
        },



        methods: {
            loadproduct() {
                var _this = this;
                var warehouse_id =this.form.inventory_current_stocks_warehouse_id;
                axios.get(base_url+'inv_load_product/'+warehouse_id).then( (response) => {
                    this.form.product = response.data;

                    /*setTimeout(function () {
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
                     },100);*/

                });
            },

            index(pageNo,perPage){
                if(pageNo){ pageNo = pageNo; }else{pageNo = this.resultData.current_page; }
                if(perPage){ perPage = perPage;}else{ perPage = this.perPage;}

                axios.get(base_url+"stock-open/?page="+pageNo+"&perPage="+perPage).then((response) => {
                    this.resultData = response.data;

                });
            },

            store:function(){
                var _this = this;
                axios.post(base_url+'stock-open/store', this.form).then( (response) => {
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
            copyQty(id){

                var i = id.index;
                var amount = Number(this.form.product[i].inventory_stocks_open_qty);
                this.form.product[i].inventory_stocks_current_qty = amount;
            },

            showMassage(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, 'Success Alert', {timeOut: 5000});
                } else {
                    toastr.error(data.message, 'Error Alert', {timeOut: 5000});
                }
            },

            /*
             changePerPage(){
             var vm = this;
             vm.index(1,vm.perPage);
             },*/

        },

        mounted(){
            var _this = this;

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'inventory_current_stocks_warehouse_id'){
                    _this.form.inventory_current_stocks_warehouse_id = selectedType.val();
                    _this.loadproduct();
                }
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
                    startDate: '-2d',
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.sales_customer_join_date = '';

                    }else {
                        _this.form.sales_customer_join_date = moment(ev.date).format('DD/MM/YYYY');

                    }

                });
            });
        },
        created(){

            var _this = this;
            /*$('body').on('click', '#addButton', function() {
             _this.add_form = true;
             _this.product_list = false;
             _this.edit_form = false;
             $('#addButton').hide();
             $('#listButton').show();
             });

             $('body').on('click', '#listButton', function() {
             _this.add_form = false;
             _this.product_list = true;
             _this.edit_form = false;
             $('#addButton').show();
             $('#listButton').hide();
             });*/

            EventBus.$on('data-changed', (id) => {
                _this.add_form = false;
                _this.product_list = true;
                _this.edit_form = false;
                $('#addButton').show();
                $('#listButton').hide();
                _this.index(1);
            });

            _this.index(1);
        },
    }
</script>

