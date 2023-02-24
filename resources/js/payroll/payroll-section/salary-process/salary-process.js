require('./../../../vue-assets.js');
Vue.component('salary-process-list', require('./SalaryProcess.vue').default);

const app = new Vue({
	el: '#erp-app'
});