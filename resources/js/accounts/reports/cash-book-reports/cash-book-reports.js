require('./../../../vue-assets.js');
Vue.component('cash-book-report-list', require('./CashBookReportPage.vue').default);

const app = new Vue({
	el: '#erp-app'
});
