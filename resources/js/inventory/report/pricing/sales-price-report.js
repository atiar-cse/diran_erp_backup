require('./../../../vue-assets.js');
Vue.component('sales-price-report-list', require('./InvSalesPriceReportList.vue').default);

const app = new Vue({
	el: '#erp-app'
});
