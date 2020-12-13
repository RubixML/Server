<template>
    <figure>
        <canvas id="dataset-scatterplot" width="600" height="480"></canvas>
    </figure>
</template>

<script>
export default {
    data() {
        return {
            chart: null,
        };
    },
    props: {
        dataset: {
            type: Object,
            required: true,
        },
    },
    mounted() {
        let context = document.getElementById('dataset-scatterplot').getContext('2d');

        this.chart = new Chart(context, {
            type: 'scatter',
            data: {
                datasets: [
                   //
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    display: true,
                    text: 'Dataset Scatterplot',
                },
                tooltips: {
                    enabled: true,
                },
                hover: {
                    mode: 'point',
                    intersect: true,
                },
            },
        });
    },
    methods: { 
        update() {
            this.chart.update();
        },
    },
}
</script>