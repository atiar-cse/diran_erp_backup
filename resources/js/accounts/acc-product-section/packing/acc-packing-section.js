require('./../../../vue-assets.js');
Vue.component('packing-section-list', require('./AccPackingSectionList.vue').default);

const app = new Vue({
	el: '#erp-app'
});