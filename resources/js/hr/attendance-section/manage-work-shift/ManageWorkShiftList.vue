<template>
    <div>
        <div class="m-portlet__body" v-if="product_list">
            <br><br>
            <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="listComponent" class="m-form m-form--fit m-form--label-align-right" >
                <div class="m-portlet__body">
                    <div class="form-group m-form__group row">
                        <label for="m_datepicker_2" class="col-lg-3 col-form-label">Date</label>
                        <div class="col-lg-4">
                            <div class="input-group date">
                                <input type="text" class="form-control form-control-sm m-input datepicker" v-model="form.attn_date" data-dateField="sales_invoices_date" readonly placeholder="Select date from picker" id="m_datepicker_2" />
                                <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                                </div>
                            </div>
                            <div class="requiredField" v-if="(errors.hasOwnProperty('attn_date'))">{{ (errors.hasOwnProperty('attn_date')) ? errors.attn_date[0] :'' }}</div>
                        </div>
                        <div class="col-lg-2">
                            <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i> Attendance Process</button>
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


        data:function(){
            return{
                product_list:true,
                form:{
                    attn_date: '',
                },
                errors: {},
            };
        },



        methods: {

            store:function(){
                var _this = this;
                axios.post(base_url+'manage-work-shift', this.form).then( (response) => {
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

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: "dd-mm-yyyy",
                    minViewMode: 1,
                    todayHighlight: true,
                    clearBtn: true,
                    //startDate: new Date(),
                    //startDate: '-1',
                }).on('changeDate', function (ev) {

                    $(this).datepicker('hide');
                    if(ev.date == undefined){
                        _this.form.attn_date = '';

                    }else {
                        _this.form.attn_date = moment(ev.date).format('DD/MM/YYYY');

                    }

                });
            });
        },
        created(){

        },
    }
</script>

