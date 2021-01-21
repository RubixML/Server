<template>
    <figure class="image is-3by1">
        <div id="response-rate-chart" class="has-ratio"></div>
    </figure>
</template>

<script lang="ts">
import Vue from 'vue';
import Plotly from '../providers/plotly';
import gql from 'graphql-tag';

const ONE_SECOND = 1000;
const DATASET_SIZE = 60;

export const fragment = gql`
    fragment ResponseRateChart on Server {
        httpStats {
            requests {
                successful
                rejected
                failed
            }
        }
    }
`;

export default Vue.extend({
    data() {
        return {
            datasets: {
                total: [],
            },
            last: {
                successful: null,
                rejected: null,
                failed: null,
            },
            timer: null,
        };
    },
    props: {
        requests: {
            type: Object,
            required: true,
        },
    },
    methods: { 
        update() : void {
            const successful : number = this.requests.successful - this.last.successful;
            const rejected : number = this.requests.rejected - this.last.rejected;
            const failed : number = this.requests.failed - this.last.failed;

            const total : number = successful + rejected + failed;

            this.datasets.total.push(total);

            if (this.datasets.total.length > DATASET_SIZE) {
                this.datasets.total = this.datasets.total.slice(-DATASET_SIZE);
            }
            
            const mu : number = this.datasets.total.reduce((sigma : number, count : number) => sigma + count, 0) / this.datasets.total.length;

            Plotly.extendTraces('response-rate-chart', {y: [[mu], [successful], [rejected], [failed]]}, [0, 1, 2, 3], DATASET_SIZE);
        
            this.last.successful = this.requests.successful;
            this.last.rejected = this.requests.rejected;
            this.last.failed = this.requests.failed;
        },
    },
    mounted() : void {
        const labels : number[] = [...Array(DATASET_SIZE).keys()].reverse();
        const zeros : number[] = Array(DATASET_SIZE).fill(0);

        Plotly.newPlot('response-rate-chart', [
            {
                name: 'Average',
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
            {
                name: 'Successful',
                x: labels,
                y: zeros,
                type: 'scatter',
                line: {
                    width: 2,
                    color: 'rgb(35, 209, 96)',
                },
                fill: 'tozeroy',
                fillcolor: 'rgba(35, 209, 96, 0.1)',
            },
            {
                name: 'Rejected',
                x: labels,
                y: zeros,
                type: 'scatter',
                line: {
                    width: 2,
                    color: 'rgb(255, 159, 64)',
                },
                fill: 'tozeroy',
                fillcolor: 'rgba(255, 159, 64, 0.1)',
            },
            {
                name: 'Failed',
                x: labels,
                y: zeros,
                type: 'scatter',
                line: {
                    width: 2,
                    color: 'rgb(255, 97, 131)',
                },
                fill: 'tozeroy',
                fillcolor: 'rgba(255, 97, 131, 0.1)',
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
                autorange: 'reversed',
                gridcolor: 'rgb(120, 120, 120)',
                fixedrange: true,
            },
            yaxis: {
                title: {
                    text: 'Requests',
                    font: {
                        size: 12,
                    },
                },
                rangemode: 'tozero',
                gridcolor: 'rgb(120, 120, 120)',
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

        this.last.successful = this.requests.successful;
        this.last.rejected = this.requests.rejected;
        this.last.failed = this.requests.failed;

        this.timer = setInterval(this.update, ONE_SECOND);
    },
    beforeDestroy() : void {
        if (this.timer) {
            clearInterval(this.timer);
        }
    },
});
</script>
