require('./../../../vue-assets.js');
Vue.component('balance-sheet-page', require('./BalanceSheetReportPage.vue').default);

const app = new Vue({
	el: '#erp-app'
});
