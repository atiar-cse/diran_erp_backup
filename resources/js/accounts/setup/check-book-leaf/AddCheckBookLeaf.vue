<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent"
              class="m-form m-form--fit m-form--label-align-right">

            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="chart_ac_id" class="col-lg-3 col-form-label">Chart of Account (Bank) <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <select style="width: 100%" class="form-control select2" id="chart_ac_id"
                                v-model="form.chart_ac_id" data-selectField="chart_ac_id" width="60%">
                            <option v-for="(value,index) in bank_coa_list" :value="value.id">
                                {{value.chart_of_accounts_title}}
                                ({{value.chart_of_account_code}})
                            </option>
                        </select>
                        <div class="requiredField" v-if="errors['chart_ac_id']">{{ errors['chart_ac_id'][0] }}</div>

                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label">Account Number <span class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <input type="text" class="form-control" v-model="form.account_no"
                               placeholder="Enter Account Number"/>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('account_no'))">
                            {{ (errors.hasOwnProperty('account_no')) ? errors.account_no[0] : '' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label">Book Number <span class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <input type="text" class="form-control" v-model="form.book_no"
                               placeholder="Enter Book Number"/>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('book_no'))">
                            {{ (errors.hasOwnProperty('book_no')) ? errors.book_no[0] : '' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label">Receive Date <span class="requiredField"></span></label>
                    <div class="col-lg-6">
                        <div class="input-group date">
                            <div class="input-group date ">
                                <input type="text" id="check_gen_date"
                                       class="form-control form-control-sm m-input datepicker" readonly
                                       v-model="form.check_gen_date" data-dateField="check_gen_date"
                                       placeholder="Select date from picker"/>
                                <div class="input-group-append">
                                    <span class="input-group-text">
                                        <i class="la la-calendar-check-o"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label">Prefix <span class="requiredField"></span></label>
                    <div class="col-lg-6">
                        <input type="text" class="form-control" v-model="form.prefix" @input="chequeLeafGenerate()"
                               placeholder="Enter Prefix"/>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('prefix'))">
                            {{ (errors.hasOwnProperty('prefix')) ? errors.prefix[0] : '' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label">Suffix <span class="requiredField"></span></label>
                    <div class="col-lg-6">
                        <input type="text" class="form-control" v-model="form.suffix" @input="chequeLeafGenerate()"
                               placeholder="Enter Suffix"/>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('suffix'))">
                            {{ (errors.hasOwnProperty('suffix')) ? errors.suffix[0] : '' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label">Starting No <span class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <input type="text" class="form-control" v-model="form.check_start" @input="chequeLeafGenerate()"
                               placeholder="Enter Starting No"/>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('check_start'))">
                            {{ (errors.hasOwnProperty('check_start')) ? errors.check_start[0] : '' }}
                        </div>
                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label">Number Of Page <span class="requiredField">*</span></label>
                    <div class="col-lg-6">
                        <input type="text" class="form-control" v-model="form.check_range" @input="chequeLeafGenerate()"
                               placeholder="Enter Number Of Page"/>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('check_range'))">
                            {{ (errors.hasOwnProperty('check_range')) ? errors.check_range[0] : '' }}
                        </div>

                    </div>
                </div>

                <div class="form-group m-form__group row">
                    <label class="col-lg-3 col-form-label">Cheque No Length </label>
                    <div class="col-lg-6">
                        <input type="text" class="form-control" v-model="form.cheque_no_length" @keyup="lengthValid()"
                               @input="chequeLeafGenerate()"
                               placeholder="Enter Cheque No Length"/>

                    </div>
                </div>

                <!--begin::Portlet-->
            </div>
            <div class="m-portlet__foot m-portlet__no-border m-portlet__foot--fit">
                <div class="m-form__actions m-form__actions--solid">
                    <div class="row">
                        <div class="col-lg-9 m--align-right">
                            <button v-if="permissions.indexOf('check-book-leaf.approve') !=-1" type="submit"
                                    class="btn btn-sm btn-success" @click.prevent="store(1)"><i class="la la-check"></i>
                                <span>Approve</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->

        <!-- @ Preview -->
        <div class="m-portlet__body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="m-portlet">
                        <div class="m-portlet__body m-portlet__body--no-padding">
                            <div class="m-invoice-2">
                                <div class="m-invoice__wrapper">
                                    <div class="m-invoice__head">
                                        <div class="m-invoice__container m-invoice__container--centered">
                                            <div class="logo_headerX" style="text-align: center">
                                                <h4>Cheque Book Leaf Preview</h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="m-invoice__body m-invoice__body--centered">
                                        <div class="row">
                                            <div class="col-md-2">
                                            </div>
                                            <div class="col-md-8">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th width="5%">SL</th>
                                                            <th class="text-center">Cheque Book Leaf No</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr v-for="(item, index) in chequeNo">
                                                            <td>{{++index}}</td>
                                                            <td class="text-left">{{ item }}</td>
                                                        </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- // Preview -->
    </div>
</template>

<script>
    import {EventBus} from '../../../vue-assets';

    export default {

        props: ['permissions', 'bank_coa_list'],

        data: function () {
            return {
                data_list: false,
                add_form: true,
                edit_form: false,
                chequeNo: [],
                form: {
                    chart_ac_id: '',
                    account_no: '',
                    book_no: '',
                    prefix: '',
                    suffix: '',
                    cheque_no_length: '',
                    check_start: '',
                    check_range: '',
                    check_gen_date: '',
                    details: '',
                    chequeList: [],
                    approve: '',
                    status: '1'
                },
                errors: {},
            };
        },

        methods: {

            lengthValid() {
                var cheque_no_length = this.form.cheque_no_length;
                var str = (this.form.prefix + this.form.check_start + this.form.suffix);
                var totalLength = str.length;
            },

            chequeLeafGenerate() {

                var _this = this;

                let totalLeaf;
                let increment = '';
                let LeafLength;

                var cheque_no_length = this.form.cheque_no_length;

                var str = (this.form.prefix + this.form.check_start + this.form.suffix);
                var totalLength = str.length;

                if (totalLength < cheque_no_length) {
                    for (let t = 1; t < cheque_no_length - totalLength; t++) {
                        increment += "0";
                    }
                }

                for (let i = 0; i < this.form.check_range; i++) {

                    if (this.form.prefix) {
                        totalLeaf = this.form.prefix + increment + (parseInt(this.form.check_start) + parseInt(i));
                        LeafLength = totalLeaf.length;

                        if (LeafLength > cheque_no_length) {
                            var dependency = '';
                            var diff = LeafLength - cheque_no_length;

                            for (var z = 0; z <= diff; z++) {
                                dependency += "0";
                            }
                            totalLeaf = totalLeaf.replace(dependency, '0');
                        }
                    } else if (this.form.suffix) {
                        totalLeaf = increment + parseInt(this.form.check_start) + parseInt(i) + this.form.suffix;
                        LeafLength = totalLeaf.length;

                        if (LeafLength > cheque_no_length) {
                            var dependency = '';
                            var diff = LeafLength - cheque_no_length;

                            for (var z = 0; z <= diff; z++) {
                                dependency += "0";
                            }
                            totalLeaf = totalLeaf.replace(dependency, '0');
                        }
                    } else {
                        totalLeaf = increment + parseInt(this.form.check_start) + parseInt(i);
                        LeafLength = totalLeaf.length;

                        if (LeafLength > cheque_no_length) {
                            var dependency = '';
                            var diff = LeafLength - cheque_no_length;

                            for (var z = 0; z <= diff; z++) {
                                dependency += "0";
                            }
                            totalLeaf = totalLeaf.replace(dependency, '0');
                        }
                    }

                    if (this.form.prefix && this.form.suffix) {
                        totalLeaf = this.form.prefix + increment + parseInt(this.form.check_start) + parseInt(i) + this.form.suffix;

                        LeafLength = totalLeaf.length;

                        if (LeafLength > cheque_no_length) {
                            var dependency = '';
                            var diff = LeafLength - cheque_no_length;

                            for (var z = 0; z <= diff; z++) {
                                dependency += "0";
                            }
                            totalLeaf = totalLeaf.replace(dependency, '0');
                        }
                    }

                    this.chequeNo[i] = totalLeaf;
                    this.form.chequeList[i] = totalLeaf;
                }
            },

            store(app) {
                var _this = this;
                _this.form.approve = app;
                alert('Do you want to Approve? please check properly.');
                _this.$loading(true);
                axios.post(base_url + 'check-book-leaf', _this.form)
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
                    toastr.success(data.message, 'Success Alert');
                } else if (data.status == 'error') {
                    toastr.error(data.message, 'Error Alert');
                } else {
                    toastr.error(data.message, 'Error Alert');
                }
            },

            getNewCheckBookNumber() {
                var _this = this;
                var chart_ac_id = _this.form.chart_ac_id;
                _this.$loading(true);
                axios.get(base_url + 'check-book-leaf?chart_ac_id=' + chart_ac_id + '&looking_for=getNewCheckBookNumber')
                    .then((response) => {
                        _this.$loading(false);
                        _this.form.book_no = response.data;
                    });
            },
        },

        mounted() {
            var _this = this;

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if (selectField == 'chart_ac_id') {
                    _this.form.chart_ac_id = selectedItem.val();

                    //Filter Account Number
                    _this.bank_coa_list.forEach(function (item) {
                        if (item.id == _this.form.chart_ac_id) {
                            var str = item.chart_of_accounts_title;
                            var sliced = str.slice(str.lastIndexOf('#') + 1);
                            sliced = sliced.replace(/\s/g, ''); // remove space
                            _this.form.account_no = sliced;
                        }
                    });
                    //Get the new check book no
                    _this.getNewCheckBookNumber();
                }
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                    /*startDate: '-2d',*/
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.check_gen_date = '';
                    } else {
                        _this.form.check_gen_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });

            var Select2 = {
                init: function () {
                    $(".select2").select2({placeholder: "Please select an option"})
                }
            };
            jQuery(document).ready(function () {
                Select2.init()
            });
        },

        created() {
            var _this = this;
            //_this.chequeLeafGenerate();
        }
    }
</script>
