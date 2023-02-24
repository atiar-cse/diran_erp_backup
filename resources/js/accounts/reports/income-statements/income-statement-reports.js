require('./../../../vue-assets.js');
Vue.component('income-statement-page', require('./IncomeStatementReportPage.vue').default);

const app = new Vue({
	el: '#erp-app'
});
