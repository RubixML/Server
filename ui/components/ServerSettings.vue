<template>
    <table class="table is-bordered is-striped is-fullwidth">
        <tbody>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The host address this server is bound to.">
                    <span class="has-text-weight-medium">Host</span>
                </span></td>
                <td class="has-text-right">{{ settings.host }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The TCP port this server is listening on.">
                    <span class="has-text-weight-medium">Port</span>
                </span></td>
                <td class="has-text-right">{{ settings.port }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="Is transport layer security (TLS) enabled?">
                    <span class="has-text-weight-medium">TLS</span>
                </span></td>
                <td class="has-text-right">{{ settings.tls ? 'Yes' : 'No' }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum number of requests to handle concurrently.">
                    <span class="has-text-weight-medium">Max Concurrent Requests</span>
                </span></td>
                <td class="has-text-right">{{ settings.maxConcurrentRequests.toLocaleString() }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum amount of memory the server is allowed to consume.">
                    <span class="has-text-weight-medium">Memory Limit</span>
                </span><span class="tag ml-3">INI</span></td>
                <td class="has-text-right">{{ memoryLimit }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum size of a request body allowed.">
                    <span class="has-text-weight-medium">Max POST Size</span>
                </span><span class="tag ml-3">INI</span></td>
                <td class="has-text-right">{{ postMaxSize }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script lang="ts">
import Vue from 'vue';
import moment from 'moment';
import gql from 'graphql-tag';

const MEGABYTE = 1000000;

export const fragment = gql`
    fragment ServerSettings on Server {
        settings {
            host
            port
            tls
            maxConcurrentRequests
            memoryLimit
            postMaxSize
        }
    }
`;

export default Vue.extend({
    props: {
        settings: {
            type: Object,
            required: true,
        },
    },
    computed: {
        memoryLimit() : string {
            return this.settings.memoryLimit !== -1
                ? (this.settings.memoryLimit / MEGABYTE).toFixed(1) + 'M'
                : 'Unlimited';
        },
        postMaxSize() : string {
            return this.settings.postMaxSize > 0
                ? (this.settings.postMaxSize / MEGABYTE).toFixed(1) + 'M'
                : 'Unlimited';
        }
    },
});
</script>
