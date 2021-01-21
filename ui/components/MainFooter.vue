<template>
    <footer class="footer mt-5">
        <div class="content">
            <p class="is-size-7 has-text-weight-light has-text-centered">© {{ year }} Rubix ML</p>
        </div>
    </footer>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
    computed: {
        year() : number {
            return new Date().getFullYear();
        },
    },
});
</script>
