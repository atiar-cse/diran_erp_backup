require('./../../../vue-assets.js');
Vue.component('team-list', require('./TeamList.vue').default);

const app = new Vue({
    el: '#erp-app'
});
