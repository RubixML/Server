<template>
    <div>
        <section class="section">
            <div class="container">
                <csv-loader></csv-loader>
                <h2 class="title is-size-6 mt-5">Select the Columns To Plot</h2>
                <dataset-column-picker :dataset="dataset"></dataset-column-picker>
                <router-view></router-view>
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