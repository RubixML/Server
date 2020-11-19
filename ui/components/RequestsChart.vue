<template>
    <canvas id="canvas" width="900" height="300"></canvas>
</template>

<script>
import Chart from 'chart.js';

const DATASET_SIZE = 60;
const UPDATE_INTERVAL = 1000;

export default {
    data() {
        return {
            chart: null,
            old: null,
        };
    },
    props: {
        requests: {
            type: Object,
            required: true,
        },
    },
    mounted() {
        let context = document.getElementById('canvas').getContext('2d');

        this.chart = new Chart(context, {
            type: 'line',
            data: {
                labels: [...Array(DATASET_SIZE).keys()].reverse(),
                datasets: [
                    {
                        label: 'Successful',
                        data: Array(DATASET_SIZE).fill(0.0),
                        borderColor: 'hsl(271, 100%, 71%)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0.1,
                        fill: false,
                    },
                    {
                        label: 'Rejected',
                        data: Array(DATASET_SIZE).fill(0.0),
                        borderColor: 'hsl(204, 86%, 53%)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0.1,
                        fill: true,
                    },
                    {
                        label: 'Failed',
                        data: Array(DATASET_SIZE).fill(0.0),
                        borderColor: 'hsl(347, 100%, 69%)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0.1,
                        fill: false,
                    },
                ],
            },
            options: {
                responsive: true,
                title: {
                    display: false,
                    text: 'Server Requests',
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
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

        setInterval(this.update, UPDATE_INTERVAL);
    },
    methods: { 
        update() {
            let datasets = this.chart.data.datasets;

            if (this.old === null) {
                this.old = Object.assign({}, this.requests);
            }

            datasets[0].data.push(Math.max(0, this.requests.successful - this.old.successful));
            datasets[1].data.push(Math.max(0, this.requests.rejected - this.old.rejected));
            datasets[2].data.push(Math.max(0, this.requests.failed - this.old.failed));
 
            datasets.forEach((dataset) => {
                if (dataset.data.length > DATASET_SIZE) {
                    dataset.data = dataset.data.slice(-DATASET_SIZE);
                }
            });

            this.old = Object.assign({}, this.requests);

            this.chart.update(0);
        }
    },
}
</script>