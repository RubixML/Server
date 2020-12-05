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
                stream: null,
            }
        };
    },
    apollo: {
        dashboard: gql`
            query GetDashboard {
                dashboard {
                    ...RequestsLevel
                    ...ResponseRateChart
                    ...TransfersLevel
                    ...ThroughputChart
                    ...ServerInfo
                    ...ServerSettings
                }
            }
            ${$options.fragments.requests}
            ${$options.fragments.memory}
            ${$options.fragments.transfers}
            ${$options.fragments.info}
            ${$options.fragments.settings}
        `,
    },
    mounted() {
        this.$sse('/server/dashboard/events', { format: 'json' }).then((stream) => {
            stream.subscribe('request-recorded', (message) => {
                this.httpStats.transfers.received += message.size;
            });

            stream.subscribe('response-recorded', (message) => {
                const code = message.code;

                if (code >= 100 && code < 400) {
                    this.httpStats.requests.successful++;
                } else if (code >= 400 && code < 500) {
                    this.httpStats.requests.rejected++;
                } else if (code >= 500) {
                    this.httpStats.requests.failed++;
                }

                this.httpStats.transfers.sent += message.size;
            });

            stream.subscribe('memory-usage-updated', (message) => {
                this.memory.current = message.current;
                this.memory.peak = message.peak;
            });

            this.stream = stream;
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