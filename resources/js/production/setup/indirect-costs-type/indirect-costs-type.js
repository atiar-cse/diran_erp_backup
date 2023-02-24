require('./../../../vue-assets.js');
Vue.component('indirect-costs-type-list', require('./IndirectCostsTypeList.vue').default);

const app = new Vue({
	el: '#erp-app'
});