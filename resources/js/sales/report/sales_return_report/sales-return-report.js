require('./../../../vue-assets.js');
Vue.component('sales-return-report-list', require('./SalesReturnReportList.vue').default);

const app = new Vue({
	el: '#erp-app'
});
