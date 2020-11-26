import Vue from 'vue';
import VueRouter from 'vue-router';
import VueAxios from 'vue-axios';
import VueSSE from 'vue-sse';
import routes from './routes';
import App from './App.vue';
import MainNav from './components/MainNav.vue';
import MainFooter from './components/MainFooter.vue';
import RequestsLevel from './components/RequestsLevel.vue';
import RequestRateChart from './components/RequestRateChart.vue';
import TransfersLevel from './components/TransfersLevel.vue';
import ThroughputChart from './components/ThroughputChart.vue';
import MemoryLevel from './components/MemoryLevel.vue';
import MemoryUsageChart from './components/MemoryUsageChart.vue';
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
Vue.component('request-rate-chart', RequestRateChart);
Vue.component('transfers-level', TransfersLevel);
Vue.component('throughput-chart', ThroughputChart);
Vue.component('memory-level', MemoryLevel);
Vue.component('memory-usage-chart', MemoryUsageChart);
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