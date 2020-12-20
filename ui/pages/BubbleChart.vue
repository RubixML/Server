<template>
    <div>
        <h2 class="title is-size-5"><span class="icon mr-3"><i class="fas fa-cogs"></i></span>Chart Settings</h2>
        <div class="columns">
            <div class="column is-one-third">
                <div class="field">
                    <label class="label">X Axis</label>
                    <div class="control">
                        <div class="select">
                            <select v-model="dataColumns.xAxis" @change="updateDataset()">
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
                        <div class="select">
                            <select v-model="dataColumns.yAxis" @change="updateDataset()">
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
                        <div class="select">
                            <select v-model="dataColumns.scale" @change="updateDataset()">
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
        <div class="columns mb-5">
            <div class="column is-one-third">
                <div class="field">
                    <label class="label">Bubble Radius</label>
                    <div class="control">
                        <input v-model="bubbleRadius" class="slider is-circle has-output is-fullwidth" step="1" min="1" max="50" type="range" @change="updateDataset()" />
                        <output>{{ bubbleRadius }}</output>
                    </div>
                </div>
            </div>
            <div class="column is-one-third">
                <div class="field">
                    <label class="label">Bubble Stroke</label>
                    <div class="control">
                        <input v-model="bubbleStroke" class="slider is-circle has-output is-fullwidth" step="1" min="1" max="5" type="range" @change="updateBubbleStroke()" />
                        <output>{{ bubbleStroke }}</output>
                    </div>
                </div>
            </div>
            <div class="column is-one-third">
                <div class="field">
                    <label class="label">Bubble Color</label>
                    <div class="control">
                        <div class="dropdown" :class="{ 'is-active' : colorPickerOpen }">
                            <div class="dropdown-trigger">
                                <span class="button" aria-haspopup="true" aria-controls="dropdown-menu2" @click="colorPickerOpen = !colorPickerOpen">
                                    <span class="tag mx-2 px-5" :style="{ background: bubbleColorRgbString }"></span>
                                    <span class="icon is-small">
                                        <i class="fas fa-angle-down" aria-hidden="true"></i>
                                    </span>
                                </span>
                            </div>
                            <div class="dropdown-menu" role="menu">
                                <div class="dropdown-content">
                                    <div class="dropdown-item">
                                        <div>
                                            <input v-model="bubbleColor.r" class="slider is-danger is-circle has-output is-fullwidth" step="1" min="0" max="255" type="range" @change="updateBubbleColor()" />
                                            <output>{{ bubbleColor.r }}</output>
                                        </div>
                                        <div>
                                            <input v-model="bubbleColor.g" class="slider is-success is-circle has-output is-fullwidth" step="1" min="0" max="255" type="range" @change="updateBubbleColor()" />
                                            <output>{{ bubbleColor.g }}</output>
                                        </div>
                                        <div>
                                            <input v-model="bubbleColor.b" class="slider is-info is-circle has-output is-fullwidth" step="1" min="0" max="255" type="range" @change="updateBubbleColor()" />
                                            <output>{{ bubbleColor.b }}</output>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <figure class="mt-5">
            <canvas id="dataset-bubble-chart" width="550" height="550"></canvas>
        </figure>
    </div>
</template>

<script>
export default {
    data() {
        return {
            dataColumns: {
                xAxis: null,
                yAxis: null,
                scale: null,
            },
            bubbleColor: {
                r: 143,
                g: 59,
                b: 222,
            },
            colorPickerOpen: false,
            bubbleRadius: 5,
            bubbleStroke: 2,
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
        bubbleColorRgbString() {
            return 'rgb(' + Object.values(this.bubbleColor).join(', ') + ')';
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
                        borderColor: this.bubbleColorRgbString,
                        borderWidth: this.bubbleStroke,
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
    },
    methods: {
        updateDataset() {
            if (this.dataColumns.xAxis !== null && this.dataColumns.yAxis !== null) {
                this.chart.options.scales.xAxes[0].scaleLabel.labelString = this.dataset.header[this.dataColumns.xAxis];
                this.chart.options.scales.yAxes[0].scaleLabel.labelString = this.dataset.header[this.dataColumns.yAxis];

                if (this.dataColumns.scale !== null) {
                    let values = [];

                    this.dataset.data.forEach((row) => {
                        values.push(row[this.dataColumns.scale])
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

                let data = [];

                this.dataset.data.forEach((row, offset) => {
                    data.push({
                        x: row[this.dataColumns.xAxis],
                        y: row[this.dataColumns.yAxis],
                        r: weights[offset] * this.bubbleRadius,
                    });
                });

                this.chart.data.datasets[0].data = data;
            } else {
                this.chart.data.datasets[0].data = [];
            }

            this.chart.update();
        },
        updateBubbleStroke() {
            this.chart.data.datasets[0].borderWidth = this.bubbleStroke;

            this.chart.update();
        },
        updateBubbleColor() {
            this.chart.data.datasets[0].borderColor = this.bubbleColorRgbString;

            this.chart.update();
        },
    },
}
</script>
