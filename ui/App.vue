<template>
    <div>
        <main-nav></main-nav>
        <main>
            <router-view></router-view>
        </main>
        <main-footer></main-footer>
        <app-update-available></app-update-available>
        <communication-error></communication-error>
        <audio id="sharp" src="./sounds/sharp.ogg" crossOrigin="anonymous"></audio>
    </div>
</template>
