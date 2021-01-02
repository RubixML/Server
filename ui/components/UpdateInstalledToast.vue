<template>
    <div class="notification is-primary is-toast" :class="{ 'is-hidden' : !open }">
        <button class="delete" @click="open = false"></button>
        <p class="has-text-centered">Update Installed</p>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import bus from '../bus';

export default Vue.extend({
    data() {
        return {
            open: false,
        };
    },
    mounted() {
        bus.$on('update-installed', (payload) => {
            if (!this.open) {
                this.open = true;
            }
        });
    },
});
</script>

<style lang="scss" scoped>
.is-toast {
    position: fixed;
    top: 30px;
    left: 50%;
    min-width: 300px;
    margin-left: -150px;
    z-index: 99;
}
</style>