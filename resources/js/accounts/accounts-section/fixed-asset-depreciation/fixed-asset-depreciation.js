require('./../../../vue-assets.js');
Vue.component('fixed-asset-depreciation-list', require('./FixedAssetDepreciationList.vue').default);

const app = new Vue({
	el: '#erp-app'
});