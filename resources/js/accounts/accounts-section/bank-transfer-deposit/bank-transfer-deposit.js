require('./../../../vue-assets.js');
Vue.component('bank-transfer-deposit-list', require('./BankTransferDepositList.vue').default);

const app = new Vue({
	el: '#erp-app'
});