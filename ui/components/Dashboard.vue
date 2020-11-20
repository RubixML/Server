<template>
    <section class="section">
        <div class="container">
            <requests-level :requests="requests"></requests-level>
            <requests-chart :requests="requests"></requests-chart>
            <h2 class="title is-size-4 mt-5">Query Log</h2>
            <div class="columns">
                <div class="column is-half">
                    <queries-table :queries="queries"></queries-table>
                </div>
                <div class="column is-half">
                    <!-- <queries-chart :queries="queries"></queries-chart> -->
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
            requests: {
                successful: undefined,
                rejected: undefined,
                failed: undefined,
            },
            queries: {
                //
            },
            memory: {
                usage: undefined,
                peak: undefined,
            },
            uptime: undefined,
            stream: null,
        };
    },
    mounted() {
        this.$http.get('/server/dashboard').then((response) => {
            this.requests = response.data.requests;
            this.queries = response.data.queries;
            this.memory = response.data.memory;
            this.uptime = response.data.uptime;
        }).catch((error) => {
            bus.$emit('communication-error', {
                error,
            });
        });

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

            stream.subscribe('query-accepted', (message) => {
                if (this.queries.hasOwnProperty(message.name)) {
                    this.queries[message.name]++;
                } else {
                    this.queries[message.name] = 1;
                }
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