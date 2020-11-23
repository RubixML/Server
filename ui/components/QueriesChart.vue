<template>
    <canvas id="queries-chart" width="500" height="500"></canvas>
</template>

<script>
import Chart from 'chart.js';

const COLORS = [
    'hsl(271, 100%, 71%)', 'hsl(204, 86%, 53%)', 'hsl(347, 100%, 69%)', 'hsl(271, 100%, 71%)',
];

const FIVE_SECONDS = 5000;

export default {
    data() {
        return {
            chart: null,
        };
    },
    props: {
        queries: {
            type: Object,
            required: true,
        },
    },
    mounted() {
        let context = document.getElementById('queries-chart').getContext('2d');

        this.chart = new Chart(context, {
            type: 'doughnut',
            data: {
                datasets: [
                    {
                        label: 'Query',
                    },
                ],
            },
            options: {
                responsive: true,
                title: {
                    display: false,
                    text: 'Queries',
                },
            },
        });

        this.update();

        setInterval(this.update, FIVE_SECONDS);
    },
    methods: { 
        update() {
            this.chart.data.datasets[0].data = Object.entries(this.queries).map((query) => {
                return query[1].fulfilled + query[1].failed;
            });

            this.chart.data.datasets[0].backgroundColor = COLORS.slice(0, this.queries.length);

            this.chart.data.labels = Object.keys(this.queries);

            this.chart.update();
        },
    },
}
</script>