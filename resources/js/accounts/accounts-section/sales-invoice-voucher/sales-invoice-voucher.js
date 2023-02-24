require('./../../../vue-assets.js');
Vue.component('sales-invoice-voucher-list', require('./SalesInvoiceVoucherList.vue').default);

const app = new Vue({
	el: '#erp-app'
});