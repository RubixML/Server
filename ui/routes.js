import Server from './pages/Server.vue';

export default [
    { 
        name: 'home',
        path: '/',
        redirect: { name: 'server' }
    },
    {
        name: 'server',
        path: '/server',
        component: Server,
    },
];