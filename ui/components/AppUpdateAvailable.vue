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
                    <button class="button is-info is-outlined" @click="update()">
                        <span class="icon"><i class="fas fa-check"></i></span>
                        <span>Update</span>
                    </button>
                    <button class="button is-outlined is-danger" @click="open = false">
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
        };
    },
    methods: {
        update() : void {
            bus.$emit('update-accepted');

            this.open = false;
        },
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

        bus.$on('update-ready', () => {
            if (!this.open) {
                this.open = true;

                this.ding();
                this.vibrate();
            }
        });
    },
});
</script>
