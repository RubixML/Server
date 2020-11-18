<template>
    <div>
        <requests-level :requests="requests"></requests-level>
        <requests-chart :requests="requests"></requests-chart>
    </div>
</template>

<script>
import bus from '../bus';

const UPDATE_INTERVAL = 2000;

export default {
    data() {
        return {
            requests: {
                received: undefined,
                rate: undefined,
                successful: undefined,
                failed: undefined,
            },
            memory: {
                usage: undefined,
                peak: undefined,
            },
            uptime: undefined,
            timer: undefined,
        };
    },
    mounted() {  
        this.timer = setInterval(this.update, UPDATE_INTERVAL);
    },
    methods: {
        update() {
            this.$http.get('/server/dashboard').then((response) => {
                this.requests = response.data.requests;
                this.memory = response.data.memory;
                this.uptime = response.data.uptime;
            }).catch((error) => {
                bus.$emit('communication-error', {
                    error,
                });

                clearInterval(this.timer);
            });
        }
    },
}
</script>