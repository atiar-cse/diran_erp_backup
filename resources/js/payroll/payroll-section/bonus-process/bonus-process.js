require('./../../../vue-assets.js');
Vue.component('bonus-process-list', require('./BonusProcess.vue').default);

const app = new Vue({
	el: '#erp-app'
});