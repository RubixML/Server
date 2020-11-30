<template>
    <div>
        <section class="section">
            <div class="container">
                <requests-level v-if="httpStats" :requests="httpStats.requests"></requests-level>
                <response-rate-chart v-if="httpStats" :requests="httpStats.requests"></response-rate-chart>
            </div>
        </section>
        <section class="section">
            <div class="container">
                <div class="columns">
                    <div class="column is-half">
                        <transfers-level v-if="httpStats" :transfers="httpStats.transfers"></transfers-level>
                        <throughput-chart v-if="httpStats" :transfers="httpStats.transfers"></throughput-chart>
                    </div>
                    <div class="column is-half">
                        <memory-level v-if="memory" :memory="memory"></memory-level>
                        <memory-usage-chart v-if="memory" :memory="memory"></memory-usage-chart>
                    </div>
                </div>
            </div>
        </section>
        <section class="section">
            <div class="container">
                <div class="columns">
                    <div class="column is-half">
                        <h2 class="title">Information<span class="icon ml-4"><i class="fas fa-info-circle"></i></span></h2>
                        <server-info v-if="info" :info="info"></server-info>
                    </div>
                    <div class="column is-half">
                        <h2 class="title">Runtime Settings<span class="icon ml-5"><i class="fas fa-cogs"></i></span></h2>
                        <server-settings v-if="configuration" :configuration="configuration"></server-settings>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import bus from '../bus';

export default {
    data() {
        return {
            httpStats: undefined,
            memory: undefined,
            info: undefined,
            configuration: undefined,
            stream: null,
        };
    },
    mounted() {
        this.$http.get('/server/dashboard').then((response) => {
            const data = response.data.data;

            this.httpStats = data.httpStats;
            this.memory = data.memory;
            this.info = data.info;
            this.configuration = data.configuration;

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