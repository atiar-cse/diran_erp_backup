require('./../../vue-assets.js');
Vue.component('inventory-product-damage-list', require('./InventoryProductDamageList.vue').default);

const app = new Vue({
	el: '#erp-app'
});