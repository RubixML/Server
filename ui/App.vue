<template>
    <div>
        <main-nav></main-nav>
        <main>
            <router-view></router-view>
        </main>
        <communication-error></communication-error>
        <audio id="sharp" src="/sounds/sharp.ogg"></audio>
    </div>
</template>

<script>
import bus from './bus';

export default {
    mounted() {
        this.$sse('/events', { format: 'json' }).then((sse) => {
            sse.subscribe('http-stats-update', (message, event) => {
                bus.$emit(event, message);
            });
        });
    },
}
</script>