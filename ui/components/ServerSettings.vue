<template>
    <table class="table is-bordered is-striped is-fullwidth">
        <thead>
            <tr>
                <th>Setting</th>
                <th class="has-text-right">Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The host address this server is bound to.">Host</span></td>
                <td class="has-text-right">{{ configuration.host }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The TCP port this server is listening on.">Port</span></td>
                <td class="has-text-right">{{ configuration.port }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum number of requets to handle concurrently.">Max Concurrent Requests</span></td>
                <td class="has-text-right">{{ configuration.maxConcurrentRequests.toLocaleString() }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum amount of memory the server is allowed to consume.">Memory Limit</span><span class="tag ml-3">INI</span></td>
                <td class="has-text-right">{{ configuration.memoryLimit }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum size of a request body allowed.">Max POST Size</span><span class="tag ml-3">INI</span></td>
                <td class="has-text-right">{{ configuration.postMaxSize }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The size of the server-sent events (SSE) reconnect buffer.">SSE Reconnect Buffer</span></td>
                <td class="has-text-right">{{ configuration.sseReconnectBuffer.toLocaleString() }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    props: {
        configuration: {
            type: Object,
            required: true,
        },
    },
}
</script>