<template>
    <div>
        <h2 class="title is-size-6">Select the Columns To Plot</h2>
        <div v-if="dataset.data" class="table-container">
            <table class="table is-bordered is-striped is-fullwidth">
                <thead>
                    <tr class="has-text-weight-semibold">
                        <td v-for="(title, offset) in header" :key="offset" nowrap>
                            <label class="checkbox">
                                <input type="checkbox">
                                <span class="ml-2">{{ title }}</span>
                            </label>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(row, offset) in preview" :key="offset">
                        <td v-for="(value, offset) in row" :key="offset">
                            {{ value }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <section v-if="!dataset.data" class="hero">
            <div class="hero-body">
                <div class="container has-text-centered">
                    <h1 class="title is-dimmed">
                        No Data To Show
                    </h1>
                    <h2 class="subtitle is-dimmed">
                        Load a dataset to begin
                    </h2>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
const PREVIEW_ROWS = 5;

export default {
    props: {
        dataset: {
            type: Object,
            required: true,
        },
    },
    computed: {
        header() {
            return this.dataset.header ? this.dataset.header : [...this.dataset.data[0].keys()].map((offset) => {
                return 'Column ' + offset;
            });
        },
        preview() {
            return this.dataset.data.slice(0, PREVIEW_ROWS);
        },
    },
}
</script>

<style lang="scss" scoped>
.is-dimmed {
    opacity: 0.5;
}
</style>
