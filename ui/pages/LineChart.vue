<template>
    <div>
        <section class="section">
            <div class="container">
                <h2 class="title is-size-4"><span class="icon mr-3"><i class="fas fa-sliders-h"></i></span>Chart Properties</h2>
                <div v-for="(line, offset) in settings.lines" :key="offset" class="columns">
                    <div class="column is-one-third">
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
                    <div class="column is-one-third">
                        <div class="field">
                            <label :for="'line-' + offset + '-thickness'" class="label">Thickness</label>
                            <div class="control">
                                <input class="slider is-circle has-output is-fullwidth"
                                    :id="'line-' + offset + '-thickness'"
                                    type="range"
                                    v-model="line.thickness"
                                    step="1"
                                    min="1"
                                    max="10"
                                    @change="updateDataset()"
                                />
                                <output>{{ line.thickness }}</output>
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
import Plotly from '../providers/plotly';
import { ALL_COLORS } from '../chart-colors';
import bus from '../providers/bus';

const CHART_LAYOUT = {
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
};

export default Vue.extend({
    data() {
        return {
            settings: {
                lines: [],
                maxLines: 10,
            },
            labels: null,
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
            return this.dataset.headers.filter((header, offset : number) => {
                return this.dataset.types[offset] === 'continuous';
            });
        },
        activeLines() : any[] {
            return this.settings.lines.filter((line) => {
                return line.dataColumn !== null;
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
            });
        },
        removeLine() : void {
            const line = this.settings.lines.pop();

            if (line.dataColumn !== null) {
                this.updateDataset();
            }
        },
        updateDataset() : void {
            let data : any[] = [];

            this.activeLines.forEach((line) => {
                const name : string = this.dataset.headers[line.dataColumn];

                const values : number[] = this.dataset.data.map((row : (string|number)[]) => {
                    return row[line.dataColumn];
                });

                const color : string = 'rgb(' + Object.values(line.color).join(', ') + ')';

                const trace = {
                    name,
                    x: this.labels,
                    y: values,
                    type: 'scattergl',
                    mode: 'lines',
                    line: {
                        width: line.thickness,
                        color,
                    },
                };

                data.push(trace);
            });

            Plotly.react('dataset-line-chart', data, CHART_LAYOUT);
        },
    },
    mounted() : void {
        bus.$on('dataset-imported', (event) => {
            this.labels = Array(event.dataset.data.length).keys();

            this.updateDataset();
        });

        this.addLine();

        Plotly.newPlot('dataset-line-chart', [], CHART_LAYOUT, {
            responsive: true,
            displayModeBar: true,
            displaylogo: false,
        });
    },
});
</script>
