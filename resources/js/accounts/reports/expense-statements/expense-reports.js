require('./../../../vue-assets.js');
Vue.component('expense-statement-page', require('./ExpenseReportPage.vue').default);

const app = new Vue({
	el: '#erp-app'
});
