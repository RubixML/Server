<template>
    <div class="modal" :class="{ 'is-active' : open }">
        <div class="modal-background"></div>
        <div class="modal-content">
            <div class="card has-text-centered">
                <div class="card-content">
                    <div class="block">
                        <span class="icon is-large">
                            <span class="fa-stack fa-lg">
                                <i class="fas fa-database fa-stack-1x"></i>
                                <i class="fas fa-ban fa-stack-2x has-text-danger"></i>
                            </span>
                        </span>
                    </div>
                    <div class="block">
                        <p class="is-size-5">
                            There was a problem importing the dataset.
                        </p>
                    </div>
                    <div class="block">
                        <p class="help">
                            {{ message }}
                        </p>
                    </div>
                </div>
                <footer class="card-footer">
                    <div class="card-footer-item">
                        <div class="field is-grouped">
                            <div class="control">
                                <button class="button" @click="open = false">
                                    <span class="icon"><i class="fas fa-times"></i></span>
                                    <span>Dismiss</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import bus from '../bus';

const VIBRATE_PATTERN = [100, 30, 100];

export default Vue.extend({
    data() {
        return {
            sound: null,
            open: false,
            message: 'Unknown error.',
        };
    },
    mounted() {
        const element = document.getElementById('sharp');

        if (element instanceof HTMLAudioElement) {
            this.sound = element;
        } else {
            console.log('Notification sound not found on DOM.');
        }

        bus.$on('dataset-import-failed', (payload) => {
            if (!this.open) {
                this.message = payload.error.message;
                this.open = true;
                
                this.beep();
                this.vibrate();
            }
        });
    },
    methods: {
        beep() : void {
            if (this.sound) {
                this.sound.play();
            }
        },
        vibrate() : void {
            window.navigator.vibrate(VIBRATE_PATTERN);
        },
    },
});
</script>
