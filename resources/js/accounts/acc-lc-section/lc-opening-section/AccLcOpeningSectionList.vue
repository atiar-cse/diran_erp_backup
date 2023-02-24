<template>
    <div>

        <div class="modal fade" id="addModel" role="dialog" aria-labelledby="" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="">Add Additional LC Opening Charge</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="la la-remove"></span>
                        </button>
                    </div>
                    <form method="POST" id="addModalComponent" enctype="multipart/form-data"
                          v-on:submit.prevent="storeLCOpeningCharge"
                          class="m-form m-form--fit m-form--label-align-right">
                        <div class="modal-body">
                            <input type="hidden" v-model="form.id">
                            <div class="form-group m-form__group row">
                                <label class="col-lg-2 col-form-label" for="insurance_date">Transaction Date </label>
                                <div class="col-lg-4">
                                    <div class="input-group date">
                                        <input required type="text"
                                               class="form-control form-control-sm m-input datepicker"
                                               v-model="form.lc_opening_date" data-dateField="insurance_date" readonly
                                               placeholder="Select date from picker" id="insurance_date"/>
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                            <i class="la la-calendar-check-o"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <label class="col-lg-2 col-form-label" for="lc_opening_charges">LC No </label>
                                <div class="col-lg-4">
                                    <input type="text" readonly class="form-control form-control-sm m-input"
                                           v-model="form.lc_no">
                                </div>
                            </div>

                            <div class="form-group m-form__group row">
                                <label class="col-lg-2 col-form-label" for="lc_opening_charges">Opening Charge </label>
                                <div class="col-lg-4">
                                    <input required type="number" step="any" min="0" value="0" id="lc_opening_charges"
                                           v-model="form.lc_opening_charges"
                                           class="form-control form-control-sm m-input"
                                           placeholder="Enter Opening Charge">
                                </div>
                                <label class="col-lg-2 col-form-label" for="narration">Narration </label>
                                <div class="col-lg-4">
                                    <textarea class="form-control form-control-sm" id="narration"
                                              v-model="form.narration" placeholder="" rows="1"></textarea>
                                </div>
                            </div>
                            <div class="form-group m-form__group row">
                                <label class="col-lg-2 col-form-label" for="debit_account_id">Debit Account <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-4">
                                    <select required class="form-control m-select2 select2" id="debit_account_id"
                                            v-model="form.debit_account_id" data-selectField="debit_account_id">
                                        <option v-for="(value,index) in account_list" :value="value.id"> {{
                                            value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}
                                        </option>
                                    </select>
                                </div>
                                <label class="col-lg-2 col-form-label" for="credit_account_id">Credit Account <span
                                    class="requiredField">*</span></label>
                                <div class="col-lg-4">
                                    <select required class="form-control m-select2 select2" id="credit_account_id"
                                            v-model="form.credit_account_id" data-selectField="credit_account_id">
                                        <option v-for="(value,index) in account_list" :value="value.id"> {{
                                            value.chart_of_accounts_title +'('+ value.chart_of_account_code +')'}}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-success"><i class="la la-check"></i>
                                <span>Save</span></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="m-portlet__body" v-if="data_list">
            <div class="row">
                <div class="item-show-limit col-md-8">
                    <span>Show</span>
                    <select name="per_page" class="per_page" @change="changePerPage" v-model="perPage">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                        <option value="40">40</option>
                        <option value="50">50</option>
                    </select>
                    <span>Entries</span>

                </div>
                <div class="col-md-4">
                    <div class="m-input-icon m-input-icon--left">
                        <input type="text" class="form-control m-input" placeholder="Search..." id="inputSearch">
                        <span class="m-input-icon__icon m-input-icon__icon--left">
                        <span><i class="la la-search"></i></span>
                    </span>
                    </div>
                </div>
            </div>
            <br><br>
            <!--begin: Datatable -->

            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                <tr>
                    <th>S/L</th>
                    <th>LC No</th>
                    <th>Opening Date</th>
                    <th>Supplier</th>
                    <th>PI No</th>
                    <th>LC Category</th>
                    <th>Type</th>
                    <th>Opening Bank</th>
                    <th>Total Value (BDT)</th>
                    <th>LC Opening Charge</th>
                    <th class="width-100 text-center">Action</th>
                </tr>
                </thead>
                <tbody v-if="resultData.data !=''" id="dataTable">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.lc_no}}</td>
                    <td>{{value.lc_opening_date}}</td>
                    <td>{{value.get_supplier ? value.get_supplier.purchase_supplier_name : ''}}</td>
                    <td>{{value.get_pi_no ? value.get_pi_no.pi_no : ''}}</td>
                    <td>
                        <span v-if="value.lc_category == 1" class="badge badge-pill badge-primary">Export</span>
                        <span v-if="value.lc_category == 2" class="badge badge-pill badge-secondary">Import</span>
                    </td>
                    <td>
                        <span v-if="value.lc_type == 1" class="badge badge-pill badge-primary">Master LC</span>
                        <span v-if="value.lc_type == 2" class="badge badge-pill badge-secondary">Deffard LC</span>
                    </td>
                    <td>{{value.get_opening_bank ? value.get_opening_bank.accounts_bank_names : ''}}</td>
                    <td class="text-right">{{value.lc_total_value}}</td>
                    <td class="text-center">
                        <a v-if="permissions.indexOf('acc-lc-opening-section.edit') !=-1 && value.lc_closing_status != 1"
                           href="javascript:void(0)" @click="PushLCData(value.id,value.lc_no)"
                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-toggle="modal" data-target="#addModel"><i class="la la-plus"></i></a>
                    </td>
                    <td scope="row" class="width-100 text-center">
                        <a v-if="permissions.indexOf('acc-lc-opening-section.show') !=-1" @click="viewData(value.id)"
                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="View"><i
                            class="la la-eye"></i></a>
                        <span v-if="value.account_status == 1" title="Approved"><i
                            class="la la-check-circle-o"></i></span>
                        <a v-if="permissions.indexOf('acc-lc-opening-section.edit') !=-1 && value.account_status != 1"
                           @click="editData(value.id)"
                           class="btn btn-outline-success m-btn m-btn--icon m-btn--icon-only m-btn--pill m-btn--air"
                           data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i
                            class="la   la-edit"></i></a>
                    </td>
                </tr>
                </tbody>
                <tbody v-else>
                <tr>
                    <td colspan="12" class="text-center">No Data Available.</td>
                </tr>
                </tbody>
            </table>
            <div class="row">
                <div class="text-center col-md-12">
                    <pagination :resultData="resultData" @clicked="index" :mid-size="9"></pagination>
                </div>
            </div>
        </div>

        <AccEditLcOpeningSection v-else-if="edit_form"
                                 :permissions="permissions"
                                 :pi_invoice_lists="pi_invoice_lists"
                                 :bank_lists="bank_lists"
                                 :account_list="account_list"
                                 :edit-id="edit_id"
        ></AccEditLcOpeningSection>
        <AccViewLcOpeningSection
            v-else-if="view_form"
            :edit-id="edit_id"
        ></AccViewLcOpeningSection>
    </div>
