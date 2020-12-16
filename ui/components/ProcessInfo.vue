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
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The version of the library the server is running on.">
                    <span class="has-text-weight-medium">Server Version</span>
                </span></td>
                <td class="has-text-right">{{ info.versions.server}}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The version of Rubix ML the model is running on.">
                    <span class="has-text-weight-medium">ML Version</span>
                </span></td>
                <td class="has-text-right">{{ info.versions.ml }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The version of PHP the server is running on.">
                    <span class="has-text-weight-medium">PHP Version</span>
                </span></td>
                <td class="has-text-right">{{ info.versions.php }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
import moment from 'moment';
import gql from 'graphql-tag';

const THIRTY_SECONDS = 30000;

export const fragment = gql`
    fragment ProcessInfo on Server {
        info {
            start
            pid
            versions {
                server
                ml
                php
            }
        }
    }
`;

export default {
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
        upSince() {
            return moment.unix(this.info.start).format('MMM. Do, YYYY [@] h:mmA');
        },
    },
    methods: {
        updateUptime() {
            this.uptime = moment.unix(this.info.start).fromNow(true);
        }
    },
    mounted() {
        this.updateUptime();

        setInterval(this.updateUptime, THIRTY_SECONDS);
    },
}
</script>