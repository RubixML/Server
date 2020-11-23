<template>
    <nav class="level">
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Successful</p>
                <p class="title">{{ requests.successful.toLocaleString() }}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Rejected</p>
                <p class="title">{{ requests.rejected.toLocaleString() }}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Failed</p>
                <p class="title">{{ requests.failed.toLocaleString() }}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Uptime</p>
                <p class="title">{{ uptime }}</p>
            </div>
        </div>
    </nav>
</template>

<script>
import moment from 'moment';

const THIRTY_SECONDS = 30000;

export default {
    data() {
        return {
            uptime: moment.unix(this.start).fromNow(true),
        };
    },
    props: {
        requests: {
            type: Object,
            required: true,
        },
        start: {
            type: Number,
            required: true,
        },
    },
    methods: {
        updateUptime() {
            this.uptime = moment.unix(this.start).fromNow(true);
        }
    },
    mounted() {
        setInterval(this.updateUptime, THIRTY_SECONDS);
    },
}
</script>