require('./../../vue-assets.js');
Vue.component('inventory-stock-transfer-list', require('./InventoryStockTransferList.vue').default);

const app = new Vue({
	el: '#erp-app'
});