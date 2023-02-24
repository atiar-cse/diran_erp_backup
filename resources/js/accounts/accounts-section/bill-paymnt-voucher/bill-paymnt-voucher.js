require('./../../../vue-assets.js');
Vue.component('bill-paymnt-voucher-list', require('./BillPaymentVoucherList.vue').default);

const app = new Vue({
	el: '#erp-app'
});