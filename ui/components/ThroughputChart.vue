<template>
    <figure>
        <canvas id="throughput-chart" width="480" height="320"></canvas>
    </figure>
</template>

<script lang="ts">
import Vue from 'vue';
import Chart from 'chart.js';
import gql from 'graphql-tag';

const MEGABYTE = 1000000;
const ONE_SECOND = 1000;
const DATASET_SIZE = 60;

export const fragment = gql`
    fragment ThroughputChart on Server {
        httpStats {
            transfers {
                received
                sent
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
        transfers: {
            type: Object,
            required: true,
        },
    },
    mounted() {
        const element = document.getElementById('throughput-chart');

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
                        label: 'Received',
                        data: Array(DATASET_SIZE).fill(0.0),
                        borderColor: 'rgb(32, 156, 238)',
                        backgroundColor: 'rgba(32, 156, 238, 0.1)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0,
                        fill: 'origin',
                    },
                    {
                        label: 'Sent',
                        data: Array(DATASET_SIZE).fill(0.0),
                        borderColor: 'rgb(184, 107, 255)',
                        backgroundColor: 'rgba(184, 107, 255, 0.1)',
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
                    text: 'Throughput',
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
                                labelString: 'Megabytes',
                            },
                            ticks: {
                                beginAtZero: true,
                                precision: 2,
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
                this.last = Object.assign({}, this.transfers);
            }

            datasets[0].data.push((this.transfers.received - this.last.received) / MEGABYTE);
            datasets[1].data.push((this.transfers.sent - this.last.sent) / MEGABYTE);
 
            datasets.forEach((dataset) => {
                if (dataset.data.length > DATASET_SIZE) {
                    dataset.data = dataset.data.slice(-DATASET_SIZE);
                }
            });

            this.last = Object.assign({}, this.transfers);

            this.chart.update(0);
        },
    },
});
</script>