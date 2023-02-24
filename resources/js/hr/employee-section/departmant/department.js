require('./../../../vue-assets.js');
Vue.component('department-list',require('./DepartmantList.vue').default);

const app =new Vue({
    el: '#erp-app'
});