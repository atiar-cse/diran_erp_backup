require('./../../../vue-assets.js');
Vue.component('position-list', require('./PositionList.vue').default);

const app = new Vue({
    el: '#erp-app'
});
