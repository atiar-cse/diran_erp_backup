require('./../../../vue-assets.js');
Vue.component('kiln-section-list', require('./KilnSectionList.vue').default);

const app = new Vue({
	el: '#erp-app'
});