require('./../../../vue-assets.js');
Vue.component('purchase-report-list', require('./PurchaseReportList.vue').default);

const app = new Vue({
	el: '#erp-app'
});
