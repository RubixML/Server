<template>
    <table class="table is-bordered is-striped is-fullwidth">
        <tbody>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The host address this server is bound to.">Host</span></td>
                <td class="has-text-right">{{ settings.host }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The TCP port this server is listening on.">Port</span></td>
                <td class="has-text-right">{{ settings.port }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum number of requests to handle concurrently.">Max Concurrent Requests</span></td>
                <td class="has-text-right">{{ settings.maxConcurrentRequests.toLocaleString() }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The size of the server-sent events (SSE) reconnect buffer.">SSE Reconnect Buffer</span></td>
                <td class="has-text-right">{{ settings.sseReconnectBuffer.toLocaleString() }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum amount of memory the server is allowed to consume.">Memory Limit</span><span class="tag ml-3">INI</span></td>
                <td class="has-text-right">{{ memoryLimit }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum size of a request body allowed.">Max POST Size</span><span class="tag ml-3">INI</span></td>
                <td class="has-text-right">{{ postMaxSize }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
const MEGABYTE = 1000000;

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
            return this.settings.postMaxSize !== -1
                ? (this.settings.postMaxSize / MEGABYTE).toFixed(1) + 'M'
                : 'Unlimited';
        }
    },
}
</script>