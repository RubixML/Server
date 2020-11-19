<template>
    <section class="section">
        <div class="container">
            <requests-level :requests="requests"></requests-level>
            <requests-chart :requests="requests"></requests-chart>
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
            memory: {
                usage: undefined,
                peak: undefined,
            },
            uptime: undefined,
            sse: null,
        };
    },
    mounted() {
        this.$http.get('/server/dashboard').then((response) => {
            this.requests = response.data.requests;
            this.memory = response.data.memory;
            this.uptime = response.data.uptime;
        }).catch((error) => {
            bus.$emit('communication-error', {
                error,
            });
        });

        this.$sse('/server/dashboard/events', { format: 'json' }).then((sse) => {
            sse.subscribe('http-stats-successful-incremented', (message) => {
                this.requests.successful++;
            });

            sse.subscribe('http-stats-rejected-incremented', (message) => {
                this.requests.rejected++;
            });

            sse.subscribe('http-stats-failed-incremented', (message) => {
                this.requests.failed++;
            });

            this.sse = sse;
        });
    },
    beforeDestroy() {
        if (this.sse) {
            this.sse.close();
        }
    },
}
</script>