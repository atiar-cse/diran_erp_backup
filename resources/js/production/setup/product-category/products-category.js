require('./../../../vue-assets.js');
Vue.component('product-category-list', require('./ProductCategoryList.vue').default);

const app = new Vue({
	el: '#erp-app'
});