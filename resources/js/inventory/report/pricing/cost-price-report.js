require('./../../../vue-assets.js');
Vue.component('cost-price-report-list', require('./InvCostPriceReportList.vue').default);

const app = new Vue({
	el: '#erp-app'
});
