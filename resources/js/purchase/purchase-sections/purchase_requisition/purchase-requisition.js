require('./../../../vue-assets.js');
Vue.component('purchase-requisition-list', require('./PurchaseRequisitionList.vue').default);

const app = new Vue({
	el: '#erp-app'
});