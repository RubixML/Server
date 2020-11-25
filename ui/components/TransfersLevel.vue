<template>
    <nav class="level is-mobile">
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Received</p>
                <p class="title">{{ received.toFixed(2) }}M</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Sent</p>
                <p class="title">{{ sent.toFixed(2) }}M</p>
            </div>
        </div>
    </nav>
</template>

<script>
const MEGABYTE = 1000000;

export default {
    props: {
        httpStats: {
            type: Object,
            required: true,
        },
    },
    computed: {
        received() {
            return this.httpStats.transferred.received / MEGABYTE;
        },
        sent() {
            return this.httpStats.transferred.sent / MEGABYTE;
        }
    }
}
</script>
