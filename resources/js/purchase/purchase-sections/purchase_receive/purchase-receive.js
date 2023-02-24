require('./../../../vue-assets.js');
Vue.component('purchase-receive-list', require('./PurchaseReceiveList.vue').default);

const app = new Vue({
	el: '#erp-app'
});