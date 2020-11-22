<template>
    <canvas id="requests-chart" width="900" height="300"></canvas>
</template>

<script>
import Chart from 'chart.js';

const DATASET_SIZE = 60;
const UPDATE_INTERVAL = 1000;

export default {
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
        let context = document.getElementById('requests-chart').getContext('2d');

        this.chart = new Chart(context, {
            type: 'line',
            data: {
                labels: [...Array(DATASET_SIZE).keys()].reverse(),
                datasets: [
                    {
                        label: 'Average',
                        data: Array(DATASET_SIZE).fill(0),
                        borderColor: 'hsl(271, 100%, 71%)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0.1,
                        fill: 'origin',
                    },
                    {
                        label: 'Successful',
                        data: Array(DATASET_SIZE).fill(0),
                        borderColor: 'hsl(141, 71%, 48%)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0.1,
                        fill: false,
                    },
                    {
                        label: 'Rejected',
                        data: Array(DATASET_SIZE).fill(0),
                        borderColor: 'hsl(204, 86%, 53%)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0.1,
                        fill: false,
                    },
                    {
                        label: 'Failed',
                        data: Array(DATASET_SIZE).fill(0),
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
                    text: 'Requests',
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

            if (!this.last) {
                this.last = Object.assign({}, this.requests);
            }

            datasets[1].data.push(this.requests.successful - this.last.successful);
            datasets[2].data.push(this.requests.rejected - this.last.rejected);
            datasets[3].data.push(this.requests.failed - this.last.failed);

            const mu = datasets[1].data.reduce((a, b) => a + b, 0) / datasets[0].data.length;

            datasets[0].data.push(Math.round(mu + 'e2') + 'e-2');
 
            datasets.forEach((dataset) => {
                if (dataset.data.length > DATASET_SIZE) {
                    dataset.data = dataset.data.slice(-DATASET_SIZE);
                }
            });

            this.last = Object.assign({}, this.requests);

            this.chart.update(0);
        }
    },
}
</script>