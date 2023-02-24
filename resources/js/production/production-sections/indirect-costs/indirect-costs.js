require('./../../../vue-assets.js');
Vue.component('indirect-costs-list', require('./IndirectCostsList.vue').default);

const app = new Vue({
	el: '#erp-app'
});