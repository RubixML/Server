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
                <p class="title is-capitalized"><abbr :title="upSince">{{ uptime }}</abbr></p>
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
            return moment.unix(this.start).format('[Up since] dddd, MMMM Do YYYY, h:mm:ssA [(server time)]');
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