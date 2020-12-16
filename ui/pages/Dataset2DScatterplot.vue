<template>
    <div>
        <h2 class="title is-size-5"><span class="icon mr-3"><i class="fas fa-check-square"></i></span>Select 2 columns</h2>
        <dataset-column-picker :dataset="dataset" :maxColumns="2"></dataset-column-picker>
        <figure class="mt-5">
            <canvas id="dataset-2d-scatterplot" width="550" height="550"></canvas>
        </figure>
    </div>
</template>

<script>
import bus from '../bus';

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
        let context = document.getElementById('dataset-2d-scatterplot').getContext('2d');

        this.chart = new Chart(context, {
            type: 'scatter',
            data: {
                datasets: [
                   {
                        label: 'Points',
                        borderColor: 'hsl(271, 100%, 71%)',
                        borderWidth: 1,
                        hoverBorderWidth: 1,
                        fill: false,
                   },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    display: true,
                    text: 'Scatterplot (2D)',
                },
                legend: {
                    display: false,
                },
                tooltips: {
                    enabled: true,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true,
                },
                scales: {
                    xAxes: [
                        {
                            scaleLabel: {
                                display: true,
                                labelString: '',
                            },
                            display: true,
                        },
                    ],
                    yAxes: [
                        {
                            scaleLabel: {
                                display: true,
                                labelString: '',
                            },
                            display: true,
                        }
                    ],
                },
            },
        });

        bus.$on('dataset-columns-selected', (payload) => {
            this.updateDataset(payload.selected);
        });
    },
    methods: {
        updateDataset(selected) {
            if (selected.length === 2) {
                this.chart.options.scales.xAxes[0].scaleLabel.labelString = this.dataset.header[selected[0]];
                this.chart.options.scales.yAxes[0].scaleLabel.labelString = this.dataset.header[selected[1]];

                let data = [];

                this.dataset.data.forEach((row) => {
                    data.push({
                        x: row[selected[0]],
                        y: row[selected[1]],
                    });
                });

                this.chart.data.datasets[0].data = data;
            } else {
                this.chart.data.datasets[0].data = [];
            }

            this.chart.update();
        },
    },
}
</script>
