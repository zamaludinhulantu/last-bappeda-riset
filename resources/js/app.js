import './bootstrap';

import Alpine from 'alpinejs';
import '@fortawesome/fontawesome-free/css/all.min.css';

window.Alpine = Alpine;

// Lazy-load Chart.js only when needed to keep the initial bundle small
window.loadChart = async () => {
    if (window.Chart) return window.Chart;
    const { default: Chart } = await import('chart.js/auto');
    window.Chart = Chart;
    return Chart;
};

Alpine.start();
