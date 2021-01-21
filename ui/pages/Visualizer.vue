<template>
    <div>
        <section class="section">
            <div class="container">
                <h2 class="title is-size-4"><span class="icon mr-3"><i class="fas fa-file-import"></i></span>Import Dataset</h2>
                <div class="tabs is-medium is-boxed">
                    <ul>
                        <li :class="{ 'is-active' : loader === 'csv' }">
                            <a @click="loader = 'csv'">CSV</a>
                        </li>
                        <li :class="{ 'is-active' : loader === 'ndjson' }">
                            <a @click="loader = 'ndjson'">NDJSON</a>
                        </li>
                    </ul>
                </div>
                <csv-loader v-show="loader === 'csv'"></csv-loader>
                <ndjson-loader v-show="loader === 'ndjson'"></ndjson-loader>
                <dataset-preview :dataset="dataset" class="mt-5"></dataset-preview>
            </div>
        </section>
        <router-view :dataset="dataset"></router-view>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import bus from '../providers/bus';

export default Vue.extend({
    data() {
        return {
            dataset: {
                data: [],
                types: [],
                header: [],
            },
            loader: 'csv',
        };
    },
    mounted() {
        bus.$on('dataset-imported', payload => {
            if (payload.dataset.header) {
                this.dataset.header = payload.dataset.header;
            } else {
                this.dataset.header = [...payload.dataset.data[0].keys()].map((offset : number) => 'Column ' + offset);
            }

            this.dataset.data = payload.dataset.data.map((row : Array<string|number>) => {
                if (!(row instanceof Array)) {
                    return Object.values(row);
                }

                return row;
            }).map(row => {
                return row.map((value : string|number) => {
                    if (Number(value) == value) {
                        return Number(value);
                    }

                    return value;
                });
            });

            this.dataset.types = this.dataset.data[0].map((value : string|number) => {
                return typeof value === 'string' ? 'categorical' : 'continuous';
            });
        });
    },
});
</script>
