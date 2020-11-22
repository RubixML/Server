import ServerDashboard from './pages/ServerDashboard.vue';

export default [
    { 
        name: 'home',
        path: '/',
        redirect: { name: 'server' }
    },
    {
        name: 'server',
        path: '/server',
        component: ServerDashboard,
    },
];