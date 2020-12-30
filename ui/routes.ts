import Visualizer from './pages/Visualizer.vue';
import LineChart from './pages/LineChart.vue';
import BubblePlot from './pages/BubblePlot.vue';
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
                name: 'line-chart',
                path: 'line',
                component: LineChart,
            },
            {
                name: 'bubble-plot',
                path: 'bubble',
                component: BubblePlot,
            },
        ],
    },
    {
        name: 'dashboard',
        path: '/ui/dashboard',
        component: Dashboard,
    },
];
