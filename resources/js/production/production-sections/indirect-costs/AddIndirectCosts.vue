<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="store" id="addComponent"
              class="m-form m-form--fit m-form--label-align-right">

            <div class="m-portlet__body">
                <div class="form-group m-form__group row">
                    <label for="indir_no" class="col-lg-2 col-form-label">#No <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="indir_no" v-model="form.indir_no"
                               class="form-control form-control-sm m-input" placeholder="Enter S/L" readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('indir_no'))">{{
                            (errors.hasOwnProperty('indir_no')) ? errors.indir_no[0] :'' }}
                        </div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Date <span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.date" data-dateField="forming_date" readonly
                                   placeholder="Select date from picker" id="m_datepicker_2" autocomplete="off"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('date'))">{{
                            (errors.hasOwnProperty('date')) ? errors.date[0] :'' }}
                        </div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label class="col-lg-2 col-form-label" for="remarks">Remarks </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.remarks" id="remarks" rows="2"></textarea>
                    </div>
                </div>
                <br>
                <!--begin::Portlet-->
                <div class="form-group m-form__group row add-manage-section">
                    <div class="m-section__content col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse">
                                <tr>
                                    <th></th>
                                    <th width="10%">Indirect Costs Type <span class="requiredField">*</span></th>
                                    <th>Remarks</th>
                                    <th>Amount <span class="requiredField">*</span></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">
                                    <th scope="row">
                                        <a href="javascript:void(0)" @click="addNewItem"
                                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Add More">
                                            <i class="la la-plus"></i>
                                        </a>
                                    </th>
                                    <td class="tdWidth">
                                        <select class="form-control select2" v-bind:data-index="index"
                                                v-model="details.prod_indir_costs_type_id" style="width:100%">
                                            <option v-for="(value,index) in indir_costs_types"
                                                    :value="value.id"
                                            > {{value.indirect_cost_type}}
                                            </option>
                                        </select>
                                        <div class="requiredField"
                                             v-if="errors['details.'+index+'.prod_indir_costs_type_id']">{{
                                            errors['details.'+index+'.prod_indir_costs_type_id'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm m-input"
                                               v-model="details.remarks" placeholder="">
                                        <div class="requiredField" v-if="errors['details.'+index+'.remarks']">{{
                                            errors['details.'+index+'.remarks'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="any" class="form-control form-control-sm m-input"
                                               v-model="details.amount" placeholder="" @input="calcTotal()">
                                        <div class="requiredField" v-if="errors['details.'+index+'.amount']">{{
                                            errors['details.'+index+'.amount'][0] }}
                                        </div>
                                    </td>
                                    <td scope="row" class="width-100 text-center">
                                        <a @click="removeItem(index)"
                                           class="btn btn-outline-danger m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top"
                                           title="Delete"><i class="la la-trash-o"></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-right">Total</td>
                                    <td>
                                        <input type="number" step="any" v-model="form.total_amount"
                                               class="form-control form-control-sm m-input" placeholder="Total"
                                               readonly>
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
                            <button type="submit" v-if="permissions.indexOf('indirect-costs.approve') != -1"
                                    class="btn btn-sm btn-primary" @click.prevent="store(1)"><i class="la la-check"></i>
                                <span>Approve</span></button>
                            <button type="submit" class="btn btn-sm btn-success" @click.prevent="store(0)"><i
                                class="la la-check"></i> <span>Save and Go List</span></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!--end::Form-->
    </div>
</template>


<script>
    import {EventBus} from '../../../vue-assets';
    import moment from 'moment';

    export default {

        props: ['permissions', 'indir_costs_types'],

        data: function () {
            return {
                data_list: false,
                add_form: true,
                edit_form: false,
                view_form: false,

                form: {
                    indir_no: '',
                    date: '',
                    total_amount: '',
                    remarks: '',
                    approve_status: '',
                    details: [
                        {
                            prod_indir_costs_type_id: '',
                            remarks: '',
                            amount: '',
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {

            addNewItem() {
                var _this = this;
                this.form.details.push({
                    prod_indir_costs_type_id: '',
                    remarks: '',
                    amount: '',
                });
                _this.initSelect2();
            },

            removeItem(index) {
                var _this = this;
                if (_this.form.details.length > 1) {
                    _this.form.details.splice(index, 1);
                    setTimeout(function () {
                        _this.initSelect2();
                    }, 100);

                }
                _this.calcTotal();
            },

            store: function (app) {
                var _this = this;
                _this.form.approve_status = app;
                _this.$loading(true);
                axios.post(base_url + 'indirect-costs', _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        if (response.data.status == 'success') {
                            _this.showMassage(response.data);
                            EventBus.$emit('data-changed');
                        } else {
                            _this.showMassage(response.data);
                        }
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

            calcTotal() {
                var _this = this;
                var total = 0;
                var details = this.form.details;
                var length = details.length;

                for (let i = 0; i < length; i++) {
                    total = Number(details[i].amount) + total;
                }
                _this.form.total_amount = total.toFixed(2);
            },

            duplicateCheck(selectedValue) {
                var no_index = this.form.details.length;
                for (let i = 0; i < no_index; i++) {
                    if (this.form.details[i].prod_indir_costs_type_id == selectedValue) {
                        alert("Duplicate Found");
                        return true;
                    }
                }
                return false;
            },

            serialNoGenerate() {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'indirect-costs?production_token=serial_no_generate')
                    .then((response) => {
                        _this.$loading(false);
                        _this.form.indir_no = response.data;
                    });
            },

            initSelect2() {
                setTimeout(function () {
                    var Select2 = {
                        init: function () {
                            $(".select2").select2({placeholder: "Please select an option"})
                        }
                    };
                    jQuery(document).ready(function () {
                        Select2.init();
                    });
                }, 250);
            },
        },

        mounted() {
            var _this = this;

            _this.initSelect2();

            $('#addComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this),
                    curerntVal = !!selectedItem.val(),
                    dataIndex = $(e.currentTarget).attr('data-index');

                var isDuplicated = _this.duplicateCheck(selectedItem.val());
                if (isDuplicated) {
                    _this.form.details[dataIndex].prod_indir_costs_type_id = '';
                    setTimeout(function () {
                        _this.initSelect2();
                    }, 250);
                } else {
                    _this.form.details[dataIndex].prod_indir_costs_type_id = selectedItem.val();
                }
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.date = '';
                    } else {
                        _this.form.date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created() {
            var _this = this;
            _this.serialNoGenerate();
        }
    }
</script>
