<template>
    <nav class="level is-mobile">
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Requests</p>
                <p class="title">{{ requests.received.toLocaleString() }}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Request Rate</p>
                <p class="title">{{ requests.rate.toFixed(1) }}/m</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Successful</p>
                <p class="title">{{ requests.successful.toLocaleString() }}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Failed</p>
                <p class="title">{{ requests.failed.toLocaleString() }}</p>
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
}
</script>