<template>
    <table class="table is-bordered is-striped is-fullwidth">
        <thead>
            <tr>
                <th>Setting</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum amount of memory the server is allowed to consume.">Memory Limit</span></td>
                <td>{{ configuration.memoryLimit }}</td>
            </tr>
            <tr>
                <td><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum size of a request body allowed.">Max POST Size</span></td>
                <td>{{ configuration.postMaxSize }}</td>
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