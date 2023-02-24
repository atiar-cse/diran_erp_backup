require('./../../../vue-assets.js');
Vue.component('cash-receipt-voucher-list', require('./CashReceiptVoucherList.vue').default);

const app = new Vue({
	el: '#erp-app'
});