require('./../../../vue-assets.js');
Vue.component('bank-account-list',require('./BankAccountList.vue').default);

const app =new Vue({
    el: '#erp-app'
});