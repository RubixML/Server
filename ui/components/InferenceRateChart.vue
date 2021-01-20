<template>
    <figure id="inference-rate-chart"></figure>
</template>

<script lang="ts">
import Vue from 'vue';
import Plotly from 'plotly.js-basic-dist';
import gql from 'graphql-tag';

const ONE_SECOND = 1000;
const DATASET_SIZE = 60;

export const fragment = gql`
    fragment InferenceRateChart on Model {
        numSamplesInferred
    }
`;

export default Vue.extend({
    data() {
        return {
            datasets: {
                total: [],
            },
            last: {
                numSamplesInferred: null,
            }
        };
    },
    props: {
        model: {
            type: Object,
            required: true,
        },
    },
    mounted() {
        const labels = [...Array(DATASET_SIZE).keys()].reverse();
        const zeros = Array(DATASET_SIZE).fill(0);

        Plotly.newPlot('inference-rate-chart', [
            {
                name: 'Average',
                x: labels,
                y: zeros,
                type: 'scatter',
                line: {
                    width: 2,
                    color: 'rgb(75, 192, 192)',
                },
                fill: 'tozeroy',
                fillcolor: 'rgba(75, 192, 192, 0.1)',
            },
            {
                name: 'Samples',
                x: labels,
                y: zeros,
                type: 'scatter',
                line: {
                    width: 2,
                    color: 'rgb(255, 205, 86)',
                },
                fill: 'tozeroy',
                fillcolor: 'rgba(255, 205, 86, 0.1)',
            },
        ], {
            title: {
                text: 'Inference Rate',
                font: {
                    size: 14,
                },
            },
            legend: {
                orientation: 'h',
            },
            xaxis: {
                title: {
                    text: 'Seconds',
                },
                autorange: 'reversed',
            },
            yaxis: {
                title: {
                    text: 'Samples',
                },
                rangemode: 'tozero',
            },
            paper_bgcolor: 'rgba(0, 0, 0, 0)',
            plot_bgcolor: 'rgba(0, 0, 0, 0)',
        }, {
            responsive: true,
            displayModeBar: false,
        });

        this.last.numSamplesInferred = this.model.numSamplesInferred;

        setInterval(this.update, ONE_SECOND);
    },
    methods: { 
        update() : void {
            const inferred = this.model.numSamplesInferred - this.last.numSamplesInferred;

            this.datasets.total.push(inferred);

            if (this.datasets.total.length > DATASET_SIZE) {
                this.datasets.total = this.datasets.total.slice(-DATASET_SIZE);
            }
    
            const mu = this.datasets.total.reduce((sigma, count) => sigma + count, 0) / this.datasets.total.length;

            this.last.numSamplesInferred = this.model.numSamplesInferred;

            Plotly.extendTraces('inference-rate-chart', {y: [[mu], [inferred]]}, [0, 1], DATASET_SIZE);
        },
    },
});
</script>