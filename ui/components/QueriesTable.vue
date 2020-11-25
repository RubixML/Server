<template>
    <table class="table is-bordered is-striped is-fullwidth">
        <thead>
            <tr>
                <th>Query</th>
                <th class="has-text-right">Fulfilled</th>
                <th class="has-text-right">Failed</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(counts, name, index) in queryLog" :key="index">
                <td>{{ name }}</td>
                <td class="has-text-right">{{ counts.fulfilled.toLocaleString() }}</td>
                <td class="has-text-right">{{ counts.failed.toLocaleString() }}</td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th>Totals</th>
                <td class="has-text-right">{{ totalFulfilled.toLocaleString() }}</td>
                <td class="has-text-right">{{ totalFailed.toLocaleString() }}</td>
            </tr>
        </tfoot>
    </table>
</template>

<script>
export default {
    props: {
        queryLog: {
            type: Object,
            required: true,
        },
    },
    computed: {
        totalFulfilled() {
            return Object.entries(this.queryLog).reduce((sigma, query) => sigma + query[1].fulfilled, 0);
        },
        totalFailed() {
            return Object.entries(this.queryLog).reduce((sigma, query) => sigma + query[1].failed, 0);
        },
    }
}
</script>