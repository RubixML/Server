<template>
    <table class="table is-bordered is-striped is-fullwidth">
        <tbody>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The date and time that the server started.">
                    <span class="has-text-weight-medium">Up Since</span>
                </span></td>
                <td class="has-text-right">{{ upSince }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The amount of time that the server has been running.">
                    <span class="has-text-weight-medium">Uptime</span>
                </span></td>
                <td class="has-text-right has-text-first-letter-capitalized">{{ uptime }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The process ID (PID) of the server.">
                    <span class="has-text-weight-medium">PID</span>
                </span></td>
                <td class="has-text-right">{{ info.pid }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script lang="ts">
import Vue from 'vue';
import moment from 'moment';
import gql from 'graphql-tag';

const THIRTY_SECONDS = 30000;

export const fragment = gql`
    fragment ProcessInfo on Server {
        info {
            start
            pid
        }
    }
`;

export default Vue.extend({
    data() {
        return {
            uptime: '',
        };
    },
    props: {
        info: {
            type: Object,
            required: true,
        },
    },
    computed: {
        upSince() : string {
            return moment.unix(this.info.start).format('MMM. Do, YYYY [@] h:mmA');
        },
    },
    methods: {
        updateUptime() : void {
            this.uptime = moment.unix(this.info.start).fromNow(true);
        }
    },
    mounted() {
        this.updateUptime();

        setInterval(this.updateUptime, THIRTY_SECONDS);
    },
});
</script>