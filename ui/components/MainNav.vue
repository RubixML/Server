<template>
    <nav class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <span class="navbar-item">
                <img src="/images/app-icon-small.png" width="28" height="28" loading="eager" alt="The Rubix ML logo" />
            </span>
            <h1 class="navbar-item"><span>Rubix</span><span class="has-text-weight-medium ml-1">ML</span></h1>
            <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" @click="open = !open">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>
        <div id="nav-menu" class="navbar-menu" :class="{ 'is-active' : open }">
            <div class="navbar-end">
                <div class="navbar-item">
                    <div class="navbar-item has-dropdown is-hoverable">
                        <div class="navbar-link">
                            <span class="icon"><i class="fas fa-chart-area"></i></span>
                            <span>Visualizer</span>
                        </div>
                        <div class="navbar-dropdown is-shadowless">
                            <router-link :to="{ name: 'line-chart' }" class="navbar-item" @click.native="open = false">
                                <span>Line Chart</span>
                            </router-link>
                            <router-link :to="{ name: 'bubble-plot' }" class="navbar-item" @click.native="open = false">
                                <span>Bubble Plot</span>
                            </router-link>
                        </div>
                    </div>
                    <router-link :to="{ name: 'dashboard' }" class="navbar-item" @click.native="open = false">
                        <span class="icon"><i class="fas fa-server"></i></span>
                        <span>Dashboard</span>
                    </router-link>
                </div>
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