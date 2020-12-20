import Visualizer from './pages/Visualizer.vue';
import BubbleChart from './pages/BubbleChart.vue';
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
                name: 'bubble-chart',
                path: 'bubble',
                component: BubbleChart,
            },
        ],
    },
    {
        name: 'dashboard',
        path: '/ui/dashboard',
        component: Dashboard,
    },
];