require('./../../../vue-assets.js');
Vue.component('ledger-report-page', require('./LedgerReportPage.vue').default);

const app = new Vue({
	el: '#erp-app'
});
