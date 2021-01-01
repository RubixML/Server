<template>
    <div class="modal" :class="{ 'is-active' : open }">
        <div class="modal-background"></div>
        <div class="modal-content">
            <div class="card has-text-centered">
                <div class="card-content">
                    <div class="block">
                        <span class="icon is-large"><i class="fas fa-3x fa-rocket"></i></span>
                    </div>
                    <div class="block">
                        <p class="is-size-5">
                            An update is ready to be installed!
                        </p>
                    </div>
                    <div class="block">
                        <p class="help">
                            Close and reopen window to install or ignore until later.
                        </p>
                    </div>
                </div>
                <footer class="card-footer">
                    <div class="card-footer-item">
                        <div class="field is-grouped">
                            <div class="control">
                                <button class="button" @click="open = false">
                                    <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
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
        };
    },
    mounted() {
        const element = document.getElementById('sharp');

        if (element instanceof HTMLAudioElement) {
            this.sound = element;
        } else {
            console.log('Notification sound not found on DOM.');
        }

        bus.$on('service-worker-installed', (payload) => {
            if (!this.open) {
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
