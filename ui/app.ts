import Vue from 'vue';
import VueRouter from 'vue-router';
import VueApollo from 'vue-apollo';
import VueSSE from 'vue-sse';
import App from './App.vue';
import MainNav from './components/MainNav.vue';
import MainFooter from './components/MainFooter.vue';
import RequestsLevel from './components/RequestsLevel.vue';
import ResponseRateChart from './components/ResponseRateChart.vue';
import TransfersLevel from './components/TransfersLevel.vue';
import ThroughputChart from './components/ThroughputChart.vue';
import InferenceLevel from './components/InferenceLevel.vue';
import InferenceRateChart from './components/InferenceRateChart.vue';
import MemoryLevel from './components/MemoryLevel.vue';
import MemoryUsageChart from './components/MemoryUsageChart.vue';
import ProcessInfo from './components/ProcessInfo.vue';
import ServerSettings from './components/ServerSettings.vue';
import AppUpdateAvailable from './components/AppUpdateAvailable.vue';
import CommunicationError from './components/CommunicationError.vue';
import { apolloClient } from './providers/apollo';
import { Workbox } from 'workbox-window';
import routes from './routes';
import bus from  './providers/bus';

require('./scss/app.scss');

/**
 * Register the service worker.
 */

if ('serviceWorker' in navigator) {
    const wb = new Workbox('/sw.js');

    wb.addEventListener('waiting', (event) => {
        if (event.isUpdate) {
            bus.$emit('update-ready');
        }
    });

    bus.$on('update-accepted', () => {
        wb.addEventListener('controlling', () => {
            window.location.reload();
        });
        
        wb.messageSkipWaiting();
    });
  
    wb.register();
}

/**
 * Register the Vue components and instantiate the Vue app.
 */

Vue.component('app', App);
Vue.component('main-nav', MainNav);
Vue.component('main-footer', MainFooter);
Vue.component('requests-level', RequestsLevel);
Vue.component('response-rate-chart', ResponseRateChart);
Vue.component('transfers-level', TransfersLevel);
Vue.component('throughput-chart', ThroughputChart);
Vue.component('inference-level', InferenceLevel);
Vue.component('inference-rate-chart', InferenceRateChart);
Vue.component('memory-level', MemoryLevel);
Vue.component('memory-usage-chart', MemoryUsageChart);
Vue.component('process-info', ProcessInfo);
Vue.component('server-settings', ServerSettings);
Vue.component('app-update-available', AppUpdateAvailable);
Vue.component('communication-error', CommunicationError);

Vue.use(VueRouter);
Vue.use(VueApollo);
Vue.use(VueSSE);

const router = new VueRouter({
    mode: 'history',
    base: '/ui/',
    routes,
});

const apolloProvider = new VueApollo({
    defaultClient: apolloClient,
});

const app = new Vue({
    el: '#app',
    router,
    apolloProvider,
});
