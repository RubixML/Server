<template>
    <section class="section">
        <div class="container">
            <requests-level v-if="httpStats && start" :httpStats="httpStats" :start="start"></requests-level>
            <requests-chart v-if="httpStats" :httpStats="httpStats"></requests-chart>
            <transfers-level v-if="httpStats" :httpStats="httpStats" class="mt-5"></transfers-level>
            <div class="columns">
                <div class="column is-half">
                    <throughput-chart v-if="httpStats" :httpStats="httpStats"></throughput-chart>
                </div>
                <div class="column is-half">
                    <transfers-chart v-if="httpStats" :httpStats="httpStats"></transfers-chart>
                </div>
            </div>
            <div class="columns mt-5">
                <div class="column is-two-thirds">
                    <queries-table v-if="queryLog" :queryLog="queryLog"></queries-table>
                </div>
                <div class="column is-one-third">
                    <queries-chart v-if="queryLog" :queryLog="queryLog"></queries-chart>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import bus from '../bus';

export default {
    data() {
        return {
            httpStats: undefined,
            queryLog: undefined,
            start: undefined,
            stream: null,
        };
    },
    mounted() {
        this.$http.get('/server/dashboard').then((response) => {
            this.httpStats = response.data.http_stats;
            this.queryLog = Object.assign({}, response.data.query_log);
            this.start = response.data.start;

            this.$sse('/server/dashboard/events', { format: 'json' }).then((stream) => {
                stream.subscribe('request-recorded', (message) => {
                    this.httpStats.requests++;
                    
                    this.httpStats.transferred.received += message.size;
                });

                stream.subscribe('response-recorded', (message) => {
                    const code = message.code;

                    if (code >= 100 && code < 400) {
                        this.httpStats.responses.successful++;
                    } else if (code >= 400 && code < 500) {
                        this.httpStats.responses.rejected++;
                    } else if (code >= 500) {
                        this.httpStats.responses.failed++;
                    }

                    this.httpStats.transferred.sent += message.size;
                });

                stream.subscribe('query-fulfilled', (message) => {
                    if (this.queryLog.hasOwnProperty(message.name)) {
                        this.queryLog[message.name].fulfilled++;
                    } else {
                        this.$set(this.queryLog, message.name, {
                            fulfilled: 1,
                            failed: 0,
                        });
                    }
                });

                stream.subscribe('query-failed', (message) => {
                    if (this.queryLog.hasOwnProperty(message.name)) {
                        this.queryLog[message.name].failed++;
                    } else {
                        this.$set(this.queryLog, message.name, {
                            fulfilled: 0,
                            failed: 1,
                        });
                    }
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