require('./../../../vue-assets.js');
Vue.component('trial-balance-page', require('./TrialBalanceReportPage.vue').default);

const app = new Vue({
	el: '#erp-app'
});
