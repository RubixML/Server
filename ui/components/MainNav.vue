<template>
    <nav class="navbar is-white is-fixed-top" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <h1 class="navbar-item is-size-5 is-uppercase">Tank Robot</h1>
            <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" @click="open = !open">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>
        <div class="navbar-menu" :class="{ 'is-active' : open }">
            <div class="navbar-end">
                <div class="navbar-item">
                    <div class="navbar-item">
                        <router-link :to="{ name: 'control'}" class="button is-white is-fullwidth" @click.native="open = false">
                            <span class="icon"><i class="fas fa-arrows-alt"></i></span>
                            <span>Controls</span>
                        </router-link>
                        <router-link :to="{ name: 'settings'}" class="button is-white is-fullwidth" @click.native="open = false">
                            <span class="icon"><i class="fas fa-cog"></i></span>
                            <span>Settings</span>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
    export default {
        data() {
            return {
                open: false,
            };
        },
    }
</script>
