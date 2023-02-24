require('./../../../vue-assets.js');
Vue.component('daily-statement-page', require('./DailyStatementReportPage.vue').default);

const app = new Vue({
	el: '#erp-app'
});
