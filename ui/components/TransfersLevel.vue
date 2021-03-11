<template>
    <div class="level">
        <div class="level-item has-text-centered">
            <div>
                <p class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The number of bytes received in request bodies.">
                    <span class="heading"><span class="icon"><i class="fas fa-download mr-3"></i></span>Received</span>
                </p>
                <p class="title">{{ received }}</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The number of bytes sent in response bodies.">
                    <span class="heading"><span class="icon"><i class="fas fa-upload mr-3"></i></span>Sent</span>
                </p>
                <p class="title">{{ sent }}</p>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import Vue from 'vue';
import gql from 'graphql-tag';

const MEGABYTE = 1000000;

export const fragment = gql`
    fragment TransfersLevel on Server {
        httpStats {
            transfers {
                received
                sent
            }
        }
    }
`;

export default Vue.extend({
    props: {
        transfers: {
            type: Object,
            required: true,
        },
    },
    computed: {
        received() : string {
            return (this.transfers.received / MEGABYTE).toFixed(1) + 'M';
        },
        sent() : string {
            return (this.transfers.sent / MEGABYTE).toFixed(1) + 'M';
        },
    },
});
</script>
