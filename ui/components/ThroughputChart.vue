<template>
    <canvas id="transfer-rate-chart" width="600" height="400"></canvas>
</template>

<script>
import Chart from 'chart.js';

const MEGABYTE = 1000000;
const ONE_SECOND = 1000;
const DATASET_SIZE = 60;

export default {
    data() {
        return {
            chart: null,
            last: null,
        };
    },
    props: {
        httpStats: {
            type: Object,
            required: true,
        },
    },
    mounted() {
        let context = document.getElementById('transfer-rate-chart').getContext('2d');

        this.chart = new Chart(context, {
            type: 'line',
            data: {
                labels: [...Array(DATASET_SIZE).keys()].reverse(),
                datasets: [
                    {
                        label: 'Received',
                        data: Array(DATASET_SIZE).fill(0.0),
                        borderColor: 'hsl(204, 86%, 53%)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0,
                        fill: 'origin',
                    },
                    {
                        label: 'Sent',
                        data: Array(DATASET_SIZE).fill(0.0),
                        borderColor: 'hsl(271, 100%, 71%)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0,
                        fill: 'origin',
                    },
                ],
            },
            options: {
                responsive: true,
                title: {
                    display: false,
                    text: 'Throughput',
                },
                tooltips: {
                    mode: 'index',
                    intersect: true,
                },
                hover: {
                    mode: 'nearest',
                    intersect: false,
                },
                scales: {
                    xAxes: [
                        {
                            scaleLabel: {
                                display: true,
                                labelString: 'T+ (seconds)',
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
                                labelString: 'Megabytes (MB) / s',
                            },
                            ticks: {
                                beginAtZero: true,
                                precision: 3,
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
        update() {
            let datasets = this.chart.data.datasets;

            if (!this.last) {
                this.last = Object.assign({}, this.httpStats.transferred);
            }

            datasets[0].data.push(Math.round(((this.httpStats.transferred.received - this.last.received) / MEGABYTE) + 'e3') + 'e-3');
            datasets[1].data.push(Math.round(((this.httpStats.transferred.sent - this.last.sent) / MEGABYTE) + 'e3') + 'e-3');
 
            datasets.forEach((dataset) => {
                if (dataset.data.length > DATASET_SIZE) {
                    dataset.data = dataset.data.slice(-DATASET_SIZE);
                }
            });

            this.last = Object.assign({}, this.httpStats.transferred);

            this.chart.update(0);
        },
    },
}
</script>