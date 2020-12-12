<template>
    <div class="table-container">
        <table class="table is-bordered is-striped is-fullwidth">
            <thead v-if="header">
                <tr>
                    <td v-for="(title, offset) in header" :key="offset">
                        {{ title }}
                    </td>
                </tr>
            </thead>
            <tbody v-if="dataset">
                <tr v-for="(row, offset) in preview" :key="offset">
                    <td v-for="(value, offset) in row" :key="offset">
                        {{ value }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: {
        dataset: {
            type: Array,
            required: true,
        },
        header: {
            type: Array,
            required: false,
        },
    },
    computed: {
        preview() {
            return this.dataset.slice(0, 5);
        },
    },
}
</script>