<template>
    <figure class="image is-16by9">
        <div id="inference-rate-chart" class="has-ratio"></div>
    </figure>
</template>

<script lang="ts">
import Vue from 'vue';
import Plotly from '../providers/plotly';
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
            },
            timer: null,
        };
    },
    props: {
        model: {
            type: Object,
            required: true,
        },
    },
    methods: { 
        update() : void {
            const inferred : number = this.model.numSamplesInferred - this.last.numSamplesInferred;

            this.datasets.total.push(inferred);

            if (this.datasets.total.length > DATASET_SIZE) {
                this.datasets.total = this.datasets.total.slice(-DATASET_SIZE);
            }
    
            const mu : number = this.datasets.total.reduce((sigma : number, count : number) => sigma + count, 0) / this.datasets.total.length;

            Plotly.extendTraces('inference-rate-chart', {y: [[mu], [inferred]]}, [0, 1], DATASET_SIZE);

            this.last.numSamplesInferred = this.model.numSamplesInferred;
        },
    },
    mounted() : void {
        const labels : number[] = [...Array(DATASET_SIZE).keys()].reverse();
        const zeros : number[] = Array(DATASET_SIZE).fill(0);

        Plotly.newPlot('inference-rate-chart', [
            {
                name: 'Average',
                x: labels,
                y: zeros,
                type: 'scattergl',
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
                mode: 'lines',
                line: {
                    width: 2,
                    color: 'rgb(255, 205, 86)',
                },
                fill: 'tozeroy',
                fillcolor: 'rgba(255, 205, 86, 0.1)',
            },
        ], {
            legend: {
                orientation: 'h',
                y: 1.2,
            },
            xaxis: {
                title: {
                    text: 'Seconds',
                    font: {
                        size: 12,
                    },
                },
                type: 'linear',
                autorange: 'reversed',
                gridcolor: 'rgb(128, 128, 128)',
                fixedrange: true,
            },
            yaxis: {
                title: {
                    text: 'Samples',
                    font: {
                        size: 12,
                    },
                },
                type: 'linear',
                rangemode: 'tozero',
                gridcolor: 'rgb(128, 128, 128)',
                fixedrange: true,
            },
            margin: {
                l: 80,
                r: 40,
                t: 40,
                b: 40,
            },
            paper_bgcolor: 'rgba(0, 0, 0, 0)',
            plot_bgcolor: 'rgba(0, 0, 0, 0)',
            modebar: {
                color: 'rgb(128, 128, 128)',
                activecolor: 'rgb(192, 192, 192)',
                bgcolor: 'rgba(0, 0, 0, 0)',
            },
        }, {
            responsive: true,
            displaylogo: false,
            modeBarButtons: [
                ['zoom2d', 'pan2d', 'resetScale2d', 'toImage'],
            ],
        });

        this.last.numSamplesInferred = this.model.numSamplesInferred;

        this.timer = setInterval(this.update, ONE_SECOND);
    },
    beforeDestroy() : void {
        if (this.timer) {
            clearInterval(this.timer);
        }
    },
});
</script>