/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
const axios = require('axios').default;

window.Vue = require('vue');
Vue.use(axios);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('variants-edit', require('./components/VariantsEdit.vue').default);
Vue.component('vicomponent', require('./components/ViComponent.vue').default);
Vue.component('vidash', require('./components/ViDashComponent.vue').default);

const app = new Vue({
    el: '#app',
});
