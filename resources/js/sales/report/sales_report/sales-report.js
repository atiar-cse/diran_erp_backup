require('./../../../vue-assets.js');
Vue.component('sales-report-list', require('./SalesReportList.vue').default);

const app = new Vue({
	el: '#erp-app'
});
