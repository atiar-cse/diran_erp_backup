require('./../../../vue-assets.js');
Vue.component('finished-stock-section-list', require('./FinishedStockSectionList.vue').default);

const app = new Vue({
	el: '#erp-app'
});