<template>
    <div>
        <div v-if="dataset.data">
            <h2 class="title is-size-5"><span class="icon mr-3"><i class="fas fa-check-square"></i></span>Select columns</h2>
            <div class="table-container">
                <table class="table is-bordered is-striped is-narrow is-fullwidth">
                    <thead>
                        <tr class="has-text-weight-semibold">
                            <td>#</td>
                            <td v-for="(title, offset) in dataset.header" :key="offset" nowrap>
                                <label class="checkbox">
                                    <input type="checkbox" class="mr-2" :value="offset" v-model="selected" @change="updateDataset()" :disabled="disabled && !selected.includes(offset)" />
                                    <span>{{ title }}</span>
                                </label>
                            </td>
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
                <button class="button" @click="next()" :disabled="cursor.offset >= dataset.data.length">
                     <span>Next</span><span class="icon"><i class="fas fa-caret-right"></i></span>
                </button>
            </div>
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
        <section class="section">
            <figure>
                <canvas id="dataset-2d-scatterplot" width="550" height="550"></canvas>
            </figure>
        </section>
    </div>
</template>

<script>
import bus from '../bus';

export default {
    data() {
        return {
            chart: null,
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
    },
    computed: {
        preview() {
            return this.dataset.data.slice(this.cursor.offset, this.cursor.offset + this.cursor.limit);
        },
        disabled() {
            return this.selected.length >= 2;
        },
    },
    mounted() {
        let context = document.getElementById('dataset-2d-scatterplot').getContext('2d');

        this.chart = new Chart(context, {
            type: 'scatter',
            data: {
                datasets: [
                   {
                        label: 'Points',
                        borderColor: 'hsl(271, 100%, 71%)',
                        borderWidth: 1,
                        hoverBorderWidth: 1,
                        fill: false,
                   },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    display: true,
                    text: '2D Scatterplot',
                },
                tooltips: {
                    enabled: true,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true,
                },
                scales: {
                    xAxes: [
                        {
                            scaleLabel: {
                                display: true,
                                labelString: '',
                            },
                            display: true,
                        },
                    ],
                    yAxes: [
                        {
                            scaleLabel: {
                                display: true,
                                labelString: '',
                            },
                            display: true,
                        }
                    ],
                },
            },
        });
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
        updateDataset() {
            if (this.selected.length === 2) {
                const xOffset = this.selected[0];
                const yOffset = this.selected[1];

                this.chart.options.scales.xAxes[0].scaleLabel.labelString = this.dataset.header[xOffset];
                this.chart.options.scales.yAxes[0].scaleLabel.labelString = this.dataset.header[yOffset];

                let data = [];

                this.dataset.data.forEach((row) => {
                    data.push({
                        x: row[xOffset],
                        y: row[yOffset],
                    });
                });

                this.chart.data.datasets[0].data = data;
            } else {
                this.chart.data.datasets[0].data = [];
            }

            this.chart.update();
        },
    },
}
</script>

<style lang="scss" scoped>
.is-dimmed {
    opacity: 0.5;
}
</style>
