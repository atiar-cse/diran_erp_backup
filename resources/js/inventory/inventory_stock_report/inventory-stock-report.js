require('./../../vue-assets.js');
Vue.component('inventory-stock-report-list', require('./InventoryStockReportList.vue').default);

const app = new Vue({
	el: '#erp-app'
});
