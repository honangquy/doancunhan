import './bootstrap';
import Alpine from 'alpinejs';
import { Chart, registerables } from 'chart.js';

// Initialize Alpine.js
window.Alpine = Alpine;
Alpine.start();

// Initialize Chart.js
Chart.register(...registerables);
window.Chart = Chart;

