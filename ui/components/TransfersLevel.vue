<template>
    <nav class="level is-mobile">
        <div class="level-item has-text-centered">
            <div>
                <p class="heading"><span class="icon"><i class="fas fa-download mr-3"></i></span><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The number of bytes received in request bodies so far.">Received</span></p>
                <p class="title">{{ received.toFixed(1) }}M</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading"><span class="icon"><i class="fas fa-upload mr-3"></i></span><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The number of bytes sent in response bodies so far.">Sent</span></p>
                <p class="title">{{ sent.toFixed(1) }}M</p>
            </div>
        </div>
    </nav>
</template>

<script>
const MEGABYTE = 1000000;

export default {
    props: {
        transfers: {
            type: Object,
            required: true,
        },
    },
    computed: {
        received() {
            return this.transfers.received / MEGABYTE;
        },
        sent() {
            return this.transfers.sent / MEGABYTE;
        },
    }
}
</script>
