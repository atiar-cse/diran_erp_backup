require('./../../../vue-assets.js');
Vue.component('section-list', require('./SectionList.vue').default);

const app = new Vue({
    el: '#erp-app'
});
