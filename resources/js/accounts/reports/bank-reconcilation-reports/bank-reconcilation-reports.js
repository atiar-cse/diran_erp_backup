require('./../../../vue-assets.js');
Vue.component('bank-reconcilation-page', require('./BankReconcilationReportPage.vue').default);

const app = new Vue({
	el: '#erp-app'
});
