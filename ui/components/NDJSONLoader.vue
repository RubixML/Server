<template>
    <ValidationObserver v-slot="{ invalid }">
        <ValidationProvider name="dataset" ref="provider" rules="ext:ndjson" v-slot="{ errors }">
            <div class="field is-grouped">
                <div class="control is-expanded">
                    <div class="file has-name is-medium is-fullwidth">
                        <label class="file-label">
                            <input class="file-input" type="file" accept=".ndjson" @change="changeFile($event)" />
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
                    <button class="button is-medium is-info"
                        :class="{ 'is-loading' : loading }"
                        :disabled="!ready || invalid"
                        @click="loadDataset()"
                    >Load Dataset</button>
                </div>
            </div>
            <p class="help has-text-first-letter-capitalized">{{ errors[0] }}</p>
        </ValidationProvider>
    </ValidationObserver>
</template>

<script lang="ts">
import Vue from 'vue';
import bus from '../providers/bus';

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
        ready() : boolean {
            return this.file && !this.loaded;
        },
    },
    methods: {
        loadDataset() : void {
            this.loading = true;

            let reader = new FileReader();
            
            reader.onload = function (event : ProgressEvent) {
                const data = String(this.result)
                    .split(EOL)
                    .filter(Boolean)
                    .map((line) => JSON.parse(line));

                const header = data[0] instanceof Array ? null : data[0].keys();
                
                bus.$emit('dataset-imported', {
                    dataset: {
                        data,
                        header,
                    },
                });
            };

            reader.onerror = function (event : ProgressEvent) {
                bus.$emit('dataset-import-failed', {
                    error: 'Unknown error.',
                });
            };

            reader.readAsText(this.file);

            this.loading = false;

            this.loaded = true;
        },
        changeFile(event : MouseEvent) : void {
            this.$refs.provider.validate(event).then((result) => {
                const target = event.target;

                if (result.valid && target instanceof HTMLInputElement) {
                    this.file = target.files[0];

                    this.loaded = false;
                }
            });
        },
    },
});
</script>

<style lang="scss" scoped>
.is-placeholder {
    opacity: 0.3;
}
</style>
