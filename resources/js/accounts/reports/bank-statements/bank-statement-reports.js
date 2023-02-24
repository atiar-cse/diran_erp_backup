require('./../../../vue-assets.js');
Vue.component('bank-statement-page', require('./BankStatementReportPage.vue').default);

const app = new Vue({
	el: '#erp-app'
});
