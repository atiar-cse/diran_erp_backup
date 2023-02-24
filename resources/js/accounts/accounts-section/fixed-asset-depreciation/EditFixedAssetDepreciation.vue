<template>
    <div>
        <!--begin::Form-->
        <form method="POST" enctype="multipart/form-data" v-on:submit.prevent="update(form.id)" id="editComponent"
              class="m-form m-form--fit m-form--label-align-right">
            <div class="m-portlet__body">

                <div class="form-group m-form__group row">
                    <label for="depreciations_no" class="col-lg-2 col-form-label">Depreciation No<span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <input type="text" id="depreciations_no" v-model="form.depreciations_no"
                               class="form-control form-control-sm m-input" placeholder="Enter Depreciation No"
                               readonly>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('depreciations_no'))">{{
                            (errors.hasOwnProperty('depreciations_no')) ? errors.depreciations_no[0] :'' }}
                        </div>
                    </div>
                    <label for="m_datepicker_2" class="col-lg-2 col-form-label">Depreciation Date<span
                        class="requiredField">*</span></label>
                    <div class="col-lg-4">
                        <div class="input-group date">
                            <input type="text" class="form-control form-control-sm m-input datepicker"
                                   v-model="form.depreciations_date" data-dateField="forming_date" readonly
                                   placeholder="Select date from picker" id="m_datepicker_2"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>

                        </div>
                        <div class="requiredField" v-if="(errors.hasOwnProperty('depreciations_date'))">{{
                            (errors.hasOwnProperty('depreciations_date')) ? errors.depreciations_date[0] :'' }}
                        </div>
                    </div>
                </div>
                <div class="form-group m-form__group row">
                    <label for="sub_code_id" class="col-lg-2 col-form-label">Sub Code </label>
                    <div class="col-lg-4">
                        <select class="form-control m-select2 select2" id="sub_code_id" data-selectField="sub_code_id"
                                v-model="form.sub_code_id">
                            <option v-for="(svalue,sindex) in sub_code_lists" :value="svalue.id">
                                {{svalue.sub_code_title}} ({{svalue.sub_code}})
                            </option>
                        </select>
                        <div class="requiredField" v-if="errors['sub_code_id']">{{ errors['sub_code_id'][0] }}</div>
                    </div>
                    <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                    <div class="col-lg-4">
                        <textarea class="form-control m-input" v-model="form.depreciations_narration" id="narration"
                                  rows="2"></textarea>
                    </div>
                </div>

                <br>

                <!---Details Work -->
                <div class="form-group m-form__group row">
                    <div class="m-section__content col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-sm m-table table-bordered borderless">
                                <thead class="thead-inverse">
                                <tr>
                                    <th width="20%">Sub Code 2<span class="requiredField">*</span></th>
                                    <th width="40%">Account</th>
                                    <th width="10%">Current Amount<span class="requiredField">*</span></th>
                                    <th width="10%">Percentage<span class="requiredField">*</span></th>
                                    <th width="10%">Depreciation Amount</th>
                                    <th width="10%">Amount after depreciation<span class="requiredField">*</span></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(details, index) in form.details">

                                    <td v-if="details.show == 0 " :rowspan="details.span">
                                        {{details.sub_code_title2}}({{details.sub_code2}})
                                    </td>
                                    <td>
                                        {{details.chart_of_accounts_title}}({{details.chart_of_account_code}})
                                        <input type="hidden" v-model="details.sub_code2_id">
                                        <input type="hidden" v-model="details.chart_acc_id">
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.amount"
                                               class="form-control form-control-sm m-input text-right width-130"
                                               readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.amount']">{{
                                            errors['details.'+index+'.amount'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.percentage" @keyup="totalQtyAndPrice()"
                                               class="form-control form-control-sm m-input text-right width-80">
                                        <div class="requiredField" v-if="errors['details.'+index+'.amount']">{{
                                            errors['details.'+index+'.amount'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.depreciations"
                                               class="form-control form-control-sm m-input text-right width-130"
                                               readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.depreciations']">{{
                                            errors['details.'+index+'.depreciations'][0] }}
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" v-model="details.current_amount"
                                               class="form-control form-control-sm m-input text-right width-130"
                                               readonly>
                                        <div class="requiredField" v-if="errors['details.'+index+'.current_amount']">{{
                                            errors['details.'+index+'.current_amount'][0] }}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-right"><b>Total</b></td>
                                    <td class="">
                                        <input type="text" v-model="form.total_amount"
                                               class="form-control form-control-sm m-input text-right" readonly>
                                    </td>
                                    <td></td>
                                    <td class="">
                                        <input type="text" v-model="form.total_depreciations"
                                               class="form-control form-control-sm m-input text-right" readonly>
                                    </td>
                                    <td class="">
                                        <input type="text" v-model="form.total_current_amount"
                                               class="form-control form-control-sm m-input text-right" readonly>
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
                            <button id="submit" type="submit" class="btn btn-sm btn-info" @click.prevent="update(form.id,0)"><i
                                class="la la-save"></i> <span>Update and Go List</span></button>
                            <button id="approve" type="submit" class="btn btn-sm btn-success" @click.prevent="update(form.id,1)"><i
                                class="la la-check"></i> <span>Approve</span></button>
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

    export default {

        props: ['sub_code_lists', 'permissions', 'editId'],

        data: function () {
            return {

                data_list: false,
                add_form: true,
                edit_form: false,

                form: {
                    depreciations_no: '',
                    depreciations_date: '',
                    sub_code_id: '',
                    depreciations_narration: '',
                    total_amount: 0,

                    total_depreciations: 0,
                    total_current_amount: 0,

                    account_status: 0,
                    status: 1,
                    approve: '',

                    details: [
                        {
                            depreciation_id: '',
                            sub_code2_id: '',
                            show: 0,
                            span: 1,
                            chart_acc_id: '',
                            amount: 0,
                            percentage: 11,
                            depreciations: 0,
                            current_amount: 0,
                        }
                    ],
                },
                errors: {},
            };
        },

        methods: {

            edit(id) {
                var _this = this;
                _this.$loading(true);
                axios.get(base_url + 'fixed-asset-depreciation/' + id + '/edit')
                    .then((response) => {
                        _this.$loading(false);
                        _this.form = response.data.data;
                        setTimeout(function () {
                            var Select2 = {
                                init: function () {
                                    $(".select2").select2({placeholder: "Please select an option"})
                                }
                            };
                            jQuery(document).ready(function () {
                                Select2.init()
                            });
                            _this.totalQtyAndPrice();
                        }, 100);
                    });
            },

            update(id, approval) {
                var _this = this;
                _this.form.approve = approval;
                _this.$loading(true);
                axios.put(base_url + 'fixed-asset-depreciation/' + id, _this.form)
                    .then((response) => {
                        document.getElementById("submit").disabled = true;
                        if ($('#approve').length > 0) {
                            document.getElementById("approve").disabled = true;
                        }
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

            totalQtyAndPrice() {
                var _this = this;

                var amount = 0;
                var dep = 0;
                var amount_aft_dep = 0;

                var total_amount = 0;
                var total_dep = 0;
                var total_amount_aft_dep = 0;

                var details = this.form.details;
                var length = details.length;

                for (let i = 0; i < length; i++) {
                    amount = Number(details[i].amount);
                    let percentage = Number(details[i].percentage);

                    dep = Number(details[i].amount) * Number(percentage / 100);
                    _this.form.details[i].depreciations = dep;

                    amount_aft_dep = amount - dep;
                    _this.form.details[i].current_amount = amount_aft_dep;

                    total_amount = amount + total_amount;
                    total_dep = dep + total_dep;
                    total_amount_aft_dep = amount_aft_dep + total_amount_aft_dep;
                }

                _this.form.total_amount = total_amount;
                _this.form.total_depreciations = total_dep;
                _this.form.total_current_amount = total_amount_aft_dep;
            },
        },

        mounted() {

            $('form').on('focus', 'input[type=number]', function (e) {
                $(this).on('mousewheel.disableScroll', function (e) {
                    e.preventDefault()
                })
            });
            $('form').on('blur', 'input[type=number]', function (e) {
                $(this).off('mousewheel.disableScroll')
            });

            var _this = this;

            $('#editComponent').on('change', '.select2', function (e) {
                var selectedType = $(this),
                    curerntVal = !!selectedType.val(),
                    dataIndex = $(e.currentTarget).attr('data-selectField');
                if (dataIndex == 'sub_code_id') {
                    _this.form.sub_code_id = selectedType.val();
                    _this.loadDetails();
                }
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true,
                    //startDate: '-2d',
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    if (ev.date == undefined) {
                        _this.form.depreciations_date = '';
                    } else {
                        _this.form.depreciations_date = moment(ev.date).format('DD/MM/YYYY');
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
            _this.edit(_this.editId);
            _this.totalQtyAndPrice();
        }
    }
</script>
