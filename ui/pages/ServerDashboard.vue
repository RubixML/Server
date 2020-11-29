<template>
    <div>
        <section class="section">
            <div class="container">
                <requests-level v-if="httpStats && start" :requests="httpStats.requests" :start="start"></requests-level>
                <request-rate-chart v-if="httpStats" :requests="httpStats.requests"></request-rate-chart>
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
                        <h2 class="title">Configuration Settings</h2>
                        <configuration-settings v-if="configuration" :configuration="configuration"></configuration-settings>
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
            configuration: undefined,
            start: undefined,
            stream: null,
        };
    },
    mounted() {
        this.$http.get('/server/dashboard').then((response) => {
            const data = response.data.data;

            this.httpStats = data.httpStats;
            this.memory = data.memory;
            this.configuration = data.configuration;
            this.start = data.start;

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