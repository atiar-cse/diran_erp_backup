require('./../../../vue-assets.js');
Vue.component('shift-manage-list', require('./ShiftManageList.vue').default);

const app = new Vue({
    el: '#erp-app'
});
