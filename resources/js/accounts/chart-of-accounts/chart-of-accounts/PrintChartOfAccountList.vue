<template>
    <div>

        <div class="m-portlet__body" v-if="chart_list">
            <!--begin: Datatable -->
            <div class="row print_links">
                <div class="col-md-10"></div>
                <div class="col-md-2 text-right">
                    <a href="#" onclick="window.print();return false" class="btn btn-info btn-sm m-btn  m-btn m-btn--icon">
                        <span><i class="fa flaticon-technology"></i><span>Print</span></span>
                    </a>
                </div>
            </div>
            <div class="row chat_acct_print" style="display:none;">
                <div class="col-md-8" style="margin-left: 450px;">
                    <h4>Chart Of Account</h4>
                </div>
                <div class="col-md-4" >
                    <div>
                        <p>
                            <img :src="cinfo.logo" style="width: 50px; height: 50px;">
                        </p>
                    </div>
                    <div class="">
                        <h3 style="font-weight: 600;font-size:20px; ">{{cinfo.name}}</h3>
                        <p>{{cinfo.address}}</p>
                        <p v-if="cinfo.address2">Address2:{{cinfo.address2}} </p>
                    </div>
                </div>
            </div>


            <br>
            <div class="table-responsive">
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                <tr>
                    <th>S/L</th>
                    <th>Main Code</th>
                    <th>Sub Code</th>
                    <th>Sub Code2</th>
                    <th>Chart Of A/C</th>
                </tr>
                </thead>
                <tbody>

                <tr v-for="(details, index) in coaData">
                    <td>{{++index}}</td>
                    <td>({{details.main_code}}) {{details.main_code_title}}</td>
                    <!--<td v-else></td>-->

                    <td>({{details.sub_code}}) {{details.sub_code_title}}</td>
                    <td>({{details.sub_code2}}) {{details.sub_code_title2}}</td>
                    <td>({{details.chart_of_account_code}}) {{details.chart_of_accounts_title}}</td>
                </tr>

                </tbody>
            </table>
            </div>
        </div>
    </div>
</template>

<script>
    import {EventBus} from '../../../vue-assets';
    import moment from 'moment';

    export default {

        props: [''],

        data: function () {
            return {
                chart_list: true,
                coaData:{},
                cinfo:{},

                errors: {},
            };
        },

        methods: {

            getChartOfAcct() {
                var _this = this;
                axios.get(base_url + 'print-chart-of-acct')
                    .then((response) => {
                        _this.coaData = response.data.data.coa_details;
                    });
            },

            comany_info(){
                var _this = this;
                var id = 1;
                axios.get(base_url+'Company-Info/'+id)
                    .then( (response) => {
                        _this.cinfo  = response.data;
                    });
            },


        },

        mounted(){
        },

        created(){
             var _this = this;
             _this.getChartOfAcct();
            _this.comany_info();
        },
    }
</script>

