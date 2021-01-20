<template>
    <div>
        <section class="section">
            <div class="container">
                <requests-level v-if="server.httpStats" :requests="server.httpStats.requests"></requests-level>
                <response-rate-chart v-if="server.httpStats" :requests="server.httpStats.requests"></response-rate-chart>
            </div>
        </section>
        <section class="section">
            <div class="container">
                <div class="columns">
                    <div class="column is-half">
                        <transfers-level v-if="server.httpStats" :transfers="server.httpStats.transfers"></transfers-level>
                        <throughput-chart v-if="server.httpStats" :transfers="server.httpStats.transfers"></throughput-chart>
                    </div>
                    <div class="column is-half">
                        <inference-level v-if="model" :model="model"></inference-level>
                        <inference-rate-chart v-if="model" :model="model"></inference-rate-chart>
                    </div>
                </div>
            </div>
        </section>
        <section class="section">
            <div class="container">
                <div class="columns">
                    <div class="column is-half">
                        <h2 class="title is-size-4"><span class="icon mr-3"><i class="fas fa-cog"></i></span>Settings</h2>
                        <server-settings v-if="server.settings" :settings="server.settings"></server-settings>
                        <h2 class="title is-size-4"><span class="icon mr-3"><i class="fas fa-microchip"></i></span>Process Info</h2>
                        <process-info v-if="server.info" :info="server.info"></process-info>
                    </div>
                    <div class="column is-half">
                        <memory-level v-if="server.memory" :memory="server.memory"></memory-level>
                        <memory-usage-chart v-if="server.memory" :memory="server.memory"></memory-usage-chart>
                    </div>
                </div>
            </div>
        </section>
        <page-loader :loading="loading"></page-loader>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import { fragment as RequestsLevelFragment } from '../components/RequestsLevel.vue';
import { fragment as ResponseRateChartFragment } from '../components/ResponseRateChart.vue';
import { fragment as TransfersLevelFragment } from '../components/TransfersLevel.vue';
import { fragment as ThroughputChartFragment } from '../components/ThroughputChart.vue';
import { fragment as InferenceLevelFragment } from '../components/InferenceLevel.vue';
import { fragment as InferenceRateChartFragment } from '../components/InferenceRateChart.vue';
import { fragment as MemoryLevelFragment } from '../components/MemoryLevel.vue';
import { fragment as MemoryUsageChartFragment } from '../components/MemoryUsageChart.vue';
import { fragment as ProcessInfoFragment } from '../components/ProcessInfo.vue';
import { fragment as ServerSettingsFragment } from '../components/ServerSettings.vue';
import gql from 'graphql-tag';
import bus from '../bus';

export default Vue.extend({
    data() {
        return {
            server: {
                httpStats: null,
                memory: null,
                info: null,
                settings: null,
            },
            model: null,
            stream: null,
            loading: false,
        };
    },
    mounted() {
        this.loading = true;

        this.$apollo.query({
            query: gql`
                query getDashboard {
                    server {
                        ...RequestsLevel
                        ...ResponseRateChart
                        ...TransfersLevel
                        ...ThroughputChart
                        ...MemoryLevel
                        ...ServerSettings
                        ...MemoryUsageChart
                        ...ProcessInfo
                    }
                    model {
                        ...InferenceLevel
                        ...InferenceRateChart
                    }
                }
                ${RequestsLevelFragment}
                ${ResponseRateChartFragment}
                ${TransfersLevelFragment}
                ${ThroughputChartFragment}
                ${InferenceLevelFragment}
                ${InferenceRateChartFragment}
                ${MemoryLevelFragment}
                ${MemoryUsageChartFragment}
                ${ProcessInfoFragment}
                ${ServerSettingsFragment}
            `,
        }).then((response) => {
            this.server = response.data.server;
            this.model = response.data.model;

            this.$sse('/dashboard/events', { format: 'json' }).then((stream) => {
                stream.subscribe('request-received', (message) => {
                    this.server.httpStats.transfers.received += message.size;
                });

                stream.subscribe('response-sent', (message) => {
                    const code = message.code;

                    if (code >= 100 && code < 400) {
                        this.server.httpStats.requests.successful++;
                    } else if (code >= 400 && code < 500) {
                        this.server.httpStats.requests.rejected++;
                    } else if (code >= 500) {
                        this.server.httpStats.requests.failed++;
                    }

                    this.server.httpStats.transfers.sent += message.size;
                });

                stream.subscribe('dataset-inferred', (message) => {
                    this.model.numSamplesInferred += message.numSamples;
                });

                stream.subscribe('memory-usage-updated', (message) => {
                    this.server.memory.current = message.current;
                    this.server.memory.peak = message.peak;
                });

                this.stream = stream;

                this.loading = false;
            });
        }).catch((error) => {
            bus.$emit('communication-error', {
                error,
            });
        });
    },
    beforeDestroy() {
        if (this.stream) {
            this.stream.close();
        }
    },
});
</script>
