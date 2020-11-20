<template>
    <nav class="level">
        <div class="level-item has-text-centered">
            <div v-if="requests.successful != undefined">
                <p class="heading">Successful</p>
                <p class="title">{{ requests.successful.toLocaleString() }}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div v-if="requests.rejected != undefined">
                <p class="heading">Rejected</p>
                <p class="title">{{ requests.rejected.toLocaleString() }}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div v-if="requests.failed != undefined">
                <p class="heading">Failed</p>
                <p class="title">{{ requests.failed.toLocaleString() }}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="heading">Total</p>
                <p class="title">{{ total.toLocaleString() }}</p>
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
        total() {
            return this.requests.successful + this.requests.rejected + this.requests.failed;
        }
    }
}
</script>