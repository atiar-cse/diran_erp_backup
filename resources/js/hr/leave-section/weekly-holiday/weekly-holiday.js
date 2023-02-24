require('./../../../vue-assets.js');
Vue.component('weekly-holiday-list',require('./WeeklyHolidayList.vue').default);

const app =new Vue({
    el: '#erp-app'
});