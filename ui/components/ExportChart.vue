<template>
    <div>
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
        <div class="field is-grouped">
            <div class="control has-icons-left is-expanded">
                <input class="input is-medium" type="text" v-model="filename" placeholder="Filename" />
                <span class="icon is-left"><i class="fas fa-file-image"></i></span>
            </div>
            <div class="control">
                <a :href="imageData" :download="filename" class="button is-medium is-info px-5" :class="{ 'is-loading' : rendering }" :disabled="rendering" @click="saveChart()">Save Chart</a>
            </div>
        </div>
        <div class="columns">
            <div v-show="isLossy" class="column is-one-third">
                <div class="field mt-5">
                    <label class="label">Quality</label>
                    <div class="control">
                        <input v-model="qualityPercentage" class="slider is-circle has-output" step="1" min="0" max="100" type="range" />
                        <output>{{ qualityPercentage }}</output>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
        saveChart() : void {
            this.rendering = true;

            this.imageData = this.canvas.toDataURL(this.format, this.quality);

            this.rendering = false;
        },
    }
});
</script>