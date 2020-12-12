import Visualizer from './pages/Visualizer.vue';
import DatasetScatterplot from './pages/DatasetScatterplot.vue';
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
                name: 'dataset-scatterplot',
                path: 'scatterplot',
                component: DatasetScatterplot,
            },
        ],
    },
    {
        name: 'dashboard',
        path: '/ui/dashboard',
        component: Dashboard,
    },
];