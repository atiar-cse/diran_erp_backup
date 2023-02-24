require('./../../../vue-assets.js');
Vue.component('cost-of-production-report', require('./CostOfProductionReport.vue').default);

const app = new Vue({
	el: '#erp-app'
});
