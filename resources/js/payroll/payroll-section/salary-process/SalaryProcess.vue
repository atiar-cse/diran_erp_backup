<template>
    <div>
        <div class="m-portlet__body" v-if="product_list">
            <br><br>
            <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="listComponent"
                  class="m-form m-form--fit m-form--label-align-right">
                <div class="m-portlet__body">
                    <div class="form-group m-form__group row">
                        <label for="salary_month" class="col-lg-3 col-form-label">Month</label>
                        <div class="col-lg-4">
                            <input type="text" id="salary_month"
                                   class="form-control form-control-sm m-input datepicker salary_month"
                                   v-model="form.salary_month" placeholder="Select date from picker"
                                   autocomplete="off"/>
                        </div>
                        <div class="col-lg-2">
                            <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i> Salary
                                Process
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>

    import {EventBus} from '../../../vue-assets';

    export default {

        data: function () {
            return {
                product_list: true,
                form: {
                    salary_month: '',
                },
                errors: {},
            };
        },

        methods: {

            store: function () {
                var _this = this;
                _this.$loading(true);
                axios.post(base_url + 'salary-process', _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        _this.showMassage(response.data);
                        EventBus.$emit('data-changed');
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
                    toastr.success(data.message, 'Success Alert', {timeOut: 5000});
                } else {
                    toastr.error(data.message, 'Error Alert', {timeOut: 5000});
                }
            },
        },

        mounted() {
            var _this = this;

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: "mm-yyyy",
                    viewMode: "months",
                    minViewMode: 1,
                    //todayHighlight: true,
                    clearBtn: true,
                    //startDate: new Date(),
                    //startDate: '-1',
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.salary_month = '';
                    } else {
                        _this.form.salary_month = moment(ev.date).format('Y-MM-01');
                    }
                });
            });
        },

        created() {

        },
    }
</script>

