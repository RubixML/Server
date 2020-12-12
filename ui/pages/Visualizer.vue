<template>
    <div>
        <section class="section">
            <div class="container">
                <dataset-loader></dataset-loader>
                <dataset-preview :dataset="dataset" :header="header"></dataset-preview>
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
            dataset: [
                //
            ],
            header: null,
        };
    },
    mounted() {
        bus.$on('dataset-imported', (payload) => {
            this.dataset = payload.results.data;
            this.header = payload.results.meta.fields;
        });
    }
}
</script>