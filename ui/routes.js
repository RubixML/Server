export default [
    { 
        name: 'home',
        path: '/',
        redirect: { name: 'server' }
    },
    {
        name: 'server',
        path: '/server',
        component: require('./pages/Server.vue').default,
    },
];