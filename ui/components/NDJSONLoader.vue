<template>
    <div>
        <div class="field is-grouped">
            <div class="control is-expanded">
                <div class="file has-name is-medium is-fullwidth">
                    <label class="file-label">
                        <input class="file-input" type="file" name="dataset" accept=".ndjson" @change="changeFile($event.target.files[0])" />
                        <span class="file-cta">
                            <span class="file-icon"><i class="fas fa-file"></i></span>
                            <span class="file-label">Choose a file</span>
                        </span>
                        <span class="file-name" :class="{ 'is-placeholder' : !file }">
                            {{ file ? file.name : 'example.ndjson' }}
                        </span>
                    </label>
                </div>
            </div>
            <div class="control">
                <button class="button is-medium is-danger px-5" :class="{ 'is-loading' : loading }" :disabled="disabled" @click="loadDataset()">Load Dataset</button>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import bus from '../bus';

const EOL = '\n';

export default Vue.extend({
    data() {
        return {
            file: null,
            loading: false,
            loaded: false,
        };
    },
    computed: {
        disabled() : boolean {
            return this.loaded || !this.file;
        },
    },
    methods: {
        loadDataset() : void {
            this.loading = true;

            let reader = new FileReader();
            
            reader.onload = function (event) {
                const data = String(this.result).split(EOL)
                    .filter(Boolean)
                    .map((line) => {
                        return JSON.parse(line);
                    });

                const header = data[0] instanceof Array ? null : data[0].keys();
                
                bus.$emit('dataset-imported', {
                    dataset: {
                        data,
                        header,
                    },
                });
            };

            reader.onerror = function (event) {
                bus.$emit('dataset-import-failed', {
                    error: 'Unknown error',
                });
            };

            reader.readAsText(this.file);

            this.loading = false;

            this.loaded = true;
        },
        changeFile(file) : void {
            this.file = file;

            this.loaded = false;
        },
    },
});
</script>

<style lang="scss" scoped>
.is-placeholder {
    opacity: 0.3;
}
</style>