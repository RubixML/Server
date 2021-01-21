<template>
    <div>
        <section class="section">
            <div class="container">
                <h2 class="title is-size-4"><span class="icon mr-3"><i class="fas fa-sliders-h"></i></span>Chart Properties</h2>
                <div v-for="(line, offset) in settings.lines" :key="offset" class="columns">
                    <div class="column is-one-quarter">
                        <div class="field">
                            <label :for="'line-' + offset + '-data'" class="label">Data Column</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select :id="'line-' + offset + '-data'" v-model="line.dataColumn" @change="updateDataset()">
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
                            <label :for="'line-' + offset + '-thickness'" class="label">Thickness</label>
                            <div class="control">
                                <input class="slider is-circle has-output is-fullwidth"
                                    :id="'line-' + offset + '-thickness'"
                                    type="range"
                                    v-model="line.thickness"
                                    step="1"
                                    min="1"
                                    max="5"
                                    @change="updateDataset()"
                                />
                                <output>{{ line.thickness }}</output>
                            </div>
                        </div>  
                    </div>
                    <div class="column is-one-quarter">
                        <div class="field">
                            <label :for="'line-' + offset + '-tension'" class="label">Tension</label>
                            <div class="control">
                                <input class="slider is-circle has-output is-fullwidth"
                                    :id="'line-' + offset + '-tension'"
                                    type="range"
                                    v-model="line.tension"
                                    step="0.1"
                                    min="0"
                                    max="1.3"
                                    @change="updateDataset()"
                                />
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
                                                    <input class="slider is-danger is-circle has-output is-fullwidth"
                                                        type="range"
                                                        v-model="line.color.r"
                                                        step="1"
                                                        min="0"
                                                        max="255"
                                                        @change="updateDataset()"
                                                    />
                                                    <output>{{ line.color.r }}</output>
                                                </div>
                                                <div>
                                                    <input class="slider is-success is-circle has-output is-fullwidth"
                                                        type="range"
                                                        v-model="line.color.g"
                                                        step="1"
                                                        min="0"
                                                        max="255"
                                                        @change="updateDataset()"
                                                    />
                                                    <output>{{ line.color.g }}</output>
                                                </div>
                                                <div>
                                                    <input class="slider is-info is-circle has-output is-fullwidth"
                                                        type="range"
                                                        v-model="line.color.b"
                                                        step="1"
                                                        min="0"
                                                        max="255"
                                                        @change="updateDataset()"
                                                    />
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
                <h2 class="title is-size-4"><span class="icon mr-3"><i class="fas fa-eye"></i></span>Visualize</h2>
                <figure class="image is-5by3">
                    <div id="dataset-line-chart" class="has-ratio"></div>
                </figure>
            </div>
        </section>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Plotly from 'plotly.js-basic-dist';
import { ALL_COLORS } from '../chart-colors';
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
            layout: {
                legend: {
                    orientation: 'h',
                    y: 1.2,
                },
                xaxis: {
                    type: 'linear',
                    gridcolor: 'rgb(128, 128, 128)',
                },
                yaxis: {
                    type: 'auto',
                    gridcolor: 'rgb(128, 128, 128)',
                },
                margin: {
                    l: 40,
                    r: 40,
                    t: 80,
                    b: 40,
                },
                paper_bgcolor: 'rgba(0, 0, 0, 0)',
                plot_bgcolor: 'rgba(0, 0, 0, 0)',
                modebar: {
                    color: 'rgb(128, 128, 128)',
                    activecolor: 'rgb(192, 192, 192)',
                    bgcolor: 'rgba(0, 0, 0, 0)',
                },
            },
        };
    },
    props: {
        dataset: {
            type: Object,
            required: true,
        },
    },
    computed: {
        continuousHeaders() : Object[] {
            return this.dataset.header.map((title : string, offset : number) => {
                return {
                    title,
                    offset,
                };
            }).filter((header, offset : number) => {
                return this.dataset.types[offset] === 'continuous';
            });
        },
    },
    methods: {
        addLine() : void {
            const color = ALL_COLORS[this.settings.lines.length % ALL_COLORS.length];

            this.settings.lines.push({
                dataColumn: null,
                color,
                thickness: 2,
                tension: 1.0,
            });
        },
        removeLine() : void {
            this.settings.lines.pop();

            this.updateDataset();
        },
        updateDataset() : void {
            let data = [];

            const labels = Array(this.dataset.data.length).keys();

            this.settings.lines.forEach((line) => {
                if (line.dataColumn !== null) {
                    const values = this.dataset.data.map((row : Array<string|number>) => {
                        return row[line.dataColumn];
                    });

                    data.push({
                        name: this.dataset.header[line.dataColumn],
                        x: labels,
                        y: values,
                        type: 'scatter',
                        line: {
                            width: line.thickness,
                            color: 'rgb(' + Object.values(line.color).join(', ') + ')',
                            shape: 'spline',
                            smoothing: 1.3 - line.tension,
                        },
                    });
                }
            });

            Plotly.react('dataset-line-chart', data, this.layout);
        },
    },
    mounted() {
        bus.$on('dataset-imported', () => {
            this.updateDataset();
        });

        this.addLine();

        Plotly.newPlot('dataset-line-chart', [], this.layout, {
            responsive: true,
            displayModeBar: true,
            displaylogo: false,
        });
    },
});
</script>
