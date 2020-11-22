<template>
    <section class="section">
        <div class="container">
            <requests-level v-if="requests" :requests="requests" :start="start"></requests-level>
            <requests-chart v-if="requests" :requests="requests"></requests-chart>
            <div class="columns mt-5">
                <div class="column is-two-thirds">
                    <queries-table v-if="queries" :queries="queries"></queries-table>
                </div>
                <div class="column is-one-third">
                    <queries-chart v-if="queries" :queries="queries"></queries-chart>
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
            requests: undefined,
            queries: undefined,
            start: undefined,
            stream: null,
        };
    },
    mounted() {
        this.$http.get('/server/dashboard').then((response) => {
            this.requests = response.data.requests;
            this.queries = Object.assign({}, response.data.queries);
            this.start = response.data.start;

            this.$sse('/server/dashboard/events', { format: 'json' }).then((stream) => {
                stream.subscribe('http-successful-incremented', (message) => {
                    this.requests.successful++;
                });

                stream.subscribe('http-rejected-incremented', (message) => {
                    this.requests.rejected++;
                });

                stream.subscribe('http-failed-incremented', (message) => {
                    this.requests.failed++;
                });

                stream.subscribe('query-fulfilled', (message) => {
                    if (this.queries.hasOwnProperty(message.name)) {
                        this.queries[message.name].fulfilled++;
                    } else {
                        this.$set(this.queries, message.name, {
                            fulfilled: 1,
                            failed: 0,
                        });
                    }
                });

                stream.subscribe('query-failed', (message) => {
                    if (this.queries.hasOwnProperty(message.name)) {
                        this.queries[message.name].failed++;
                    } else {
                        this.$set(this.queries, message.name, {
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