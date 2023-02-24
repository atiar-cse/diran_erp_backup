require('./../../vue-assets.js');
Vue.component('lc-ledger-report-page', require('./LcLedgerReportPage.vue').default);

const app = new Vue({
	el: '#erp-app'
});
