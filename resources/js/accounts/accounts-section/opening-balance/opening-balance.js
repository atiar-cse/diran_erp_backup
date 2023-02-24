require('./../../../vue-assets.js');
Vue.component('opening-balance-list', require('./OpeningBalanceList.vue').default);

const app = new Vue({
	el: '#erp-app'
});