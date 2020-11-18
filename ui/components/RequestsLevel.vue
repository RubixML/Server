<template>
    <nav class="level is-mobile">
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Requests</p>
                <p class="title">{{ received }}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Request Rate</p>
                <p><span class="title">{{ rate }}</span>/min</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Successful</p>
                <p class="title">{{ successful }}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Failed</p>
                <p class="title">{{ failed }}</p>
            </div>
        </div>
    </nav>
</template>

<script>
export default {
    props: {
        requests: {
            type: Object,
            required: true,
        },
    },
    computed: {
        received() {
            return this.requests.received ? this.requests.received.toLocaleString() : 0;
        },
        rate() {
            return this.requests.rate ? this.requests.rate.toFixed(1) : 0.0;
        },
        successful() {
            return this.requests.successful ? this.requests.successful.toLocaleString() : 0;
        },
        failed() {
            return this.requests.failed ? this.requests.failed.toLocaleString() : 0;
        },
    }
}
</script>