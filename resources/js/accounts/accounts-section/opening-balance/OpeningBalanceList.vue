<template>
    <div>

        <div class="m-portlet__body" v-if="product_list">
            <br><br>
            <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent" class="m-form m-form--fit m-form--label-align-right" >
                <div class="m-portlet__body">
                    <div class="form-group m-form__group row">
                        <div class="col-lg-2"></div>
                        <label for="main_code_id" class="col-lg-2 col-form-label">Main Code</label>
                        <div class="col-lg-4">
                            <select class="form-control m-select2 select2" data-selectField="main_code_id" id="main_code_id" v-model="form.main_code_id"  @change="load_chart_ac()">
                                <option v-for="(value,index) in main_lists" :value="value.id" > {{value.main_code_title}} ({{value.main_code}})</option>
                            </select>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('main_code_id'))">{{ (errors.hasOwnProperty('main_code_id')) ? errors.main_code_id[0] :'' }}</div>
                        </div>
                        <div class="col-lg-2"></div>
                    </div>
                    <br><br>
                    <!--begin::Portlet-->

                    <div v-if="form.main_code_id  != '' ">
                        <div class="form-group m-form__group row">
                            <div class="m-section__content col-lg-12">
                                <div class ="table-responsive">
                                    <table class="table table-sm m-table table-bordered borderless">
                                        <thead class="thead-inverse" >
                                        <tr>
                                            <th>Sl</th>
                                            <th>Title</th>
                                            <th>Code </th>
                                            <th>Oepning Stock</th>
                                        </tr>
                                        </thead>
                                        <tbody >

                                        <tr v-for="(acc, index) in form.acc">
                                            <td scope="row">
                                                {{ index +1 }}
                                                <input type="hidden" class="small" v-model="acc.id"   >
                                            </td>
                                            <td>
                                                {{ acc.chart_of_accounts_title }} ({{ acc.chart_of_account_code }})
                                            </td>
                                            <td>
                                                {{ acc.chart_of_account_code }}
                                            </td>
                                            <td>
                                                <input type="number" v-model="acc.opening_balance" class="form-control form-control-sm m-input number" placeholder="" >
                                                <div class="requiredField" v-if="errors['acc.'+index+'.opening_balance']">{{ errors['acc.'+index+'.opening_balance'][0] }}</div>
                                            </td>
                                        </tr>
                                        </tbody>

                                    </table>
                                </div>
                                <br><br>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                    <div class="m-form__actions m-form__actions--solid">
                        <div class="row">
                            <div class="col-lg-12 m--align-right">
                                <button type="reset" class="btn btn-sm btn-secondary">Cancel</button>
                                <button type="submit" class="btn btn-sm btn-success" ><i class="la la-check"></i> <span>Save</span></button>
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

        props:['permission','main_lists','acc_chart_lists',],

        data: function(){
            return {
                product_list:true,
                form:{
                    main_code_id: '',
                    acc: [
                        {
                            chart_of_accounts_title:'',
                            chart_of_account_code:'',
                            opening_balance:'',
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {

            load_chart_ac() {
                var _this = this;
                var main_id =this.form.main_code_id;
                axios.get(base_url+'load_chart_ac/'+main_id).then( (response) => {
                    _this.form.acc = response.data;
                });
            },

            store:function(){
                var _this = this;
                axios.post(base_url+'opening-balance', this.form).then( (response) => {
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

            showMassage(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, 'Success Alert', {timeOut: 5000});
                } else {
                    toastr.error(data.message, 'Error Alert', {timeOut: 5000});
                }
            },

        },

        mounted(){
            var _this = this;
            $('#addComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if(dataIndex == 'main_code_id'){
                    _this.form.main_code_id = selectedType.val();
                    _this.load_chart_ac();
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

            $('.number').keypress(function(event) {
                if (((event.which != 46 || (event.which == 46 && $(this).val() == '')) ||
                    $(this).val().indexOf('.') != -1) && (event.which < 48 || event.which > 57)) {
                    event.preventDefault();
                }
            }).on('paste', function(event) {
                event.preventDefault();
            });
        },

        created(){

            var _this = this;

            /*$('body').on('click', '#listButton', function() {
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
                _this.load_chart_ac();

            });


        },
    }
</script>

