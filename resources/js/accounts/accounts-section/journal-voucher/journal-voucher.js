require('./../../../vue-assets.js');
Vue.component('journal-voucher-list', require('./JournalVoucherList.vue').default);

const app = new Vue({
	el: '#erp-app'
});