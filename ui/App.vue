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
