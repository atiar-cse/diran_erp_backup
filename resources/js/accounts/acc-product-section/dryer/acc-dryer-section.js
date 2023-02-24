require('./../../../vue-assets.js');
Vue.component('dryer-section-list', require('./AccDryerSectionList.vue').default);

const app = new Vue({
	el: '#erp-app'
});