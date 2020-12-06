<template>
    <div>
        <section class="section">
            <div class="container">
                <requests-level v-if="dashboard.httpStats" :requests="dashboard.httpStats.requests"></requests-level>
                <response-rate-chart v-if="dashboard.httpStats" :requests="dashboard.httpStats.requests"></response-rate-chart>
            </div>
        </section>
        <section class="section">
            <div class="container">
                <div class="columns">
                    <div class="column is-half">
                        <transfers-level v-if="dashboard.httpStats" :transfers="dashboard.httpStats.transfers"></transfers-level>
                        <throughput-chart v-if="dashboard.httpStats" :transfers="dashboard.httpStats.transfers"></throughput-chart>
                    </div>
                    <div class="column is-half">
                        <memory-level v-if="dashboard.memory" :memory="dashboard.memory"></memory-level>
                        <memory-usage-chart v-if="dashboard.memory" :memory="dashboard.memory"></memory-usage-chart>
                    </div>
                </div>
            </div>
        </section>
        <section class="section">
            <div class="container">
                <div class="columns">
                    <div class="column is-half">
                        <h2 class="title">Information<span class="icon ml-4"><i class="fas fa-info-circle"></i></span></h2>
                        <server-info v-if="dashboard.info" :info="dashboard.info"></server-info>
                    </div>
                    <div class="column is-half">
                        <h2 class="title">Settings<span class="icon ml-5"><i class="fas fa-cogs"></i></span></h2>
                        <server-settings v-if="dashboard.settings" :settings="dashboard.settings"></server-settings>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import { fragment as RequestsLevelFragment } from '../components/RequestsLevel.vue';
import { fragment as ResponseRateChartFragment } from '../components/ResponseRateChart.vue';
import { fragment as TransfersLevelFragment } from '../components/TransfersLevel.vue';
import { fragment as ThroughputChartFragment } from '../components/ThroughputChart.vue';
import { fragment as MemoryLevelFragment } from '../components/MemoryLevel.vue';
import { fragment as MemoryUsageChartFragment } from '../components/MemoryUsageChart.vue';
import { fragment as ServerInfoFragment } from '../components/ServerInfo.vue';
import { fragment as ServerSettingsFragment } from '../components/ServerSettings.vue';
import gql from 'graphql-tag';
import bus from '../bus';

export default {
    data() {
        return {
            dashboard: {
                httpStats: undefined,
                memory: undefined,
                info: undefined,
                settings: undefined,
            },
            stream: null,
        };
    },
    mounted() {
        this.$apollo.query({
            query: gql`
                query getDashboard {
                    dashboard {
                        ...RequestsLevel
                        ...ResponseRateChart
                        ...TransfersLevel
                        ...ThroughputChart
                        ...MemoryLevel
                        ...MemoryUsageChart
                        ...ServerInfo
                        ...ServerSettings
                    }
                }
                ${RequestsLevelFragment}
                ${ResponseRateChartFragment}
                ${TransfersLevelFragment}
                ${ThroughputChartFragment}
                ${MemoryLevelFragment}
                ${MemoryUsageChartFragment}
                ${ServerInfoFragment}
                ${ServerSettingsFragment}
            `,
        }).then((response) => {
            this.dashboard = response.data.dashboard;

            this.$sse('/server/dashboard/events', { format: 'json' }).then((stream) => {
                stream.subscribe('request-recorded', (message) => {
                    this.dashboard.httpStats.transfers.received += message.size;
                });

                stream.subscribe('response-recorded', (message) => {
                    const code = message.code;

                    if (code >= 100 && code < 400) {
                        this.dashboard.httpStats.requests.successful++;
                    } else if (code >= 400 && code < 500) {
                        this.dashboard.httpStats.requests.rejected++;
                    } else if (code >= 500) {
                        this.dashboard.httpStats.requests.failed++;
                    }

                    this.dashboard.httpStats.transfers.sent += message.size;
                });

                stream.subscribe('memory-usage-updated', (message) => {
                    this.dashboard.memory.current = message.current;
                    this.dashboard.memory.peak = message.peak;
                });

                this.stream = stream;
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
}
</script>