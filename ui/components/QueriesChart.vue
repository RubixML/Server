<template>
    <canvas id="canvas" width="600" height="600"></canvas>
</template>

<script>
export default {
    data() {
        return {
            chart: null,
        };
    },
    props: {
        queries: {
            type: Object,
            required: true,
        },
    },
    mounted() {
        let context = document.getElementById('canvas').getContext('2d');
    },
}
</script>