<template>
    <div class="pageloader is-bottom-to-top" :class="{ 'is-active' : loading }">
        <img src="/images/grid.svg" width="100" height="100" class="is-loading-image" />
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

.is-loading-image {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100px;
    height: 100px;
    margin-top: -50px;
    margin-left: -50px;
}
</style>
