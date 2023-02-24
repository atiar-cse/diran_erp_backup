require('./../../vue-assets.js');
Vue.component('inventory-stock-adjust-list', require('./InventoryStockAdjustList.vue').default);

const app = new Vue({
	el: '#erp-app'
});