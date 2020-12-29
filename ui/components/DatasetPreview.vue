<template>
    <div>
        <div v-if="dataset.data.length && dataset.header.length">
            <div class="table-container">
                <table class="table is-bordered is-striped is-narrow is-fullwidth">
                    <thead>
                        <tr>
                            <th class="has-text-weight-medium">#</th>
                            <th v-for="(title, offset) in dataset.header" :key="offset" class="has-text-weight-medium" nowrap>
                                {{ title }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, offset) in preview" :key="offset">
                            <td class="has-text-weight-medium">{{ cursor.offset + offset }}</td>
                            <td v-for="(value, offset) in row" :key="offset" class="has-text-weight-light">
                                {{ value }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="has-text-centered">
                <button class="button" @click="previous()" :disabled="cursor.offset <= 0">
                    <span class="icon"><i class="fas fa-caret-left"></i></span><span>Prev</span>
                </button>
                <button class="button" @click="less()" :disabled="cursor.limit <= 0">
                    <span>Less</span>
                </button>
                <button class="button" @click="more()" :disabled="cursor.limit >= cursor.maxLimit">
                    <span>More</span>
                </button>
                <button class="button" @click="next()" :disabled="cursor.offset + cursor.limit >= dataset.data.length">
                    <span>Next</span><span class="icon"><i class="fas fa-caret-right"></i></span>
                </button>
            </div>
        </div>
        <section v-else class="hero">
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

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
    data() {
        return {
            cursor: {
                offset: 0,
                limit: 5,
                increment: 5,
                maxLimit: 25,
            },
        };
    },
    props: {
        dataset: {
            type: Object,
            required: true,
        },
    },
    computed: {
        preview() : any[][] {
            return this.dataset.data.slice(this.cursor.offset, this.cursor.offset + this.cursor.limit);
        },
    },
    methods: {
        more() : void {
            this.cursor.limit = Math.min(this.cursor.maxLimit, this.cursor.limit + this.cursor.increment);
        },
        less() : void {
            this.cursor.limit = Math.max(0, this.cursor.limit - this.cursor.increment);
        },
        next() : void {
            this.cursor.offset = Math.min(this.dataset.data.length, this.cursor.offset + this.cursor.limit);
        },
        previous() : void {
            this.cursor.offset = Math.max(0, this.cursor.offset - this.cursor.limit);
        },
    },
});
</script>

<style lang="scss" scoped>
.is-dimmed {
    opacity: 0.5;
}
</style>
