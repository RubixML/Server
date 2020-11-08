import Vue from 'vue';
import VueRouter from 'vue-router';
import VueAxios from 'vue-axios';
import VueSSE from 'vue-sse';
import routes from './routes';
import App from './App.vue';
import MainNav from './components/MainNav.vue';
import CommunicationError from './components/CommunicationError.vue';

const axios = require('axios');

require('./scss/app.scss');

Vue.component('app', App);
Vue.component('main-nav', MainNav);
Vue.component('communication-error', CommunicationError);

Vue.use(VueRouter);
Vue.use(VueAxios, axios);
Vue.use(VueSSE);

const router = new VueRouter({
    mode: 'history',
    routes,
});

const app = new Vue({
    el: '#app',
    router,
});