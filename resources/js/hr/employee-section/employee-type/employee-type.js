require('./../../../vue-assets.js');
Vue.component('employee-type-list',require('./EmployeeTypeList.vue').default);

const app =new Vue({
    el: '#erp-app'
});