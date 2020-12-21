<template>
    <div class="columns">
        <div class="column is-three-quarters">
            <div class="field">
                <div class="control">
                    <div class="file has-name is-medium is-fullwidth">
                        <label class="file-label">
                            <input class="file-input" type="file" name="dataset" accept=".csv" @change="changeFile($event.target.files[0])" />
                            <span class="file-cta">
                                <span class="file-icon"><i class="fas fa-file-csv"></i></span>
                                <span class="file-label">Choose a file</span>
                            </span>
                            <span class="file-name" :class="{ 'is-placeholder' : !file }">
                                {{ file ? file.name : 'example.csv' }}
                            </span>
                        </label>
                    </div>
                    <div class="field mt-5">
                        <div class="control">
                            <span class="mr-2">File has a header?</span>
                            <label class="radio">
                                <input type="radio" :value="true" v-model="hasHeader" @change="loaded = false">
                                <span>Yes</span>
                            </label>
                            <label class="radio">
                                <input type="radio" :value="false" v-model="hasHeader" @change="loaded = false">
                                <span>No</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="column is-one-quarter">
            <button class="button is-medium is-danger is-fullwidth" :class="{ 'is-loading' : loading }" :disabled="disabled" @click="loadDataset()">Load Dataset</button>
        </div>
    </div>
</template>

<script>
import Papa from 'papaparse';
import bus from '../bus';

export default {
    data() {
        return {
            file: null,
            hasHeader: true,
            loading: false,
            loaded: false,
        };
    },
    computed: {
        disabled() {
            return this.loaded || !this.file;
        },
    },
    methods: {
        loadDataset() {
            this.loading = true;

            Papa.parse(this.file, {
                header: this.hasHeader,
                dynamicTyping: true,
                worker: true,
                skipEmptyLines: true,
                complete: (result) => {
                    bus.$emit('dataset-imported', {
                        dataset: {
                            data: result.data,
                            header: result.meta.fields,
                        },
                    });

                    this.loading = false;

                    this.loaded = true;
                },
                error: (error) => {
                    bus.$emit('dataset-import-failed', {
                        error,
                    });

                    this.loading = false;
                },
            });
        },
        changeFile(file) {
            this.file = file;

            this.loaded = false;
        },
    },
}
</script>

<style lang="scss" scoped>
.is-placeholder {
    opacity: 0.3;
}
</style>