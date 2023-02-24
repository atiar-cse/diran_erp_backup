require('./../../../vue-assets.js');
Vue.component('purchase-related-cost-entry-list', require('./PurchaseRelatedCostEntryList.vue').default);

const app = new Vue({
	el: '#erp-app'
});