require('./../../../vue-assets.js');
Vue.component('shift-schedule-list', require('./ShiftScheduleList.vue').default);

const app = new Vue({
    el: '#erp-app'
});
