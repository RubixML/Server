<template>
    <div class="modal" :class="{ 'is-active' : open }">
        <div class="modal-background"></div>
        <div class="modal-content">
            <div class="box has-text-centered">
                <div class="block">
                    <span class="icon is-large">
                        <span class="fa-stack fa-lg">
                            <i class="fas fa-database fa-stack-1x"></i>
                            <i class="fas fa-ban fa-stack-2x has-text-danger"></i>
                        </span>
                    </span>
                </div>
                <p class="block is-size-5">
                    There was a problem importing the dataset.
                </p>
                <p class="block help">
                    {{ message }}
                </p>
                <div class="block buttons is-centered">
                    <button class="button is-danger is-outlined" @click="open = false">
                        <span class="icon"><i class="fas fa-times"></i></span>
                        <span>Dismiss</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import bus from '../providers/bus';

const VIBRATE_PATTERN = [100, 30, 100];

export default Vue.extend({
    data() {
        return {
            sound: null,
            open: false,
            message: 'Unknown error.',
        };
    },
    methods: {
        ding() : void {
            if (this.sound) {
                this.sound.play();
            }
        },
        vibrate() : void {
            window.navigator.vibrate(VIBRATE_PATTERN);
        },
    },
    mounted() {
        const element : HTMLElement = document.getElementById('sharp');

        if (element instanceof HTMLAudioElement) {
            this.sound = element;
        }

        bus.$on('dataset-import-failed', (event) => {
            if (!this.open) {
                this.message = event.error.message;
                this.open = true;
                
                this.ding();
                this.vibrate();
            }
        });
    },
});
</script>
