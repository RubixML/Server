<template>
    <section class="section">
        Hello World!
    </section>
</template>

<script>
export default {
    data() {
        return {
            dashboard: {
                response_counts: {
                    total: 0,
                    successful: 0,
                    failed: 0,
                },
                uptime: 0,
            },
        };
    },
    created() {
        this.$http.get('/server/dashboard').then((response) => {
            this.dashboard = response.data;
        }).catch((error) => {
            bus.$emit('communication-error', {
                error,
            });
        });

        this.$sse('/events', { format: 'json' }).then((sse) => {
            sse.subscribe('dashboard-update', (message, event) => {
                //
            });
        });
    },
}
</script>