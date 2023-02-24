require('./../../vue-assets.js');
Vue.component('proforma-invoice-entry-list', require('./ProformaInvoiceEntryList.vue').default);

const app = new Vue({
	el: '#erp-app'
});