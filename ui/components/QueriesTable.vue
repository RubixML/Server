<template>
    <table class="table is-striped is-fullwidth">
        <thead>
            <tr>
                <th>Query</th>
                <th class="has-text-right">Count</th>
                <th class="has-text-right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(count, name, index) in queries.accepted" :key="index">
                <td>{{ name }}</td>
                <td class="has-text-right">{{ count.toLocaleString() }}</td>
                <td class="has-text-right">50%</td>
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