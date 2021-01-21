<template>
    <div>
        <section class="section">
            <div class="container">
                <h2 class="title is-size-4"><span class="icon mr-3"><i class="fas fa-sliders-h"></i></span>Chart Properties</h2>
                <div class="columns">
                    <div class="column is-one-third">
                        <div class="field">
                            <label for="x-axis-data" class="label">X Axis</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="x-axis-data" v-model="settings.dataColumns.xAxis" @change="updateDataset()">
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
                    <div class="column is-one-third">
                        <div class="field">
                            <label for="y-axis-data" class="label">Y Axis</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="y-axis-data" v-model="settings.dataColumns.yAxis" @change="updateDataset()">
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
                    <div class="column is-one-third">
                        <div class="field">
                            <label for="scale-data" class="label">Scale</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select id="scale-data" v-model="settings.dataColumns.scale" @change="updateDataset()">
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
                </div>
                <div class="columns">
                    <div class="column is-one-third">
                        <div class="field">
                            <label for="bubble-radius" class="label">Radius</label>
                            <div class="control">
                                <input class="slider is-circle has-output is-fullwidth"
                                    id="bubble-radius"
                                    type="range"
                                    v-model="settings.radius"
                                    step="1"
                                    min="1"
                                    max="100"
                                    @change="updateDataset()"
                                />
                                <output>{{ settings.radius }}</output>
                            </div>
                        </div>
                    </div>
                    <div class="column is-one-third">
                        <div class="field">
                            <label class="label">Color</label>
                            <div class="control">
                                <div class="dropdown is-hoverable">
                                    <div class="dropdown-trigger">
                                        <span class="button" aria-haspopup="true">
                                            <span class="tag mx-3 px-5" :style="{ background: rgbColorString }"></span>
                                            <span class="icon is-small">
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
                                                        v-model="settings.color.r"
                                                        step="1"
                                                        min="0"
                                                        max="255"
                                                        @change="updateDataset()"
                                                    />
                                                    <output>{{ settings.color.r }}</output>
                                                </div>
                                                <div>
                                                    <input class="slider is-success is-circle has-output is-fullwidth"
                                                        type="range"
                                                        v-model="settings.color.g"
                                                        step="1"
                                                        min="0"
                                                        max="255"
                                                        @change="updateDataset()"
                                                    />
                                                    <output>{{ settings.color.g }}</output>
                                                </div>
                                                <div>
                                                    <input class="slider is-info is-circle has-output is-fullwidth"
                                                        type="range"
                                                        v-model="settings.color.b"
                                                        step="1"
                                                        min="0"
                                                        max="255"
                                                        @change="updateDataset()"
                                                    />
                                                    <output>{{ settings.color.b }}</output>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="section">
            <div class="container">
                <h2 class="title is-size-4"><span class="icon mr-3"><i class="fas fa-eye"></i></span>Visualize</h2>
                <figure class="image is-5by3">
                    <div id="dataset-bubble-chart" class="has-ratio"></div>
                </figure>
            </div>
        </section>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import Plotly from 'plotly.js-basic-dist';
import { PURPLE } from '../chart-colors';
import bus from '../bus';

export default Vue.extend({
    data() {
        return {
            settings: {
                dataColumns: {
                    xAxis: null,
                    yAxis: null,
                    scale: null,
                },
                radius: 5,
                color: PURPLE,
            },
            layout: {
                legend: {
                    orientation: 'h',
                    y: 1.2,
                },
                xaxis: {
                    title: {
                        text: '',
                        font: {
                            size: 12,
                        },
                    },
                    type: 'auto',
                    gridcolor: 'rgb(128, 128, 128)',
                },
                yaxis: {
                    title: {
                        text: '',
                        font: {
                            size: 12,
                        },
                    },
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
        categoricalHeaders() : Object[] {
            return this.dataset.header.map((title : string, offset : number) => {
                return {
                    title,
                    offset,
                };
            }).filter((header, offset : number) => {
                return this.dataset.types[offset] === 'categorical';
            });
        },
        rgbColorString() : string {
            return 'rgb(' + Object.values(this.settings.color).join(', ') + ')';
        },
    },
    methods: {
        updateDataset() : void {
            if (this.settings.dataColumns.xAxis !== null && this.settings.dataColumns.yAxis !== null) {
                this.layout.xaxis.title.text = this.dataset.header[this.settings.dataColumns.xAxis];
                this.layout.yaxis.title.text = this.dataset.header[this.settings.dataColumns.yAxis];

                const xData = [];
                const yData = [];

                this.dataset.data.forEach((row : Array<string|number>) => {
                    xData.push(row[this.settings.dataColumns.xAxis]);
                    yData.push(row[this.settings.dataColumns.yAxis]);
                });

                var weights;

                if (this.settings.dataColumns.scale !== null) {
                    const values = this.dataset.data.map((row : Array<string|number>) => row[this.settings.dataColumns.scale]);

                    const min = Math.min(...values);
                    const max = Math.max(...values);

                    const delta = max - min;

                    weights = values.map((value : number) => (value - min) / delta);
                } else {
                    weights = Array(this.dataset.data.length).fill(1.0);
                }

                Plotly.react('dataset-bubble-chart', [{
                    x: xData,
                    y: yData,
                    mode: 'markers',
                    marker: {
                        symbol: 'circle',
                        color: this.rgbColorString,
                        size: weights.map((weight : number) => weight * this.settings.radius),
                    },
                }], this.layout);
            } else {
                Plotly.react('dataset-bubble-chart', [], this.layout);
            }
        },
    },
    mounted() {
        bus.$on('dataset-imported', () => {
           this.settings.dataColumns.xAxis = null;
           this.settings.dataColumns.yAxis = null;
           this.settings.dataColumns.scale = null;

           this.updateDataset();
        });

        Plotly.newPlot('dataset-bubble-chart', [], this.layout, {
            responsive: true,
            displayModeBar: true,
            displaylogo: false,
        });
    },
});
</script>
