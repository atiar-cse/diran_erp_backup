require('./../../../vue-assets.js');
Vue.component('bank-payment-voucher-list', require('./BankPaymentVoucherList.vue').default);

const app = new Vue({
	el: '#erp-app'
});