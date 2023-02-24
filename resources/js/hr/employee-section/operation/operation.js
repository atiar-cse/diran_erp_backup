require('./../../../vue-assets.js');
Vue.component('operation-list', require('./OperationList.vue').default);

const app = new Vue({
    el: '#erp-app'
});
