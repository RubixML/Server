<template>
    <figure class="image is-3by2">
        <div id="memory-usage-chart" class="has-ratio"></div>
    </figure>
</template>

<script lang="ts">
import Vue from 'vue';
import Plotly from '../providers/plotly';
import gql from 'graphql-tag';

const MEGABYTE = 1000000;
const THREE_SECONDS = 3000;

export const fragment = gql`
    fragment MemoryUsageChart on Server {
        memory {
            current
            peak
        }
    }
`;

export default Vue.extend({
    data() {
        return {
            timer: null,
        };
    },
    props: {
        memory: {
            type: Object,
            required: true,
        },
    },
    computed: {
        current() : number {
            return this.memory.current / MEGABYTE;
        },
        peak() : number {
            return this.memory.peak / MEGABYTE;
        },
    },
    methods: { 
        update() : void {
            Plotly.extendTraces('memory-usage-chart', {y: [[this.current], [this.peak]]}, [0, 1], 1);
        },
    },
    mounted() : void {
        Plotly.newPlot('memory-usage-chart', [
            {
                name: 'Current',
                x: ['Memory'],
                y: [this.memory.current],
                type: 'bar',
                marker: {
                    color: 'rgb(35, 209, 96)',
                },
            },
            {
                name: 'Peak',
                x: ['Memory'],
                y: [this.memory.peak],
                type: 'bar',
                marker: {
                    color: 'rgb(255, 97, 131)',
                },
            },
        ], {
            legend: {
                orientation: 'h',
                y: 1.2,
            },
            xaxis: {
                title: {
                    text: 'Usage',
                    font: {
                        size: 12,
                    },
                },
                gridcolor: 'rgb(128, 128, 128)',
                fixedrange: true,
            },
            yaxis: {
                title: {
                    text: 'Megabytes',
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
            displayModeBar: true,
            displaylogo: false,
            modeBarButtons: [
                ['toImage'],
            ],
        });

        this.timer = setInterval(this.update, THREE_SECONDS);
    },
    beforeDestroy() : void {
        if (this.timer) {
            clearInterval(this.timer);
        }
    },
});
</script>