<template>
    <div>
        <section class="section">
            <div class="container">
                <div class="tabs is-medium is-boxed">
                    <ul>
                        <li :class="loader === 'csv' ? 'is-active' : ''">
                            <a @click="loader = 'csv'">CSV</a>
                        </li>
                    </ul>
                </div>
                <csv-loader v-if="loader === 'csv'"></csv-loader>
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
            this.dataset.data = payload.data;
            this.dataset.header = payload.header;
        });
    }
}
</script>
