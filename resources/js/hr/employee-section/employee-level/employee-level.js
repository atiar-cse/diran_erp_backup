require('./../../../vue-assets.js');
Vue.component('employee-level-list', require('./EmployeeLevelList.vue').default);

const app = new Vue({
    el: '#erp-app'
});
