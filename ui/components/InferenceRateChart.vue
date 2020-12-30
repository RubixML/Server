<template>
    <figure>
        <canvas id="inference-rate-chart" width="480" height="320"></canvas>
    </figure>
</template>

<script lang="ts">
import Vue from 'vue';
import Chart from 'chart.js';
import gql from 'graphql-tag';

const ONE_SECOND = 1000;
const DATASET_SIZE = 60;

export const fragment = gql`
    fragment InferenceRateChart on Model {
        numSamplesInferred
    }
`;

export default Vue.extend({
    data() {
        return {
            chart: null,
            last: null,
        };
    },
    props: {
        model: {
            type: Object,
            required: true,
        },
    },
    mounted() {
        const element = document.getElementById('inference-rate-chart');

        if (!(element instanceof HTMLCanvasElement)) {
            console.log('Canvas not found!');

            return;
        }

        const context = element.getContext('2d');

        this.chart = new Chart(context, {
            type: 'line',
            data: {
                labels: [...Array(DATASET_SIZE).keys()].reverse(),
                datasets: [
                    {
                        label: 'Average',
                        data: Array(DATASET_SIZE).fill(0.0),
                        borderColor: 'hsl(39, 100%, 50%)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0,
                        fill: 'origin',
                    },
                    {
                        label: 'Samples',
                        data: Array(DATASET_SIZE).fill(0.0),
                        borderColor: 'hsl(60, 100%, 47%)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0,
                        fill: 'origin',
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    display: true,
                    text: 'Inference Rate',
                },
                tooltips: {
                    enabled: false,
                },
                hover: {
                    mode: 'point',
                    intersect: true,
                },
                scales: {
                    xAxes: [
                        {
                            scaleLabel: {
                                display: true,
                                labelString: 'Seconds',
                            },
                            ticks: {
                                precision: 0,
                            },
                            display: true,
                        },
                    ],
                    yAxes: [
                        {
                            scaleLabel: {
                                display: true,
                                labelString: 'Samples',
                            },
                            ticks: {
                                beginAtZero: true,
                                precision: 0,
                            },
                            display: true,
                        }
                    ],
                },
            },
        });

        setInterval(this.update, ONE_SECOND);
    },
    methods: { 
        update() : void {
            let datasets = this.chart.data.datasets;

            if (!this.last) {
                this.last = Object.assign({}, this.model);
            }

            datasets[1].data.push((this.model.numSamplesInferred - this.last.numSamplesInferred));
 
            datasets.forEach((dataset) => {
                if (dataset.data.length > DATASET_SIZE) {
                    dataset.data = dataset.data.slice(-DATASET_SIZE);
                }
            });

            const mu = datasets[1].data.reduce((sigma, count) => sigma + count, 0) / datasets[1].data.length;

            datasets[0].data.push(mu);

            this.last = Object.assign({}, this.model);

            this.chart.update(0);
        },
    },
});
</script>