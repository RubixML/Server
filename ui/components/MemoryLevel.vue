<template>
    <nav class="level">
        <div class="level-item has-text-centered">
            <div>
                <p class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The number of bytes currently allocated to the server process.">
                    <span class="heading">Current<span class="icon"><i class="fas fa-memory ml-3"></i></span></span>
                </p>
                <p class="title">{{ current.toFixed(1) }}M</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The maximum number of bytes consumed by the server process so far.">
                    <span class="heading">Peak<span class="icon"><i class="fas fa-memory ml-3"></i></span></span>
                </p>
                <p class="title">{{ peak.toFixed(1) }}M</p>
            </div>
        </div>
    </nav>
</template>

<script>
import gql from 'graphql-tag';

const MEGABYTE = 1000000;

export const fragment = gql`
    fragment MemoryLevel on Server {
        memory {
            current
            peak
        }
    }
`;

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
    },
}
</script>
