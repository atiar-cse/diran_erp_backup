require('./../../../vue-assets.js');
Vue.component('product-brand-list', require('./ProductBrandList.vue').default);

const app = new Vue({
	el: '#erp-app'
});