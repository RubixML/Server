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
                <p class="heading"><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="Requests that were rejected due to a client error (4xx).">Rejected</span></p>
                <p class="title">{{ requests.rejected.toLocaleString() }}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading"><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="Requests that failed due to a server error (5xx).">Failed</span></p>
                <p class="title">{{ requests.failed.toLocaleString() }}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading"><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" :data-tooltip="upSince">Uptime</span></p>
                <p class="title has-text-first-letter-capitalized">{{ uptime }}</p>
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
            uptime: '',
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
    computed: {
        upSince() {
            return moment.unix(this.start).format('[Up since] dddd, MMMM Do YYYY, h:mmA [(server time)]');
        },
    },
    methods: {
        updateUptime() {
            this.uptime = moment.unix(this.start).fromNow(true);
        }
    },
    mounted() {
        this.updateUptime();

        setInterval(this.updateUptime, THIRTY_SECONDS);
    },
}
</script>
