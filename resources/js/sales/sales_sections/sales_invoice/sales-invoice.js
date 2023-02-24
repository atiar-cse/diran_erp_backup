require('./../../../vue-assets.js');
Vue.component('sales-invoice-list', require('./SalesInvoiceList.vue').default);

const app = new Vue({
	el: '#erp-app'
});