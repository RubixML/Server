<template>
    <div>
        <section class="section">
            <div class="container">
                <div class="tabs is-medium is-boxed">
                    <ul>
                        <li :class="loader === 'csv' ? 'is-active' : ''">
                            <a @click="loader = 'csv'">CSV</a>
                        </li>
                        <li :class="loader === 'ndjson' ? 'is-active' : ''">
                            <a @click="loader = 'ndjson'">NDJSON</a>
                        </li>
                    </ul>
                </div>
                <csv-loader v-if="loader === 'csv'"></csv-loader>
                <ndjson-loader v-if="loader === 'ndjson'"></ndjson-loader>
            </div>
        </section>
        <section class="section">
            <div class="container">
                <router-view :dataset="dataset"></router-view>
            </div>
        </section>
    </div>
</template>

<script>
import bus from '../bus';

export default {
    data() {
        return {
            loader: 'csv',
            dataset: {
                data: null,
                header: null,
            },
        };
    },
    mounted() {
        bus.$on('dataset-imported', (payload) => {
            if (payload.dataset.header) {
                this.dataset.header = payload.dataset.header;
            } else {
                this.dataset.header = [...payload.dataset.data[0].keys()].map((offset) => {
                    return 'Column ' + offset;
                });
            }

            this.dataset.data = payload.dataset.data.map((row) => {
                return row instanceof Array ? row : Object.values(row);
            });
        });
    }
}
</script>
