<template>
    <div>
        <requests-level :requests="requests"></requests-level>
        <canvas id="canvas"></canvas>
    </div>
</template>

<script>
import Chart from 'chart.js';
import bus from '../bus';

const DATASET_SIZE = 60;
const UPDATE_INTERVAL = 2000;

export default {
    data() {
        return {
            requests: {
                received: 0,
                rate: 0.0,
                successful: 0,
                failed: 0,
            },
            memory: {
                usage: 0.0,
                peak: 0.0,
            },
            uptime: 0,
            timer: undefined,
        };
    },
    mounted() {
        let config = {
            type: 'line',
            data: {
                labels: [...Array(DATASET_SIZE)].map((_, i) => i * UPDATE_INTERVAL / 1000).reverse(),
                datasets: [{
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
                    borderWidth: 1,
                    pointRadius: 0,
                    lineTension: 0.1,
                    fill: true,
                }],
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
                    intersect: true,
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'T+ (seconds)',
                        },
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: false,
                            labelString: '# of Requests',
                        },
                    }],
                },
            },
        };

        let context = document.getElementById('canvas').getContext('2d');

        window.chart = new Chart(context, config);
            
        this.timer = setInterval(this.update, UPDATE_INTERVAL);
    },
    methods: {
        update() {
            this.$http.get('/server/dashboard').then((response) => {
                this.requests = response.data.requests;
                this.memory = response.data.memory;
                this.uptime = response.data.uptime;
            }).catch((error) => {
                bus.$emit('communication-error', {
                    error,
                });

                clearInterval(this.timer);
            });
        }
    },
    watch: { 
        requests: (newValue, oldValue) => {
            let datasets = window.chart.data.datasets;

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