require('./../../../vue-assets.js');
Vue.component('year-end-list', require('./YearEndList.vue').default);

const app = new Vue({
	el: '#erp-app'
});