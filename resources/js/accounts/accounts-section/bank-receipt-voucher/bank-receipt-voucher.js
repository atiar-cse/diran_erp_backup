require('./../../../vue-assets.js');
Vue.component('bank-receipt-voucher-list', require('./BankReceiptVoucherList.vue').default);

const app = new Vue({
	el: '#erp-app'
});