</template>

<script>

    import {EventBus} from '../../../vue-assets';
    import Pagination from '../../../components/Pagination.vue';
    import AccEditLcOpeningSection from './AccEditLcOpeningSection.vue';
    import AccViewLcOpeningSection from './AccViewLcOpeningSection.vue';


    export default {
        components: {
            AccEditLcOpeningSection,
            AccViewLcOpeningSection,
            Pagination
        },

        props: ['permissions', 'pi_invoice_lists', 'bank_lists', 'account_list'],

        data: function () {
            return {
                data_list: true,
                add_form: false,
                edit_form: false,
                edit_id: false,
                view_form: false,
                resultData: {},
                perPage: 10,

                form: {
                    id: '',
                    lc_no: '',
                    lc_opening_date: '',

                    debit_account_id: 47,
                    credit_account_id: 16,

                    narration: '',
                    approve: 1,

                },
            };
        },

        methods: {
            index(pageNo, perPage) {
                var _this = this;

                if (pageNo) {
                    pageNo = pageNo;
                } else {
                    pageNo = _this.resultData.current_page;
                }
                if (perPage) {
                    perPage = perPage;
                } else {
                    perPage = _this.perPage;
                }

                _this.$loading(true);
                axios.get(base_url + "acc-lc-opening-section/?page=" + pageNo + "&perPage=" + perPage)
                    .then((response) => {
                        _this.$loading(false);
                        _this.resultData = response.data;
                    });
            },

            storeLCOpeningCharge: function () {
                var _this = this;

                _this.$loading(true);
                axios.post(base_url + 'acc-lc-opening-section/add-lc-charge', _this.form)
                    .then((response) => {
                        _this.$loading(false);
                        _this.showMassage(response.data);
                        $('#addModel').modal('hide');
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

            PushLCData(rowID, LCNo) {
                var _this = this;
                _this.form.id = rowID;
                _this.form.lc_no = LCNo;

                setTimeout(function () {
                    var Select2 = {
                        init: function () {
                            $(".select2").select2({placeholder: "Please select an option"})
                        }
                    };
                    jQuery(document).ready(function () {
                        Select2.init()
                    });
                }, 500);
            },

            editData(id) {
                var _this = this;
                _this.add_form = false;
                _this.data_list = false;
                _this.edit_form = true;
                _this.edit_id = id;
                view_form:false;
                $('#addButton').hide();
                $('#listButton').show();
            },

            viewData(id) {
                var _this = this;
                _this.add_form = false;
                _this.data_list = false;
                _this.edit_form = false;
                _this.view_form = true;
                _this.edit_id = id;
                $('#addButton').hide();
                $('#listButton').show();
            },

            showMassage(data) {
                if (data.status == 'success') {
                    toastr.success(data.message, 'Success Alert', {timeOut: 5000});
                } else {
                    toastr.error(data.message, 'Error Alert', {timeOut: 5000});
                }
            },

            changePerPage() {
                var vm = this;
                vm.index(1, vm.perPage);
            },

            datatables() {
                $(document).ready(function () {
                    $("#inputSearch").on("keyup", function () {
                        var value = $(this).val().toLowerCase();
                        $("#dataTable tr").filter(function () {
                            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                        });
                    });
                });
            },
        },

        mounted() {
            var _this = this;

            var Select2 = {
                init: function () {
                    $(".select2").select2({placeholder: "Please select an option"})
                }
            };
            jQuery(document).ready(function () {
                Select2.init()
            });

            $('#addModalComponent').on('change', '.select2', function (e) {
                var selectedItem = $(this);
                var selectField = $(e.currentTarget).attr('data-selectField');

                if (selectField == 'debit_account_id') {
                    _this.form.debit_account_id = selectedItem.val();
                } else if (selectField == 'credit_account_id') {
                    _this.form.credit_account_id = selectedItem.val();
                }
            });

            $(document).on("focus", ".datepicker", function () {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    todayHighlight: true,
                    clearBtn: true
                }).on('changeDate', function (ev) {
                    $(this).datepicker('hide');
                    var selectField = $(ev.currentTarget).attr('data-dateField');
                    if (selectField == 'insurance_date') {
                        _this.form.lc_opening_date = moment(ev.date).format('DD/MM/YYYY');
                    }
                });
            });
        },

        created() {
            var _this = this;

            $('body').on('click', '#listButton', function () {
                _this.add_form = false;
                _this.data_list = true;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').show();
                $('#listButton').hide();
            });

            EventBus.$on('data-changed', (id) => {
                _this.add_form = false;
                _this.data_list = true;
                _this.edit_form = false;
                _this.view_form = false;
                $('#addButton').show();
                $('#listButton').hide();
                _this.index(1);
            });

            _this.index(1);
            _this.form.lc_opening_date = moment().format('DD/MM/YYYY');

            _this.datatables();
        },
    }
</script>

