require('./../../../vue-assets.js');
Vue.component('manage-employee-list',require('./ManageEmployeeList.vue').default);

const app =new Vue({
    el: '#erp-app'
});