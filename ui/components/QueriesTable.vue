<template>
    <table class="table is-striped is-fullwidth">
        <thead>
            <tr>
                <th>Query</th>
                <th class="has-text-right">Fulfilled</th>
                <th class="has-text-right">Failed</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(counts, name, index) in queries" :key="index">
                <td>{{ name }}</td>
                <td class="has-text-right">{{ counts.fulfilled.toLocaleString() }}</td>
                <td class="has-text-right">{{ counts.failed.toLocaleString() }}</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
export default {
    props: {
        queries: {
            type: Object,
            required: true,
        },
    },
}
</script>