require('./../../../vue-assets.js');
Vue.component('product-entry-list', require('./ProductsEntryList.vue').default);

const app = new Vue({
	el: '#erp-app'
});