<template>
    <nav class="level">
        <div class="level-item has-text-centered">
            <div>
                <p class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The number of bytes received in request bodies.">
                    <span class="heading">Received<span class="icon"><i class="fas fa-download ml-3"></i></span></span>
                </p>
                <p class="title">{{ received.toFixed(1) }}M</p>
            </div>
        </div>
        <div class="level-item has-text-centered">
            <div>
                <p class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The number of bytes sent in response bodies.">
                    <span class="heading">Sent<span class="icon"><i class="fas fa-upload ml-3"></i></span></span>
                </p>
                <p class="title">{{ sent.toFixed(1) }}M</p>
            </div>
        </div>
    </nav>
</template>

<script>
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
    },
}
</script>
