<template>
    <figure>
        <canvas id="response-rate-chart" width="640" height="360"></canvas>
    </figure>
</template>

<script lang="ts">
import Vue from 'vue';
import Chart from 'chart.js';
import gql from 'graphql-tag';

const ONE_SECOND = 1000;
const DATASET_SIZE = 60;

export const fragment = gql`
    fragment ResponseRateChart on Server {
        httpStats {
            requests {
                successful
                rejected
                failed
            }
        }
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
        requests: {
            type: Object,
            required: true,
        },
    },
    mounted() {
        const element = document.getElementById('response-rate-chart');

        if (!(element instanceof HTMLCanvasElement)) {
            console.log('Canvas not found!');

            return;
        }

        const context = element.getContext('2d');

        this.chart = new Chart(context, {
            type: 'line',
            data: {
                labels: [...Array(60).keys()].reverse(),
                datasets: [
                    {
                        label: 'Average',
                        data: Array(DATASET_SIZE).fill(0),
                        borderColor: 'hsl(271, 100%, 71%)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0,
                        fill: true,
                    },
                    {
                        label: 'Successful',
                        data: Array(DATASET_SIZE).fill(0),
                        borderColor: 'hsl(141, 71%, 48%)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0,
                        fill: 'origin',
                    },
                    {
                        label: 'Rejected',
                        data: Array(DATASET_SIZE).fill(0),
                        borderColor: 'hsl(204, 86%, 53%)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0,
                        fill: 'origin',
                    },
                    {
                        label: 'Failed',
                        data: Array(DATASET_SIZE).fill(0),
                        borderColor: 'hsl(347, 100%, 69%)',
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
                    text: 'Response Rate',
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
                                labelString: 'Requests',
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
                this.last = Object.assign({}, this.requests);
            }

            const successful = this.requests.successful - this.last.successful;
            const rejected = this.requests.rejected - this.last.rejected;
            const failed = this.requests.failed - this.last.failed;

            datasets[1].data.push(successful);
            datasets[2].data.push(rejected);
            datasets[3].data.push(failed);
 
            datasets.forEach((dataset) => {
                if (dataset.data.length > DATASET_SIZE) {
                    dataset.data = dataset.data.slice(-DATASET_SIZE);
                }
            });

            const mu = datasets[1].data.reduce((sigma, count) => sigma + count, 0) / datasets[1].data.length;

            datasets[0].data.push(mu);

            this.last = Object.assign({}, this.requests);

            this.chart.update(0);
        }
    },
});
</script>