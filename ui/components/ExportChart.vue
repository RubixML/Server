<template>
    <ValidationObserver v-slot="{ invalid }">
        <div class="tabs is-medium is-boxed">
            <ul>
                <li :class="{ 'is-active' : format === 'image/png' }">
                    <a @click="format = 'image/png'">PNG</a>
                </li>
                <li :class="{ 'is-active' : format === 'image/webp' }">
                    <a @click="format = 'image/webp'">WEBP</a>
                </li>
            </ul>
        </div>
        <ValidationProvider name="filename" ref="beek" rules="required|max:255" v-slot="{ errors }">
            <div class="field is-grouped">
                <div class="control has-icons-left is-expanded">
                    <input class="input is-medium" type="text" maxlength="255" v-model="filename" placeholder="Filename" />
                    <span class="icon is-left"><i class="fas fa-file"></i></span>
                </div>
                <div class="control">
                    <button class="button is-medium is-info"
                        :class="{ 'is-loading' : rendering }"
                        :download="filename"
                        :disabled="rendering || invalid"
                        @click="saveChart($event)"
                    >Save Chart</button>
                </div>
            </div>
            <p class="help has-text-first-letter-capitalized">{{ errors[0] }}</p>
        </ValidationProvider>
        <div class="columns">
            <div v-show="isLossy" class="column is-one-third">
                <div class="field mt-5">
                    <label class="label">Quality</label>
                    <div class="control">
                        <input class="slider is-circle has-output"
                            type="range"
                            v-model="qualityPercentage"
                            step="1"
                            min="0"
                            max="100"
                        />
                        <output>{{ qualityPercentage }}</output>
                    </div>
                </div>
            </div>
        </div>
    </ValidationObserver>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
    data() {
        return {
            filename: '',
            format: 'image/png',
            qualityPercentage: 92,
            imageData: null,
            rendering: false,
        };
    },
    props: {
        canvas: {
            type: HTMLCanvasElement,
            required: true,
        },
    },
    computed: {
        quality() : number {
            return this.qualityPercentage / 100;
        },
        isLossy() : boolean {
            return this.format === 'image/webp';
        },
    },
    methods: {
        saveChart(event : MouseEvent) : void {
            this.rendering = true;

            let link = document.createElement('a');

            link.download = this.filename;
            link.href = this.canvas.toDataURL(this.format, this.quality);

            link.click();
            link.remove();

            this.rendering = false;
        },
    },
});
</script>