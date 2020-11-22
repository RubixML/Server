import Vue from 'vue';
import VueRouter from 'vue-router';
import VueAxios from 'vue-axios';
import VueSSE from 'vue-sse';
import routes from './routes';
import App from './App.vue';
import MainNav from './components/MainNav.vue';
import MainFooter from './components/MainFooter.vue';
import RequestsLevel from './components/RequestsLevel.vue';
import RequestsChart from './components/RequestsChart.vue';
import QueriesTable from './components/QueriesTable.vue';
import QueriesChart from './components/QueriesChart.vue';
import CommunicationError from './components/CommunicationError.vue';

const axios = require('axios');

require('./scss/app.scss');

/**
 * Register the background service worker.
 */

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js');
    });
}

/**
 * Register the Vue components and instantiate the Vue app.
 */

Vue.component('app', App);
Vue.component('main-nav', MainNav);
Vue.component('main-footer', MainFooter);
Vue.component('requests-level', RequestsLevel);
Vue.component('requests-chart', RequestsChart);
Vue.component('queries-table', QueriesTable);
Vue.component('queries-chart', QueriesChart);
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