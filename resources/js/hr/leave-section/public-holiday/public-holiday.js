require('./../../../vue-assets.js');
Vue.component('public-holiday-list',require('./PublicHolidayList.vue').default);

const app =new Vue({
    el: '#erp-app'
});