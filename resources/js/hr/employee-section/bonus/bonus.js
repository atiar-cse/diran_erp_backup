require('./../../../vue-assets.js');
Vue.component('bonus-list', require('./BonusList.vue').default);

const app = new Vue({
    el: '#erp-app'
});
