<template>
    <div>
        <div class="m-portlet__body">
            <div class="item-show-limit">
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
            <br><br>
            <!--begin: Datatable -->
            <table class="table table-bordered table-hover" id="m_table_1">
                <thead>
                <tr>
                    <th class="w-20">S/L</th>
                    <th class="w-20">Total Income</th>
                    <th class="w-20">Tax Rate</th>
                    <th class="w-20">Taxable amount</th>
                    <th class="w-20">Update</th>
                </tr>
                </thead>

                <!--<tbody v-if="resultData.data !=''">
                <tr v-for="(value,index) in resultData.data">
                    <td>{{++index}}</td>
                    <td>{{value.product_category_name}}</td>
                    <td>{{value.product_category_name}}</td>
                    <td>{{value.product_category_name}}</td>
                    <td scope="row" class="width-100 text-center">
                        <a  @click="openUpdateModal(value.id)"  class="btn btn-outline-success m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Edit"><i class="la 	la-edit"></i></a>
                        <a  @click="deleteData(value.id)" class="btn btn-outline-danger m-btn m-btn&#45;&#45;icon m-btn&#45;&#45;icon-only m-btn&#45;&#45;pill m-btn&#45;&#45;air" data-offset="-20px -20px" data-toggle="m-tooltip" data-placement="top" title="Delete"><i class="la la-trash-o"></i></a>
                        &lt;!&ndash;<a v-if="permissions.indexOf('businessTypeRegistrationDestroy') !=-1"  @click="deleteData(value.id)" class="btn btn-danger btn-sm"><i aria-hidden="true" class="fa fa-trash-o btnColor"></i></a>
                        <a v-if="permissions.indexOf('businessTypeRegistrationEdit') !=-1" @click="openUpdateModal(value.id)" class="btn btn-primary btn-sm"><i aria-hidden="true" class="fa fa-pencil-square-o btnColor"></i></a>&ndash;&gt;
                    </td>
                </tr>
                </tbody>-->
                <!--<tbody v-else>
                <tr>
                    <td colspan="7">No Data Available.</td>
                </tr>
                </tbody>-->
            </table >
            <div class="font-weight-bold">Tax Rules (Male)</div>
            <table class="table table-bordered table-hover" id="m_table_2">
                <tbody>
                <tr>
                    <td class="w-20">First</td>
                    <td class="w-20">
                        <input type="number"  class="form-control form-control-sm m-input" placeholder="Enter 1000 kg ratio">
                    </td>
                    <td class="w-20">
                        <select class="form-control select2 select-product"  style="width:100%;">
                            <option>0%</option>
                        </select>
                    </td>
                    <td class="w-20">
                        <input type="number"  class="form-control form-control-sm m-input" placeholder="0">
                    </td>
                    <td class="w-20">
                        <input type="submit"  class="btn btn-sm btn-primary" value="Update">
                    </td>

                </tr>

                </tbody>

            </table>
            <div class="font-weight-bold">Tax Rules (Female)</div>
            <table class="table table-bordered table-hover" id="m_table_3">
                <tbody>
                <tr>
                    <td class="w-20">First</td>
                    <td class="w-20">
                        <input type="number"  class="form-control form-control-sm m-input" placeholder="Enter 1000 kg ratio">
                    </td>
                    <td class="w-20">
                        <select class="form-control select2 select-product"  style="width:100%;">
                            <option>0%</option>
                        </select>
                    </td>
                    <td class="w-20">
                        <input type="number"  class="form-control form-control-sm m-input" placeholder="0">
                    </td>
                    <td class="w-20">
                        <input type="submit"  class="btn btn-sm btn-primary" value="Update">
                    </td>

                </tr>

                </tbody>

            </table>
            <div class="row">
                <div class="text-center col-md-12" >
                    <pagination :resultData="resultData" @clicked="index" :mid-size="9"></pagination>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

    import { EventBus } from '../../../vue-assets';
    import Pagination from  '../../../components/Pagination.vue';

    export default {
        components: {
            Pagination
        },

        props:['permissions'],

        data: function(){
            return {
                resultData: {},
                perPage: 10
            };
        },

        methods: {
            index(pageNo,perPage){
                if(pageNo){ pageNo = pageNo; }else{pageNo = this.resultData.current_page; }
                if(perPage){ perPage = perPage;}else{ perPage = this.perPage;}

                axios.get(base_url+"production-category/?page="+pageNo+"&perPage="+perPage).then((response) => {
                    this.resultData = response.data;

                });
            },

            /*deleteData: function(id){
             var _this = this;

             swal({
             title: "Are you sure?",
             text: "You will not be able to recover this file!",
             icon: "warning",
             showCancelButton: true,
             buttons: {
             cancel: {
             text: "No, cancel pls!",
             value: null,
             visible: true,
             className: "btn-warning",
             closeModal: true,
             },
             confirm: {
             text: "Yes, delete it!",
             value: true,
             visible: true,
             className: "",
             closeModal: true
             }
             }
             }).then(isConfirm => {
             if (isConfirm) {
             axios.get(base_url+'production-category/'+id+'/delete').then((response) => {
             _this.index(1);
             _this.showMassage(response.data);
             }).catch((error)=>{
             _this.showMassage(response.data);
             });
             }
             });
             },*/

            deleteData(id){
                var _this = this;
                swal({
                        title: "Are you sure?",
                        text: "You will not be able to recover this information!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false
                    },
                    function(){
                        swal("Deleted!", "Your imaginary file has been deleted.", "success");
                        axios.delete(base_url+'production-category/'+id+'/delete').then((response) => {
                            _this.index(1);
                            _this.showMassage(response.data);
                        }).catch((error)=>{
                            swal('Error:','Something Error Found !, Please try again','error');
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

            openUpdateModal(id){
                EventBus.$emit('update-button-clicked', id);
            },

            changePerPage(){
                var vm = this;
                vm.index(1,vm.perPage);
            },

        },

        created() {
            var _this = this;
            _this.index(1);
            EventBus.$on('new-data-created', function () {
                _this.index(1);
            });
        }

    }
</script>

