require('./../../../vue-assets.js');
Vue.component('kiln-section-list', require('./AccKilnSectionList.vue').default);

const app = new Vue({
	el: '#erp-app'
});