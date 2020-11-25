<template>
    <canvas id="transfers-chart" width="600" height="400"></canvas>
</template>

<script>
import Chart from 'chart.js';

const MEGABYTE = 1000000;
const FIVE_SECONDS = 5000;

export default {
    data() {
        return {
            chart: null,
        };
    },
    props: {
        httpStats: {
            type: Object,
            required: true,
        },
    },
    mounted() {
        let context = document.getElementById('transfers-chart').getContext('2d');

        this.chart = new Chart(context, {
            type: 'bar',
            data: {
                datasets: [
                    {
                        label: 'Received',
                        backgroundColor: 'hsl(204, 86%, 53%)',
                    },
                    {
                        label: 'Sent',
                        backgroundColor: 'hsl(271, 100%, 71%)',
                    },
                ],
                labels: ['Transferred'],
            },
            options: {
                responsive: true,
                title: {
                    display: false,
                    text: 'Transfers',
                },
                 scales: {
                    yAxes: [
                        {
                            scaleLabel: {
                                display: true,
                                labelString: 'Megabytes (MB)',
                            },
                            ticks: {
                                beginAtZero: true,
                                precision: 0,
                            },
                        },
                    ],
                },
            },
        });

        this.update();

        setInterval(this.update, FIVE_SECONDS);
    },
    methods: { 
        update() {
            this.chart.data.datasets[0].data[0] = Math.round((this.httpStats.transferred.received / MEGABYTE) + 'e2') + 'e-2';
            this.chart.data.datasets[1].data[0] = Math.round((this.httpStats.transferred.sent / MEGABYTE) + 'e2') + 'e-2';

            this.chart.update();
        },
    },
}
</script>