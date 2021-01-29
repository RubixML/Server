import Dashboard from './pages/Dashboard.vue';

export default [
    { 
        name: 'home',
        path: '/',
        redirect: { name: 'dashboard' },
    },
    {
        name: 'dashboard',
        path: '/dashboard',
        component: Dashboard,
    },
];
