<template>
    <div class="columns">
        <div class="column is-half">
            <nav class="level is-mobile">
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading"><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="Requests that returned a 100, 200, or 300 level response code.">Successful</span></p>
                        <p class="title">{{ requests.successful.toLocaleString() }}</p>
                    </div>
                </div>
            </nav>
        </div>
        <div class="column is-half">
            <nav class="level is-mobile">
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading"><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="Requests that were rejected due to a client error (4xx).">Rejected</span></p>
                        <p class="title">{{ requests.rejected.toLocaleString() }}</p>
                    </div>
                </div>
                <div class="level-item has-text-centered">
                    <div>
                        <p class="heading"><span class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="Requests that failed due to a server error (5xx).">Failed</span></p>
                        <p class="title">{{ requests.failed.toLocaleString() }}</p>
                    </div>
                </div>
            </nav>
        </div>
    </div>
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
