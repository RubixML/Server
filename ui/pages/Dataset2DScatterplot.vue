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
                    <label class="label">Point Color</label>
                    <div class="control">
                        <div class="dropdown" :class="{ 'is-active' : colorPickerOpen }">
                            <div class="dropdown-trigger">
                                <span class="button" aria-haspopup="true" aria-controls="dropdown-menu2" @click="colorPickerOpen = !colorPickerOpen">
                                    <span>Color Picker</span>
                                    <span class="icon is-small">
                                        <i class="fas fa-angle-down" aria-hidden="true"></i>
                                    </span>
                                </span>
                            </div>
                            <div class="dropdown-menu" role="menu">
                                <div class="dropdown-content">
                                    <div class="dropdown-item">
                                        <input v-model="pointColor.r" class="slider is-danger is-circle is-fullwidth" step="1" min="0" max="255" type="range" @change="updatePointColor()" />
                                        <input v-model="pointColor.g" class="slider is-success is-circle is-fullwidth" step="1" min="0" max="255" type="range" @change="updatePointColor()" />
                                        <input v-model="pointColor.b" class="slider is-info is-circle is-fullwidth" step="1" min="0" max="255" type="range" @change="updatePointColor()" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <figure class="mt-5">
            <canvas id="dataset-2d-scatterplot" width="550" height="550"></canvas>
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
            },
            colorPickerOpen: false,
            pointColor: {
                r: 143,
                g: 59,
                b: 222,
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
        pointColorRgbString() {
            return 'rgb(' + Object.values(this.pointColor).join(', ') + ')';
        },
    },
    mounted() {
        let context = document.getElementById('dataset-2d-scatterplot').getContext('2d');

        this.chart = new Chart(context, {
            type: 'scatter',
            data: {
                datasets: [
                   {
                        label: 'Points',
                        borderColor: this.pointColorRgbString,
                        borderWidth: 1,
                        hoverBorderWidth: 1,
                        fill: false,
                   },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    display: true,
                    text: 'Scatterplot (2D)',
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

                let data = [];

                this.dataset.data.forEach((row) => {
                    data.push({
                        x: row[this.dataColumns.xAxis],
                        y: row[this.dataColumns.yAxis],
                    });
                });

                this.chart.data.datasets[0].data = data;
            } else {
                this.chart.data.datasets[0].data = [];
            }

            this.chart.update();
        },
        updatePointColor() {
            this.chart.data.datasets[0].borderColor = this.pointColorRgbString;

            this.chart.update();
        },
    },
}
</script>
