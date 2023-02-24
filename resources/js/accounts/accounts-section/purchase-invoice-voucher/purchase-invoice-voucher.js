require('./../../../vue-assets.js');
Vue.component('purchase-invoice-voucher-list', require('./PurchaseInvoiceVoucherList.vue').default);

const app = new Vue({
	el: '#erp-app'
});