require('./../../vue-assets.js');
Vue.component('inventory-current-stock-list', require('./InventoryCurrentStockList.vue').default);

const app = new Vue({
	el: '#erp-app'
});