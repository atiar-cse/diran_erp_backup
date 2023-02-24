require('./../../../vue-assets.js');
Vue.component('semi-finished-stock', require('./SemiFinishedStock.vue').default);

const app = new Vue({
	el: '#erp-app'
});