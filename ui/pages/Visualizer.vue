<template>
    <div>
        <section class="section">
            <div class="container">
                <csv-loader></csv-loader>
            </div>
        </section>
        <section class="section">
            <div class="container">
                <dataset-column-picker :dataset="dataset"></dataset-column-picker>
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
