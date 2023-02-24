<template>
    <div>
        <div class="m-portlet__body">
            <!--begin: Datatable -->
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                <tr>
                    <th class="w-20">For Days</th>
                    <th class="w-20">Day Of Salary Deduction</th>
                    <th class="w-20">Status</th>
                    <th class="w-20">Update</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <input type="hidden" name="id" readonly class="form-control form-control-sm m-input"  placeholder="3">
                        <input type="number" name="for_days"   class="form-control form-control-sm m-input"  placeholder="3">
                    </td>
                    <td>
                        <input type="number" name="day_of_salary_deduction"  class="form-control form-control-sm m-input"  placeholder="1">
                    </td>
                    <td>
                        <select class="form-control select2 select-product" name="status" style="width:100%;">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                    </td>
                    <td>
                       <input type="button" @click="updateSalaryRules()" class="btn btn-sm btn-success updateRule" value="Update">
                    </td>

                </tr>
                </tbody>
            </table >


            <!--<div class="row">
                <div class="text-center col-md-12" >
                    <pagination :resultData="resultData" @clicked="index" :mid-size="9"></pagination>
                </div>
            </div>-->
        </div>
    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
   // import Pagination from  '../../../components/Pagination.vue';

    export default {
        /*components: {
            Pagination
        },*/

        props:['permissions'],

        data: function(){
            return {
                resultData: {},

            };
        },

        methods: {

           /* updateSalaryRules(){
                var _this = this;
                axios.post(base_url + 'update-rules-of-salary').then((response) => {
                    _this.showMassage(response.data);
                });

            },*/

            updateSalaryRules(){

                var _this = this;
                swal({
                        title: "Are you sure?",
                        text: "You want to permanent this information!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, Permanent it!",
                        closeOnConfirm: false
                    },
                    function(){
                        //alert(id);
                        axios.post(base_url + 'rules-of-salary-deduction/update-rules-of-salary/'+id).then((response) => {
                            //_this.index(1);
                            _this.showMassage(response.data);
                            swal("Permanent!", "Your imaginary file has been permanent.", "success");
                            location.reload();
                        }).catch((error)=>{
                            swal('Error:',error,'error');

                        });

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

        created() {

        }

    }
</script>

