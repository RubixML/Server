<template>
    <div class="modal" :class="{ 'is-active' : open }">
        <div class="modal-background"></div>
        <div class="modal-content">
            <div class="box has-text-centered">
                <div class="block">
                    <span class="icon is-large"><i class="fas fa-2x fa-rocket"></i></span>
                </div>
                <p class="block is-size-5">
                    An update is ready to be installed
                </p>
                <p class="block help">
                    Install the new update now or ignore to install later.
                </p>
                <div class="block buttons is-centered">
                    <button class="button is-info" @click="update()">
                        <span class="icon"><i class="fas fa-check"></i></span>
                        <span>Update</span>
                    </button>
                    <button class="button is-danger" @click="open = false">
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

        bus.$on('update-ready', (payload) => {
            if (!this.open) {
                this.open = true;

                this.beep();
                this.vibrate();
            }
        });
    },
    methods: {
        update() : void {
            bus.$emit('update-accepted');

            this.open = false;
        },
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
