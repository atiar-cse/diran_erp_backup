require('./../../../vue-assets.js');
Vue.component('finished-stock-section-list', require('./AccFinishedStockSectionList.vue').default);

const app = new Vue({
	el: '#erp-app'
});