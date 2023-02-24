require('./../../../vue-assets.js');
Vue.component('testing-section-list', require('./AccTestingSectionList.vue').default);

const app = new Vue({
	el: '#erp-app'
});