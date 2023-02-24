require('./../../../vue-assets.js');
Vue.component('generate-bonus-list', require('./GenerateBonusList.vue').default);

const app = new Vue({
	el: '#erp-app'
});