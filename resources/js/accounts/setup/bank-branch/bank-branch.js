require('./../../../vue-assets.js');
Vue.component('bank-branch-list',require('./BankBranchList.vue').default);

const app =new Vue({
    el: '#erp-app'
});