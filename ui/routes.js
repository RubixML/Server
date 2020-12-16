import Visualizer from './pages/Visualizer.vue';
import Dataset2DScatterplot from './pages/Dataset2DScatterplot.vue';
import Dashboard from './pages/Dashboard.vue';

export default [
    { 
        name: 'home',
        path: '/',
        redirect: { name: 'dashboard' }
    },
    {
        name: 'visualizer',
        path: '/ui/visualizer',
        component: Visualizer,
        children: [
            {
                name: 'dataset-2d-scatterplot',
                path: 'scatterplot-2d',
                component: Dataset2DScatterplot,
            },
        ],
    },
    {
        name: 'dashboard',
        path: '/ui/dashboard',
        component: Dashboard,
    },
];