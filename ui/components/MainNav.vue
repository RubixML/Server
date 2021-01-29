<template>
    <nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <span class="navbar-item">
                <img src="../images/app-icon-small.png" width="28" height="28" loading="eager" alt="The Rubix ML logo" />
            </span>
            <h1 class="navbar-item">Rubix Server</h1>
            <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" @click="open = !open">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>
        <div id="nav-menu" class="navbar-menu" :class="{ 'is-active' : open }">
            <div class="navbar-end">
                <router-link :to="{ name: 'dashboard' }" class="navbar-item" @click="open = false">
                    <span class="icon"><i class="fas fa-server"></i></span>
                    <span>Dashboard</span>
                </router-link>
            </div>
        </div>
    </nav>
</template>

<script lang="ts">
import Vue from 'vue';

export default Vue.extend({
    data() {
        return {
            open: false,
        };
    },
});
</script>
