<template>
    <div class="modal" :class="{ 'is-active' : open }">
        <div class="modal-background"></div>
        <div class="modal-content">
            <div class="card has-text-centered">
                <div class="card-content">
                    <div class="content">
                        <span class="icon is-large">
                            <span class="fa-stack fa-lg">
                                <i class="fas fa-file fa-stack-1x"></i>
                                <i class="fas fa-ban fa-stack-2x has-text-danger"></i>
                            </span>
                        </span>
                        <p class="is-size-5">
                            There was a problem importing the dataset.
                        </p>
                        <p class="error-message is-size-7">
                            {{ message }}
                        </p>
                    </div>
                </div>
                <footer class="card-footer">
                    <div class="card-footer-item">
                        <button class="button is-white" @click="open = false">
                            <span class="icon"><i class="fas fa-times"></i></span>
                            <span>Dismiss</span>
                        </button>
                    </div>
                </footer>
            </div>
        </div>
    </div>
</template>

<script>
import bus from '../bus';

const VIBRATE_PATTERN = [100, 30, 100];

export default {
    data() {
        return {
            open: false,
            message: '',
        };
    },
    mounted() {
        bus.$on('dataset-import-failed', (payload) => {
            if (!this.open) {
                this.message = payload.error.message;
                this.open = true;

                document.getElementById('sharp').play();

                window.navigator.vibrate(VIBRATE_PATTERN);
            }
        });
    }
}
</script>
