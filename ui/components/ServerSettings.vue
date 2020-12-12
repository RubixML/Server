<template>
    <table class="table is-bordered is-striped is-fullwidth">
        <tbody>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The host address this server is bound to.">
                    <span class="has-text-weight-semibold">Host</span>
                </span></td>
                <td class="has-text-right">{{ settings.host }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The TCP port this server is listening on.">
                    <span class="has-text-weight-semibold">Port</span>
                </span></td>
                <td class="has-text-right">{{ settings.port }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum number of requests to handle concurrently.">
                    <span class="has-text-weight-semibold">Max Concurrent Requests</span>
                </span></td>
                <td class="has-text-right">{{ settings.maxConcurrentRequests.toLocaleString() }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The size of the server-sent events (SSE) reconnect buffer.">
                    <span class="has-text-weight-semibold">SSE Reconnect Buffer</span>
                </span></td>
                <td class="has-text-right">{{ settings.sseReconnectBuffer.toLocaleString() }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum amount of memory the server is allowed to consume.">
                    <span class="has-text-weight-semibold">Memory Limit</span>
                </span><span class="tag ml-3">INI</span></td>
                <td class="has-text-right">{{ memoryLimit }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum size of a request body allowed.">
                    <span class="has-text-weight-semibold">Max POST Size</span>
                </span><span class="tag ml-3">INI</span></td>
                <td class="has-text-right">{{ postMaxSize }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
import gql from 'graphql-tag';

const MEGABYTE = 1000000;

export const fragment = gql`
    fragment ServerSettings on Server {
        settings {
            host
            port
            maxConcurrentRequests
            sseReconnectBuffer
            memoryLimit
            postMaxSize
        }
    }
`;

export default {
    props: {
        settings: {
            type: Object,
            required: true,
        },
    },
    computed: {
        memoryLimit() {
            return this.settings.memoryLimit !== -1
                ? (this.settings.memoryLimit / MEGABYTE).toFixed(1) + 'M'
                : 'Unlimited';
        },
        postMaxSize() {
            return this.settings.postMaxSize > 0
                ? (this.settings.postMaxSize / MEGABYTE).toFixed(1) + 'M'
                : 'Unlimited';
        }
    },
}
</script>