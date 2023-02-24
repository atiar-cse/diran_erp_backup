require('./app-assets');

window.Vue = require('vue');

Vue.component('manage-role', require('./ManageRole.vue').default);

const app = new Vue({
    el: '#app'
});
