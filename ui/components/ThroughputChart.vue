<template>
    <figure id="throughput-chart"></figure>
</template>

<script lang="ts">
import Vue from 'vue';
import Plotly from 'plotly.js-basic-dist';
import gql from 'graphql-tag';

const MEGABYTE = 1000000;
const ONE_SECOND = 1000;
const DATASET_SIZE = 60;

export const fragment = gql`
    fragment ThroughputChart on Server {
        httpStats {
            transfers {
                received
                sent
            }
        }
    }
`;

export default Vue.extend({
    data() {
        return {
            last: {
                received: null,
                sent: null,
            },
            timer: null,
        };
    },
    props: {
        transfers: {
            type: Object,
            required: true,
        },
    },
    methods: { 
        update() : void {
            const received = (this.transfers.received - this.last.received) / MEGABYTE;
            const sent = (this.transfers.sent - this.last.sent) / MEGABYTE;
 
            this.last.received = this.transfers.received;
            this.last.sent = this.transfers.sent;

            Plotly.extendTraces('throughput-chart', {y: [[received], [sent]]}, [0, 1], DATASET_SIZE);
        },
    },
    mounted() {
        const labels = [...Array(DATASET_SIZE).keys()].reverse();
        const zeros = Array(DATASET_SIZE).fill(0);

        Plotly.newPlot('throughput-chart', [
            {
                name: 'Received',
                x: labels,
                y: zeros,
                type: 'scatter',
                line: {
                    width: 2,
                    color: 'rgb(32, 156, 238)',
                },
                fill: 'tozeroy',
                fillcolor: 'rgba(32, 156, 238, 0.1)',
            },
            {
                name: 'Sent',
                x: labels,
                y: zeros,
                type: 'scatter',
                line: {
                    width: 2,
                    color: 'rgb(184, 107, 255)',
                },
                fill: 'tozeroy',
                fillcolor: 'rgba(184, 107, 255, 0.1)',
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
                gridcolor: 'rgb(120, 120, 120)',
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
                gridcolor: 'rgb(120, 120, 120)',
            },
            margin: {
                l: 80,
                r: 40,
                t: 40,
                b: 40,
            },
            paper_bgcolor: 'rgba(0, 0, 0, 0)',
            plot_bgcolor: 'rgba(0, 0, 0, 0)',
        }, {
            responsive: true,
            displayModeBar: false,
        });

        this.last.received = this.transfers.received;
        this.last.sent = this.transfers.sent;

        this.timer = setInterval(this.update, ONE_SECOND);
    },
    beforeDestroy() {
        if (this.timer) {
            clearInterval(this.timer);
        }
    },
});
</script>