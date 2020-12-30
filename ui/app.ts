import Vue from 'vue';
import VueRouter from 'vue-router';
import VueApollo from 'vue-apollo';
import VueSSE from 'vue-sse';
import App from './App.vue';
import MainNav from './components/MainNav.vue';
import MainFooter from './components/MainFooter.vue';
import CSVLoader from './components/CSVLoader.vue';
import NDJSONLoader from './components/NDJSONLoader.vue';
import DatasetPreview from './components/DatasetPreview.vue';
import ExportChart from './components/ExportChart.vue';
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
import CommunicationError from './components/CommunicationError.vue';
import DatasetImportFailure from './components/DatasetImportFailure.vue';
import { ApolloClient } from 'apollo-client';
import { createHttpLink } from 'apollo-link-http';
import { InMemoryCache } from 'apollo-cache-inmemory';
import routes from './routes';

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
 * Boot up the Apollo Graph QL client.
 */

const httpLink = createHttpLink({
    uri: '/graphql',
});

const cache = new InMemoryCache();
  
const apolloClient = new ApolloClient({
    link: httpLink,
    cache,
});

/**
 * Register the Vue components and instantiate the Vue app.
 */

Vue.component('app', App);
Vue.component('main-nav', MainNav);
Vue.component('main-footer', MainFooter);
Vue.component('csv-loader', CSVLoader);
Vue.component('ndjson-loader', NDJSONLoader);
Vue.component('dataset-preview', DatasetPreview);
Vue.component('export-chart', ExportChart);
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
Vue.component('communication-error', CommunicationError);
Vue.component('dataset-import-failure', DatasetImportFailure);

Vue.use(VueRouter);
Vue.use(VueApollo);
Vue.use(VueSSE);

const router = new VueRouter({
    mode: 'history',
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
