<template>
    <div class="pageloader is-bottom-to-top" :class="{ 'is-active' : loading }">
        <div class="is-loading-grid"></div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue'

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
.pageloader {
    background-color: hsl(0, 0%, 96%);
    z-index: 9;
}

.pageloader::after {
    display: none;
}

@media (prefers-color-scheme: dark) {
    .pageloader {
        background-color: hsl(0, 0%, 11%);
    }
}

.is-loading-grid {
    width: 100%;
    height: 100%;
    background-image: url('../images/grid.svg');
    background-size: 15%;
    background-position: center;
    background-repeat: no-repeat;
}
</style>
