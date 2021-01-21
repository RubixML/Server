<template>
    <ValidationObserver v-slot="{ invalid }">
        <ValidationProvider name="dataset" ref="provider" rules="ext:csv" v-slot="{ errors }">
            <div class="field is-grouped">
                <div class="control is-expanded">
                    <div class="file has-name is-medium is-fullwidth">
                        <label class="file-label">
                            <input class="file-input" type="file" accept=".csv" @change="changeFile($event)" />
                            <span class="file-cta">
                                <span class="file-icon"><i class="fas fa-file"></i></span>
                                <span class="file-label">Choose a file</span>
                            </span>
                            <span class="file-name" :class="{ 'is-placeholder' : !file }">
                                {{ file ? file.name : 'example.csv' }}
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
    </ValidationObserver>
</template>

<script lang="ts">
import Vue from 'vue';
import Papa, { ParseError, ParseResult } from 'papaparse';
import bus from '../bus';

export default Vue.extend({
    data() {
        return {
            file: null,
            hasHeader: true,
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

            Papa.parse(this.file, {
                header: this.hasHeader,
                dynamicTyping: true,
                worker: true,
                skipEmptyLines: true,
                complete: (result : ParseResult<any>) => {
                    bus.$emit('dataset-imported', {
                        dataset: {
                            data: result.data,
                            header: result.meta.fields,
                        },
                    });

                    this.loading = false;

                    this.loaded = true;
                },
                error: (error : ParseError) => {
                    bus.$emit('dataset-import-failed', {
                        error,
                    });

                    this.loading = false;
                },
            });
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
