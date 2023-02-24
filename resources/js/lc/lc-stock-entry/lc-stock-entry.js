require('./../../vue-assets.js');
Vue.component('lc-stock-entry-list',require('./LcStockEntryList.vue').default);

const app =new Vue({
    el: '#erp-app'
});