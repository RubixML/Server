<template>
    <nav class="level is-mobile">
        <div class="level-item has-text-centered">
            <div>
                <p class="heading"><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The number of bytes currently allocated to the server process.">Current</span></p>
                <p class="title">{{ current.toFixed(2) }}M</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading"><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum number of bytes consumed by the server process so far.">Peak</span></p>
                <p class="title">{{ peak.toFixed(2) }}M</p>
            </div>
        </div>
    </nav>
</template>

<script>
const MEGABYTE = 1000000;

export default {
    props: {
        memory: {
            type: Object,
            required: true,
        },
    },
    computed: {
        current() {
            return this.memory.current / MEGABYTE;
        },
        peak() {
            return this.memory.peak / MEGABYTE;
        },
    }
}
</script>
