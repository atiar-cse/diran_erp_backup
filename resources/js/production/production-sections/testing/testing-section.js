require('./../../../vue-assets.js');
Vue.component('testing-section-list', require('./TestingSectionList.vue').default);

const app = new Vue({
	el: '#erp-app'
});