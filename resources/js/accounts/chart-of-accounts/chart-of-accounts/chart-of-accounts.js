require('./../../../vue-assets.js');
Vue.component('chart-of-accounts-list',require('./ChartOfAccountList.vue').default);

const app =new Vue({
    el: '#erp-app'
});