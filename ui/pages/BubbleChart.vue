<template>
    <div>
        <section class="section">
            <div class="container">
                <h2 class="title is-size-5"><span class="icon mr-3"><i class="fas fa-table"></i></span>Data Columns</h2>
                <div class="columns">
                    <div class="column is-one-third">
                        <div class="field">
                            <label class="label">X Axis</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select v-model="settings.dataColumns.xAxis" @change="updateDataset()">
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
                            <label class="label">Y Axis</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select v-model="settings.dataColumns.yAxis" @change="updateDataset()">
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
                            <label class="label">Scale</label>
                            <div class="control">
                                <div class="select is-fullwidth">
                                    <select v-model="settings.dataColumns.scale" @change="updateDataset()">
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
                <h2 class="title is-size-5 mt-5"><span class="icon mr-3"><i class="fas fa-cogs"></i></span>Chart Settings</h2>
                <div class="columns">
                    <div class="column is-one-third">
                        <div class="field">
                            <label class="label">Radius</label>
                            <div class="control">
                                <input v-model="settings.radius" class="slider is-circle has-output is-fullwidth" step="1" min="1" max="50" type="range" @change="updateDataset()" />
                                <output>{{ settings.radius }}</output>
                            </div>
                        </div>
                    </div>
                    <div class="column is-one-third">
                        <div class="field">
                            <label class="label">Stroke</label>
                            <div class="control">
                                <input v-model="settings.stroke" class="slider is-circle has-output is-fullwidth" step="1" min="1" max="5" type="range" @change="updateStroke()" />
                                <output>{{ settings.stroke }}</output>
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
                                                    <input v-model="settings.color.r" class="slider is-danger is-circle has-output is-fullwidth" step="1" min="0" max="255" type="range" @change="updateColor()" />
                                                    <output>{{ settings.color.r }}</output>
                                                </div>
                                                <div>
                                                    <input v-model="settings.color.g" class="slider is-success is-circle has-output is-fullwidth" step="1" min="0" max="255" type="range" @change="updateColor()" />
                                                    <output>{{ settings.color.g }}</output>
                                                </div>
                                                <div>
                                                    <input v-model="settings.color.b" class="slider is-info is-circle has-output is-fullwidth" step="1" min="0" max="255" type="range" @change="updateColor()" />
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
                <figure>
                    <canvas id="dataset-bubble-chart" width="550" height="550"></canvas>
                </figure>
            </div>
        </section>
    </div>
</template>

<script>
import bus from '../bus';

export default {
    data() {
        return {
            settings: {
                dataColumns: {
                    xAxis: null,
                    yAxis: null,
                    scale: null,
                },
                radius: 5,
                stroke: 2,
                color: {
                    r: 143,
                    g: 59,
                    b: 222,
                },
            },
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
        rgbColorString() {
            return 'rgb(' + Object.values(this.settings.color).join(', ') + ')';
        },
        continuousHeaders() {
            return this.dataset.header.map((title, offset) => {
                return {
                    title,
                    offset,
                };
            }).filter((header, offset) => {
                return Number( this.dataset.data[0][offset]) == this.dataset.data[0][offset];
            });
        },
    },
    mounted() {
        let context = document.getElementById('dataset-bubble-chart').getContext('2d');

        this.chart = new Chart(context, {
            type: 'bubble',
            data: {
                datasets: [
                   {
                        label: 'Bubbles',
                        borderColor: this.rgbColorString,
                        borderWidth: this.settings.stroke,
                        fill: true,
                   },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    display: true,
                    text: 'Bubble Chart',
                },
                legend: {
                    display: false,
                },
                tooltips: {
                    enabled: true,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true,
                },
                scales: {
                    xAxes: [
                        {
                            scaleLabel: {
                                display: true,
                                labelString: '',
                            },
                            display: true,
                        },
                    ],
                    yAxes: [
                        {
                            scaleLabel: {
                                display: true,
                                labelString: '',
                            },
                            display: true,
                        }
                    ],
                },
            },
        });

        bus.$on('dataset-imported', (payload) => {
           this.settings.dataColumns.xAxis = null;
           this.settings.dataColumns.yAxis = null;
           this.settings.dataColumns.scale = null;

           this.updateDataset();
        });
    },
    methods: {
        updateDataset() {
            if (this.settings.dataColumns.xAxis !== null && this.settings.dataColumns.yAxis !== null) {
                this.chart.options.scales.xAxes[0].scaleLabel.labelString = this.dataset.header[this.settings.dataColumns.xAxis];
                this.chart.options.scales.yAxes[0].scaleLabel.labelString = this.dataset.header[this.settings.dataColumns.yAxis];

                if (this.settings.dataColumns.scale !== null) {
                    const values = this.dataset.data.map((row) => {
                        return row[this.settings.dataColumns.scale];
                    });

                    const min = Math.min(...values);
                    const max = Math.max(...values);

                    const delta = max - min;

                    var weights = values.map((value) => {
                        return (value - min) / delta;
                    });
                } else {
                    var weights = Array(this.dataset.data.length).fill(1.0);
                }

                const data = this.dataset.data.map((row, offset) => {
                    return {
                        x: row[this.settings.dataColumns.xAxis],
                        y: row[this.settings.dataColumns.yAxis],
                        r: weights[offset] * this.settings.radius,
                    };
                });

                this.chart.data.datasets[0].data = data;
            } else {
                this.chart.data.datasets[0].data = [];
            }

            this.chart.update();
        },
        updateStroke() {
            this.chart.data.datasets[0].borderWidth = this.settings.stroke;

            this.chart.update();
        },
        updateColor() {
            this.chart.data.datasets[0].borderColor = this.rgbColorString;

            this.chart.update();
        },
    },
}
</script>
