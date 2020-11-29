<template>
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <span class="navbar-item">
                <img src="/images/app-icon-small.png" width="28" height="28" loading="eager" alt="The Rubix ML logo" />
            </span>
            <h1 class="navbar-item has-text-weight-normal">Rubix ML</h1>
            <a role="button" class="navbar-burger burger" aria-label="menu" aria-expanded="false" @click="open = !open">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>
        <div id="nav-menu" class="navbar-menu" :class="{ 'is-active' : open }">
            <div class="navbar-end">
                <div class="navbar-item">
                    <router-link :to="{ name: 'server'}" class="navbar-item button is-light" @click.native="open = false">
                        <span class="icon"><i class="fas fa-server"></i></span>
                        <span>Server</span>
                    </router-link>
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