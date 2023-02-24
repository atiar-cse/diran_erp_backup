require('./../../../vue-assets.js');
Vue.component('sales-customer-list',require('./SalesCustomerList.vue').default);

const app =new Vue({
    el: '#erp-app'
});
