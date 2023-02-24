require('./../../../vue-assets.js');
Vue.component('purchase-return-list', require('./PurchaseReturnList.vue').default);

const app = new Vue({
	el: '#erp-app'
});