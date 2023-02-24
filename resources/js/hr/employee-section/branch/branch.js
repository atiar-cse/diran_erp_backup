require('./../../../vue-assets.js');
Vue.component('branch-list',require('./BranchList.vue').default);

const app =new Vue({
    el: '#erp-app'
});