<template>
    <div class="is-page-loader" v-show="loading">
        <img src="../images/grid.svg" class="grid-loader" width="128" height="128" alt="Loading image" />
    </div>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
    props: {
        loading: {
            type: Boolean,
            required: true,
        },
    },
});
</script>

<style lang="scss" scoped>
.is-page-loader {
    display: flex;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: hsl(0, 0%, 96%);
    justify-content: center;
    align-items: center;
    overflow: hidden;
    z-index: 9;
}

@media (prefers-color-scheme: dark) {
    .is-page-loader {
        background-color: hsl(228, 9.8%, 10%);
    }
}

.grid-loader {
    width: 128px;
    height: 128px;
}
</style>
