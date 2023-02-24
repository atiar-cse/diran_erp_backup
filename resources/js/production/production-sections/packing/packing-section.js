require('./../../../vue-assets.js');
Vue.component('packing-section-list', require('./PackingSectionList.vue').default);

const app = new Vue({
	el: '#erp-app'
});