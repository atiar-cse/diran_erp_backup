require('./../../../vue-assets.js');
Vue.component('cash-payment-voucher-list', require('./CashPaymentVoucherList.vue').default);

const app = new Vue({
	el: '#erp-app'
});