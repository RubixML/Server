<template>
    <div>
        <main-nav></main-nav>
        <main>
            <router-view></router-view>
        </main>
        <main-footer></main-footer>
        <audio id="sharp" src="/sounds/sharp.ogg" crossOrigin="anonymous"></audio>
        <communication-error></communication-error>
        <dataset-import-failure></dataset-import-failure>
    </div>
</template>
