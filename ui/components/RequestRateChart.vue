<template>
    <figure>
        <canvas id="requests-chart" width="640" height="360"></canvas>
    </figure>
</template>

<script>
import Chart from 'chart.js';

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
                labels: [...Array(60).keys()].reverse(),
                datasets: [
                    {
                        label: 'Average',
                        data: Array(DATASET_SIZE).fill(0),
                        borderColor: 'hsl(271, 100%, 71%)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0,
                        fill: false,
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
                    text: 'Request Rate',
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
                                labelString: 'T- (seconds)',
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
        update() {
            let datasets = this.chart.data.datasets;

            if (!this.last) {
                this.last = Object.assign({}, this.requests);
            }

            datasets[1].data.push(this.requests.successful - this.last.successful);
            datasets[2].data.push(this.requests.rejected - this.last.rejected);
            datasets[3].data.push(this.requests.failed - this.last.failed);
 
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
}
</script>