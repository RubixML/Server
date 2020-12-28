<template>
    <div class="level">
        <div class="level-item has-text-centered">
            <div>
                <p class="has-tooltip-arrow has-tooltip-top has-tooltip-multiline" data-tooltip="The number of samples that have been predicted by the model.">
                    <span class="heading"><span class="icon"><i class="fas fa-brain mr-3"></i></span>Samples Predicted</span>
                </p>
                <p class="title">{{ model.numSamplesInferred.toLocaleString() }}</p>
            </div>
        </div>
    </div>
</template>

<script>
import gql from 'graphql-tag';

export const fragment = gql`
    fragment InferenceLevel on Model {
        numSamplesInferred
    }
`;

export default {
    props: {
        model: {
            type: Object,
            required: true,
        },
    },
}
</script>
