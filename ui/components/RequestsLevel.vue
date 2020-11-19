<template>
    <nav class="level is-mobile">
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Successful</p>
                <p class="title">{{ successful }}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Rejected</p>
                <p class="title">{{ rejected }}</p>
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
        successful() {
            return this.requests.successful ? this.requests.successful.toLocaleString() : 0;
        },
        rejected() {
            return this.requests.rejected ? this.requests.rejected.toLocaleString() : 0;
        },
        failed() {
            return this.requests.failed ? this.requests.failed.toLocaleString() : 0;
        },
    }
}
</script>