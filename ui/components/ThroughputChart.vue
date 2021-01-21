<template>
    <figure class="image is-16by9">
        <div id="throughput-chart" class="has-ratio"></div>
    </figure>
</template>

<script lang="ts">
import Vue from 'vue';
import Plotly from '../providers/plotly';
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
            const received : number = (this.transfers.received - this.last.received) / MEGABYTE;
            const sent : number = (this.transfers.sent - this.last.sent) / MEGABYTE;

            Plotly.extendTraces('throughput-chart', {y: [[received], [sent]]}, [0, 1], DATASET_SIZE);

            this.last.received = this.transfers.received;
            this.last.sent = this.transfers.sent;
        },
    },
    mounted() : void {
        const labels : number[] = [...Array(DATASET_SIZE).keys()].reverse();
        const zeros : number[] = Array(DATASET_SIZE).fill(0);

        Plotly.newPlot('throughput-chart', [
            {
                name: 'Received',
                x: labels,
                y: zeros,
                type: 'scattergl',
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
                mode: 'lines',
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

        this.last.received = this.transfers.received;
        this.last.sent = this.transfers.sent;

        this.timer = setInterval(this.update, ONE_SECOND);
    },
    beforeDestroy() : void {
        if (this.timer) {
            clearInterval(this.timer);
        }
    },
});
</script>
