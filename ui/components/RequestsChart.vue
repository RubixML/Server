<template>
    <canvas id="canvas" width="900" height="300"></canvas>
</template>


<script>
import Chart from 'chart.js';

const DATASET_SIZE = 60;

export default {
    props: {
        requests: {
            type: Object,
            required: true,
        },
    },
    mounted() {
        let context = document.getElementById('canvas').getContext('2d');

        window.chart = new Chart(context, {
            type: 'line',
            data: {
                labels: [...Array(DATASET_SIZE)].map((_, i) => i * 2).reverse(),
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
                        label: 'Failed',
                        data: Array(DATASET_SIZE).fill(0.0),
                        borderColor: 'hsl(204, 86%, 53%)',
                        borderWidth: 2,
                        pointRadius: 0,
                        lineTension: 0.1,
                        fill: true,
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
    },
    watch: { 
        requests: (newValue, oldValue) => {
            let datasets = window.chart.data.datasets;

            if (oldValue.successful === undefined) {
               oldValue.successful = newValue.successful; 
            }

            if (oldValue.failed === undefined) {
               oldValue.failed = newValue.failed; 
            }

            datasets[0].data.push(newValue.successful - oldValue.successful);
            datasets[1].data.push(newValue.failed - oldValue.failed);

            datasets.forEach((dataset) => {
                if (dataset.data.length > DATASET_SIZE) {
                    dataset.data = dataset.data.slice(-DATASET_SIZE);
                }
            });

            window.chart.update(0);
        },
    },
}
</script>