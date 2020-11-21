<template>
    <canvas id="queries-chart" width="500" height="500"></canvas>
</template>

<script>
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

        let chart = new Chart(context, {
            type: 'doughnut',
            data: {
                datasets: [
                    {
                        data: Object.entries(this.queries).map((counts) => {
                            return counts.fulfilled;
                        }),
                    },
                ],
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Queries',
                },
            },
        });

        this.chart = chart;
    },
}
</script>