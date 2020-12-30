<template>
    <div>
        <section class="section">
            <div class="container">
                <h2 class="title is-size-5"><span class="icon mr-3"><i class="fas fa-cogs"></i></span>Chart Properties</h2>
                <div v-for="(line, offset) in settings.lines" :key ="offset" class="columns">
                    <div class="column is-one-quarter">
                        <div class="field">
                            <label class="label">Data Column</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select v-model="line.dataColumn" @change="updateDataset()">
                                        <option :value="null">Select column</option>
                                        <option v-for="(header, offset) in continuousHeaders"
                                            :key="offset"
                                            :value="header.offset"
                                        >{{ header.title }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="column is-one-quarter">
                        <div class="field">
                            <label class="label">Thickness</label>
                            <div class="control">
                                <input v-model="line.thickness" class="slider is-circle has-output is-fullwidth" step="1" min="1" max="5" type="range" @change="updateDataset()" />
                                <output>{{ line.thickness }}</output>
                            </div>
                        </div>  
                    </div>
                    <div class="column is-one-quarter">
                        <div class="field">
                            <label class="label">Tension</label>
                            <div class="control">
                                <input v-model="line.tension" class="slider is-circle has-output is-fullwidth" step="0.1" min="0" max="1" type="range" @change="updateDataset()" />
                                <output>{{ line.tension }}</output>
                            </div>
                        </div>  
                    </div>
                    <div class="column is-one-quarter">
                        <div class="field">
                            <label class="label">Color</label>
                            <div class="control">
                                <div class="dropdown is-hoverable">
                                    <div class="dropdown-trigger">
                                        <span class="button" aria-haspopup="true">
                                            <span class="tag mx-3 px-5" :style="{ background: 'rgb(' + Object.values(line.color).join(', ') + ')' }"></span>
                                            <span class="icon">
                                                <i class="fas fa-angle-down" aria-hidden="true"></i>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="dropdown-menu" role="menu">
                                        <div class="dropdown-content">
                                            <div class="dropdown-item">
                                                <div>
                                                    <input v-model="line.color.r" class="slider is-danger is-circle has-output is-fullwidth" step="1" min="0" max="255" type="range" @change="updateDataset()" />
                                                    <output>{{ line.color.r }}</output>
                                                </div>
                                                <div>
                                                    <input v-model="line.color.g" class="slider is-success is-circle has-output is-fullwidth" step="1" min="0" max="255" type="range" @change="updateDataset()" />
                                                    <output>{{ line.color.g }}</output>
                                                </div>
                                                <div>
                                                    <input v-model="line.color.b" class="slider is-info is-circle has-output is-fullwidth" step="1" min="0" max="255" type="range" @change="updateDataset()" />
                                                    <output>{{ line.color.b }}</output>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="has-text-centered">
                    <button class="button" @click="addLine()" :disabled="settings.lines.length >= settings.maxLines">
                        <span class="icon"><i class="fas fa-plus"></i></span><span>Add Line</span>
                    </button>
                    <button class="button" @click="removeLine()" :disabled="settings.lines.length <= 1">
                        <span class="icon"><i class="fas fa-minus"></i></span><span>Remove Line</span>
                    </button>
                </div>
            </div>
        </section>
        <section class="section">
            <div class="container">
                <h2 class="title is-size-5 mt-5"><span class="icon mr-3"><i class="fas fa-eye"></i></span>Visualize</h2>
                <figure>
                    <canvas id="dataset-line-chart" width="550" height="550"></canvas>
                </figure>
            </div>
        </section>
        <section class="section">
            <div class="container">
                <h2 class="title is-size-5"><span class="icon mr-3"><i class="fas fa-file-export"></i></span>Export Chart</h2>
                <export-chart v-if="canvas" :canvas="canvas"></export-chart>
            </div>
        </section>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Chart from 'chart.js';
import bus from '../bus';

export default Vue.extend({
    data() {
        return {
            settings: {
                lines: [
                    //
                ],
                maxLines: 10,
            },
            canvas: null,
            chart: null,
        };
    },
    props: {
        dataset: {
            type: Object,
            required: true,
        },
    },
    computed: {
        continuousHeaders() : any[] {
            return this.dataset.header.map((title, offset) => {
                return {
                    title,
                    offset,
                };
            }).filter((header, offset) => {
                return Number(this.dataset.data[0][offset]) == this.dataset.data[0][offset];
            });
        },
    },
    mounted() {
        const element = document.getElementById('dataset-line-chart');

        if (!(element instanceof HTMLCanvasElement)) {
            console.log('Canvas not found!');

            return;
        }

        const context = element.getContext('2d');

        this.canvas = element;

        this.chart = new Chart(context, {
            type: 'line',
            data: {
                labels: [
                    //
                ],
                datasets: [
                    //
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    text: 'Line Chart',
                    display: false,
                },
                legend: {
                    display: true,
                },
                tooltips: {
                    enabled: true,
                    mode: 'nearest',
                },
                hover: {
                    mode: 'nearest',
                    intersect: true,
                },
                scales: {
                    xAxes: [
                        {
                            scaleLabel: {
                                display: false,
                                labelString: '',
                            },
                            display: true,
                        },
                    ],
                    yAxes: [
                        {
                            scaleLabel: {
                                display: false,
                                labelString: '',
                            },
                            display: true,
                        }
                    ],
                },
            },
        });

        bus.$on('dataset-imported', (payload) => {
            this.chart.data.labels = [...Array(payload.dataset.data.length).keys()];

            this.updateDataset();
        });

        this.addLine();
    },
    methods: {
        addLine() : void {
            this.settings.lines.push({
                dataColumn: null,
                thickness: 2,
                tension: 1.0,
                color: {
                    r: 143,
                    g: 59,
                    b: 222,
                },
                colorPickerOpen: false,
            });
        },
        removeLine() : void {
            this.settings.lines.pop();

            this.updateDataset();
        },
        updateDataset() : void {
            this.chart.data.datasets = [];

            this.settings.lines.forEach((line) => {
                if (line.dataColumn !== null) {
                    const values = this.dataset.data.map((row) => {
                        return row[line.dataColumn];
                    });

                    this.chart.data.datasets.push({
                        label: this.dataset.header[line.dataColumn],
                        data: values,
                        borderWidth: line.thickness,
                        borderColor: 'rgb(' + Object.values(line.color).join(', ') + ')',
                        tension: 1.0 - line.tension,
                        pointRadius: 0,
                        pointHitRadius: line.thickness,
                        fill: false,
                    });
                }
            });

            this.chart.update();
        },
    },
});
</script>
