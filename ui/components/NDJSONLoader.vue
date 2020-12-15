<template>
    <div class="columns">
        <div class="column is-three-quarters">
            <div class="field">
                <div class="control">
                    <div class="file has-name is-medium is-fullwidth">
                        <label class="file-label">
                            <input class="file-input" type="file" name="dataset" accept=".ndjson" @change="changeFile($event.target.files[0])" />
                            <span class="file-cta">
                                <span class="file-icon"><i class="fas fa-file"></i></span>
                                <span class="file-label">Choose a file</span>
                            </span>
                            <span class="file-name" :class="file ? '' : 'is-placeholder'">
                                {{ file ? file.name : 'example.ndjson' }}
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="column is-one-quarter">
            <button class="button is-medium is-danger is-fullwidth" :class="loading ? 'is-loading' : ''" :disabled="disabled" @click="loadDataset()">Load Dataset</button>
        </div>
    </div>
</template>

<script>
import bus from '../bus';

const EOL = '\n';

export default {
    data() {
        return {
            file: null,
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

            let reader = new FileReader();
            
            reader.onload = function (event) {
                let lines = this.result.split(EOL).filter(Boolean);

                let data = [];

                lines.forEach((line) => {
                    data.push(JSON.parse(line));
                });
                
                bus.$emit('dataset-imported', {
                    dataset: {
                        data,
                        header: data[0] instanceof Array ? null : data[0].keys(),
                    },
                });
            };

            reader.onerror = function (event) {
                bus.$emit('dataset-import-failed', {
                    error: 'There was an error importing the dataset.',
                });

                this.loading = false;
            };

            reader.readAsText(this.file);

            this.loading = false;

            this.loaded = true;
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