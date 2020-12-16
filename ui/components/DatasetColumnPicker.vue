<template>
    <div>
        <div v-if="dataset.data && dataset.header">
            <div class="table-container">
                <table class="table is-bordered is-striped is-narrow is-fullwidth">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th v-for="(title, offset) in dataset.header" :key="offset" nowrap>
                                <label class="checkbox">
                                    <input type="checkbox"
                                        v-if="isContinuous(offset)"
                                        :value="offset"
                                        v-model="selected"
                                        :disabled="disabled && !selected.includes(offset)"
                                    />
                                    <input v-else type="checkbox" disabled />
                                    <span class="ml-2" >{{ title }}</span>
                                </label>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, offset) in preview" :key="offset">
                            <td>{{ cursor.offset + offset }}</td>
                            <td v-for="(value, offset) in row" :key="offset">
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
        <section v-if="!dataset.data || !dataset.header" class="hero">
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
import bus from '../bus';

export default {
    data() {
        return {
            selected: [
                //
            ],
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
        maxColumns: {
            type: Number,
            required: false,
            default: Infinity,
        },
    },
    computed: {
        preview() {
            return this.dataset.data.slice(this.cursor.offset, this.cursor.offset + this.cursor.limit);
        },
        disabled() {
            return this.selected.length >= this.maxColumns;
        },
    },
    watch: {
        selected(newValue, oldValue) {
            bus.$emit('dataset-columns-selected', {
                selected: newValue,
            });
        },
        dataset: {
            deep: true,
            handler: function(newValue, oldValue) {
                this.selected = [];
            },
        },
    },
    methods: {
        more() {
            this.cursor.limit = Math.min(this.cursor.maxLimit, this.cursor.limit + this.cursor.increment);
        },
        less() {
            this.cursor.limit = Math.max(0, this.cursor.limit - this.cursor.increment);
        },
        next() {
            this.cursor.offset = Math.min(this.dataset.data.length, this.cursor.offset + this.cursor.limit);
        },
        previous() {
            this.cursor.offset = Math.max(0, this.cursor.offset - this.cursor.limit);
        },
        isContinuous(offset) {
            const value = this.dataset.data[0][offset];

            return Number(value) == value;
        },
    },
}
</script>

<style lang="scss" scoped>
.is-dimmed {
    opacity: 0.5;
}
</style>
