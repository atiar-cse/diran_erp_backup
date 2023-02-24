require('./../../../vue-assets.js');
Vue.component('sales-summary-report-list', require('./SalesSummaryReportList.vue').default);

const app = new Vue({
    el: '#erp-app'
});
