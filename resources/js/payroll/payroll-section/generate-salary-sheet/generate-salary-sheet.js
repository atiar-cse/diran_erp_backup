require('./../../../vue-assets.js');
Vue.component('generate-salary-sheet-list', require('./GenerateSalarySheetList.vue').default);

const app = new Vue({
	el: '#erp-app'
});