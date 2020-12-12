<template>
    <div class="columns">
        <div class="column is-three-quarters">
            <div class="file has-name is-medium is-fullwidth">
                <label class="file-label">
                    <input class="file-input" type="file" name="dataset" accept=".csv" @change="changeFile($event.target.files)" />
                    <span class="file-cta">
                        <span class="file-icon"><i class="fas fa-file-csv"></i></span>
                        <span class="file-label">Choose a file</span>
                    </span>
                    <span class="file-name">
                        {{ file.name }}
                    </span>
                </label>
            </div>
        </div>
        <div class="column is-one-quarter">
            <div class="buttons are-medium">
                <button class="button is-outlined" :class="header ? 'is-success' : 'is-light'" @click="header = !header"><span class="icon"><i class="fas fa-table"></i></span></button>
                <button class="button is-danger" @click="loadDataset()">Load Dataset</button>
            </div>
        </div>
    </div>
</template>

<script>
import Papa from 'papaparse';
import bus from '../bus';

export default {
    data() {
        return {
            file: {
                name: undefined,
                type: undefined,
            },
            header: false,
        };
    },
    methods: {
        changeFile(files) {
            this.file = files[0];
        },
        loadDataset() {
            Papa.parse(this.file, {
                header: this.header,
                dynamicTyping: true,
                worker: true,
                skipEmptyLines: true,
                complete(results, file) {
                    bus.$emit('dataset-imported', {
                        results,
                    });
                },
                error(error, file) {
                    bus.$emit('dataset-import-failed', {
                        error,
                    });
                },
            });
        },
    },
}
</script>