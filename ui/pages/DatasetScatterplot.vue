<template>
    <div>
        <h2 class="title is-size-5"><span class="icon mr-3"><i class="fas fa-check-square"></i></span>Select 2 columns</h2>
        <div v-if="dataset.data" class="table-container">
            <table class="table is-bordered is-striped is-fullwidth">
                <thead>
                    <tr class="has-text-weight-semibold">
                        <td v-for="(title, offset) in header" :key="offset" nowrap>
                            <label class="checkbox">
                                <input type="checkbox" :value="offset" v-model="selected" @change="update()">
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
        <figure>
            <canvas id="dataset-scatterplot" width="600" height="600"></canvas>
        </figure>
    </div>
</template>

<script>
import bus from '../bus';

const PREVIEW_ROWS = 3;

export default {
    data() {
        return {
            chart: null,
            selected: [
                //
            ],
        };
    },
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
    mounted() {
        let context = document.getElementById('dataset-scatterplot').getContext('2d');

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
                    display: false,
                    text: 'Dataset Scatterplot',
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
        update() {
            if (this.selected.length >= 2) {
                const xLabel = this.dataset.header[this.selected[0]];
                const yLabel = this.dataset.header[this.selected[1]];

                this.chart.options.scales.xAxes[0].scaleLabel.labelString = xLabel;
                this.chart.options.scales.yAxes[0].scaleLabel.labelString = yLabel;

                let data = [];

                this.dataset.data.forEach((row) => {
                    data.push({
                        x: row[xLabel],
                        y: row[yLabel],
                    });
                });

                this.chart.data.datasets[0].data = data;

                this.chart.update();
            }
        },
    },
}
</script>

<style lang="scss" scoped>
.is-dimmed {
    opacity: 0.5;
}
</style>
